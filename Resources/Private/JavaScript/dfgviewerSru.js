/***************************************************************
*  Copyright notice
*
*  (c) 2014 Kitodo. Key to digital objects e. V. <contact@kitodo.org>
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
			middleware: "dfgviewer/sru",
			q: $( "input[name='tx_dlf[query]']" ).val(),
			L: $( "input[name='tx_dfgviewer[L]']" ).val(),
			id: $( "input[name='tx_dfgviewer[id]']" ).val(),
			sru: $( "input[name='tx_dfgviewer[sru]']" ).val(),
			action: $( "input[name='tx_dfgviewer[action]']" ).val(),
		},
		function(data) {
			var resultItems = [];
            var resultList = $('<div class="sru-results-active-indicator"></div><ul>');

            if (data.error) {
                $('<li/>', {
                    class: "noresult",
                    text: $('#tx-dfgviewer-sru-label-noresult').text()
                }).appendTo(resultList);
            } else {
                for (var i = 0; i < data.length; i++) {
                    var linkCurrent = $(location).attr('href');
                    var linkBase = linkCurrent.substring(0, linkCurrent.indexOf('?'));
                    var linkParams = linkCurrent.substring(linkBase.length + 1, linkCurrent.length);
                    var linkId = linkParams.match(/id=(\d)*/g);

                    if (linkId) {
                        linkParams = linkId + '&';
                    } else {
                        linkParams = '&';
                    }

                    var linkNew = linkBase + '?' + (linkParams
                        + 'tx_dlf[id]=' + data[i].link
                        + '&tx_dlf[origimage]=' + data[i].origImage
                        + '&tx_dlf[highlight]=' + encodeURIComponent(data[i].highlight)
                        + '&tx_dlf[page]=' + (data[i].page));

                    if (data[i].previewImage) {
                        $('<li/>').append(
                            $('<a/>', {
                                href: linkNew,
                                text: data[i].previewImage
                            })
                        ).appendTo(resultList);
                    }
                    if (data[i].previewText) {
                        $('<li/>').append(
                            $('<a/>', {
                                href: linkNew,
                                text: data[i].previewText
                            })
                        ).appendTo(resultList);
                    }
                }

                if (resultItems.length === 0) {
                    $('<li/>', {
                        class: "noresult",
                        text: $('#tx-dfgviewer-sru-label-noresult').text()
                    }).appendTo(resultList);
                }
            }

            $('#tx-dfgviewer-sru-results').empty().append(resultList);
		},
		"json"
	)
	.done(function( data ) {
		$('#tx-dfgviewer-sru-results-loading').hide();
		$('#tx-dfgviewer-sru-results-clearing').show();
	});
});

// clearing button
$('#tx-dfgviewer-sru-results-clearing').click(function() {
	$('#tx-dfgviewer-sru-results ul').remove();
	$('.sru-results-active-indicator').remove();
	$('#tx-dfgviewer-sru-query').val('');
});


});
