var demoMovieFile = '/typo3conf/ext/dfgviewer/Resources/Public/dummy/content/bbb_sunflower_1080p_30fps_normal.mp4';
var fps = 30;
var viewport;
$(document).ready(function () {
    viewport = $("#mediaplayer-viewport");
    if(viewport && viewport.length > 0) {
        initializePlayer();
        bindPlayerFunctions();
        bindKeyboardEvents();
    }
});

/**
 * binds all necessary video player functions
 */
function bindPlayerFunctions() {

    $('.button-settings').bind('click', function() {
        toggleSettingsMenu();
    });

    $('.button-nextframe').bind('click', function() {
        frameForward();
    });

    $('.button-lastframe').bind('click', function() {
        frameBackward();
    });

    $('.button-backward').bind('click', function() {
        backward();
    })

    $('.button-forward').bind('click', function () {
        forward();
    })

    bindSettingsMenuItems();

    bindSpeedSettings();

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

/**
 * binds the settings menu items (outsourced for better overview)
 */
function bindSettingsMenuItems() {

    // right click on mediaplayer-viewport for settings menu

    $('#mediaplayer-viewport').contextmenu(function(event) {
        event.preventDefault();
        $('.viewport-menu').show('fast');
    });
    // bind back buttons
    $('.menu-item-back').bind('click', function() {
        $('.viewport-menu').children().hide();
        $('.settings-menu').show('fast');
    });
    // bind speed settings
    $('.settings-menu-item-speed-menu').bind('click', function() {
        $('.settings-menu').hide();
        $('.speed-menu').show('fast');
    });
    // bind quality settings
    $('.settings-menu-item-quality-menu').bind('click', function() {
        $('.settings-menu').hide();
        $('.quality-menu').show('fast');
    });
    // bind subtitle settings
    $('.settings-menu-item-subtitle').bind('click', function() {
        $('.settings-menu').hide();
        $('.subtitle-menu').show('fast');
    });
    // bind subtitle settings
    $('.settings-menu-item-language').bind('click', function() {
        $('.settings-menu').hide();
        $('.language-menu').show('fast');
    });
    $('.settings-menu-item-help').bind('click', function() {
        $('.viewport-menu').hide();
        $('.dfgplayer-help').show('fast');
    })

    $('.modal-close').bind('click', function() {
        $('.dfgplayer-help').hide('fast');
    })
}

function bindSpeedSettings() {
    $('.speed-menu').children().each(function() {
        if($(this).data('speed')) {
            $(this).bind('click', function() {
                viewport.jPlayer('option', 'playbackRate', $(this).data('speed'));
                $('.speed-label').text($(this).data('speed') + 'x');
                $('.viewport-menu').children().hide();
                $('.settings-menu').show();
            });
        }
    });
}

/**
 * binds keyboard events for player keyboard controls
 */
function bindKeyboardEvents() {
 $(document).keydown(function (e) {
     switch (e.keyCode) {
         case 13:
             // toggle Fullscreen (ALT + Return)
             (e.altKey && viewport.data("jPlayer").options.fullScreen) ? viewport.jPlayer("option", "fullScreen", false) : viewport.jPlayer("option", "fullScreen", true);
             break;
         case 32:
             // toggle Play / Pause (Space)
             viewport.data("jPlayer").status.paused ? viewport.jPlayer( "play") : viewport.jPlayer( "pause");
            break;
         case 37:
             // frameskip backward / fast backward (left / shift left)
             (e.shiftKey === true) ? backward() : frameBackward();
             break;
         case 39:
             // frameskip forward / fast forward (right / shift right)
             (e.shiftKey === true) ? forward() : frameForward();
             break;
         case 72:
             toggleHelp();
             break;
         case 112:
             e.preventDefault();
             toggleHelp();
             break;
         case 77:
             // toggle volume - mute (m)
             viewport.data("jPlayer").options.muted ? viewport.jPlayer("option", "muted", false) : viewport.jPlayer("option", "muted", true);
             break;
     }
 });
}

/**
 * initializes the jplayer
 */
function initializePlayer() {
    viewport.jPlayer( {
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
        size: {
            width: "100%",
            height: "auto"
        },
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
    });
    viewport.jPlayer( "load" )
}

/**
 * generates timeline markers for chapter selection
 */
function generateChapters() {
    var length = getMediaLength();
    var seekBar = $('.jp-seek-bar');

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

/**
 * toggles the media player settings window
 */
function toggleSettingsMenu() {
    var menuContainer = $('.viewport-menu');
    menuContainer.children().hide();
    $('.settings-menu').show();
    menuContainer.toggle('fast');

}

/**
 * returns the length from initialized media file
 * @returns {string|number|string}
 */
function getMediaLength() {
    return viewport.data("jPlayer").status.duration;
}

/**
 * shows the JPlayer informations in browserconsole - debugging only
 * TODO: remove this function in production
 */
function getMediaInfo() {
    console.log(viewport.data("jPlayer"));
}

/**
 * shows next frame
 */
function frameForward() {
    if(viewport.data("jPlayer").status.currentTime < viewport.data("jPlayer").status.duration) {
        var timecode = viewport.data("jPlayer").status.currentTime + (1 / fps);
        viewport.jPlayer( "pause", timecode );
        $(".button-play").css("display", "block");
    }
}

/**
 * shows last frame
 */
function frameBackward() {
    if(viewport.data("jPlayer").status.currentTime > 0) {
        var timecode = viewport.data("jPlayer").status.currentTime - (1 / fps);
        viewport.jPlayer( "pause", timecode );
        $(".button-play").css("display", "block");
    }
}

function forward() {
    if((viewport.data("jPlayer").status.currentTime + 10) < viewport.data("jPlayer").status.duration) {
        viewport.jPlayer( "play", viewport.data("jPlayer").status.currentTime + 10 );
    }
}

function backward() {
    if((viewport.data("jPlayer").status.currentTime - 10) > 0) {
        viewport.jPlayer( "play", viewport.data("jPlayer").status.currentTime - 10 );
    }
}
/**
 * plays the media from a individual position in media stream
 * @param seconds
 */
function play(seconds) {
    viewport.jPlayer( "play", seconds );
}

function toggleHelp() {
    var helpModal = $('.dfgplayer-help');
    helpModal.css('display') === 'none' ? helpModal.show('fast') : helpModal.hide('fast');
}