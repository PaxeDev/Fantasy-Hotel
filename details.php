<?php
session_start();
//protect the page from ppl who play in url

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location:login.php");
    exit();
}
if (isset($_SESSION["admin"])) {
    header("Location: ../index.php");
    exit();
}


require_once "connection.php";
$sql_user = "SELECT * FROM users WHERE id = {$_SESSION["user"]}";
$result_user = mysqli_query($connect, $sql_user);
$row_user = mysqli_fetch_assoc($result_user);

$id = $_GET["id"];

$sql = "SELECT * FROM `rooms` WHERE room_id = $id";

$result = mysqli_query($connect, $sql);

# ussing fetch_assoc bring only one record 

$row = mysqli_fetch_assoc($result);

# echo var_dump($row);
$layout = "<div>
        <div class='card mx-auto bg-primary-subtle my-3' style='max-width: 100%;'>
            <div class='row g-0'>
                <div class='col-md-4'>
                    <img src='../pictures/{$row["picture"]} ' class='img-fluid rounded-start' alt='...'>
                </div>
                <div class='col-md-8'>
                    <div class='card-body'>
                        <h5 class='card-title'>Room Name: {$row["room_name"]} </h5>
                        <h6 class='card-title'>Room Number: {$row["room_number"]} </h6>
                        <p class='card-text'>Details:  {$row["Details"]} </p>
                        <p class='card-text'><small class='text-body-secondary'>Type:  {$row["type"]} </small></p>
                        <p class='card-text'><small class='text-body-secondary'>Price:  {$row["price"]} €</small></p>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class= 'd-flex justify-content-center'>
                         <a href='home.php' class='btn btn-secondary text-center'>Go Back</a>
                         </div>
    "
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="pictures/<?= $row_user["images"] ?>" alt="user pic" width="30" height="24">
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
    <div class="container">
        <?= $layout ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>