<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to fetch the winner candidate for each department
function fetchWinner($conn, $department)
{
    $sql = "SELECT v.Candidate_ID, c.Name AS Candidate_Name, COUNT(*) AS vote_count 
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

// Fetch all departments
$sql = "SELECT DISTINCT Department FROM candidates";
$result = mysqli_query($conn, $sql);
$departments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = $row['Department'];
}

// Fetch winner and other candidates for each department
$departmentWinners = [];
foreach ($departments as $department) {
    $winner = fetchWinner($conn, $department);
    $departmentWinners[$department]['Winner'] = $winner;
    $departmentWinners[$department]['OtherCandidates'] = [];

    // Fetch details of other candidates for the department
    $sql = "SELECT Candidate_ID, Name, Gender, Position FROM candidates WHERE Department = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $department);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        // Exclude winner candidate from other candidates
        if ($row['Candidate_ID'] !== $winner['Candidate_ID']) {
            $departmentWinners[$department]['OtherCandidates'][] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Winners</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2>Announce Winners by Department</h2>
    <?php foreach ($departmentWinners as $department => $data): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h4><?php echo $department; ?> Department</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($data['Winner'])): ?>
                    <h5 class="card-title">Winner: <?php echo $data['Winner']['Candidate_Name']; ?></h5>
                    <p class="card-text">Votes Taken: <?php echo $data['Winner']['vote_count']; ?></p>
                <?php else: ?>
                    <p class="card-text">No winner yet for this department.</p>
                <?php endif; ?>
                <h6 class="card-subtitle mb-2 text-muted">Other Candidates:</h6>
                <ul class="list-group">
                    <?php if (!empty($data['OtherCandidates'])): ?>
                        <?php foreach ($data['OtherCandidates'] as $candidate): ?>
                            <li class="list-group-item">
                                <?php echo $candidate['Name']; ?> - <?php echo $candidate['Gender']; ?> - <?php echo $candidate['Position']; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">No other candidates found.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
