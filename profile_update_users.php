<?php
session_start();

// Verificar si la sesión no es de administrador y redirigir según corresponda
if (!isset($_SESSION["admin"])) {
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    } else {
        header("Location: home.php");
        exit();
    }
}

require_once "connection.php";
require_once "file_upload.php";

// Verificar si el parámetro id está presente en la URL y es un número válido
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];
} else {
    echo "Invalid user ID";
    exit();
}

$session = $_SESSION["admin"];
$backTo = "dashboard.php";

$error = false;
$email_error = "";

// Consultar los datos del usuario
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($connect, $sql);

if (!$result) {
    echo "Error in query: " . mysqli_error($connect);
    exit();
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST["edit"])) {
    $fname = cleanInput($_POST["first_name"]);
    $lname = cleanInput($_POST["last_name"]);
    $email = cleanInput($_POST["email"]);
    $date_of_birth = cleanInput($_POST["date_of_birth"]);
    $picture = fileUpload($_FILES["picture"]);

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please, type a valid email";
    }

    if (!$error) {
        if ($_FILES["picture"]["error"] == 4) {
            $sqlUpdate = "UPDATE users SET first_name = '$fname', last_name = '$lname', date_of_birth = '$date_of_birth', email = '$email' WHERE id = $id";
        } else {
            if ($row["images"] != 'avatar.png') {
                unlink("pictures/" . $row["images"]);
            }
            $sqlUpdate = "UPDATE users SET first_name = '$fname', last_name = '$lname', date_of_birth = '$date_of_birth', email = '$email', picture = '$picture[0]' WHERE id = $id";
        }
        $result = mysqli_query($connect, $sqlUpdate);

        if ($result) {
            header("Location: " . $backTo);
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($connect);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
                        <a class="nav-link active" aria-current="page" href="index.php">Index</a>
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
        <h1>Edit Profile: <?= "{$row["first_name"]} {$row["last_name"]}" ?></h1>
        <!-- Añadir el ID del usuario en la acción del formulario -->
        <form enctype="multipart/form-data" method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=$id" ?>">
            <input type="text" name="first_name" class="form-control mb-3" value="<?= $row["first_name"] ?>">
            <input type="text" name="last_name" class="form-control mb-3" value="<?= $row["last_name"] ?>">
            <input type="email" name="email" class="form-control mb-3" value="<?= $row["email"] ?>">
            <p class="text-danger"><?= $email_error ?></p>
            <input type="date" name="date_of_birth" class="form-control mb-3" value="<?= $row["date_of_birth"] ?>">
            <input type="file" name="picture" class="form-control mb-3">
            <input type="submit" name="edit" value="Update Profile" class="btn btn-warning">
            <div class='d-flex justify-content-center'>
                <a href='<?= $backTo ?>' class='btn btn-secondary text-center'>Go Back</a>
            </div>
        </form>
    </div>
</body>

</html>