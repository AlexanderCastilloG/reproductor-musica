<?php 

$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");
$resultArray = array();

while($row = mysqli_fetch_array($songQuery)){
    array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);

?>

<script>
    


    $(document).ready(function(){

        //currentPlaylist
        var newPlaylist = <?php echo $jsonArray; ?>;
        audioElement = new Audio();

          /*Elevento ended es lanzado cuando haya sido finalizado un audio */
        audioElement.audio.addEventListener("ended", function() {
            nextSong();
        });
       
        setTrack(newPlaylist[0], newPlaylist, false);
        updateVolumeProgressBar(audioElement.audio);

        var buttonPlay = document.getElementById('play');
        var buttonPause = document.getElementById('pause');
        var buttonNext = document.getElementById('next');
        var buttonRepeat = document.getElementById('repeat');
        var buttonPrevious = document.getElementById('previous');
        var buttonVolume = document.getElementById('volume');
        var buttonShuffle = document.getElementById('shuffle');


        var progressBarSong = document.querySelector(".playbackBar .progressBar");
        var progressBarVolumen = document.querySelector(".volumeBar .progressBar");

        var nowPlayingBarContainer = document.getElementById("nowPlayingBarContainer");

        /*Evitar que los controles se resalten al arrastrar el mouse */
        nowPlayingBarContainer.addEventListener("mousedown", e => todoFunction(e));
        nowPlayingBarContainer.addEventListener("touchstart", e => todoFunction(e));
        nowPlayingBarContainer.addEventListener("mousemove", e => todoFunction(e));
        nowPlayingBarContainer.addEventListener("touchmove", e => todoFunction(e));

        var todoFunction  = event => event.preventDefault();

        /*JQuery => $(".playbackBar .progressBar").mousedown(function(){})*/
        /* El evento mousedown se activa cuando el botón de un dispositivo apuntador (usualmente el botón de un ratón) es presionado en un elemento. */
        progressBarSong.addEventListener("mousedown", () => {
            mouseDown = true;
        });

        progressBarSong.addEventListener("mousemove", (e) => {
            if(mouseDown == true){
                //establecer el tiempo de la canción, dependiendo de la posición del mouse
                timeFromOffset(e, progressBarSong);
            }
        });

        /* Ejecute un JavaScript al soltar un botón del mouse sobre un párrafo: */
        progressBarSong.addEventListener("mouseup", (e) => {
            timeFromOffset(e, progressBarSong);
            mouseDown = false;
        });

        /*Volumen - Progress*/
        progressBarVolumen.addEventListener("mousedown", () => {
            mouseDown = true;
        });

        progressBarVolumen.addEventListener("mousemove", (e) => {
            if(mouseDown == true){
                
                let percentage = e.offsetX / progressBarVolumen.clientWidth;
                
                if(percentage >= 0 && percentage <= 1){

                    
                    percentage = (percentage > 0.90 ) ? percentage.toFixed(1) : percentage;
                    audioElement.audio.volume = percentage;
                    // console.log(percentage);
                    // console.log("volumen " + audioElement.audio.volume); //el volumen total es 1;
                }
            }
        });

        progressBarVolumen.addEventListener("mouseup", (e) => {
            let percentage = e.offsetX / progressBarVolumen.clientWidth;

                if(percentage >= 0 && percentage <= 1){
                    percentage = (percentage > 0.90 ) ? percentage.toFixed(1) : percentage;
                    audioElement.audio.volume = percentage;
                }
            mouseDown = false;
        });

        /**===================================================================================================== */


        /*Evento de buttons */
        buttonPlay.addEventListener("click", () => {
            /** JQuery =>  $("#play").hide(); / $("#pause").show();*/
            playSong();
        });

        buttonPause.addEventListener("click", () => {
            pauseSong();
        });

        buttonNext.addEventListener("click", () => nextSong() );

        function timeFromOffset(mouse, eventoThis) {
            var percentage = mouse.offsetX / eventoThis.clientWidth * 100;
            var seconds = audioElement.audio.duration * (percentage / 100);
            audioElement.setTime(seconds);
        }

        buttonRepeat.addEventListener("click", () => setRepeat());

        buttonPrevious.addEventListener("click", () => prevSong());

        buttonVolume.addEventListener("click", () => setMute());

        buttonShuffle.addEventListener("click", () => setShuffle());

        function prevSong() {
            /*si el currentTime es mayor a 3 segundo vuelve a repetirlo la canción sino va al anterior canción o si es la primera canción*/
            if(audioElement.audio.currentTime >= 3 || currentIndex == 0){ 
                audioElement.setTime(0);
            }else {
                currentIndex = currentIndex -1 ;
                setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
            }
        }

        function nextSong() {

            if(repeat == true){
                audioElement.setTime(0);
                playSong();
                return;
            }

            if(currentIndex == currentPlaylist.length - 1){ // 0-9 - 9
                currentIndex = 0;
            }else {
                currentIndex++;
            }

            let trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex]; //id del index indicado
            setTrack(trackToPlay, currentPlaylist, true);
        }

        function setRepeat(){
            repeat = !repeat;
            var imageName = repeat ? "repeat-active.png" : "repeat.png";
            document.querySelector(".controlButton.repeat img").src="assets/images/icons/" + imageName;
        }

        function setMute(){
            audioElement.audio.muted = !audioElement.audio.muted;
            var changeImage = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
            document.querySelector(".controlButton.volume img").src="assets/images/icons/" + changeImage;
        }

        function setShuffle() {
            shuffle = !shuffle;
            var changeImg = shuffle ? "shuffle-active.png" : "shuffle.png";
            document.querySelector(".controlButton.shuffle img").src="assets/images/icons/" + changeImg; 

            console.log(currentPlaylist);
            console.log(shufflePlaylist);

            if(shuffle == true){
                //Aleatorizar lista de reproducción
                shuffleArray(shufflePlaylist);
                currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
            }else {
                //Aleatorio ha sido desactivado
                //volver a la lista de reproducción habitual
                currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
            }
        }

        
        
        
        
    });
    
    function shuffleArray(a){
        var j, x, i;
    
        for(i=a.length; i; i--){
            j = Math.floor(Math.random() * i);
            x = a[i-1];
            a[i-1] = a[j];
            a[j] = x;
        }
    }
            
    function setTrack(trackId, newPlaylist, play){
        
        if(newPlaylist != currentPlaylist){
            currentPlaylist = newPlaylist;
            shufflePlaylist = currentPlaylist.slice();
            shuffleArray(shufflePlaylist);
        }
    
        if(shuffle == true){
            currentIndex = shufflePlaylist.indexOf(trackId);
        }else {
            currentIndex = currentPlaylist.indexOf(trackId);
        }
    
        $.post("includes/handlers/ajax/getSongJson.php", { songId: trackId}, function(data){
            
            pauseSong();
            const track = JSON.parse(data);
            
            /**JQuery => $(".trackName span").text(track.title); */
            document.querySelector(".trackName span").textContent = track.title;
    
            $.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist}, function(data){
                const artist = JSON.parse(data);

                let span = document.querySelector(".trackInfo .artistName span")
                span.textContent = artist.name;
                span.setAttribute("onclick", "openPage('artist.php?id=" + artist.id + "')");
                // $(".artistName span").attr("onclick", "openPage('artist.php?id=" + artist.id + "')");
            });
    
            $.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album}, function(data){
                const album = JSON.parse(data);
                /*jQuery -> $(".albumLink img").attr("src", album.artworkPath)*/
                let span = document.querySelector(".content .albumLink img");
                span.src = album.artworkPath;
                span.setAttribute("onclick", "openPage('album.php?id="+ album.id + "')" );
                document.querySelector(".trackInfo .trackName span").setAttribute("onclick", "openPage('album.php?id="+ album.id + "')");
                        
                audioElement.setTrack(track);
                if(play){
                    playSong();
                    // audioElement.play();
                }
            });
                
        });
    
    }
    
    function playSong(){

        if(audioElement.audio.currentTime == 0){
            $.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id});
        }else {
            console.log("DONT UPDATE TIME");
        }
    
        // buttonPlay.style.display = "none";
        document.getElementById('play').style.display = "none";
        // buttonPause.style.display = "inline";
        document.getElementById('pause').style.display = "inline";
        audioElement.play();
    }

    function pauseSong(){

        // buttonPause.style.display = "none";
        document.getElementById('pause').style.display = "none";
        // buttonPlay.style.display = "inline";
        document.getElementById('play').style.display = "inline";
        audioElement.pause();
    }


