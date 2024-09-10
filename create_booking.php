<?php
session_start();

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
require_once "connection.php";

// Protect the page from unauthorized access


if (isset($_SESSION["admin"])) {
    $session = $_SESSION["admin"];
    $backTo = "CRUD/index.php";
    $navbarLinks = [
        "Index" => "CRUD/index.php",
        "Booking List" => "CRUD/index.php#bookinglist",
        "Dashboard" => "dashboard.php",
        "Edit Profile" => "profile_update.php",
        "Add new room" => "CRUD/create.php",
        "Create a reservation" => "create_booking.php"
    ];
} else {
    $session = $_SESSION["user"];
    $backTo = "home.php";
    $navbarLinks = [
        "Rooms" => "home.php",
        "Edit Profile" => "profile_update.php",
    ];
}

$errors = [];
$message = $room_id = $user_id = $start_date = $end_date = "";
$isAdmin = isset($_SESSION["admin"]);

// Handle form submission
if (isset($_POST["submit"]) || isset($_POST["book"])) {
    if ($isAdmin) {
        // Admin booking
        $room_id = $_POST['room_id'];
        $user_id = $_POST['user_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    } else {
        // User booking
        $room_id = $_GET['id'];
        $user_id = $_SESSION['user'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    }

    $today = date("Y-m-d");

    // Validate dates
    if ($start_date < $today) {
        $errors[] = "The start date cannot be before today.";
    }
    if ($start_date > $end_date) {
        $errors[] = "Start date cannot be later than end date.";
    }
    if ($start_date == $end_date) {
        $errors[] = "You should book at least one night.";
    }
    if (strtotime($start_date) > strtotime('+2 years', strtotime($today))) {
        $errors[] = "You cannot book a room more than 2 years in advance.";
    }

    if (empty($errors)) {
        // Check for date conflicts
        $conflict_sql = "
        SELECT * FROM bookings 
        WHERE fk_rooms_id = '$room_id' 
        AND status != 'cancelled'
        AND (
            ('$start_date' BETWEEN start_date AND end_date)
            OR ('$end_date' BETWEEN start_date AND end_date)
            OR (start_date BETWEEN '$start_date' AND '$end_date')
            OR (end_date BETWEEN '$start_date' AND '$end_date')
        )
    ";
        $conflict_result = mysqli_query($connect, $conflict_sql);

        if (mysqli_num_rows($conflict_result) > 0) {
            $errors[] = "These dates are already booked. Please choose different dates.";
        } else {
            // Insert booking record
            $sql = "INSERT INTO bookings (fk_rooms_id, fk_users_id, start_date, end_date, status) 
                VALUES ('$room_id', '$user_id', '$start_date', '$end_date', 'accepted')";
            if (mysqli_query($connect, $sql)) {
                $message = "Booking created successfully!";
                header("refresh: 3; url={$backTo}");
            } else {
                $errors[] = "Error: " . $sql . "<br>" . mysqli_error($connect);
            }
        }
    }
}

if ($isAdmin) {
    // Admin view setup

    $sql = "SELECT * FROM users WHERE id = {$_SESSION['admin']}";
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        die("Error fetching user details: " . mysqli_error($connect));
    }
    $row = mysqli_fetch_assoc($result);
    $sql_rooms = "SELECT * FROM rooms";
    $result_rooms = mysqli_query($connect, $sql_rooms);

    $sql_users = "SELECT * FROM users";
    $result_users = mysqli_query($connect, $sql_users);

    // Prepare the room and user options for the select elements
    $roomOptions = "";
    while ($room = mysqli_fetch_assoc($result_rooms)) {
        $selected = $room_id == $room['room_id'] ? "selected" : "";
        $roomOptions .= "<option value='{$room['room_id']}' $selected>{$room['room_name']} - Room Number: {$room['room_number']}</option>";
    }

    $userOptions = "";
    while ($user = mysqli_fetch_assoc($result_users)) {
        $selected = $user_id == $user['id'] ? "selected" : "";
        $userOptions .= "<option value='{$user['id']}' $selected>{$user['first_name']} {$user['last_name']}</option>";
    }
} else {
    // User view setup
    $sql = "SELECT * FROM users WHERE id = {$_SESSION['user']}";
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        die("Error fetching user details: " . mysqli_error($connect));
    }
    $row = mysqli_fetch_assoc($result);

    $id = $_GET["id"];
    $sqlR = "SELECT * FROM rooms WHERE room_id = $id";
    $resultR = mysqli_query($connect, $sqlR);
    if (!$resultR) {
        die("Error fetching room details: " . mysqli_error($connect));
    }
    $rowR = mysqli_fetch_assoc($resultR);
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdmin ? 'Create Booking' : 'Book Room' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="pictures/<?= $row["images"] ?? '' ?>" alt="user pic" width="30" height="24">
            </a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($navbarLinks as $label => $url) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $label === "Edit Profile" ? "active" : "" ?>" href="<?= $url ?>"><?= $label ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex">
                <a class="btn btn-danger" href="../logout.php?logout">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2><?= $isAdmin ? 'Create Booking' : 'Book Room: ' . htmlspecialchars($rowR["room_name"]) ?></h2>
        <?= !empty($errors) ? "<div class='alert alert-danger'>" . implode("<br>", $errors) . "</div>" : '' ?>
        <?= $message ? "<div class='alert alert-success'><p>$message You will be redirected in <span id='timer'>3</span> seconds!</p></div>" : '' ?>
        <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) . ($isAdmin ? '' : "?id=$id") ?>" class="w-50 mx-auto">
            <?php if ($isAdmin) : ?>
                <div class="mb-3">
                    <label for="room_id" class="form-label">Room</label>
                    <select id="room_id" name="room_id" class="form-control" required>
                        <option value="">Select a room</option>
                        <?= $roomOptions ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">User</label>
                    <select id="user_id" name="user_id" class="form-control" required>
                        <option value="">Select a user</option>
                        <?= $userOptions ?>
                    </select>
                </div>
            <?php else : ?>
                <div>
                    <div class='card' style='width: 18rem;'>
                        <img src='pictures/<?= htmlspecialchars($rowR["picture"]) ?>' class='card-img-top' alt='...'>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date); ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date); ?>" required>
            </div>
            <input type="submit" name="<?= $isAdmin ? 'submit' : 'book' ?>" class="btn btn-primary" value="<?= $isAdmin ? 'Create Booking' : 'Book' ?>">
            <div class='d-flex justify-content-center'>
                <a href='<?= $backTo ?>' class='btn btn-secondary text-center'>Go Back</a>
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