<?php
$localhost = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "hotel_room_booking";

$connect = mysqli_connect($localhost, $username, $password, $dbname);

if (!$connect) {
    die("Connection failed");
}
function cleanInput($input)
{
    $data = trim($input); // removing extra spaces, tabs, newlines out of the string
    $data = strip_tags($data); // removing tags from the string
    $data = htmlspecialchars($data); // converting special characters to HTML entities, something like "<" and ">", it will be replaced by "&lt;" and "&gt";

    return $data;
}
