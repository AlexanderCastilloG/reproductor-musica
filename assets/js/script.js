var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;

$(document).click(function(click) {
    var target = $(click.target);

    if (!target.hasClass("item") && !target.hasClass("optionButton")) {
        hideOptionsMenu();
    }

    // console.log(target);
});

$(window).scroll(function() {
    hideOptionsMenu();
});

$(document).on("change", "select.playlist", function() {
    var select = $(this);
    var playlistId = select.val();
    /*Para seleccionar el elemento que viene inmediatamente antes del elemento select.playlist */
    var songId = select.prev(".songId").val();

    $.post("includes/handlers/ajax/addToPlaylist.php", {
        playlistId: playlistId,
        songId: songId
    }).done(function(error) {

        if (error != "") {
            alert(error);
            return;
        }

        hideOptionsMenu();
        select.val("");
    });
});

function updateEmail(emailClass) {
    var emailValue = $("." + emailClass).val();

    $.post("includes/handlers/ajax/updateEmail.php", {
        email: emailValue,
        username: userLoggedIn
    }).done(function(response) {
        $("." + emailClass).nextAll(".message").text(response);
        // $("." + emailClass).nextUntil(".message").text(response);
    });
}

function updatePassword(oldPasswordClass, newPasswordClass1, newPasswordClass2) {
    var oldPassword = $("." + oldPasswordClass).val();
    var newPassword1 = $("." + newPasswordClass1).val();
    var newPassword2 = $("." + newPasswordClass2).val();

    $.post("includes/handlers/ajax/updatePassword.php", {
        oldPassword: oldPassword,
        newPassword1: newPassword1,
        newPassword2: newPassword2,
        username: userLoggedIn
    }).done(function(response) {
        $("." + oldPasswordClass).nextAll(".message").text(response);
    });
}

function logout() {
    $.post("includes/handlers/ajax/logout.php", function() {
        location.reload();
    });
}

function openPage(url) {

    if (timer != null) { //si esta lleno
        clearTimeout(timer);
    }

    if (url.indexOf("?") == -1) {
        url = url + "?";
    }

    var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
    console.log(encodedUrl);
    console.log(url);
    /*Con JQuery */
    /**
     * Este método es la forma más sencilla de obtener datos del servidor. Es más o menos equivalente
     * a $ .get (url, datos, éxito), excepto que es un método en lugar de una función global y 
     * tiene una función de devolución de llamada implícita. Cuando se detecta una respuesta exitosa
     * (es decir, cuando textStatus es "exitoso" o "no modificado"), .load () establece el contenido
     * HTML de los elementos coincidentes en los datos devueltos. Esto significa que la mayoría de los
     * usos del método pueden ser bastante simples:
     */
    $("#mainContent").load(encodedUrl);

    /*Añadiendo y modificando entradas del historial */
    /**
     * pushState() toma tres parámetros: un objeto estado, un título (el cual es normalmente ignorado)
     *  y (opcionalmente) una URL.  Vamos a examinar cada uno de estos tres parametros en más detalle:
     */
    $("body").scrollTop(0);
    history.pushState(null, null, url);
}

function removeFromPlaylist(button, playlistId) {
    var songId = $(button).prevAll(".songId").val();

    $.post("includes/handlers/ajax/removeFromPlaylist.php", {
        playlistId: playlistId,
        songId: songId
    }).done(function(error) {

        if (error != "") {
            alert(error);
            return;
        }

        //do something when ajax returns
        openPage("playlist.php?id=" + playlistId);
    });
}

function createPlaylist(username) {

    var popup = prompt("Please enter the name of your playlist");

    if (popup != null) {
        $.post("includes/handlers/ajax/createPlaylist.php", { name: popup, username: userLoggedIn })
            .done(function(error) {
                console.log("error " + error);

                if (error != "") {
                    alert(error);
                    return;
                }

                // do something when ajax returns
                openPage("yourMusic.php");
            });
    }
}

