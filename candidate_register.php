<?php
include('config.php');

session_start();

// Define variables and initialize with empty values
$name = $department = $gender = "";
$name_err = $department_err = $gender_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate department
    if (empty(trim($_POST["department"]))) {
        $department_err = "Please enter your department.";
    } else {
        $department = trim($_POST["department"]);
    }

    // Validate gender
    if (empty(trim($_POST["gender"]))) {
        $gender_err = "Please select your gender.";
    } else {
        $gender = trim($_POST["gender"]);
    }

    // Validate profile picture
    if (empty($_FILES["profile_pic"]["name"])) {
        $profile_pic_err = "Please select a profile picture.";
    } else {
        // Get file extension
        $file_extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
        // Generate a unique filename
        $target_file = "candidate_pics/" . uniqid() . "." . $file_extension;
        // Move uploaded file to candidate_pics folder
        if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic_err = "Error uploading file.";
        }
    }

    // Check for errors before inserting into database
    if (empty($name_err) && empty($department_err) && empty($gender_err) && empty($profile_pic_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO candidates (Profile_Pic, Name, Department, Gender, Position, Approval_Status) VALUES (?, ?, ?, ?, ?, 'Pending')";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_profile_pic, $param_name, $param_department, $param_gender, $param_position);

            // Set parameters
            $param_profile_pic = $target_file;
            $param_name = $name;
            $param_department = $department;
            $param_gender = $gender;
            $param_position = 'Chairperson';

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page after successful registration
                echo "Candidate Registered Successfully - Wait For Admin Approval.";

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
    <title>Candidate Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php
include('navbar.php');
?>
<div class="container">
    <h2 class="mt-5">Candidate Registration</h2>
    <p>Please fill in your details to register as a candidate.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
            <span class="invalid-feedback"><?php echo $name_err; ?></span>
        </div>
        <div class="form-group">
            <label for="department">Department:</label>
            <input type="text" name="department" class="form-control <?php echo (!empty($department_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $department; ?>">
            <span class="invalid-feedback"><?php echo $department_err; ?></span>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select name="gender" class="form-control <?php echo (!empty($gender_err)) ? 'is-invalid' : ''; ?>">
                <option value="" selected disabled>Select your gender</option>
                <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
            </select>
            <span class="invalid-feedback"><?php echo $gender_err; ?></span>
        </div>
        <!-- Position field (disabled) -->
        <div class="form-group">
            <label for="position">Position:</label>
            <input type="text" name="position" class="form-control" value="Chairperson" disabled>
        </div>
        <div class="form-group">
            <label for="profile_pic">Profile Picture:</label>
            <input type="file" name="profile_pic" class="form-control-file <?php echo (!empty($profile_pic_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $profile_pic_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Register">
        </div>
    </form>
</div>
</body>
</html>
