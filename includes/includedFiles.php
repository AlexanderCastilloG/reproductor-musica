<?php 

/*Cómo detectar peticiones de AJAX con PHP */
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
    // echo "Vino de AJAX <br>";
    include("includes/config.php");
    include("includes/clases/User.php");
    include("includes/clases/Artist.php");
    include("includes/clases/Album.php");
    include("includes/clases/Song.php");
    include("includes/clases/Playlist.php");

    if(isset($_GET['userLoggedIn'])){
        $userLoggedIn = new User($con, $_GET['userLoggedIn']);
    }else {
        echo "username variable was not passed into page. Check the openPage JS function";
        exit();
    }

}else {
    include("includes/header.php");
    include("includes/footer.php");

    /* $_SERVER['REQUEST_URI'] => 
    La URI que se empleó para acceder a la página. Por ejemplo: '/index.html' */
    $url = $_SERVER['REQUEST_URI'];
    echo "<script>openPage('$url')</script>";
    exit();
}

?>