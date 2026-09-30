/**
 * Copyright notice
 *
 * (c) Saxon State and University Library Dresden <typo3@slub-dresden.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

$(document).ready(function () {

    $("#tx-dfgviewer-sru-form").submit(function (event) {

        // Stop form from submitting normally
        event.preventDefault();

        $('#tx-dfgviewer-sru-results-loading').show();
        $('#tx-dfgviewer-sru-results-clearing').hide();

        // Send the data using post
        $.post(
            "/",
            {
                middleware: "dfgviewer/sru",
                q: $("input[name='tx_dlf[query]']").val(),
                L: $("input[name='tx_dfgviewer[L]']").val(),
                id: $("input[name='tx_dfgviewer[id]']").val(),
                sru: $("input[name='tx_dfgviewer[sru]']").val(),
                action: $("input[name='tx_dfgviewer[action]']").val(),
            },
            function (data) {
                let resultCount = 0;
                let resultList = $('<div class="sru-results-active-indicator"></div><ul>');

                if (data.error) {
                    $('<li/>', {
                        class: "noresult",
                        text: $('#tx-dfgviewer-sru-label-noresult').text()
                    }).appendTo(resultList);
                } else {
                    for (let i = 0; i < data.length; i++) {
                        let linkCurrent = $(location).attr('href');
                        let linkBase = linkCurrent.substring(0, linkCurrent.indexOf('?'));
                        let linkParams = linkCurrent.substring(linkBase.length + 1, linkCurrent.length);
                        let linkId = linkParams.match(/id=(\d)*/g);

                        if (linkId) {
                            linkParams = linkId + '&';
                        } else {
                            linkParams = '&';
                        }

                        let linkNew = linkBase + '?' + (linkParams
                            + 'tx_dlf[id]=' + data[i].link
                            + '&tx_dlf[origimage]=' + data[i].origImage
                            + '&tx_dlf[highlight]=' + encodeURIComponent(data[i].highlight)
                            + '&tx_dlf[page]=' + (data[i].page));

                        if (data[i].previewImage) {
                            let preview = $('<span/>', {
                                class: 'sru-preview'
                            }).append($('<img/>', {
                                src: data[i].previewImage
                            }));
                            $('<li/>').append(
                                $('<a/>', {
                                    href: linkNew
                                })
                                    .append(preview)
                            ).appendTo(resultList);
                            resultCount++;
                        }
                        if (data[i].previewText && data[i].previewText.length > 0) {
                            let textSnippet = $('<span/>', {
                                class: 'sru-textsnippet'
                            });
                            data[i].previewText.forEach(function (fragment) {
                                if (fragment.highlight) {
                                    textSnippet.append($('<span/>', {
                                        class: 'highlight',
                                        text: fragment.text
                                    }));
                                } else {
                                    textSnippet.append(document.createTextNode(fragment.text));
                                }
                            });
                            $('<li/>').append(
                                $('<a/>', {
                                    href: linkNew
                                })
                                    .append(textSnippet)
                            ).appendTo(resultList);
                            resultCount++;
                        }
                    }

                    if (resultCount === 0) {
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
            .done(function (data) {
                $('#tx-dfgviewer-sru-results-loading').hide();
                $('#tx-dfgviewer-sru-results-clearing').show();
            });
    });

// clearing button
    $('#tx-dfgviewer-sru-results-clearing').click(function () {
        $('#tx-dfgviewer-sru-results ul').remove();
        $('.sru-results-active-indicator').remove();
        $('#tx-dfgviewer-sru-query').val('');
    });

});
