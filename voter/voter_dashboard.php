<?php
include('config.php');
session_start();

// Check if the user is logged in as a voter
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

// Retrieve voter's department from the user table
$user_id = $_SESSION["user_id"];
$sql = "SELECT department FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $department);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Retrieve approved candidates of the voter's department
$sql = "SELECT Candidate_ID, Name,Profile_Pic, Department, Gender, Position FROM candidates WHERE Approval_Status = 'Approved' AND Department = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $department);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$candidates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $candidates[] = $row;
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
<div class="container mt-5">
    <h2>Voter Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION["voter_id"]; ?>!</p>
    <h4>Approved Candidates in Your Department</h4>
    <div class="row">
        <?php $i = 0; ?>
        <?php foreach ($candidates as $candidate): ?>
            <?php if ($i % 3 == 0): ?>
                </div><div class="row">
            <?php endif; ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../<?php echo $candidate['Profile_Pic']; ?>" class="card-img-top" alt="Profile Picture">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $candidate['Name']; ?></h5>
                        <p class="card-text">Department: <?php echo $candidate['Department']; ?></p>
                        <p class="card-text">Gender: <?php echo $candidate['Gender']; ?></p>
                        <p class="card-text">Position: <?php echo $candidate['Position']; ?></p>
                        <a href="vote.php?candidate_id=<?php echo $candidate['Candidate_ID']; ?>" class="btn btn-primary <?php echo ($vote_count > 0) ? 'disabled' : ''; ?>">Vote</a>
                    </div>
                </div>
            </div>
            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
    <?php if ($vote_count > 0): ?>
        <p>You have already voted.</p>
    <?php endif; ?>
</div>
</body>
</html>

