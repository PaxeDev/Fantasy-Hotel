<?php
session_start();
//protect the page from ppl who play in url

if (!isset($_SESSION["user"]) && !isset($_SESSION["admin"])) {
    header("Location:../login.php");
    exit();
}
if (isset($_SESSION["user"])) {
    header("Location: ../home.php");
    exit();
}

require_once "../connection.php";

if (isset($_GET["id"])) {


    $id = $_GET["id"];

    $sql = "SELECT * FROM rooms WHERE room_id = $id";

    $result = mysqli_query($connect, $sql);
    $row = mysqli_fetch_assoc($result);

    if (isset($_GET["delete"])) {
        if ($row["image"] != "picture.jpg") {
            unlink("../pictures/{$row["picture"]}");
        }
        $sql_delete = "DELETE FROM rooms WHERE room_id = $id";
        mysqli_query($connect, $sql_delete);

        header("Location: index.php");
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <style>
            html,
            body {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            body {
                display: flex;
                flex-direction: column;
                background: linear-gradient(to bottom, #4E2394, #DBC9F5);
                background-repeat: no-repeat;
                min-height: 100%;
            }

            .container {
                flex-grow: 1;
                padding-top: 70px;
            }
        </style>
    </head>

    <body>
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Are you sure you want to remove the room number <?= $row["room_number"] ?></strong>
                <a href="delete.php?id=<?= $row["room_id"] ?>&delete=true" class="btn btn-danger">Yes</a>
                <a href="index.php" class="btn btn-secondary">No</a>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>

    </html>
<?php

} else {
    header("Location: index.php");
}
?>