<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "connection.php";

if (isset($_GET["id"]) && isset($_GET["status"])) {
    $id = $_GET["id"];
    $status = $_GET["status"];

    // Check if the status change is confirmed
    if (isset($_GET["confirm"]) && $_GET["confirm"] == "true") {
        $sql_update = "UPDATE bookings SET status = '$status' WHERE id_booking = $id";
        if (mysqli_query($connect, $sql_update)) {
            echo "<div class='alert alert-success' role='alert'>
                <h4 class='alert-heading'>Status Updated Successfully!</h4>
                <p>You will be redirected in <span id='timer'>3</span> seconds!</p>
            </div>";
            header("refresh: 3; url=index.php");
        } else {
            echo "Error updating record: " . mysqli_error($connect);
        }
    } else {
        // Fetch booking details
        $sql = "SELECT bookings.*, rooms.room_name, users.first_name, users.last_name 
                FROM bookings 
                JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
                JOIN users ON bookings.fk_users_id = users.id 
                WHERE id_booking = $id";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Booking Status</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        </head>

        <body>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Are you sure you want to change the status of the booking for room <?= $row["room_name"] ?> reserved by <?= $row["first_name"] ?> <?= $row["last_name"] ?> to <?= $status ?>?</strong>
                <a href="update_booking_status.php?id=<?= $id ?>&status=<?= $status ?>&confirm=true" class="btn btn-success">Yes</a>
                <a href="CRUD/index.php" class="btn btn-secondary">No</a>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        </body>

        </html>
<?php
    }
} else {
    header("url: CRUD/index.php");
    exit();
}

mysqli_close($connect);
?>