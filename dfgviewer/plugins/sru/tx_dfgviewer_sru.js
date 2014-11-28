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

	var $form = $( this ),
		term = $form.find( "input[name='tx_dlf[query]']" ).val(),
		sru = $form.find( "input[name='tx_dfgviewer[sru]']" ).val();

	// Send the data using post
	$.post(
		"/",
		{
			eID: "tx_dfgviewer_sru_eid",
			q: escape(term),
			sru: $("input[name='tx_dfgviewer[sru]']").val(),
		},
		function(data) {
			console.log(data);
			$('#tx-dfgviewer-sru-results').html(data);
		},
		"html");
});


});
