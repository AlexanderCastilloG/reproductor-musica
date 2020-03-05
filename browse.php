<?php 
    // include("includes/header.php");
    include("includes/includedFiles.php");
?>

<h1 class="pageHeadingBig">
    You Might Also Like</h1>
<div class="gridViewContainer">
    <?php
        $albumsQuery = mysqli_query($con, "SELECT * FROM albums ORDER BY RAND() LIMIT 10");

        while($row = mysqli_fetch_array($albumsQuery)){ //para convetir en un array asociativo el resultado
                              
        echo "<div class='gridViewItem'>
                <span role='link' tabindex='0' onclick='openPage(\"album.php?id=". $row['id'] ."\")'>
                    <img src='".$row["artworkPath"]."' alt='".$row['title']."'>
                    <div class='gridViewInfo'>
                            ".$row['title']."
                    </div>
                </span>
             </div>";
        }
    ?>
</div>

<?php 
// include("includes/footer.php");
 ?> 