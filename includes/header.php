<?php

include("includes/config.php");
include("includes/clases/User.php");
include("includes/clases/Artist.php");
include("includes/clases/Album.php");
include("includes/clases/Song.php");
include("includes/clases/Playlist.php");
// session_destroy();

if(isset($_SESSION['userLoggedIn'])){
    // $userLoggedIn = $_SESSION['userLoggedIn'];
    $userLoggedIn = new User($con, $_SESSION['userLoggedIn']);
    $username = $userLoggedIn->getUsername();

    echo "<script>userLoggedIn = '$username';</script>";
}else {
    header("Location: register.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</head>
<body>
    <!-- <script>
        var audioElement = new Audio();
        audioElement.setTrack("assets/music/celos.mp3");
        audioElement.audio.play();
    </script> -->

    <div id="mainContainer">
        <div id="topContainer">
            <?php include("includes/navBarContainer.php"); ?>

            <div id="mainViewContainer">
                <div id="mainContent">