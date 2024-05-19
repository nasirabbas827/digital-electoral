<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch casted votes for each candidate including department
$sql = "SELECT v.Candidate_ID, c.Name AS Candidate_Name, c.Department, COUNT(*) AS vote_count 
        FROM votes v
        INNER JOIN candidates c ON v.Candidate_ID = c.Candidate_ID 
        GROUP BY v.Candidate_ID";
$result = mysqli_query($conn, $sql);

// Total votes taken
$total_votes = 0;
$votes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total_votes += $row['vote_count'];
    $votes[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2>Casted Votes</h2>
    <!-- Search input field for filtering by department -->
    <input type="text" id="departmentSearch" class="form-control mb-3" placeholder="Search by Department">
    
    <table class="table" id="votesTable">
        <thead>
            <tr>
                <th>Candidate ID</th>
                <th>Candidate Name</th>
                <th>Candidate Department</th>
                <th>Vote Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($votes as $vote): ?>
                <tr>
                    <td><?php echo $vote['Candidate_ID']; ?></td>
                    <td><?php echo $vote['Candidate_Name']; ?></td>
                    <td><?php echo $vote['Department']; ?></td>
                    <td><?php echo $vote['vote_count']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>Total Votes Taken: <?php echo $total_votes; ?></p>
    
    <!-- Download Excel button -->
    <button class="btn btn-primary" id="downloadExcel">Download Excel</button>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

<script>
$(document).ready(function(){
    // Search and filter by department
    $("#departmentSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#votesTable tbody tr").filter(function() {
            $(this).toggle($(this).children().eq(2).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Download Excel file
    $("#downloadExcel").on("click", function() {
        var table = document.getElementById('votesTable');
        var sheet = XLSX.utils.table_to_sheet(table);
        var workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, sheet, 'Casted Votes');
        XLSX.writeFile(workbook, 'votes_records.xlsx');
    });
});
</script>
</body>
</html>

