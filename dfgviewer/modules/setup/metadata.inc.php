<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Sebastian Meyer <sebastian.meyer@slub-dresden.de>
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

// Define metadata elements.
// @see http://dfg-viewer.de/en/profile-of-the-metadata/
$metadata = array (
	'settlement' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:msIdentifier/teihdr:settlement',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.required = 1\nall.noTrimWrap = |<h2>|,</h2> |",
		'is_listed' => 1,
	),
	'repository' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:msIdentifier/teihdr:repository',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.required = 1\nall.noTrimWrap = |<h2>|,</h2> |",
		'is_listed' => 1,
	),
	'type' => array (
		'hidden' => 1,
		'format' => array (),
		'default_value' => '',
		'wrap' => '',
		'is_listed' => 1,
	),
	'author' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:head/teihdr:title/teihdr:persName[@role="author"]',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.ifEmpty.field = parentAuthor\nvalue.required = 1\nvalue.noTrimWrap = ||; |\nall.substring = 0,-2\nall.noTrimWrap = |<span class=\"author\">|:</span> |",
		'is_listed' => 1,
	),
	'parentAuthor' => array (
		'hidden' => 1,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:relatedItem[@type="host"]/mods:name[./mods:role/mods:roleTerm[@authority="marcrelator"][@type="code"]="aut"]/mods:displayForm',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => '',
		'is_listed' => 0,
	),
	'shelfmark' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:msIdentifier/teihdr:idno',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.required = 1\nall.noTrimWrap = |<h2>|,</h2> |",
		'is_listed' => 1,
	),
	'title' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => 'concat(./mods:titleInfo[not(@type="alternative")]/mods:nonSort," ",./mods:titleInfo[not(@type="alternative")]/mods:title," ",./mods:titleInfo[not(@type="alternative")]/mods:partNumber," ",./mods:titleInfo[not(@type="alternative")]/mods:partName)',
				'xpath_sorting' => '',
			),
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:head/teihdr:title',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.ifEmpty.field = parentTitle\nvalue.ifEmpty.wrap = [|]\nvalue.required = 1\nall.noTrimWrap = |<h2>|</h2> |",
		'is_listed' => 1,
	),
	'parentTitle' => array (
		'hidden' => 1,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => 'concat(./mods:relatedItem[@type="host"]/mods:titleInfo[not(@type="alternative")]/mods:nonSort," ",./mods:relatedItem[@type="host"]/mods:titleInfo[not(@type="alternative")]/mods:title)',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => '',
		'is_listed' => 0,
	),
	'volume' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:part/mods:detail/mods:number',
				'xpath_sorting' => './mods:part[@type="host"]/@order',
			),
		),
		'default_value' => '',
		'wrap' => "key.noTrimWrap = || |\nvalue.if.value.field = type\nvalue.if.equals = volume\nvalue.required = 1\nall.noTrimWrap = |<span class=\"volume\">|</span> |",
		'is_listed' => 1,
	),
	'issue' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:part/mods:detail/mods:number',
				'xpath_sorting' => './mods:part[@type="host"]/@order',
			),
		),
		'default_value' => '',
		'wrap' => "key.noTrimWrap = || |\nvalue.if.value.field = type\nvalue.if.equals = issue\nvalue.required = 1\nall.noTrimWrap = |<span class=\"volume\">|</span> |",
		'is_listed' => 1,
	),
	'material' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:physDesc/teihdr:objectDesc/teihdr:supportDesc/teihdr:support',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.required = 1\nall.noTrimWrap = |<span class=\"date\">|,</span> |",
		'is_listed' => 1,
	),
	'leavesCount' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:physDesc/teihdr:objectDesc/teihdr:supportDesc/teihdr:extent/teihdr:measure[@type="leavesCount"]',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.required = 1\nall.noTrimWrap = |<span class=\"date\">|,</span> |",
		'is_listed' => 1,
	),
	'format' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => 'concat(./teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:physDesc/teihdr:objectDesc/teihdr:supportDesc/teihdr:extent/teihdr:dimensions[@type="leaves"]/teihdr:width,"x",./teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:physDesc/teihdr:objectDesc/teihdr:supportDesc/teihdr:extent/teihdr:dimensions[@type="leaves"]/teihdr:height,./teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:physDesc/teihdr:objectDesc/teihdr:supportDesc/teihdr:extent/teihdr:dimensions[@type="leaves"]/@unit)',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.if.value.field = format\nvalue.if.equals = x\nvalue.if.negate = 1\nvalue.required = 1\nall.noTrimWrap = |<span class=\"date\">|,</span> |",
		'is_listed' => 1,
	),
	'place' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:history/teihdr:origin/teihdr:origPlace',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.ifEmpty.field = parentPlace\nvalue.required = 1\nvalue.noTrimWrap = ||, |\nall.substring = 0,-2\nall.noTrimWrap = |<span class=\"date\">|</span> |",
		'is_listed' => 1,
	),
	'parentPlace' => array (
		'hidden' => 1,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:relatedItem[@type="host"]/mods:originInfo[@eventType="production" or @eventType="publication" or not(./mods:edition="[Electronic ed.]")]/mods:place/mods:placeTerm',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => '',
		'is_listed' => 0,
	),
	'publicationRun' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => 'concat(./mods:originInfo[@eventType="production" or @eventType="publication" or not(./mods:edition="[Electronic ed.]")]/mods:dateIssued[@point="start"]," - ",./mods:originInfo[@eventType="production" or @eventType="publication" or not(./mods:edition="[Electronic ed.]")]/mods:dateIssued[@point="end"])',
				'xpath_sorting' => '',
			),
			array (
				'encoded' => 2,
				'xpath' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:history/teihdr:origin/teihdr:origDate',
				'xpath_sorting' => './teihdr:fileDesc/teihdr:sourceDesc/teihdr:msDesc/teihdr:history/teihdr:origin/teihdr:origDate/@when',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = <span style=\"display:none;\">|: </span>\nvalue.replacement.1.search = /^-$/\nvalue.replacement.1.replace.field = year // parentYear\nvalue.replacement.1.useRegExp = 1\nvalue.replacement.2.search = /([0-9]{4})-([0-1]?[0-9])-([0-3]?[0-9])/\nvalue.replacement.2.replace = $3.$2.$1\nvalue.replacement.2.useRegExp = 1\nvalue.required = 1\nvalue.noTrimWrap = ||, |\nall.substring = 0,-2\nall.noTrimWrap = |<span class=\"date\">|</span> |",
		'is_listed' => 1,
	),
	'year' => array (
		'hidden' => 1,
		'format' => array (),
		'default_value' => '',
		'wrap' => '',
		'is_listed' => 0,
	),
	'parentYear' => array (
		'hidden' => 1,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:relatedItem[@type="host"]/mods:originInfo[@eventType="production" or @eventType="publication" or not(./mods:edition="[Electronic ed.]")]/mods:dateIssued[@keyDate="yes"]',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => '',
		'is_listed' => 0,
	),
	'vd16' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:identifier[@type="vd16"]',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = |&nbsp;\nvalue.setContentToCurrent = 1\nvalue.required = 1\nvalue.typolink.parameter.current = 1\nvalue.typolink.parameter.rawUrlEncode = 1\nvalue.typolink.parameter.prepend = TEXT\nvalue.typolink.parameter.prepend.value = http://gateway-bayern.bib-bvb.de/aleph-cgi/bvb_suche?sid=VD16&find_code_1=WVD&find_request_1=\nall.noTrimWrap = |<span class=\"catid\">[|]</span> |",
		'is_listed' => 1,
	),
	'vd17' => array (
		'hidden' => 0,
		'format' => array (
			array (
				'encoded' => 1,
				'xpath' => './mods:identifier[@type="vd17"]',
				'xpath_sorting' => '',
			),
		),
		'default_value' => '',
		'wrap' => "key.wrap = |&nbsp;\nvalue.setContentToCurrent = 1\nvalue.required = 1\nvalue.typolink.parameter.current = 1\nvalue.typolink.parameter.rawUrlEncode = 1\nvalue.typolink.parameter.prepend = TEXT\nvalue.typolink.parameter.prepend.value = http://gso.gbv.de/xslt/DB=1.28/SET=1/TTL=1/CMD?ACT=SRCHA&IKT=8002&TRM=\nall.noTrimWrap = |<span class=\"catid\">[|]</span> |",
		'is_listed' => 1,
	)
);
