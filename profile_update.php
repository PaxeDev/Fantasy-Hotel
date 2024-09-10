<?php
session_start();

// Redirigir a la página de login si no hay sesión
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "connection.php";
require_once "file_upload.php";

// Obtener el usuario en sesión para la imagen de la navbar
$sessionUserId = isset($_SESSION["user"]) ? $_SESSION["user"] : $_SESSION["admin"];
$sqlSessionUser = "SELECT * FROM users WHERE id = $sessionUserId";
$resultSessionUser = mysqli_query($connect, $sqlSessionUser);
if (!$resultSessionUser || mysqli_num_rows($resultSessionUser) === 0) {
    die("Error fetching session user details: " . mysqli_error($connect));
}
$sessionUser = mysqli_fetch_assoc($resultSessionUser);

// Verificar si es administrador o usuario normal
if (isset($_SESSION["admin"])) {
    // Administrador puede editar cualquier perfil
    $session = $_SESSION["admin"];
    $backTo = "dashboard.php";
    $navbarLinks = [
        "Index" => "CRUD/index.php",
        "Booking List" => "CRUD/index.php#bookinglist",
        "Dashboard" => "dashboard.php",
        "Edit Profile" => "profile_update.php",
        "Add new room" => "CRUD/create.php",
        "Create a reservation" => "create_booking.php"
    ];
    // Si hay un id en la URL, es un administrador editando otro perfil
    $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : $session;
} else {
    // Usuario normal editando su propio perfil, ignorar el ID en la URL si es usuario normal
    $session = $_SESSION["user"];
    $id = $session; // El usuario solo puede editar su propio perfil
    $backTo = "home.php";
    $navbarLinks = [
        "Home" => "home.php",
        "Reservations" => "home.php#reservations",
        "Edit Profile" => "profile_update.php"

    ];
}

// Obtener datos del usuario a editar
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($connect, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Invalid user ID or user not found.";
    exit();
}
$row = mysqli_fetch_assoc($result);

// Manejar la actualización del perfil
$error = false;
$first_name = $last_name = $date_of_birth = $email = $picture = "";
$first_name_error = $last_name_error = $date_of_birth_error = $email_error = $picture_error = "";

if (isset($_POST["edit"])) {
    $fname = cleanInput($_POST["first_name"]);
    $lname = cleanInput($_POST["last_name"]);
    $email = cleanInput($_POST["email"]);
    $date_of_birth = cleanInput($_POST["date_of_birth"]);
    $picture = fileUpload($_FILES["picture"]);

    // Validaciones de los campos
    if (empty($fname)) {
        $error = true;
        $first_name_error = "First name cannot be empty!";
    } elseif (strlen($fname) < 3) {
        $error = true;
        $first_name_error = "First name must be at least 3 characters!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $fname)) {
        $error = true;
        $first_name_error = "First name must only contain letters and spaces!";
    }

    if (empty($lname)) {
        $error = true;
        $last_name_error = "Last name cannot be empty!";
    } elseif (strlen($lname) < 3) {
        $error = true;
        $last_name_error = "Last name must be at least 3 characters!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $lname)) {
        $error = true;
        $last_name_error = "Last name must only contain letters and spaces!";
    }

    if (empty($date_of_birth)) {
        $error = true;
        $date_of_birth_error = "Date of birth cannot be empty!";
    }

    // Validar el email
    if (empty($email)) {
        $error = true;
        $email_error = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please, type a valid email";
    } else {
        // Verificar si el email ya existe en otro usuario
        $sqlEmailCheck = "SELECT id FROM users WHERE email = '$email' AND id != $id";
        $emailResult = mysqli_query($connect, $sqlEmailCheck);
        if (mysqli_num_rows($emailResult) > 0) {
            $error = true;
            $email_error = "This email is already in use. Please choose a different email.";
        }
    }

    // Si no hay errores, actualizar el perfil
    if (!$error) {
        if ($_FILES["picture"]["error"] == 4) {
            $sqlUpdate = "UPDATE users SET first_name = '$fname', last_name = '$lname', date_of_birth = '$date_of_birth', email = '$email' WHERE id = $id";
        } else {
            // Eliminar imagen anterior si no es el avatar por defecto
            if ($row["images"] != 'avatar.png') {
                unlink("pictures/" . $row["images"]);
            }
            $sqlUpdate = "UPDATE users SET first_name = '$fname', last_name = '$lname', date_of_birth = '$date_of_birth', email = '$email', images = '$picture[0]' WHERE id = $id";
        }
        $resultUpdate = mysqli_query($connect, $sqlUpdate);

        if ($resultUpdate) {
            header("Location: " . $backTo);
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($connect);
        }
    }
}

mysqli_close($connect);
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
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="pictures/<?= $sessionUser["images"] ?>" alt="user pic" width="30" height="24">
            </a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($navbarLinks as $label => $url) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $label === "Edit Profile" ? "active" : "" ?>" href="<?= $url ?>"><?= $label ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex">
                <a class="btn btn-danger" href="logout.php?logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <h1>Edit Profile: <?= "{$row["first_name"]} {$row["last_name"]}" ?></h1>
        <form enctype="multipart/form-data" method="post">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" class="form-control mb-3" id="first_name" value="<?= $row["first_name"] ?>">
            <p class="text-danger"><?= $first_name_error ?></p>
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" class="form-control mb-3" id="last_name" value="<?= $row["last_name"] ?>">
            <p class="text-danger"><?= $last_name_error ?></p>
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control mb-3" id="email" value="<?= $row["email"] ?>">
            <p class="text-danger"><?= $email_error ?></p>
            <label for="date_of_birth">Date of birth</label>
            <input type="date" name="date_of_birth" class="form-control mb-3" id="date_of_birth" value="<?= $row["date_of_birth"] ?>">
            <p class="text-danger"><?= $date_of_birth_error ?></p>
            <p><img src="pictures/<?= $row["images"] ?>" class="rounded" width="150" height="150"></p>
            <label for="picture">Your profile picture</label>
            <input type="file" name="picture" id="picture" class="form-control mb-3">
            <input type="submit" name="edit" value="Update Profile" class="btn btn-warning">
            <div class='d-flex justify-content-center'>
                <a href='<?= $backTo ?>' class='btn btn-secondary text-center'>Go Back</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>