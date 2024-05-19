<?php
include('config.php');
session_start();

// Check if the user is logged in as a voter
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

// Check if the voter has already voted
$voter_id = $_SESSION["voter_id"];
$sql = "SELECT COUNT(*) FROM votes WHERE Voter_ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $voter_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $vote_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// If the voter has already voted, redirect them to the dashboard
if ($vote_count > 0) {
    header("Location: voter_dashboard.php");
    exit;
}

// Process the vote submission
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["candidate_id"])) {
    $candidate_id = $_GET["candidate_id"];

    // Insert the vote into the database
    $sql = "INSERT INTO votes (Voter_ID, Candidate_ID) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $voter_id, $candidate_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect to the dashboard after successful vote submission
    header("Location: voter_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>Vote for a Candidate</h2>
        <p>Select a candidate to cast your vote:</p>
        <ul class="list-group">
            <?php
            // Retrieve approved candidates
            $sql = "SELECT Candidate_ID, Name, Department, Gender, Position FROM candidates WHERE Approval_Status = 'Approved'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<li class='list-group-item'>" . $row["Name"] . " | " . $row["Department"] . " | " . $row["Gender"] . " | " . $row["Position"] . " | <a href='vote.php?candidate_id=" . $row["Candidate_ID"] . "' class='btn btn-primary'>Vote</a></li>";
                }
            } else {
                echo "<li class='list-group-item'>No candidates available</li>";
            }
            ?>
        </ul>
        <p><a href="voter_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
