<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if voter ID parameter exists in URL
if (!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
    header("Location: view_voters.php");
    exit;
}

// Initialize variables
$voter_id = trim($_GET["id"]);
$voter_password = $new_password = $approval_status = "";
$voter_password_err = $new_password_err = $approval_status_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate approval status
    if (empty(trim($_POST["approval_status"]))) {
        $approval_status_err = "Please select the approval status.";
    } else {
        $approval_status = trim($_POST["approval_status"]);
    }

    // Check for errors before updating the database
    if (empty($new_password_err) && empty($approval_status_err)) {

        // Prepare an update statement
        $sql = "UPDATE voter_table SET Voter_Password = ?, Approval_Status = ? WHERE Voter_ID = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_new_password, $param_approval_status, $param_voter_id);

            // Set parameters
            $param_new_password = $new_password;
            $param_approval_status = $approval_status;
            $param_voter_id = $voter_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the view voters page after successful update
                header("location: view_voters.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}

// Fetch voter details based on voter ID
$sql = "SELECT Voter_Password, Approval_Status FROM voter_table WHERE Voter_ID = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_voter_id);

    // Set parameters
    $param_voter_id = $voter_id;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if voter ID exists, if yes then fetch the password and approval status
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $voter_password, $approval_status);
            if (mysqli_stmt_fetch($stmt)) {
                // Voter password and approval status are fetched successfully
            }
        } else {
            // Voter ID does not exist, redirect to error page
            header("location: error.php");
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Voter Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Edit Voter Details</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $voter_id; ?>" method="post">
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
            <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
        </div>
        <div class="form-group">
            <label for="approval_status">Approval Status:</label>
            <select name="approval_status" class="form-control <?php echo (!empty($approval_status_err)) ? 'is-invalid' : ''; ?>">
                <option value="" disabled>Select Approval Status</option>
                <option value="Approved" <?php if ($approval_status === "Approved") echo "selected"; ?>>Approved</option>
                <option value="Rejected" <?php if ($approval_status === "Rejected") echo "selected"; ?>>Rejected</option>
            </select>
            <span class="invalid-feedback"><?php echo $approval_status_err; ?></span>
        </div>
        <div class="form-group text-center">
            <input type="submit" class="btn btn-primary" value="Update">
            <a href="view_voters.php" class="btn btn-secondary ml-2">Cancel</a>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
