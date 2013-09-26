/* DFG-Viewer Javascripts by TJ @ 2012 */

$(document).ready(function() {
	viewPortAdapt(); // inital Height Adapt
	$(window).resize(function() { viewPortAdapt(); }); // resize pageViewArea on window resize
	$('#nav ul li.current').prepend('<span class="currentArrow"></span>'); // add current icon to nav
});

viewPortAdapt = function() {
	var heightCalculation = ($('#title').outerHeight()+115+80+100); // (dynamic height of title + static height of header + footer + margins)
	var pageViewHeight = ($('body').height()-heightCalculation)+"px";  // convert to a viewport based px-string
	var pageViewWidth = ($('body').width()-325)+"px"; // width calculation minus the toc and some margins
	if(($('body').height()-heightCalculation) > $('#nav').outerHeight()) { $('.tx-dfgviewer-map').css({'height':pageViewHeight}); }
	$('.tx-dfgviewer-map').css({'width':pageViewWidth});
};