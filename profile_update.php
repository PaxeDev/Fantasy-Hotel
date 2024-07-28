<?php
session_start();
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "connection.php";
require_once "file_upload.php";

if (isset($_SESSION["admin"])) {
    $session = $_SESSION["admin"];
    $backTo = "dashboard.php";
} else {
    $session = $_SESSION["user"];
    $backTo = "home.php";
}


$error = false;
$email_error = "";
$sql = "SELECT * FROM users WHERE id = $session";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);
if (isset($_POST["edit"])) {
    $fname = cleanInput($_POST["first_name"]);
    $lname = cleanInput($_POST["last_name"]);
    $email = cleanInput($_POST["email"]);
    $date_of_birth = cleanInput($_POST["date_of_birth"]);
    $picture = fileUpload($_FILES["picture"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { # check the format
        $error = true;
        $email_error = "Please, type a valid email";
    }
    if (!$error) {
        if ($_FILES["picture"]["error"] == 4) {
            $sqlUpdate = "UPDATE users SET first_name = '$fname', last_name = '$lname', date_of_birth= '$date_of_birth', email = '$email' WHERE id = $session ";
        } else {
            if ($row["images"] != 'avatar.png') {
                unlink("pictures/" . $row["images"]);
            }
            $sqlUpdate = "UPDATE users SET first_name = '$fname', last_name = '$lname', date_of_birth= '$date_of_birth', email = '$email', picture = '$picture[0]' WHERE id = $session ";
        }
        $result = mysqli_query($connect, $sqlUpdate);

        if ($result) {
            header("Location: " . $backTo);
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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
        <h1>Edit profile! <?= "{$row["first_name"]}" . " {$row["last_name"]}" ?></h1>

        <form enctype="multipart/form-data" method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <input type="text" name="first_name" class="form-control mb-3" value="<?= $row["first_name"] ?>">
            <input type="text" name="last_name" class="form-control mb-3" value="<?= $row["last_name"] ?>">
            <input type="email" name="email" class="form-control mb-3" value="<?= $row["email"] ?>">
            <p class="text-danger"><?= $email_error ?></p>
            <input type="date" name="date_of_birth" class="form-control mb-3" value="<?= $row["date_of_birth"] ?>">
            <input type="file" name="picture" class="form-control mb-3">
            <input type="submit" name="edit" value="Update profile" class="btn btn-warning">

            <!-- <a href='change_password.php' class='btn btn-secondary text-center btn-info'>Chage Password</a> -->

            <div class='d-flex justify-content-center'>
                <a href='home.php' class='btn btn-secondary text-center'>Go Back</a>
            </div>
        </form>
    </div>
</body>

</html>