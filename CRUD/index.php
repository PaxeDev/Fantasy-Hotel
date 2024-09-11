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

$roomList = "";
if (mysqli_num_rows($resultRooms) > 0) {
    while ($rowRoom = mysqli_fetch_assoc($resultRooms)) {
        $roomList
            .= "<li class='list-group-item'>
            <div class='d-flex align-items-center'>
                <img src='../pictures/{$rowRoom["picture"]}' alt='Room image' width='150' height='100' class='img-thumbnail me-3'>
                <div>
                    <h5>{$rowRoom["room_name"]}</h5>
                    <p>Number room: {$rowRoom["room_number"]}</p>
                    <p>Price: {$rowRoom["price"]}€/night</p>
                </div>
                <div class='ms-auto'>
                    <a href='../details.php?id={$rowRoom["room_id"]}' class='btn btn-success btn-sm'>Details</a>
                    <a href='update.php?id={$rowRoom["room_id"]}' class='btn btn-warning btn-sm'>Update Room</a>
                    <a href='delete.php?id={$rowRoom["room_id"]}' class='btn btn-danger btn-sm'>Delete</a>
                </div>
            </div>
        </li>";
    }
} else {
    $roomList = "<p>No rooms found</p>";
}

$sqlBookings = "
    SELECT bookings.*, rooms.room_name, rooms.room_number, users.first_name, users.last_name 
    FROM bookings 
    JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
    JOIN users ON bookings.fk_users_id = users.id 
    ORDER BY 
        CASE 
            WHEN bookings.status = 'accepted' THEN 1 
            WHEN bookings.status = 'confirmed' THEN 2 
            WHEN bookings.status = 'cancelled' THEN 3 
            ELSE 4 
        END, 
        bookings.start_date ASC
";
$resultBookings = mysqli_query($connect, $sqlBookings);

$bookingList = "";
if (mysqli_num_rows($resultBookings) > 0) {
    while ($rowBooking = mysqli_fetch_assoc($resultBookings)) {
        $start_date = date("d-m-Y", strtotime($rowBooking["start_date"]));
        $end_date = date("d-m-Y", strtotime($rowBooking["end_date"]));

        // Generar la lista de reservas
        $bookingList .= "<li class='list-group-item'>
            <div class='d-flex justify-content-between'>
                <div>
                    <h4>Booking Number: {$rowBooking["id_booking"]}</h4>
                    <h5>Room: {$rowBooking["room_name"]}</h5>
                    <p>Room Number: {$rowBooking["room_number"]}</p>
                    <p>Booking dates: $start_date to $end_date</p>
                    <p>Reserved by: {$rowBooking["first_name"]} {$rowBooking["last_name"]}</p>
                    <p>Status: {$rowBooking["status"]}</p>                    
                </div>
                <div class='text-end'>
                    <a href='../update_booking.php?id={$rowBooking["id_booking"]}' class='btn btn-warning btn-sm'>Update Booking</a><br>
                    <a href='../delete_booking.php?id={$rowBooking["id_booking"]}' class='btn btn-danger btn-sm mt-3'>Delete Booking</a><br>";

        // Mostrar el botón Confirm solo si el estado no es "confirmed"
        if ($rowBooking["status"] !== 'confirmed' && $rowBooking["status"] !== 'cancelled') {
            $bookingList .= "<a href='../update_booking_status.php?id={$rowBooking["id_booking"]}&status=confirmed' class='btn btn-success btn-sm mt-3'>Confirm</a><br>";
        }

        // Mostrar el botón Cancel solo si el estado no es "cancelled"
        if ($rowBooking["status"] !== 'cancelled') {
            $bookingList .= "<a href='../update_booking_status.php?id={$rowBooking["id_booking"]}&status=cancelled' class='btn btn-secondary btn-sm mt-3'>Cancel</a><br>";
        }

        $bookingList .= "</div></div></li>";
    }
} else {
    $bookingList = "<p>No bookings found</p>";
}


mysqli_close($connect);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        body {
            padding-top: 70px;
        }

        body {
            background: linear-gradient(to bottom, #4E2394, #DBC9F5);
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../pictures/<?= $row["images"] ?>" alt="user pic" width="30" height="24">
            </a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="index.php">Index</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#bookinglist">Booking List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../profile_update.php">Edit Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="create.php">Add new room</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../create_booking.php">Create a reservation</a>
                </li>
            </ul>

            <div class="d-flex">
                <a class="btn btn-danger" href="../logout.php?logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Rooms List</h1>
        <ul class="list-group">
            <?= $roomList ?>
        </ul>

        <h1 class="mt-5" id="bookinglist">Bookings List</h1>
        <ul class="list-group">
            <?= $bookingList ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguR3rMwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>