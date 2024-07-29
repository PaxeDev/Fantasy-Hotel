<?php
session_start();

// Protect the page from unauthorized access
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["user"])) { // if a session "admin" is exist and have a value
    header("Location: home.php");
    exit(); // redirect the admin to the dashboard page
}

require_once "connection.php";

$errors = [];
$message = "";

if (isset($_POST["submit"])) {
    $room_id = cleanInput($_POST['room_id']);
    $user_id = cleanInput($_POST['user_id']);
    $start_date = cleanInput($_POST['start_date']);
    $end_date = cleanInput($_POST['end_date']);
    $today = date("Y-m-d");

    if (empty($room_id) || empty($user_id) || empty($start_date) || empty($end_date)) {
        $errors[] = "All fields are required.";
    } else {
        // Verifica si las fechas son válidas
        if ($start_date < $today) {
            $errors[] = "The start date cannot be before today.";
        }
        if ($start_date > $end_date) {
            $errors[] = "Start date cannot be later than end date.";
        }
        if (strtotime($start_date) > strtotime('+2 years', strtotime($today))) {
            $errors[] = "You cannot book a room more than 2 years in advance.";
        }

        // Verifica si hay conflictos de fechas
        if (empty($errors)) {
            $conflict_sql = "SELECT * FROM bookings WHERE fk_rooms_id = '$room_id' AND ('$start_date' BETWEEN start_date AND end_date OR '$end_date' BETWEEN start_date AND end_date OR start_date BETWEEN '$start_date' AND '$end_date' OR end_date BETWEEN '$start_date' AND '$end_date')";
            $conflict_result = mysqli_query($connect, $conflict_sql);

            if (mysqli_num_rows($conflict_result) > 0) {
                $errors[] = "These dates are already booked. Please choose different dates.";
            } else {
                // Insert booking record
                $sql = "INSERT INTO bookings (fk_rooms_id, fk_users_id, start_date, end_date) 
                        VALUES ('$room_id', '$user_id', '$start_date', '$end_date')";

                if (mysqli_query($connect, $sql)) {
                    $message = "Booking created successfully!";
                } else {
                    $errors[] = "Error: " . $sql . "<br>" . mysqli_error($connect);
                }
            }
        }
    }
}

$sql_rooms = "SELECT * FROM rooms";
$result_rooms = mysqli_query($connect, $sql_rooms);

$sql_users = "SELECT * FROM users";
$result_users = mysqli_query($connect, $sql_users);

mysqli_close($connect);

// Prepare the room and user options for the select elements
$roomOptions = "";
while ($room = mysqli_fetch_assoc($result_rooms)) {
    $roomOptions .= "<option value='{$room['room_id']}'>{$room['room_name']} - Room Number: {$room['room_number']}</option>";
}

$userOptions = "";
while ($user = mysqli_fetch_assoc($result_users)) {
    $userOptions .= "<option value='{$user['id']}'>{$user['first_name']} {$user['last_name']}</option>";
}

$errorMessages = "";
if (!empty($errors)) {
    foreach ($errors as $error) {
        $errorMessages .= "<p>$error</p>";
    }
    $errorMessages = "<div class='alert alert-danger'>$errorMessages</div>";
}

$successMessage = "";
if ($message) {
    $successMessage = "<div class='alert alert-success'><p>$message You will be redirected in <span id ='timer'>3</span> seconds!</p></div>";
    header("refresh: 3; url=CRUD/index.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h2>Crear Reserva</h2>
        <?= $errorMessages ?>
        <?= $successMessage ?>
        <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="room_id" class="form-label">Habitación</label>
                <select id="room_id" name="room_id" class="form-control" required>
                    <option value="">Selecciona una habitación</option>
                    <?= $roomOptions ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Usuario</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Selecciona un usuario</option>
                    <?= $userOptions ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Fecha de Inicio</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Fecha de Fin</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Crear Reserva</button>
            <div class='d-flex justify-content-center'>
                <a href='home.php' class='btn btn-secondary text-center'>Go Back</a>
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