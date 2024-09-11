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

$error = false;
$email = "";
$email_error = $password_error = "";

if (isset($_POST["login"])) {
    $email = cleanInput($_POST["email"]);
    $password = cleanInput($_POST["password"]);

    if (empty($email)) {
        $error = true;
        $email_error = "Email can´t be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please enter a valid email address!";
    }
    if (empty($password)) {
        $error = true;
        $password_error = "Password can´t be empty!";
    }
    if (!$error) {
        $password = hash("sha256", $password);
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) == 1) {
            if ($row["status"] == "admin") {
                $_SESSION["admin"] = $row["id"];
                header("Location: dashboard.php");
            } else {
                $_SESSION["user"] = $row["id"];
                header("Location: home.php");
            }
        } else {
            echo "<div class='alert alert-danger'>
            <p>Incorrect credentials!</p>
          </div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <h2 class="text-center fs-2 fw-bold">Login Page</h2>
        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" autocomplete="off" class="w-50 mx-auto ">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold fs-4">Email address</label>
                <input type="email" class="form-control" id="email" aria-describedby="email" name="email" placeholder="Email address" value="<?= $email ?>">
                <p class="text-danger"><?= $email_error ?></p>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold fs-4">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <p class="text-danger"><?= $password_error ?></p>
                <button type="submit" class="btn btn-primary mt-3" name="login">Login</button>
                <div>or <a class='link-opacity-100 mt-3' href='register.php'>Create a new account</a></div>
            </div>
        </form>
    </div>

    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>