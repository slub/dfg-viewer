/* DFG-VIEWER JS-ADAPTIONS */


$(document).ready(function() {

	// trigger height adjustment on initial load and on window resize
	$(document).rearrangeTitleBar();
	$(window).resize(function(){ $(document).rearrangeTitleBar(); });

	// hide additional functions in page browser in a hidden menu
	$('.downloads, .grid, .doublepage').addClass('hiddenFunctions').hide();
	$('#browser_top, #browser_bottom').fadeIn(120).css({'width':'252px'}).prepend('<div class="moreFunctionsTrigger"></div>');
	$('.moreFunctionsTrigger').click(function() { $(this).fadeOut().parent().animate({'padding-left':'144px'}, 200, function() { $(this).parent().find('.hiddenFunctions').show('slide', {'direction': 'left'}, 400) }).find('.pages').animate({'right':'400px'},200); });

	// show/hide metadata in title bar (with cookie-saved status)
	if($('.mdblock').length > 1) { $('#title').append('<div title="weitere Metadaten anzeigen" class="moreMetaData"></div>'); }
	var metaDataStatus = getCookie('dfgviewer-metaDataStatus');
	if(metaDataStatus == "open") { $('.mdblock').show(); $(document).rearrangeTitleBar(); $('.moreMetaData').addClass('allMetaDataShown'); }
	$('.moreMetaData').click(function() {
		if($(this).hasClass('allMetaDataShown')) {
			$('.mdblock:not(:first-child)').slideUp(200,function() { $(document).rearrangeTitleBar() });
			$(this).attr('title','weitere Metadaten anzeigen');
			document.cookie = 'dfgviewer-metaDataStatus'+'=closed; path=/';
		} else {
			$('.mdblock').slideDown(200,function() { $(document).rearrangeTitleBar() });
			$(this).attr('title','weniger Metadaten anzeigen');
			document.cookie = 'dfgviewer-metaDataStatus'+'=open; path=/';
		}
		$(this).toggleClass('allMetaDataShown');
	});

	// show/hide navigation (with cookie-saved status)
	$('.tx-dlf-toc').append('<div class="hideNav" title="Navigation ein- und ausblenden"></div>');
	var navigationStatus = getCookie('dfgviewer-navigationStatus');
	// hide navigation if cookie set to closed OR on small windows as default
	if(navigationStatus == "closed" || (!navigationStatus && $('#whiteboxcontainer').outerWidth() < 700)) {
		$('#navcontainer').hide();
		$('.tx-dlf-toc').css({'width':'30px'});
		$('#whiteboxcontainer').css({'right':'30px'});
		$('.hideNav').addClass('hiddenNav');
	}

	$('.hideNav').click(function() {
		if($(this).hasClass('hiddenNav')) {
			$('.tx-dlf-toc').css({'width':'300px'});
			$('#navcontainer').show('slide', {'direction': 'right'}, 400);
			$('#whiteboxcontainer').animate({'right':'350px'},400, function() { if(!$('body').hasClass('noHeader')) { $(document).rearrangeTitleBar(); } });
			document.cookie = 'dfgviewer-navigationStatus'+'=open; path=/';
		} else {
			$('#navcontainer').hide('slide', {'direction': 'right'}, 200, function() { $('.tx-dlf-toc').css({'width':'30px'}) });
			$('#whiteboxcontainer').animate({'right':'30px'},200, function() { if(!$('body').hasClass('noHeader')) { $(document).rearrangeTitleBar(); } });
			document.cookie = 'dfgviewer-navigationStatus'+'=closed; path=/';
		}
		$(this).toggleClass('hiddenNav');
	});

	//go fullwidth if no navbar exists
	if(!$('.tx-dlf-toc')[0]) { $('#whiteboxcontainer').css({'right':'30px'}); };

	// extend reference menu if there are more than one references
	if($('#local .ref').length > 1) {
		$('#local .ref').wrapAll('<div class="refsContainer" />').wrapAll('<div class="refs" />');
		$('.refs').before('<div class="referenceTrigger"></div>');
	}

  // clone dfg and provider logos for fullscreen view
  $('#sitespanner').prepend('<div class="fullscreenLogos">'+$('#provider').clone().html()+$('#sponsor').clone().html()+'</div>')

  
  // inital changes at active fullscreen view
  var headerStatus = getCookie('dfgviewer-headerStatus');
  if(headerStatus == "closed") {
    $('body').toggleClass('noHeader');
    $('#header').css({'overflow':'hidden', 'height': '0' });
    $('#whiteboxcontainer, .tx-dlf-toc').css({ 'top': '20px' });
    $('#whitebox, #navcontainer').css({'top':'42px'});
    $('#toprow').css({ 'height': '30px' });
    $('#title *').hide();
    $('.fullscreenLogos').css({'top':'0px'});
  }
  
   
  // add fullscreen button to navigationbar click toggle
  $('.zoom').append('<span class="fullscreen"></span>');
  $('.tx-dlf-navigation .zoom .fullscreen').click(function() {
   if($('body').hasClass('noHeader')) {
      $('#header').css({'overflow':'visible'}).animate({ 'height': '85px' });
      $('#whiteboxcontainer, .tx-dlf-toc').animate({ 'top': '100px' });
      $('#title *').fadeToggle('fast',function() { $(document).rearrangeTitleBar(); });
      $('.fullscreenLogos').animate({'top':'-60px'});
      $('body').toggleClass('noHeader');
      document.cookie = 'dfgviewer-headerStatus'+'=open; path=/';
    } else {
      $('#header').css({'overflow':'hidden'}).animate({ 'height': '0' });
      $('#whiteboxcontainer, .tx-dlf-toc').animate({ 'top': '20px' });
      $('#whitebox, #navcontainer').animate({'top':'42px'});
      $('#toprow').animate({ 'height': '30px' });
      $('#title *').fadeToggle('fast',function() { $(document).rearrangeTitleBar(); });
      $('.fullscreenLogos').animate({'top':'0'});
      $('body').toggleClass('noHeader');
      document.cookie = 'dfgviewer-headerStatus'+'=closed; path=/';
    }          
  });

});


/* check dynamic height of titlebar and add height and top values to relevant elements */
$.fn.rearrangeTitleBar = function() {
  if(!$('body').hasClass('noHeader')) {
	var titleHeightValue = $('#title').outerHeight()+'px';
	$('#whitebox, #navcontainer').css({'top':titleHeightValue});
	$('#toprow').css({'height':titleHeightValue});
  }
};


/* read the cookie for nav and metadata status */
getCookie = function(name) {
	var results = document.cookie.match("(^|;) ?"+name+"=([^;]*)(;|$)");
	if (results) {
		return unescape(results[2]);
	} else {
		return null;
	}
}


/* EOF */
