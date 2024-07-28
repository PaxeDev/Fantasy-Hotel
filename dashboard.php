<?php
session_start();

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) { // if the session user and the session adm have no value
    header("Location: login.php");
    exit(); // redirect the user to the home page
}
if (isset($_SESSION["user"])) { // if a session "user" is exist and have a value
    header("Location: home.php");
    exit(); // redirect the user to the user page
}

require_once "connection.php";

$sql = "SELECT * FROM users WHERE id = {$_SESSION["admin"]}";

$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);

$sql_users = "SELECT * FROM users WHERE status != 'admin'";
$result_users = mysqli_query($connect, $sql_users);

$layout = "";
if (mysqli_num_rows($result_users) > 0) {
    while ($user_row = mysqli_fetch_assoc($result_users)) {
        $layout .= "<div>
           <div class='card' style='width: 18rem;'>
               <img src='pictures/{$user_row["images"]}' class='card-img-top' alt='...'>
               <div class='card-body'>
               <h5 class='card-title'>{$user_row["first_name"]} {$user_row["last_name"]}</h5>
               <p class='card-text'>{$user_row["email"]}</p>
               <a href='profile_update_users.php?id={$user_row["id"]}' class='btn btn-warning'>Update</a>
           </div>
       </div>
     </div>";
    }
} else {
    $layout .= "No results found!";
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

</head>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="pictures/<?= $row["images"] ?>" alt="user pic" width="30" height="24">
                </a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="CRUD/index.php">Index</a>
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
    <h2 class="text-center">Welcome <?= $row["first_name"] . " " . $row["last_name"] ?></h2>

    <div class="container">
        <h3>Users</h3>
        <div class="row row-cols-3">
            <?= $layout ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>