<?php
session_start();
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) { // if the session user and the session adm have no value
    header("Location: login.php");
    exit(); // redirect the user to the home page
}
if (isset($_SESSION["admin"])) { // if a session "adm" is exist and have a value
    header("Location: dashboard.php");
    exit(); // redirect the admin to the dashboard page
}
require_once "connection.php";
$userId = $_SESSION["user"];
$sql = "SELECT * FROM users WHERE id = {$_SESSION["user"]}";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);

$sqlR = "SELECT * FROM rooms /*WHERE available = 'yes'*/";
$resultR = mysqli_query($connect, $sqlR);

$cards = "";

if (mysqli_num_rows($result) > 0) {
    while ($rowR = mysqli_fetch_assoc($resultR)) {
        $cards .= "<div>
               <div class='card mt-3' style='width: 18rem;'>
                   <img src='../pictures/{$rowR["picture"]}' class='card-img-top' alt='...'>
                   <div class='card-body'>
                   <h5 class='card-title'>{$rowR["room_name"]}</h5>
                   <p class='card-text'>Number room: {$rowR["room_number"]}</p>
                   <p class='card-text'>Price: {$rowR["price"]}</p>
                   <p class='card-text'>Type: {$rowR["type"]}</p>
                   <a href='details.php?id={$rowR["room_id"]}' class='btn btn-success'>Details</a>
                   <a href='create_booking.php?id={$rowR["room_id"]}' class='btn btn-warning'>Booking</a>                  

                </div>
           </div>
         </div>";
    }
} else {
    $cards = "<p>No results found</p>";
}
// Fetch user reservations
$sqlReservations = "SELECT * 
                    FROM bookings 
                    JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
                    WHERE fk_users_id = $userId";
$resultReservations = mysqli_query($connect, $sqlReservations);

$reservations = "";
if (mysqli_num_rows($resultReservations) > 0) {
    while ($rowRes = mysqli_fetch_assoc($resultReservations)) {
        $start_date = date("d-m-Y", strtotime($rowRes["start_date"]));
        $end_date = date("d-m-Y", strtotime($rowRes["end_date"]));
        $reservations .= "<div class='card mt-3' style='width: 18rem;'>
            <div class='card-body'>
                <h5 class='card-title'>Room: {$rowRes["room_name"]}</h5>
                <p class='card-text'>Room Number: {$rowRes["room_number"]}</p>
                <p class='card-text'>Start Date: $start_date</p>
                <p class='card-text'>End Date: $end_date</p>
                <a href='update_booking.php?id={$rowRes["id_booking"]}' class='btn btn-warning'>Update Booking</a>
                
            </div>
        </div>";
    }
} else {
    $reservations = "<p>No reservations found</p>";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?= $row["first_name"] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="pictures/<?= $row["images"] ?>" alt="user pic" width="30" height="24">
                </a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="home.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile_update.php">Edit Profile</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="btn btn-danger" href="logout.php?logout">Logout</a>
                </div>
            </div>
    </div>
    </nav>
    <h2 class="text-center">Welcome <?= $row["first_name"] . " " . $row["last_name"] ?></h2>
    <div class="container mt-5">

        <h1 class="mt-5">Book your room</h1>
        <div class="row row-cols-lg-3 row-cols-md-2 row-cols-sm-1 row-cols-xs-1">
            <?= $cards ?>
        </div>
    </div>
    <div class="container mt-5">
        <h1 class="mt-5">Your Reservations</h1>
        <div class="row row-cols-lg-3 row-cols-md-2 row-cols-sm-1 row-cols-xs-1">
            <?= $reservations ?>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>