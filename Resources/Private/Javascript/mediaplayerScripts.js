var demoMovieFile = '/typo3conf/ext/dfgviewer/Resources/Public/dummy/content/bbb_sunflower_1080p_30fps_normal.mp4';
var mediaIsPlaying = false;
var mediaIsMuted = false;

$(document).ready(function () {
    initializePlayer();
    bindPlayerFunctions();
    bindKeyboardEvents();
});

function bindPlayerFunctions() {

    var viewport = $("#mediaplayer-viewport");
    viewport.bind($.jPlayer.event.timeupdate, function(event) { // Add a listener to report the time play began
        $(".time-current").text($.jPlayer.convertTime( event.jPlayer.status.currentTime ));
        $(".time-remaining").text($.jPlayer.convertTime( event.jPlayer.status.duration - event.jPlayer.status.currentTime ));
    });
    viewport.bind($.jPlayer.event.canplay, function(event) {
        generateChapters();
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
    });
    $("#mediaplayer-viewport").jPlayer( "load" )
}

function generateChapters() {
    var length = getMediaLength();
    var seekBar = $('.jp-seek-bar');
    var chapters = $('.chapter');

    $('.chapter').each(function() {
        var timecode = $(this).data('timecode');
        var title = $(this).data('title');
        $('<span />', {
            'class': 'jp-chapter-marker',
            title: $(this).data('title'),
            style: 'position: absolute; left: ' + ((timecode -0.5) * 100 / length) + '%',
            click: function() {
                play(timecode);
            }

        }).appendTo(seekBar);
    });
}

function getMediaLength() {
    return $("#mediaplayer-viewport").data("jPlayer").status.duration;
}
function play(seconds) {
    $("#mediaplayer-viewport").jPlayer( "play", seconds );
}