function deletePlaylist(playlistId) {
    var prompt = confirm("Are you sure you want to delete this playlist");

    if (prompt == true) {
        $.post("includes/handlers/ajax/deletePlaylist.php", { playlistId: playlistId })
            .done(function(error) {

                if (error != "") {
                    alert(error);
                    return;
                }

                //hacer algo cuando regrese aja
                openPage("yourMusic.php");
            });
    }
}

function hideOptionsMenu() {
    var menu = $(".optionsMenu");
    if (menu.css("display") != "none") {
        menu.css("display", "none");
    }
}

function showOptionsMenu(button) {

    var songId = $(button).prevAll(".songId").val();
    var menu = $(".optionsMenu");
    var menuWidth = menu.width();

    menu.find(".songId").val(songId); //valor de menu

    /*distancia desde la parte superior de la ventana hasta la parte superior del documento*/
    var scrollTop = $(window).scrollTop();
    /*distancia desde la parte superior del documento*/
    var elementOffset = $(button).offset().top;

    /* la distancia del boton con el documento */
    var top = elementOffset - scrollTop;

    var left = $(button).position().left;

    //5px;

    menu.css({
        "top": top + "px",
        "left": left - menuWidth + "px",
        "display": "inline"
    });

    // console.log(scrollTop);
    // console.log(elementOffset);
    // console.log("left" + (left - menuWidth));
}

function formatTime(secondss) {
    var time = Math.round(secondss);
    var minutes = Math.floor(time / 60); //Rounds down
    var seconds = time - (minutes * 60);

    var extraZero = (seconds < 10) ? "0" : "";

    return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
    /*JQuery -> $(".progressTime.current").text(formatTime(audio.currentTime)); */
    document.querySelector(".progressTime.current").textContent = formatTime(audio.currentTime);
    document.querySelector(".progressTime.remaining").textContent = formatTime(audio.duration - audio.currentTime);

    var progress = audio.currentTime / audio.duration * 100; //porcentajes
    /*jquery => $(".progress").css("width", progress + "%"); */
    document.querySelector(".playbackBar .progress").style.width = progress + "%";
}

function updateVolumeProgressBar(audio) {
    var volume = audio.volume * 100;
    document.querySelector(".volumeBar .progress").style.width = volume + "%";
}

function playFirstSong() {
    setTrack(tempPlaylist[0], tempPlaylist, true);
}

function Audio() {

    this.currentlyPlaying = [];
    this.audio = document.createElement('audio');

    // /*Elevento ended es lanzado cuando haya sido finalizado un audio */
    // this.audio.addEventListener("ended", function() {
    //     nextSong();
    // });

    /*El evento canplay es lanzado cuando el elemento <video> o <audio> puede ser iniciado o fue iniciado satisfactoriamente.*/
    this.audio.addEventListener("canplay", function() {
        /* JQuery -> this -> se refiere al objeto en el que se llamó el evento 
        $(".progressTime.remaining").text(this.duration);
        */
        var duration = formatTime(this.duration);
        document.querySelector(".progressTime.remaining").textContent = duration;
        // updateVolumeProgressBar(this);
    });

    /*El evento timeupdate es llamado cuando el tiempo indicado por el atributo currentTime es actualizado.*/
    this.audio.addEventListener("timeupdate", function() {
        if (this.duration) {
            updateTimeProgressBar(this);
        }
    });

    /*Cuando el audio de la reprodución cambia */
    this.audio.addEventListener("volumechange", function() {
        updateVolumeProgressBar(this);
    });

    this.setTrack = function(track) {
        this.currentlyPlaying = track;
        this.audio.src = track.path;
    };

    this.play = function() {
        this.audio.play();
    };

    this.pause = function() {
        this.audio.pause();
    };

    this.setTime = function(seconds) {
        this.audio.currentTime = seconds;
    };
}