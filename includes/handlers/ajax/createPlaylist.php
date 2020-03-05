<?php 

include("../../config.php");

if(isset($_POST['name']) && isset($_POST['username'])){
    $name = $_POST['name'];
    $username = $_POST['username'];
    $date = date("Y-m-d"); /*para crear una fecha en php */

    if(empty($name)){
        echo "El nombre no puede estar vacio";
        die(); 
    } 

    $query = mysqli_query($con, "INSERT INTO playlists VALUES('', '$name', '$username', '$date') ");
}else {
    echo "Name or username parameters not passed into file";
}

?>