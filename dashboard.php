<?php
session_start();

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}

require_once "connection.php";

$sql = "SELECT * FROM users WHERE id = {$_SESSION["admin"]}";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);

$sql_users = "SELECT * FROM users WHERE status != 'admin'";
$result_users = mysqli_query($connect, $sql_users);

$sql_admins = "SELECT * FROM users WHERE status = 'admin'";
$result_admins = mysqli_query($connect, $sql_admins);

$userList = "";
if (mysqli_num_rows($result_users) > 0) {
    while ($user_row = mysqli_fetch_assoc($result_users)) {
        $userList .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
            <div>
                <img src='pictures/{$user_row["images"]}' class='rounded-circle me-2' alt='...' style='width: 40px; height: 40px;'>
                {$user_row["first_name"]} {$user_row["last_name"]} 
            </div>
            {$user_row["email"]}
            <div>
                <a href='profile_update.php?id={$user_row["id"]}' class='btn btn-warning btn-sm'>Update</a>
            </div>
        </li>";
    }
} else {
    $userList .= "<li class='list-group-item'>No users found!</li>";
}

$adminList = "";
if (mysqli_num_rows($result_admins) > 0) {
    while ($admin_row = mysqli_fetch_assoc($result_admins)) {
        $adminList .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
            <div>
                <img src='pictures/{$admin_row["images"]}' class='rounded-circle me-2' alt='...' style='width: 40px; height: 40px;'>
                {$admin_row["first_name"]} {$admin_row["last_name"]} 
            </div>
            {$admin_row["email"]}
            <div>
                <a href='profile_update.php?id={$admin_row["id"]}' class='btn btn-warning btn-sm'>Update</a>
            </div>
        </li>";
    }
} else {
    $adminList .= "<li class='list-group-item'>No admins found!</li>";
}

mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?= $row["first_name"] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            padding-top: 70px;
            background: linear-gradient(to bottom, #4E2394, #DBC9F5);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="profile_update.php">
                <img src="pictures/<?= $row["images"] ?>" alt="user pic" width="30" height="24">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="CRUD/index.php">Index</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="CRUD/index.php#bookinglist">Booking List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile_update.php">Edit Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="CRUD/create.php">Add new room</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_booking.php">Create a reservation</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="btn btn-danger" href="logout.php?logout">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center fs-1 fw-bold mt-5 text-light">Welcome <?= $row["first_name"] . " " . $row["last_name"] ?></h2>

        <div class="container mt-4">
            <h3>Users</h3>
            <ul class="list-group">
                <?= $userList ?>
            </ul>
        </div>

        <div class="container mt-4">
            <h3>Admins</h3>
            <ul class="list-group">
                <?= $adminList ?>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>