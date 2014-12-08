/***************************************************************
*  Copyright notice
*
*  (c) 2014 Goobi. Digitalisieren im Verein e.V. <contact@goobi.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

$(document).ready(function() {

$("#tx-dfgviewer-sru-form").submit(function( event ) {

	// Stop form from submitting normally
	event.preventDefault();

	$('#tx-dfgviewer-sru-results-loading').show();
	$('#tx-dfgviewer-sru-results-clearing').hide();

	// Send the data using post
	$.post(
		"/",
		{
			eID: "tx_dfgviewer_sru_eid",
			q: escape( $( "input[name='tx_dlf[query]']" ).val() ),
			L: escape( $( "input[name='L']" ).val() ),
			id: $( "input[name='tx_dfgviewer[id]']" ).val(),
			sru: $( "input[name='tx_dfgviewer[sru]']" ).val(),
			action: $( "input[name='tx_dfgviewer[action]']" ).val(),
		},
		function(data) {
			$('#tx-dfgviewer-sru-results').html(data);
		},
		"html")
		.done(function( data ) {
			$('#tx-dfgviewer-sru-results-loading').hide();
			$('#tx-dfgviewer-sru-results-clearing').show();
		});
});

// clearing button
$('#tx-dfgviewer-sru-results-clearing').click(function() {
	$('#tx-dfgviewer-sru-results').remove();
	$('#tx-dfgviewer-sru-query').val('');
});



});
