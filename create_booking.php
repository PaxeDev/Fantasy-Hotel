<?php
session_start();

// Protect the page from unauthorized access
if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["admin"])) { // if a session "admin" is exist and have a value
    header("Location: dashboard.php");
    exit(); // redirect the admin to the dashboard page
}

require_once "connection.php";

// Debugging: Check session values
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// Fetch user details
$sql = "SELECT * FROM users WHERE id = {$_SESSION['user']}";
$result = mysqli_query($connect, $sql);
if (!$result) {
    die("Error fetching user details: " . mysqli_error($connect));
}
$row = mysqli_fetch_assoc($result);

// Fetch room details
$id = $_GET["id"];
$sqlR = "SELECT * FROM rooms WHERE room_id = $id";
$resultR = mysqli_query($connect, $sqlR);
if (!$resultR) {
    die("Error fetching room details: " . mysqli_error($connect));
}
$rowR = mysqli_fetch_assoc($resultR);
$today = date("m.d.y");
// echo $today;
if (isset($_POST["book"])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user'];
    $today = date("m.d.y");
    // Verifica si las fechas son válidas
    if ($start_date < $today) {
        echo "<div class='alert alert-danger' role='alert'>
                  <h3>The date cannot be before today.</h3>
              </div>";
    } elseif ($start_date > $end_date) {
        echo "<div class='alert alert-danger' role='alert'>
                  <h3>Start date cannot be later than end date.</h3>
              </div>";
    } elseif (strtotime($start_date) > strtotime('+2 years', strtotime($today))) {
        echo "<div class='alert alert-danger' role='alert'>
                  <h3>You cannot book a room more than 2 years in advance.</h3>
              </div>";
    } else {
        // Verifica si hay conflictos de fechas
        $conflict_sql = "SELECT * FROM bookings WHERE fk_rooms_id = '$id' AND ('$start_date' BETWEEN start_date AND end_date OR '$end_date' BETWEEN start_date AND end_date OR start_date BETWEEN '$start_date' AND '$end_date' OR end_date BETWEEN '$start_date' AND '$end_date')";
        $conflict_result = mysqli_query($connect, $conflict_sql);

        if (mysqli_num_rows($conflict_result) > 0) {
            echo "<div class='alert alert-danger' role='alert'>
                      <h3>These dates are already booked. Please choose different dates.</h3>
                  </div>";
        } else {
            // Insert booking record
            $sql_book = "INSERT INTO bookings (fk_users_id, fk_rooms_id, start_date, end_date) VALUES ('$user_id', '$id', '$start_date', '$end_date')";
            $result_book = mysqli_query($connect, $sql_book);

            if ($result_book) {
                echo "<div class='alert alert-success' role='alert'>
            <h4 class='alert-heading'>Booked Successfully!</h4>
            <p>Aww yeah, you successfully booked a room. You will be redirected in <span id ='timer'>3</span> seconds!</p>
        </div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>
                      <h3>Something went wrong, please try again later!</h3>
                  </div>";
            }
            header("refresh: 3; url= home.php");
        }
        // // Debugging: Check for SQL errors
        // if (!$result_book) {
        //     echo "Booking Error: " . mysqli_error($connect) . "<br>";
        //     echo "Query: " . $sql_book . "<br>";
        // }
        // if (!$result_update) {
        //     echo "Update Error: " . mysqli_error($connect) . "<br>";
        //     echo "Query: " . $update_sql . "<br>";
        // }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
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
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?id=" . $id ?>" class="w-50 mx-auto">
            <h2 class="mb-3">Book Room: <?= ($rowR["room_name"]) ?></h2><br>
            <div>
                <div class='card' style='width: 18rem;'>
                    <img src='pictures/<?= $rowR["picture"] ?>' class='card-img-top' alt='...'>
                </div>
            </div>
            <div class="mb-3">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" style='width: 18rem;' id="start_date" name="start_date" required>
            </div>
            <div class="mb-3">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" style='width: 18rem;' id="end_date" name="end_date" required>
            </div>
            <input type="submit" class="btn btn-primary" name="book" value="Book">
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