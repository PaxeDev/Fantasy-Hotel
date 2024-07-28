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

$sqlRooms = "SELECT * FROM rooms";
$resultRooms = mysqli_query($connect, $sqlRooms);

$roomCards = "";
if (mysqli_num_rows($resultRooms) > 0) {
    while ($row = mysqli_fetch_assoc($resultRooms)) {
        $roomCards .= "<div class='card mt-2 mx-2 ' style='width: 18rem;'>
            <img src='../pictures/{$row["picture"]}' class='card-img-top' alt='...'>
            <div class='card-body'>
                <h5 class='card-title'>{$row["room_name"]}</h5>
                <p class='card-text'>Number room: {$row["room_number"]}</p>
                <p class='card-text'>Price: {$row["price"]}</p>
                <a href='details.php?id={$row["room_id"]}' class='btn btn-success'>Details</a>
                <a href='update.php?id={$row["room_id"]}' class='btn btn-warning'>Update Room</a>
                <a href='delete.php?id={$row["room_id"]}' class='btn btn-danger mt-2'>Delete</a>
            </div>
        </div>";
    }
} else {
    $roomCards = "<p>No rooms found</p>";
}
$sqlBookings = "SELECT * 
                FROM bookings 
                JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
                JOIN users ON bookings.fk_users_id = users.id";
$resultBookings = mysqli_query($connect, $sqlBookings);

$bookingCards = "";
if (mysqli_num_rows($resultBookings) > 0) {
    while ($row = mysqli_fetch_assoc($resultBookings)) {
        $start_date = date("d-m-Y", strtotime($row["start_date"]));
        $end_date = date("d-m-Y", strtotime($row["end_date"]));
        $bookingCards .= "<div class='card mt-2 mx-2 ' style='width: 18rem;'>
            <div class='card-body'>
                <h5 class='card-title'>Room: {$row["room_name"]}</h5>
                <p class='card-text'>Room Number: {$row["room_number"]}</p>
                <p class='card-text'>Booking dates: $start_date to $end_date</p>
                <p class='card-text'>Reserved by: {$row["first_name"]} {$row["last_name"]}</p>
                <a href='../update_booking.php?id={$row["id_booking"]}' class='btn btn-warning'>Update Booking</a>
                <a href='../delete_booking.php?id={$row["id_booking"]}' class='btn btn-danger mt-2'>Delete Booking</a>
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
    <title>PHP CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5">
        <a class="btn btn-secondary" href="create.php">Add a room</a>
        <a class="btn btn-success" href="../dashboard.php">Dashboard</a>
        <a class="btn btn-warning" href="../create_booking_admin.php">Create a reservation</a>

        <h1 class="mt-5">Rooms List</h1>
        <div class="row row-cols-lg-3 row-cols-md-2 row-cols-sm-1 row-cols-xs-1">
            <?= $roomCards ?>
        </div>

        <h1 class="mt-5">Bookings List</h1>
        <div class="row row-cols-lg-3 row-cols-md-2 row-cols-sm-1 row-cols-xs-1">
            <?= $bookingCards ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>