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
require_once "../file_upload.php";

$id = $_GET["id"]; // to take the value from the parameter "id" in the url
$sql = "SELECT * FROM rooms WHERE room_id = $id";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST["update"])) {
    $room_number = $_POST["room_number"];
    $room_name = $_POST["room_name"];
    $price = $_POST["price"];
    $type = $_POST["type"];
    $Details = $_POST["Details"];
    $picture = FileUpload($_FILES["picture"], "room");

    if ($_FILES["picture"]["error"] == 4) {
        $sql_update_pic = "UPDATE `rooms` SET `room_name`='{$room_name}',`room_number`='{$room_number}',`price`='{$price}',`Details`='{$Details}',`type`='{$type}' WHERE room_id = $id";
    } else {
        if ($row["picture"] != "default_hotel_room.jpg") {
            unlink("../pictures/" . $row["picture"]); # Helps to remove a file (delete)
        }
        $sql_update_pic = "UPDATE `rooms` SET `room_name`='{$room_name}',`room_number`='{$room_number}',`price`='{$price}',`Details`='{$Details}',`type`='{$type}',`picture`='{$picture[0]}' WHERE room_id = $id";
    }
    $result_update_pic = mysqli_query($connect, $sql_update_pic);

    if (mysqli_query($connect, $sql)) {
        echo "<div class='alert alert-success' role='alert'>
        product has been updated, {$picture[1]}
      </div>";
        header("refresh: 3; url= index.php");
    } else {
        echo "<div class='alert alert-danger' role='alert'>
        error found, {$picture[1]}
      </div>";
    }
}
mysqli_close($connect);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

</head>

<body>
    <div class="container mt-5">
        <h2>Update room <?= $row["room_number"] ?> </h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="room_number" class="form-label">Room Number</label>
                <input type="number" class="form-control" id="room_number" aria-describedby="room_number" name="room_number" value="<?= $row["room_number"] ?>">
            </div>
            <div class="mb-3 mt-3">
                <label for="room_name" class="form-label">Room Name</label>
                <input type="text" class="form-control" id="room_name" aria-describedby="room_name" name="room_name" value="<?= $row["room_name"] ?>">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" aria-describedby="price" name="price" value="<?= $row["price"] ?>">
            </div>
            <div class="mb-3">
                <label for="Details" class="form-label">Details</label>
                <input type="text" class="form-control" id="Details" aria-describedby="Details" name="Details" value="<?= $row["Details"] ?>">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select id="type" class="form-select my-3" aria-label="Default select example" name="type">
                    <option selected><?= $row["type"] ?></option>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Familiar">Familiar</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="picture" class="form-label">Picture</label>
                <input type="file" class="form-control" id="picture" aria-describedby="picture" name="picture">
            </div>
            <button name="update" type="submit" class="btn btn-primary">Update room</button>
            <a href="index.php" class="btn btn-warning">Back to Index</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

</body>

</html>