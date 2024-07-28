<?php
function fileUpload($picture, $source = "user")
{
    if ($picture["error"] == 4) {
        $pictureName = "avatar.png";
        $message = "No picture has been selected, but you can upload one later!";
        if ($source == "room") {
            $pictureName = "default_hotel_room.jpg";
        }
    } else {
        $check_if_image = getimagesize($picture["tmp_name"]);
        $message = $check_if_image ? "Ok" : "Not an image";
    }
    if ($message == "Ok") {
        $ext = strtolower(pathinfo($picture["name"], PATHINFO_EXTENSION));
        $pictureName = uniqid() . "." . $ext;
        $destination = "pictures/{$pictureName}";
        if ($source == "room") {
            $destination = "../pictures/{$pictureName}";
        }
        move_uploaded_file($picture["tmp_name"], $destination);
    }
    return [$pictureName, $message];
}
