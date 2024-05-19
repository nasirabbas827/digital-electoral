<?php
include('config.php');

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    // Redirect user to login page if not logged in
    header("location: login.php");
    exit;
}

// Check if the user has a voter ID and password assigned
$user_id = $_SESSION["id"];
$sql = "SELECT Voter_ID, Voter_Password FROM voter_table WHERE user_ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$voter_info = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <?php if ($voter_info): ?>
        <!-- Your home page content here -->
        <p>Your Voter ID: <?php echo $voter_info['Voter_ID']; ?></p>
        <p>Your Voter Password: <?php echo $voter_info['Voter_Password']; ?></p>
    <?php else: ?>
        <!-- Voter ID and password not assigned, show message -->
        <div class="alert alert-warning" role="alert">
            Your voter ID and password are not assigned yet. Please wait for approval.
        </div>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>