</script>

<div id="nowPlayingBarContainer">
    <div id="nowPlayingBar">
        <div id="nowPlayingLeft">
            <div class="content">
                <span class="albumLink">
                    <img role="link" tabindex="0" class="albumArtwork">
                </span>

                <div class="trackInfo">

                    <span class="trackName">
                        <span role="link" tabindex="0"></span>
                    </span>

                    <span class="artistName">
                        <span role="link" tabindex="0"></span>
                    </span>
                </div>
            </div>
        </div>

        <div id="nowPlayingCenter">
            <div class="content playerControls">
                <div class="buttons">
                    <button class="controlButton shuffle" title="Shuffle button" id="shuffle">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle">
                    </button>

                    <button class="controlButton previous" title="Previous button" id="previous"> 
                        <img src="assets/images/icons/previous.png" alt="Previous">
                    </button>

                    <button class="controlButton play" title="Play button" id="play">
                        <img src="assets/images/icons/play.png" alt="play">
                    </button>

                    <button class="controlButton pause" title="Pause button" style="display: none;" id="pause">
                        <img src="assets/images/icons/pause.png" alt="Pause">
                    </button>

                    <button class="controlButton next" title="Next button" id="next">
                        <img src="assets/images/icons/next.png" alt="Next">
                    </button>

                    <button class="controlButton repeat" title="Repeat button" id="repeat">
                        <img src="assets/images/icons/repeat.png" alt="Repeat">
                    </button>
                </div>

                <div class="playbackBar">
                    <span class="progressTime current">0.00</span>

                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                    </div>

                    <span class="progressTime remaining">0.00</span>
                </div>
            </div>
        </div>

        <div id="nowPlayingRight">
            <div class="volumeBar">
                <button class="controlButton volume" title="Volume button" id="volume">
                    <img src="assets/images/icons/volume.png" alt="Volume">
                </button>

                <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>