<?php
session_start();
// avoid user try to go to this page using url
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit();
}
if (isset($_SESSION["user"])) {
    header("Location: ../home.php");
    exit();
}

// connection to our connection page
require_once "../connection.php";

// Fetch admin user details
$sql = "SELECT * FROM users WHERE id = {$_SESSION["admin"]}";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);

$sqlRooms = "SELECT * FROM rooms";
$resultRooms = mysqli_query($connect, $sqlRooms);

$roomCards = "";
if (mysqli_num_rows($resultRooms) > 0) {
    while ($rowRoom = mysqli_fetch_assoc($resultRooms)) {
        $roomCards .= "<div class='card mt-2 mx-2 ' style='width: 18rem;'>
            <img src='../pictures/{$rowRoom["picture"]}' class='card-img-top' alt='...'>
            <div class='card-body'>
                <h5 class='card-title'>{$rowRoom["room_name"]}</h5>
                <p class='card-text'>Number room: {$rowRoom["room_number"]}</p>
                <p class='card-text'>Price: {$rowRoom["price"]}€/night</p>
                <a href='details.php?id={$rowRoom["room_id"]}' class='btn btn-success'>Details</a>
                <a href='update.php?id={$rowRoom["room_id"]}' class='btn btn-warning'>Update Room</a>
                <a href='delete.php?id={$rowRoom["room_id"]}' class='btn btn-danger mt-2'>Delete</a>
            </div>
        </div>";
    }
} else {
    $roomCards = "<p>No rooms found</p>";
}

$sqlBookings = "SELECT bookings.*, rooms.room_name, rooms.room_number, users.first_name, users.last_name 
                FROM bookings 
                JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
                JOIN users ON bookings.fk_users_id = users.id";
$resultBookings = mysqli_query($connect, $sqlBookings);

$bookingCards = "";
if (mysqli_num_rows($resultBookings) > 0) {
    while ($rowBooking = mysqli_fetch_assoc($resultBookings)) {
        $start_date = date("d-m-Y", strtotime($rowBooking["start_date"]));
        $end_date = date("d-m-Y", strtotime($rowBooking["end_date"]));
        $bookingCards .= "<div class='card mt-2 mx-2 ' style='width: 18rem;'>
            <div class='card-body'>
                <h5 class='card-title'>Room: {$rowBooking["room_name"]}</h5>
                <p class='card-text'>Room Number: {$rowBooking["room_number"]}</p>
                <p class='card-text'>Booking dates: $start_date to $end_date</p>
                <p class='card-text'>Reserved by: {$rowBooking["first_name"]} {$rowBooking["last_name"]}</p>
                <p class='card-text'>Status: {$rowBooking["status"]}</p>
                <p class='card-text'>Booking Number: {$rowBooking["id_booking"]}</p>
                <a href='../update_booking.php?id={$rowBooking["id_booking"]}' class='btn btn-warning'>Update Booking</a>
                <a href='../delete_booking.php?id={$rowBooking["id_booking"]}' class='btn btn-danger mt-2'>Delete Booking</a>
                <a href='../update_booking_status.php?id={$rowBooking["id_booking"]}&status=confirmed' class='btn btn-success mt-2'>Confirm</a>
                <a href='../update_booking_status.php?id={$rowBooking["id_booking"]}&status=cancelled' class='btn btn-secondary mt-2'>Cancel</a>
            </div>
        </div>";
    }
} else {
    $bookingCards = "<p>No bookings found</p>";
}

mysqli_close($connect);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INDEX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <!-- Navigation bar -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="../pictures/<?= $row["images"] ?>" alt="user pic" width="30" height="24">
                </a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Index</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../profile_update.php">Edit Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bookinglist">Booking List</a>
                    </li>
                </ul>

                <div class="d-flex">
                    <a class="btn btn-danger" href="../logout.php?logout">Logout</a>
                </div>
            </div>
        </nav>

        <a class="btn btn-secondary mt-3" href="create.php">Add a room</a>
        <a class="btn btn-success mt-3" href="../dashboard.php">Dashboard</a>
        <a class="btn btn-warning mt-3" href="../create_booking_admin.php">Create a reservation</a>
        <div class="mt-5">
            <h1 class="mt-5">Rooms List</h1>
            <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-3 row-cols-xs-1">
                <?= $roomCards ?>
            </div>
        </div>
        <div class="mt-5" id="bookinglist">
            <h1 class="mt-5">Bookings List</h1>
            <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-xs-1">
                <?= $bookingCards ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguR3rMwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>