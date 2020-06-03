var demoMovieFile = '/typo3conf/ext/dfgviewer/Resources/Public/dummy/content/bbb_sunflower_1080p_30fps_normal.mp4';
var mediaIsPlaying = false;
var mediaIsMuted = false;

$(document).ready(function () {
    initializePlayer();
    bindPlayerFunctions();
    bindKeyboardEvents();
});

function bindPlayerFunctions() {

//    $('.control-bars').hover(function() {
//        $('.control-bars').animate({ opacity: 1 });
//    }, function() {
//        $('.control-bars').animate({ opacity: 0 });
//    });

//    $('.button-play').click(function() {
//        buttonPlayPause();
//    });

//    $('.button-stop').click(function() {
//        buttonStop();
//    });

//    $('.button-fullscreen').click(function() {
//        buttonFullscreen();
//    });

//    $('.button-mute').click(function() {
//        buttonMute();
//    });

    $("#mediaplayer-viewport").bind($.jPlayer.event.timeupdate, function(event) { // Add a listener to report the time play began
        $(".time-current").text($.jPlayer.convertTime( event.jPlayer.status.currentTime ));
        $(".time-remaining").text($.jPlayer.convertTime( event.jPlayer.status.duration - event.jPlayer.status.currentTime ));
    });
}

function bindKeyboardEvents() {
 $(document).keydown(function (e) {
     switch (e.keyCode) {
         case 32:
            console.log('space');
            break;
     }
 });
}

function initializePlayer() {
    $("#mediaplayer-viewport").jPlayer( {
        ready: function() {
            $(this).jPlayer( "setMedia", {
                m4v: demoMovieFile,

            });
        },
        play: function() {
            console.log('media set');
            $(this).jPlayer("option", "size", {
                width: '100%',
                height: 'auto'
            } );
        },
        backgroundColor: "#000000",
        supplied: "m4v",
        swfPath: "/typo3conf/ext/dlf/Resources/Public/Javascript/jPlayer/jquery.jplayer.swf",
        cssSelectorAncestor: ".ol-viewport",
        cssSelector: {
            videoPlay: ".button-play",
            play: ".button-play",
            pause: ".button-pause",
            stop: ".button-stop",
            seekBar: ".jp-seek-bar",
            playBar: ".jp-play-bar",
            mute: ".button-mute",
            unmute: ".button-unmute",
            volumeBar: ".jp-volume-bar",
            volumeBarValue: ".jp-volume-bar-value",
            volumeMax: ".jp-volume-max",
            playbackRateBar: ".jp-playback-rate-bar",
            playbackRateBarValue: ".jp-playback-rate-bar-value",
            currentTime: ".jp-current-time",
            duration: ".jp-duration",
            title: ".jp-title",
            fullScreen: ".button-fullscreen",
            restoreScreen: ".button-minimize",
            repeat: ".jp-repeat",
            repeatOff: ".jp-repeat-off",
            gui: ".control-bars",
            noSolution: ".jp-no-solution"
        },
        keyBindings: {
            play: {
                key: 32,
                fn: function(f) {
                    if(f.status.paused) {
                        f.play();
                        conole.log('plays');
                    } else {
                        f.pause();
                        consle.log('stops');
                    }
                }
            }
        },
        // size: {
        //     width: '100%',
        //     height: 'auto'
        // }
    });
    $("#mediaplayer-viewport").jPlayer( "load" )
}

//function buttonPlayPause() {
//    if(!mediaIsPlaying) {
//        $("#mediaplayer-viewport").jPlayer('play');
//        $('.button-play').removeClass('icon-play').addClass('icon-pause');
//        mediaIsPlaying = true;
//    } else {
//        $("#mediaplayer-viewport").jPlayer('pause');
//        $('.button-play').removeClass('icon-pause').addClass('icon-play');
//        mediaIsPlaying = false
//    }
//}

// function buttonFullscreen() {
//    $("#mediaplayer-viewport").jPlayer('fullScreen')
//}

// function buttonMute() {
//    if(!mediaIsMuted) {
//        $("#mediaplayer-viewport").jPlayer('mute');
//        $('.button-mute').removeClass('icon-unmute').addClass('icon-mute');
//        mediaIsMuted = true;
//    } else {
//        $("#mediaplayer-viewport").jPlayer('unmute');
//        $('.button-mute').removeClass('icon-mute').addClass('icon-unmute');
//        mediaIsMuted = false;
//    }
//}
//function buttonStop() {
//    $("#mediaplayer-viewport").jPlayer('stop');
//    $('.button-play').removeClass('icon-pause').addClass('icon-play');
//    mediaIsPlaying = false
//}