<?php
session_start();

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "connection.php";
$id = $_GET["id"];
$sql = "SELECT * FROM bookings WHERE id_booking = $id";
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
} else {
    echo "No booking found with this ID.";
    exit();
}

if (isset($_POST["update"])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    // Verify if the dates are valid
    if ($start_date > $end_date) {
        echo "<div class='alert alert-danger' role='alert'>
                  <h3>Start date cannot be later than end date.</h3>
              </div>";
    } else {
        // Update booking record
        $sql_update = "UPDATE bookings SET start_date = '$start_date', end_date = '$end_date', status = '$status' WHERE id_booking = $id";
        $result_update = mysqli_query($connect, $sql_update);

        if ($result_update) {
            echo "<div class='alert alert-success' role='alert'>
            <h4 class='alert-heading'>Updated Successfully!</h4>
            <p>You will be redirected in <span id ='timer'>3</span> seconds!</p>
        </div>";
            header("refresh: 3; url=CRUD/index.php");
        } else {
            echo "<div class='alert alert-danger' role='alert'>
                      <h3>Something went wrong, please try again later!</h3>
                  </div>";
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
    <title>Update Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?id=" . $id ?>" class="w-50 mx-auto">
            <h2 class="mb-3">Update Booking </h2>
            <div class="mb-3">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" style='width: 18rem;' id="start_date" name="start_date" value="<?= $row['start_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" style='width: 18rem;' id="end_date" name="end_date" value="<?= $row['end_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="status">Status</label>
                <select class="form-control" style='width: 18rem;' id="status" name="status" required>
                    <option value="accepted" <?= $row['status'] == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="confirmed" <?= $row['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <input type="submit" class="btn btn-primary" name="update" value="Update Booking">
            <div class='d-flex justify-content-center'>
                <a href='CRUD/index.php' class='btn btn-secondary text-center'>Go Back</a>
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