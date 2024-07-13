<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$user_id = "";
$user_id_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate user ID
    if (empty(trim($_POST["user_id"]))) {
        $user_id_err = "Please select a user.";
    } else {
        $user_id = trim($_POST["user_id"]);
    }

    // Check for errors before inserting into database
    if (empty($user_id_err)) {
        // Generate random voter ID and password
        $voter_id = bin2hex(random_bytes(4));
        $voter_password = bin2hex(random_bytes(6));

        // Prepare an insert statement
        $sql = "INSERT INTO voter_table (Voter_ID, Voter_Password, Registered_Date, Approval_Status, user_ID) VALUES (?, ?, NOW(), 'Approved', ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_voter_id, $param_voter_password, $param_user_id);

            // Set parameters
            $param_voter_id = $voter_id;
            $param_voter_password = $voter_password;
            $param_user_id = $user_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to admin dashboard after successful assignment
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Voter ID and Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Assign Voter ID and Password</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select name="user_id" class="form-control <?php echo (!empty($user_id_err)) ? 'is-invalid' : ''; ?>" required>
                <option value="" selected disabled>Select User</option>
                <?php
                // Fetch users who do not have an assigned voter ID and password from the database
                $sql = "SELECT u.id, u.name FROM users u LEFT JOIN voter_table v ON u.id = v.user_ID WHERE v.user_ID IS NULL";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                }
                ?>
            </select>
            <span class="invalid-feedback"><?php echo $user_id_err; ?></span>
        </div>
        <div class="form-group text-center">
            <button type="button" id="generateBtn" class="btn btn-primary">Generate Voter ID and Password</button>
            <a class="btn btn-outline-dark" href="view_voters.php">View Voters</a>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#generateBtn').click(function() {
        $('form').submit();
    });
});
</script>
</body>
</html>
