<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background: linear-gradient(to bottom, #4E2394, #DBC9F5);
            display: flex;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Project Overview</h1>
        <p>This project is developed in PHP, using MySQL for the database and Bootstrap for styling.</p>

        <h2>Credentials</h2>
        <p>There are two types of credentials:</p>
        <ul>
            <li><strong>Admin:</strong> You can access all functionalities, including user management.</li>
            <li><strong>User:</strong> You can make reservations and manage your profile.</li>
        </ul>

        <h2>Current Status</h2>
        <p>Please note that this project is not 100% completed yet, but it is functional and showcases the core features.</p>

        <h2>Login Credentials</h2>
        <p>If you'd like to test the project, here are the credentials:</p>
        <h3>Admin Credentials:</h3>
        <p><strong>Email:</strong> admin@admin.com</p>
        <p><strong>Password:</strong> 123123</p>

        <h3>User Credentials:</h3>
        <p><strong>Email:</strong> user@user.com</p>
        <p><strong>Password:</strong> 123123</p>

        <h2>GitHub Repository</h2>
        <p>You can find the project on GitHub at the following link:</p>
        <p><a href="https://github.com/yourusername/yourproject" target="_blank">View Project on GitHub</a></p>

        <h2>Login Page</h2>
        <p>If you want to access the login page, click <a href="login.php">here</a>.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>