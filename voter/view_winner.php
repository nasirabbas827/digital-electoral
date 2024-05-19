<?php
include('config.php');
session_start();

// Check if the user is logged in as a voter
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

// Check if the voter has voted
if (!isset($_SESSION["voter_id"])) {
    // Redirect to vote page if the voter hasn't voted
    header("Location: vote.php");
    exit;
}

// Fetch the voter's department
$user_id = $_SESSION["user_id"];
$sql = "SELECT department FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $department);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Function to fetch the winner for the voter's department
function fetchWinner($conn, $department)
{
    $sql = "SELECT v.Candidate_ID, c.Name AS Candidate_Name, c.Profile_Pic AS Candidate_Pic, COUNT(*) AS vote_count 
            FROM votes v
            INNER JOIN candidates c ON v.Candidate_ID = c.Candidate_ID
            WHERE c.Department = ?
            GROUP BY v.Candidate_ID
            ORDER BY vote_count DESC
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $department);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $winner = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $winner;
}

// Fetch the winner for the voter's department
$winner = fetchWinner($conn, $department);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5 mb-5">
    <h2>Voter Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION["voter_id"]; ?>!</p>
    <?php if ($winner): ?>
        <h4>Winner of Your Department</h4>
        <div class="card mt-3">
            <div class="card-header">
                <h5><?php echo $department; ?> Department Winner</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <img src="../<?php echo $winner['Candidate_Pic']; ?>" class="img-fluid" alt="Winner's Picture">
                    </div>
                    <div class="col-sm-9">
                        <h6 class="card-title">Name: <?php echo $winner['Candidate_Name']; ?></h6>
                        <p class="card-text">Votes Taken: <?php echo $winner['vote_count']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-3" role="alert">
            You need to cast your vote to view the winner of your department.
        </div>
    <?php endif; ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
