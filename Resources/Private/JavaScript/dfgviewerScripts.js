/*!

    Custom scripts
    ------------------------
    DFG viewer script for cookies, sidebar adaption eg.

!*/

$(document).ready(function () {

    // check mobile device to specify click events
    function mobileCheck() {
        var check = false;
        (function (a) {
            if (/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    }
    var mobileEvent = mobileCheck() ? 'touchend' : 'click';

    // menu toggles for offcanvas toc and metadata
    $('.offcanvas-toggle').on(mobileEvent, function (event) {
        $(this).parent().toggleClass('open');
    });

    // active toggle for submenus
    $('.document-functions li.submenu > a').on(mobileEvent, function (event) {
        $('li.submenu.open a').not(this).parent().removeClass('open');
        $(this).parent().toggleClass('open');
        return false;
    });

    // secondary nav toggle
    $('nav .nav-toggle').on(mobileEvent, function (event) {
        $(this).toggleClass('active');
        $('nav .viewer-nav').toggleClass('open');
    });

    // calendar dropdowns
    $('.calendar-view .contains-issues').on(mobileEvent, function (event) {
        $('.calendar-view table td.open').not($(this).parent()).removeClass('open');
        $(this).parent().toggleClass('open');
    });

    // add body class if any calendar is present
    $('.tx-dfgviewer-newspaper-calendar').parents('body').addClass('calendar');

    // Inject view switch functions for calendar/list view (initial show calendar)
    $('.tx-dfgviewer-newspaper-calendar .calendar-list-selection a.select-calendar-view, .tx-dfgviewer-newspaper-calendar .calendar-view').addClass('active');
    $('.tx-dfgviewer-newspaper-calendar .calendar-list-selection a').on(mobileEvent, function (event) {
        if (!$(this).hasClass('active')) {
            var targetElement = '.' + $(this).attr('class').replace('select-', '');
            $('.tx-dfgviewer-newspaper-calendar .active').removeClass('active');
            $(this).addClass('active');
            $(targetElement).addClass('active');
        }
    });

    // Avoid broken image display if METS definitions are wrong
    $('.provider img').each(function () {
        if ((typeof this.naturalWidth != "undefined" && this.naturalWidth == 0) || this.readyState == 'uninitialized') {
            $(this).parents('.document-functions').addClass('missing-provider-image');
        }
    });

    // Copy selected page number to mobile meta (in order to transform select field to ui button)
    if($('.pages select option[selected]')[0]) {
        const pageNumberText = $('.pages select option[selected]').text();
        $('dl.mobile-meta').append('<dt class="mobile-page-number">No.</dt><dd class="mobile-page-number"></dd>');
        $('dl.mobile-meta dd.mobile-page-number').text(pageNumberText);
    }

    // Copy some controls for mobile (page select, fullscreen)
    $('.provider').append('<div class="mobile-controls" />');
    $('.view-functions .pages form, .view-functions .zoom a.fullscreen, .fulltext-search-toggle').clone().appendTo('.provider .mobile-controls');

    // Shorten mobile meta title
    shortenMobileMetaElement = $('.provider dl.mobile-meta dd.tx-dlf-title a');
    shortenMobileMetaTitle = shortenMobileMetaElement.text();
    if (shortenMobileMetaTitle.length > 140) {
        shortenMobileMetaTitle = shortenMobileMetaTitle.substr(0, 140) + '...';
        shortenMobileMetaElement.text(shortenMobileMetaTitle);
    }

    // Check if there are is a download list. Otherwise change a to span to disable button
    if(!$('.submenu.downloads ul li')[0]) {
        $("#tab-downloads").replaceWith(function () {
            // Create a new element using jQuery with sanitized content
            return $("<span/>", {
                "title": $(this).attr('title'),
                "class": $(this).attr('class'),
                "id": $(this).attr('id'),
                "text": $(this).html() // Use "text" to set the text content, escaping it
            });
        });
    }

    // if cookie for fullscreen view is present adapat initial page rendering
    if (Cookies.get('tx-dlf-pageview-zoomFullscreen') === 'true') {
        $('body').addClass('fullscreen static');
        $('a.fullscreen').addClass('active');
    }

    // enable click on fullscreen button
    $('a.fullscreen').on(mobileEvent, function () {
        if ($('body.fullscreen')[0]) {
            exitFullscreen();
        } else {
            enterFullscreen();
        }
    });

    // Complex page turning mechanism and check if a click on page control was made and unfold next/back navigation
    if (Modernizr.touchevents) {
        $('.fwds, .backs')
            .on('touchstart', function () {
                $(this).addClass('over');
                triggeredElement = $(this);
                setTimeout(function () {
                    triggeredElement.addClass('enable-touchevent');
                }, 250);
            })
            .on('touchend', function () {
                localStorage.txDlfFromPage = $(this).attr('class').split(' ')[0];
            });
        $('body').on('touchstart', function (event) {
            target = $(event.target);
            if (!target.closest('.page-control')[0]) {
                $('.fwds, .backs').removeClass('over enable-touchevent');
                localStorage.clear();
            }
        });
        if (localStorage.txDlfFromPage) {
            $('.' + localStorage.txDlfFromPage).addClass('no-transition over enable-touchevent');
            localStorage.clear();
        }
    } else {
        $('.fwds, .backs')
            .on('mouseenter', function () {
                $(this).addClass('over');
            })
            .on('mouseleave', function () {
                $(this).removeClass('over');
            })
            .on('click', function () {
                localStorage.txDlfFromPage = $(this).attr('class').split(' ')[0];
            });
        if (localStorage.txDlfFromPage) {
            $('.' + localStorage.txDlfFromPage).addClass('no-transition over');
            localStorage.clear();
        }
    }

    // hide outdated browser hint, if cookie was found
    if (Cookies.get('tx-dlf-pageview-hidebrowseralert') === 'true') {
        $('#browser-hint').addClass('hidden');
    }


    // Finally all things are settled. Bring back animations a second later.
    setTimeout(function () {
        localStorage.clear();
        $('.fwds, .backs').removeClass('no-transition');
        $('body').removeClass('static');
    }, 1000);

});

(function () {
    let docController = null;
    window.addEventListener('tx-dlf-documentLoaded', (e) => {
        docController = e.detail.docController;

        // Update URL in page grid button
        docController.eventTarget.addEventListener('tx-dlf-stateChanged', e => {
            if (docController === null) {
                return;
            }

            $('#dfgviewer-enable-grid-view')
                .attr('href', docController.makePageUrl(e.detail.page, true));
        });
    });

    $('.tx-dlf-navigation-doubleOn').click(function (e) {
        if (docController === null) {
            return;
        }

        e.preventDefault();
        docController.changeState({
            source: 'navigation',
            simultaneousPages: 2,
        });
    });

    $('.tx-dlf-navigation-doubleOff').click(function (e) {
        if (docController === null) {
            return;
        }

        e.preventDefault();
        docController.changeState({
            source: 'navigation',
            simultaneousPages: 1,
        });
    });

    $('.tx-dlf-navigation-doublePlusOne').click(function (e) {
        if (docController === null) {
            return;
        }

        e.preventDefault();
        const pageIdx = docController.currentPageNo - 1;
        const simultaneousPages = docController.simultaneousPages;

        const rectoVerso = pageIdx % simultaneousPages;
        const newRectoVerso = (rectoVerso + 1) % simultaneousPages;
        const newPageNo = (pageIdx - rectoVerso + newRectoVerso) + 1;

        docController.changePage(newPageNo);
    });
})();

$(document).keyup(function (e) {

    // Check if ESC key is pressed. Then end fullscreen mode or close SRU form.
    if (e.keyCode == 27) {
        if ($('body.fullscreen')[0]) {
            return exitFullscreen();
        }
        if ($('.document-functions .search.open')[0]) {
            $('.document-functions .search').removeClass('open');
        }
    }
    // Check if the F key is pressed and no text input in SRU form is taking place.
    if (e.keyCode == 70 && !$('#tx-dfgviewer-sru-query').is(':focus')) {
        return enterFullscreen();
    }

});

// Activate fullscreen mode and set corresponding cookie
function enterFullscreen() {
    setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 220);
    $("body").addClass('fullscreen');
    $('a.fullscreen').addClass('active');
    Cookies.set('tx-dlf-pageview-zoomFullscreen', 'true', { sameSite: 'lax' });
}

// Exit fullscreen mode and drop cookie
function exitFullscreen() {
    setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 220);
    $("body").removeClass('fullscreen');
    $('a.fullscreen').removeClass('active');
    Cookies.remove('tx-dlf-pageview-zoomFullscreen');
}

// hide warning about outdated browser and save decision to cookie
function hideBrowserAlert() {

    $('#browser-hint').addClass('hidden');
    Cookies.set('tx-dlf-pageview-hidebrowseralert', 'true', { sameSite: 'lax' });

}
