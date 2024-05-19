<?php
include('config.php');
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Define variables for storing candidates data and errors
$candidates = [];
$candidate_id = $approval_status = "";
$candidate_id_err = $approval_status_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate candidate ID
    if (empty(trim($_POST["candidate_id"]))) {
        $candidate_id_err = "Please select a candidate.";
    } else {
        $candidate_id = trim($_POST["candidate_id"]);
    }

    // Validate approval status
    if (empty(trim($_POST["approval_status"]))) {
        $approval_status_err = "Please select approval status.";
    } else {
        $approval_status = trim($_POST["approval_status"]);
    }

    // Check for input errors before updating the database
    if (empty($candidate_id_err) && empty($approval_status_err)) {
        // Update candidate approval status
        $sql = "UPDATE candidates SET Approval_Status = ? WHERE Candidate_ID = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $param_approval_status, $param_candidate_id);
            $param_approval_status = $approval_status;
            $param_candidate_id = $candidate_id;
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to current page after successful update
                header("Location: ".$_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}

// Retrieve candidates data from database
$sql = "SELECT Candidate_ID, Profile_Pic, Name, Department, Gender, Position, Approval_Status FROM candidates";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $candidates[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include('admin_navbar.php'); ?>
    <div class="container mt-5">
        <h2 class="mb-3">Candidates List</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="candidate_id">Select Candidate:</label>
                <select name="candidate_id" class="form-control <?php echo (!empty($candidate_id_err)) ? 'is-invalid' : ''; ?>">
                    <option value="" selected disabled>Select candidate</option>
                    <?php foreach ($candidates as $candidate): ?>
                        <option value="<?php echo $candidate['Candidate_ID']; ?>"><?php echo $candidate['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback"><?php echo $candidate_id_err; ?></span>
            </div>
            <div class="form-group">
                <label for="approval_status">Approval Status:</label>
                <select name="approval_status" class="form-control <?php echo (!empty($approval_status_err)) ? 'is-invalid' : ''; ?>">
                    <option value="" selected disabled>Select approval status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
                <span class="invalid-feedback"><?php echo $approval_status_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Update Approval Status">
            </div>
        </form>
        <div class="mt-5">
            <h4>Candidates List:</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Profile Pic</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Gender</th>
                        <th>Position</th>
                        <th>Approval Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr>
                            <td><?php echo $candidate['Candidate_ID']; ?></td>
                            <td><img src="../<?php echo $candidate['Profile_Pic']; ?>" alt="Profile Pic" style="max-width: 100px;"></td>
                            <td><?php echo $candidate['Name']; ?></td>
                            <td><?php echo $candidate['Department']; ?></td>
                            <td><?php echo $candidate['Gender']; ?></td>
                            <td><?php echo $candidate['Position']; ?></td>
                            <td><?php echo $candidate['Approval_Status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
