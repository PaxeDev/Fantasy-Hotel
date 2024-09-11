<?php
session_start();

// user try to go to this page using url
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
$first_name_error =  $last_name_error = $date_of_birth_error = $email_error = $picture_error = $rpass_error = $pass_error = "";

if (isset($_POST["submit"])) {
    $first_name = cleanInput($_POST["first_name"]);
    $last_name = cleanInput($_POST["last_name"]);
    $date_of_birth = cleanInput($_POST["date_of_birth"]);
    $email = cleanInput($_POST["email"]);
    $pass = cleanInput($_POST["pass"]);
    $rpass = cleanInput($_POST["rpass"]);
    $picture = FileUpload($_FILES["picture"]);

    if (empty($first_name)) {
        $error = true;
        $first_name_error = " First name can not be empty!";
    } elseif (strlen($first_name) < 3) {
        $error = true;
        $first_name_error = "First name can´t be less than 2 Chars!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        $error = true;
        $first_name_error = "First name must only contain letters and spaces!";
    }
    if (empty($last_name)) {
        $error = true;
        $last_name_error = " Last name can not be empty!";
    } elseif (strlen($last_name) < 3) {
        $error = true;
        $last_name_error = "Last name can´t be less than 2 Chars!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $error = true;
        $last_name_error = "Last name must only contain letters and spaces!";
    }
    if (empty($date_of_birth)) {
        $error = true;
        $date_of_birth_error = "Date of birth can not be empty!";
    }
    if (empty($email)) {
        $error = true;
        $email_error = "Email can not be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { # check the format
        $error = true;
        $email_error = "Please, type a valid email";
    } else {
        $search_if_email_exist = "SELECT email from users WHERE email = '$email'";
        $result = mysqli_query($connect, $search_if_email_exist);
        if (mysqli_num_rows($result) != 0) {
            $error = true;
            $email_error = "Email already exists!";
        }
    }
    if (empty($pass)) {
        $error = true;
        $pass_error = "Password is required!";
    } elseif (strlen($pass) < 6) {
        $error = true;
        $pass_error = "Password can´t be less than 6 Chars!";
    } elseif ($pass !== $rpass) {
        $error = true;
        $rpass_error = "Confirm your password!";
    }

    if (!$error) {
        $pass = hash("sha256", $pass);
        $sql = "INSERT INTO `users`(`first_name`, `last_name`, `email`, `date_of_birth`, `password`, `images`) VALUES ('$first_name','$last_name','$email','$date_of_birth','$pass','$picture[0]')";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            echo "<div class='alert alert-success' role='alert'>
                    <h4 class='alert-heading'>Register Successfuly!</h4>
                    <p>Aww yeah, you successfully create a new account on our website!<br>Enjoy it while is free!</p>
                    <hr>
                    <p class='mb-0'>$picture[1]You will be redirected in <span id ='timer'>3</span> seconds!</p>
                </div>";
            $first_name = $last_name = $date_of_birth = $email = "";
        } else {
            echo "<div class='alert alert-alert' role='alert'>
                    <h3>Something went wrong, please try again later!</h3>
                </div>";
        }
        header("refresh: 3; url= login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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
    <div class="position-absolute top-0 start-50 translate-middle-x mt-5">
        <h1 class="text-center fs-2 fw-bold text-light">WELCOME FANTASY HOTEL</h1>
    </div>
    <div class="container position-absolute top-50 start-50 translate-middle">
        <h2 class="position-absolute top-0 start-50 translate-middle-x fs-1 fw-bold">Registration Form</h2><br>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?> " enctype="multipart/form-data" method="POST" class="w-50 mx-auto mt-5">
            <div class="mb-3">
                <label for="first_name" class="fw-semibold fs-4">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $first_name ?>">
                <p class="text-danger"><?= $first_name_error ?></p>
            </div>
            <div class="mb-3">
                <label for="last_name" class="fw-semibold fs-4">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $last_name ?>">
                <p class="text-danger"><?= $last_name_error ?></p>
            </div>
            <div class="mb-3">
                <label for="email" class="fw-semibold fs-4">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="<?= $email ?>">
                <p class="text-danger"><?= $email_error ?></p>
            </div>
            <div class="mb-3">
                <label for="date_of_birth" class="fw-semibold fs-4">Date of birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= $date_of_birth ?>">
                <p class="text-danger"><?= $date_of_birth_error ?></p>

            </div>
            <div class="mb-3">
                <label for="picture" class="fw-semibold fs-4">Your profile picture</label>
                <input type="file" class="form-control" id="picture" name="picture">
            </div>
            <div class="mb-3">
                <label for="pass" class="fw-semibold fs-4">Password</label>
                <input type="password" class="form-control" id="pass" name="pass">
                <p class="text-danger"><?= $pass_error ?></p>

            </div>
            <div class="mb-3">
                <label for="rpass" class="fw-semibold fs-4">Confirm password</label>
                <input type="password" class="form-control" id="rpass" name="rpass">
                <p class="text-danger"><?= $rpass_error ?></p>

            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary" name="submit">Create account</button>
                <span>you have an account already? <a href="login.php">sign in here</a></span>
        </form>
    </div>
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