<?php
session_start();
// Protect the page from unauthorized access
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}

require_once "connection.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $sql = "SELECT * FROM bookings 
            JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
            JOIN users ON bookings.fk_users_id = users.id 
            WHERE id_booking = $id";

    $result = mysqli_query($connect, $sql);
    $row = mysqli_fetch_assoc($result);

    if (isset($_GET["delete"])) {
        $sql_delete = "DELETE FROM bookings WHERE id_booking = $id";
        mysqli_query($connect, $sql_delete);
        header("Location: CRUD/index.php");
        exit();
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Booking</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>

    <body>
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Are you sure you want to remove the booking for room <?= $row["room_name"] ?> reserved by <?= $row["first_name"] ?> <?= $row["last_name"] ?>?</strong>
                <a href="delete_booking.php?id=<?= $row["id_booking"] ?>&delete=true" class="btn btn-danger">Yes</a>
                <a href="CRUD/index.php" class="btn btn-secondary">No</a>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>

    </html>
<?php
} else {
    header("Location: index.php");
    exit();
}
?>