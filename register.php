<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}
if (isset($_SESSION["admin"])) {
    header("Location: dashboard.php");
    exit();
}
require_once "connection.php";
require_once "file_upload.php";

$error = false;
$first_name = $last_name = $pass = $date_of_birth = $email = $picture = $rpass = "";
$first_name_error = $last_name_error = $date_of_birth_error = $email_error = $picture_error = $rpass_error = $pass_error = "";
$errors = [];
$message = "";

if (isset($_POST["submit"])) {
    $first_name = cleanInput($_POST["first_name"]);
    $last_name = cleanInput($_POST["last_name"]);
    $date_of_birth = cleanInput($_POST["date_of_birth"]);
    $email = cleanInput($_POST["email"]);
    $pass = cleanInput($_POST["pass"]);
    $rpass = cleanInput($_POST["rpass"]);
    $picture = FileUpload($_FILES["picture"]);

    if (empty($first_name)) {
        $errors['first_name'] = "First name cannot be empty!";
    } elseif (strlen($first_name) < 3) {
        $errors['first_name'] = "First name can't be less than 3 characters!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        $errors['first_name'] = "First name must only contain letters and spaces!";
    }

    if (empty($last_name)) {
        $errors['last_name'] = "Last name cannot be empty!";
    } elseif (strlen($last_name) < 3) {
        $errors['last_name'] = "Last name can't be less than 3 characters!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $errors['last_name'] = "Last name must only contain letters and spaces!";
    }

    if (empty($date_of_birth)) {
        $errors['date_of_birth'] = "Date of birth cannot be empty!";
    }

    if (empty($email)) {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please type a valid email!";
    } else {
        $search_if_email_exist = "SELECT email from users WHERE email = '$email'";
        $result = mysqli_query($connect, $search_if_email_exist);
        if (mysqli_num_rows($result) != 0) {
            $errors['email'] = "Email already exists!";
        }
    }

    if (empty($pass)) {
        $errors['pass'] = "Password is required!";
    } elseif (strlen($pass) < 6) {
        $errors['pass'] = "Password can't be less than 6 characters!";
    } elseif ($pass !== $rpass) {
        $errors['rpass'] = "Passwords do not match!";
    }

    if ($picture[1] !== "Ok" && $picture[1] !== "No picture has been selected, but you can upload one later!") {
        $errors['picture'] = $picture[1];
    }

    if (empty($errors)) {
        $pass = hash("sha256", $pass);
        $sql = "INSERT INTO `users`(`first_name`, `last_name`, `email`, `date_of_birth`, `password`, `images`) 
                VALUES ('$first_name','$last_name','$email','$date_of_birth','$pass','$picture[0]')";
        $result = mysqli_query($connect, $sql);

        if ($result) {
            $message = "Registration successful!";
            if ($picture[1] === "No picture has been selected, but you can upload one later!") {
                $message .= "<br>" . $picture[1];
            }
        } else {
            $errors[] = "Something went wrong, please try again later!";
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
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="position-absolute top-0 start-50 translate-middle-x mt-5">
        <h1 class="text-center fs-2 fw-bold text-light">WELCOME FANTASY HOTEL</h1>
    </div>
    <div class="container position-absolute top-50 start-50 translate-middle">
        <h2 class="position-absolute top-0 start-50 translate-middle-x fs-1 fw-bold">Registration Form</h2><br>

        <?= !empty($errors) ? "<div class='alert alert-danger mt-5'>" . implode("<br>", $errors) . "</div>" : '' ?>
        <?= $message ? "<div class='alert alert-success mt-5'><p>$message You will be redirected in <span id='timer'>3</span> seconds!</p></div>" : '' ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" method="POST" class="w-50 mx-auto mt-5">
            <div class="mb-3">
                <label for="first_name" class="fw-semibold fs-4">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
                <?= !empty($errors['first_name']) ? "<small class='text-warning'>{$errors['first_name']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <label for="last_name" class="fw-semibold fs-4">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
                <?= !empty($errors['last_name']) ? "<small class='text-warning'>{$errors['last_name']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <label for="email" class="fw-semibold fs-4">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
                <?= !empty($errors['email']) ? "<small class='text-warning'>{$errors['email']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <label for="date_of_birth" class="fw-semibold fs-4">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth) ?>">
                <?= !empty($errors['date_of_birth']) ? "<small class='text-warning'>{$errors['date_of_birth']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <label for="picture" class="fw-semibold fs-4">Your Profile Picture</label>
                <input type="file" class="form-control" id="picture" name="picture">
                <?= !empty($errors['picture']) ? "<small class='text-warning'>{$errors['picture']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <label for="pass" class="fw-semibold fs-4">Password</label>
                <input type="password" class="form-control" id="pass" name="pass">
                <?= !empty($errors['pass']) ? "<small class='text-warning'>{$errors['pass']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <label for="rpass" class="fw-semibold fs-4">Confirm Password</label>
                <input type="password" class="form-control" id="rpass" name="rpass">
                <?= !empty($errors['rpass']) ? "<small class='text-warning'>{$errors['rpass']}</small>" : '' ?>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary" name="submit">Create Account</button>
                <span>Already have an account? <a href="login.php">Sign in here</a></span>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let timer = 3;
        setInterval(() => {
            timer--;
            document.getElementById("timer").innerText = timer;
            if (timer <= 0) {
                window.location.href = 'login.php';
            }
        }, 1000);
    </script>
</body>

</html>