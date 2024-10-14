<?php
function fileUpload($picture, $source = "user")
{
    $pictureName = "";
    $message = "";

    if ($picture["error"] == 4) {
        $pictureName = "avatar.png";
        $message = "No picture has been selected, but you can upload one later!";
        if ($source == "room") {
            $pictureName = "default_hotel_room.jpg";
        }
    } else {
        $check_if_image = getimagesize($picture["tmp_name"]);
        if ($check_if_image) {
            $ext = strtolower(pathinfo($picture["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
                $pictureName = uniqid() . "." . $ext;
                $destination = "pictures/{$pictureName}";
                if ($source == "room") {
                    $destination = "../pictures/{$pictureName}";
                }
                move_uploaded_file($picture["tmp_name"], $destination);
                $message = "Ok";
            } else {
                $message = "Only JPG, JPEG, PNG, and GIF files are allowed!";
            }
        } else {
            $message = "File is not a valid image!";
        }
    }

    return [$pictureName, $message];
}
