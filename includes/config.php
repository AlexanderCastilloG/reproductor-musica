<?php 
/**
 * Y, básicamente, lo que hace es que una de las cargas de la página BHP lo envíe al servidor en pedazos
    Esto solo significa mucho hasta que tengamos todos los datos para enviarlos al servidor.
 */
// ob_start — Activa el almacenamiento en búfer de la salida
ob_start();
session_start();

$timezome = date_default_timezone_get("America/Lima");

$con = mysqli_connect("localhost", "root", "Calexander1994", "slotify");

mysqli_set_charset($con, 'utf8'); //ver las ñ y tildes

if(mysqli_connect_errno()){
    echo "Failed to connect: " . mysqli_connect_errno();
}

?>