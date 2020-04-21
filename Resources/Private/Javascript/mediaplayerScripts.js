var demoMovieFile = '/typo3conf/ext/dfgviewer/Resources/Public/dummy/content/bbb_sunflower_1080p_30fps_normal.mp4';

$(document).ready(function () {
    initializePlayer();

});

function initializePlayer() {
    $("#mediaplayer-viewport").jPlayer( {
        ready: function() {
            $(this).jPlayer( "setMedia", {
                m4v: demoMovieFile,

            });
        },
        backgroundColor: "#000000",
        supplied: "m4v",
        swfPath: "/typo3conf/ext/dlf/Resources/Public/Javascript/jPlayer/jquery.jplayer.swf"
    });
}