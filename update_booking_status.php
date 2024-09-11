<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
require_once "connection.php";

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$status = isset($_GET["status"]) ? $_GET["status"] : null;

if (isset($_SESSION["user"])) {
    $userId = $_SESSION["user"];
    $sql = "SELECT * FROM bookings WHERE id_booking = $id AND fk_users_id = $userId";
    $result = mysqli_query($connect, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "<div class='alert alert-alert' role='alert'>
                    <h3>Unauthorized access.</h3>
                </div>";
        exit();
    }
    $status = 'cancelled';
}

if ($id && ($status || isset($_GET["confirm"]))) {
    if (isset($_GET["confirm"]) && $_GET["confirm"] == "true") {
        $sql_update = "UPDATE bookings SET status = '$status' WHERE id_booking = $id";
        if (mysqli_query($connect, $sql_update)) {
            echo "<div class='alert alert-success' role='alert'>
                <h4 class='alert-heading'>Status Updated Successfully!</h4>
                <p>You will be redirected in <span id='timer'>3</span> seconds!</p>
            </div>";
            header("refresh: 3; url=" . (isset($_SESSION["admin"]) ? "CRUD/index.php" : "home.php"));
        } else {
            echo "Error updating record: " . mysqli_error($connect);
        }
    } else {
        $sql = "SELECT bookings.*, rooms.room_name, users.first_name, users.last_name 
                FROM bookings 
                JOIN rooms ON bookings.fk_rooms_id = rooms.room_id 
                JOIN users ON bookings.fk_users_id = users.id 
                WHERE id_booking = $id";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            echo "No booking found with this ID.";
            exit();
        }

        $start_date = date("d-m-Y", strtotime($row["start_date"]));
        $end_date = date("d-m-Y", strtotime($row["end_date"]));
        $confirmation_message = isset($_SESSION["user"])
            ? "Are you sure you want to cancel the booking for room {$row["room_name"]} from $start_date to $end_date?"
            : "Are you sure you want to change the status of the booking for room {$row["room_name"]} reserved by {$row["first_name"]} {$row["last_name"]} to {$status}?";
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Booking Status</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <style>
                body {
                    background: linear-gradient(to bottom, #4E2394, #DBC9F5);
                    margin: 0;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
            </style>
        </head>

        <body>
            <div class="container mt-5">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><?= $confirmation_message ?></strong>
                    <a href="update_booking_status.php?id=<?= $id ?>&status=<?= $status ?>&confirm=true" class="btn btn-success">Yes</a>
                    <a href="<?= isset($_SESSION["admin"]) ? 'CRUD/index.php' : 'home.php' ?>" class="btn btn-secondary">No</a>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        </body>

        </html>
<?php
    }
} else {
    header("Location: " . (isset($_SESSION["admin"]) ? "CRUD/index.php" : "home.php"));
    exit();
}

mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        let timer = 3;

        setInterval(() => {
            timer--;
            document.getElementById("timer").innerText = timer;
        }, 1000);
    </script>
</body>

</html>