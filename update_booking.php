<?php
session_start();

// Protect the page from unauthorized access
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "connection.php";
$sessionUserId = isset($_SESSION["user"]) ? $_SESSION["user"] : $_SESSION["admin"];
$sqlSessionUser = "SELECT * FROM users WHERE id = $sessionUserId";
$resultSessionUser = mysqli_query($connect, $sqlSessionUser);
if (!$resultSessionUser || mysqli_num_rows($resultSessionUser) === 0) {
    die("Error fetching session user details: " . mysqli_error($connect));
}
$sessionUser = mysqli_fetch_assoc($resultSessionUser);

if (isset($_SESSION["admin"])) {
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
    $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : $session;
} else {
    $session = $_SESSION["user"];
    $id = $session;
    $backTo = "home.php";
    $navbarLinks = [
        "Home" => "home.php",
        "Reservations" => "home.php#reservations",
        "Edit Profile" => "profile_update.php"

    ];
}


$id = $_GET["id"];
$sql = "SELECT * FROM bookings WHERE id_booking = $id";
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
} else {
    echo "No booking found with this ID.";
    exit();
}

$isAdmin = isset($_SESSION["admin"]);
$isUser = isset($_SESSION["user"]) && !$isAdmin;

if (isset($_POST["update"])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Verify if the dates are valid
    if ($start_date > $end_date) {
        $error_message = "<div class='alert alert-danger' role='alert'>
                            <h3>Start date cannot be later than end date.</h3>
                        </div>";
    } else {
        // Set the status to 'accepted' by default for users
        $status = $isAdmin ? $_POST['status'] : 'accepted';

        // Update booking record
        $sql_update = "UPDATE bookings SET start_date = '$start_date', end_date = '$end_date', status = '$status' WHERE id_booking = $id";
        $result_update = mysqli_query($connect, $sql_update);

        if ($result_update) {
            $success_message = "<div class='alert alert-success' role='alert'>
                                <h4 class='alert-heading'>Updated Successfully!</h4>
                                <p>You will be redirected in <span id ='timer'>3</span> seconds!</p>
                            </div>";
            header("refresh: 3; url=CRUD/index.php");
        } else {
            $error_message = "<div class='alert alert-danger' role='alert'>
                                <h3>Something went wrong, please try again later!</h3>
                            </div>";
        }
    }
}

// Prepare the form fields based on user role
$status_options = '';
if ($isAdmin) {
    $status_options = "<div class='mb-3'>
                        <label for='status'>Status</label>
                        <select class='form-control' style='width: 18rem;' id='status' name='status' required>
                            <option value='accepted' " . ($row['status'] == 'accepted' ? 'selected' : '') . ">Accepted</option>
                            <option value='confirmed' " . ($row['status'] == 'confirmed' ? 'selected' : '') . ">Confirmed</option>
                            <option value='cancelled' " . ($row['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                        </select>
                    </div>";
} else {
    $status_options = "<input type='hidden' name='status' value='accepted'>";
}

mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(to bottom, #4E2394, #DBC9F5);
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
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

    <div class="container">
        <?php if (isset($error_message)) echo $error_message; ?>
        <?php if (isset($success_message)) echo $success_message; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?id=" . $id ?>" class="w-50 mx-auto">
            <h2 class="mb-3 d-flex justify-content-center fs-1 fw-bold">Update Booking</h2>
            <div class="mb-3">
                <label for="start_date" class="fw-semibold fs-4">Start Date</label>
                <input type="date" class="form-control" style='width: 18rem;' id="start_date" name="start_date" value="<?= $row['start_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="fw-semibold fs-4">End Date</label>
                <input type="date" class="form-control" style='width: 18rem;' id="end_date" name="end_date" value="<?= $row['end_date'] ?>" required>
            </div>
            <?php echo $status_options; ?>

            <input type="submit" class="btn btn-primary" name="update" value="Update Booking">
            <div class='d-flex justify-content-center'>
                <a href='CRUD/index.php' class='btn btn-secondary'>Go Back</a>
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