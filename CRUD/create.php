<?php
session_start();
//protect the page from ppl who play in url

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location:login.php");
    exit();
}
if (isset($_SESSION["user"])) {
    header("Location: ../home.php");
    exit();
}
require_once "../connection.php";
require_once "../file_upload.php";

if (isset($_POST["create"])) {
    $room_name = $_POST["room_name"];
    $room_number = $_POST["room_number"];
    $price = $_POST["price"];
    $type = $_POST["type"];
    $Details = $_POST["Details"];
    $picture = FileUpload($_FILES["picture"], "room");
    # $picture = $_POST["picture"];
    $error = false;

    if (!$error) {
        $sql = "INSERT INTO `rooms`(`picture`, `room_name`, `room_number`, `price`, `Details`, `type`) 
                VALUES ('{$picture[0]}','{$room_name}','{$room_number}','{$price}','{$Details}','{$type}')";

        $result = mysqli_query($connect, $sql);

        if ($result) {
            echo "<div class='alert alert-success' role='alert'>
                New room has been create wiht the name {$room_name}. {$picture[1]}! You will be redirected in <span id ='timer'>3</span> seconds!
            </div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>
                Something went wrong, please try again later!
            </div>";
        }
        # Redirect after 3 second to main page

        header("refresh: 3; url= index.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Dish</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <h1>Create new room</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" placeholder="Room name" class="form-control mt-3" name="room_name">
            <input type="number" placeholder="Room number" class="form-control mt-3" name="room_number">
            <input type="number" step="0.01" placeholder="Room price" class="form-control mt-3" name="price">
            <textarea class="form-control mt-3" placeholder="Details" rows="3" name="Details"></textarea>
            <input type="file" placeholder="Room picture" class="form-control mt-3" name="picture">
            <select class="form-select mt-3" aria-label="Default select example" name="type">
                <option selected>Choose type</option>
                <option value="Double">Double</option>
                <option value="Triple">Triple</option>
                <option value="Familiar">Familiar</option>
            </select>
            <input type="submit" class="btn btn-primary mt-3" value="Create Product" name="create">
            <div class='d-flex justify-content-center'>
                <a href='index.php' class='btn btn-secondary text-center'>Go Back</a>
            </div>

        </form>
    </div>
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