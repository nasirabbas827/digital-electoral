<?php
include('config.php');
session_start();

// Define variables and initialize with empty values
$voter_id = $voter_password = "";
$voter_id_err = $voter_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Voter ID
    if (empty(trim($_POST["voter_id"]))) {
        $voter_id_err = "Please enter your Voter ID.";
    } else {
        $voter_id = trim($_POST["voter_id"]);
    }

    // Validate Voter Password
    if (empty(trim($_POST["voter_password"]))) {
        $voter_password_err = "Please enter your password.";
    } else {
        $voter_password = trim($_POST["voter_password"]);
    }

    // Check for errors before querying the database
    if (empty($voter_id_err) && empty($voter_password_err)) {
        // Prepare a select statement
        $sql = "SELECT Voter_ID, Voter_Password, user_ID FROM voter_table WHERE Voter_ID = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_voter_id);

            // Set parameters
            $param_voter_id = $voter_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if Voter ID exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $voter_id, $hashed_password, $user_id);
                    if (mysqli_stmt_fetch($stmt)) {
                        if ($voter_password == $hashed_password) {
                            // Password is correct, start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["voter_id"] = $voter_id;
                            $_SESSION["user_id"] = $user_id;

                            // Redirect user to dashboard page
                            header("location: voter/voter_dashboard.php");
                        } else {
                            // Display an error message if password is not valid
                            $voter_password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if Voter ID doesn't exist
                    $voter_id_err = "No account found with that Voter ID.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php
include('navbar.php');
?>
    <div class="container mt-5">
        <h2>Voter Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Voter ID</label>
                <input type="text" name="voter_id" class="form-control <?php echo (!empty($voter_id_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $voter_id; ?>">
                <span class="invalid-feedback"><?php echo $voter_id_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="voter_password" class="form-control <?php echo (!empty($voter_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $voter_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="voter_register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>
