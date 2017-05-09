<?php
# Generates - Random Captch Code and Image
#
# Reference Material:
# - Generate Image: http://stuweb.cms.gre.ac.uk/~ha07/web/PHP/graphics.html
#
# Common functions
require ("functions.php");
# Start session to store value on server
session_start();
# Read background image
$image = ImageCreateFromPng("img/ca.png");
# Randomise the text colour
$red = rand(80, 130);
$green = rand(80, 130);
$blue = 320 - $red - $green;
$textColour = ImageColorAllocate($image, $red, $green, $blue);
# Store random string
$captchaString = generateRandomString();
# Edit the image
ImageString($image, 5, 10, 10, $captchaString, $textColour);
# Enlarge the image
$bigImage = imagecreatetruecolor(200, 80);
imagecopyresized($bigImage, $image, 0, 0, 0, 0, 200, 80, 100, 40);
# Output the image as a low quality JPEG
header("Content-Type: image/jpeg");
Imagejpeg($bigImage, NULL, 8);
# clean up
ImageDestroy($image);
ImageDestroy($bigImage);
# Store activation code in server
$_SESSION["code"] = $captchaString;