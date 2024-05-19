<!DOCTYPE html>
<html>
<head>
    <title>Digital Electoral System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }

        .card-deck {
            margin-top: 20px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<div class="jumbotron text-center">
    <h1>Welcome to Digital Electoral System for Chairperson Selection</h1>
    <p>Empowering Your Vote, Shaping Your Future</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Participate</a>
</div>

<div class="container">
    <?php
    include "config.php";
    // Fetch unique departments
    $sql = "SELECT DISTINCT Department FROM candidates";
    $result = mysqli_query($conn, $sql);

    // Display candidates for each department
    while ($row = mysqli_fetch_assoc($result)) {
        $department = $row['Department'];
        echo '<h2 class="mt-4">' . $department . '</h2>';
        echo '<div class="row">';
        $sql = "SELECT * FROM candidates WHERE Department = '$department'";
        $result_candidates = mysqli_query($conn, $sql);
        $count = 0;
        while ($candidate = mysqli_fetch_assoc($result_candidates)) {
            if ($count % 3 == 0) {
                echo '</div><div class="row mt-4">';
            }
            echo '<div class="col-md-4">';
            echo '<div class="card">';
            // Display candidate picture
            echo '<img src="' . $candidate['Profile_Pic'] . '" class="card-img-top" alt="Candidate Picture">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $candidate['Name'] . '</h5>';
            echo '<p class="card-text">Gender: ' . $candidate['Gender'] . '</p>';
            echo '<p class="card-text">Position: ' . $candidate['Position'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $count++;
        }
        echo '</div>';
    }
    ?>
</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 Digital Electoral System. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
