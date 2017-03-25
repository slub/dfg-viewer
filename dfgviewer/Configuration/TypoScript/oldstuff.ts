

lib.info = TEXT
lib.info {
	value = DFG-Viewer
	value.typolink.parameter = http://dfg-viewer.de/
	value.typolink.title = DFG-Viewer
	value.typolink.ATagParams = id="infos"
}

lib.langswitcher = HMENU
lib.langswitcher {
	special = language
	special.value = 1,0
	special.normalWhenNoLanguage = 1
	addQueryString = 1
	addQueryString.method = GET
	addQueryString.exclude = cHash
	1 = TMENU
	1.accessKey = 1
	1.noBlur = 1
	1.NO = 1
	1.NO.ATagParams = id="lang_en" || id="lang_de"
	1.NO.ATagTitle = Language: English || Sprache: Deutsch
	1.NO.allWrap = <li>|</li>
}

lib.gridswitcher = TEXT
lib.gridswitcher {
	value = Thumbnails
	value.typolink.parameter = #
	value.typolink.useCacheHash = 1
	value.typolink.addQueryString = 1
	value.typolink.addQueryString.method = GET
	value.typolink.addQueryString.exclude = cHash
}
[globalVar = GP:tx_dlf|pagegrid=1]
lib.gridswitcher.value.typolink.additionalParams = &tx_dlf[pagegrid]=0
lib.gridswitcher.value.typolink.title = Einzelbildansicht
lib.gridswitcher.value.typolink.ATagParams = class="gridoff"
[else]
lib.gridswitcher.value.typolink.additionalParams = &tx_dlf[pagegrid]=1
lib.gridswitcher.value.typolink.title = Thumbnail-Vorschau
lib.gridswitcher.value.typolink.ATagParams = class="gridon"
[global]


page.includeCSS.tooltipster = {$plugin.tx_dfgviewer.css_tooltipster}
page.includeCSS.tooltipster.media = screen,projection

page.includeJS.tooltipster = {$plugin.tx_dfgviewer.js_tooltipster}

page.2 = TEMPLATE
page.2.template = FILE
page.2.template.file = {$plugin.tx_dfgviewer.templateFile}
page.2.workOnSubpart = TEMPLATE

page.2.marks.AMD < plugin.tx_dfgviewer_amd
page.2.marks.INFOS < lib.info
page.2.marks.LANGSWITCHER < lib.langswitcher
page.2.marks.METADATA < plugin.tx_dlf_metadata
page.2.marks.URI < plugin.tx_dfgviewer_uri
page.2.marks.TOOLBOX < plugin.tx_dlf_toolbox
page.2.marks.AUDIO < plugin.tx_dlf_audioplayer
page.2.marks.GRIDSWITCHER < lib.gridswitcher
page.2.marks.SRU = TEXT

[globalVar = GP:tx_dlf|pagegrid=1]
page.2.marks.GUI < plugin.tx_dfgviewer_gridpager
page.2.marks.IMAGE < plugin.tx_dlf_pagegrid
page.2.marks.NAVIGATION = TEXT
[else]
page.2.marks.GUI < plugin.tx_dlf_navigation
page.2.marks.IMAGE < plugin.tx_dlf_pageview
page.2.marks.NAVIGATION < plugin.tx_dlf_toc
[global]

[userFunc = user_dlf_docTypeCheck(newspaper)]
page.2.template.file = {$plugin.tx_dfgviewer.templateFileNewspaper}
page.2.marks.IMAGE < plugin.tx_dfgviewer_newspaperyears
page.2.marks.GRIDSWITCHER >
page.2.marks.NAVIGATION >
page.2.marks.SRU < plugin.tx_dfgviewer_sru
page.2.marks.GUI = TEXT
[global]

[userFunc = user_dlf_docTypeCheck(year)]
page.2.template.file = {$plugin.tx_dfgviewer.templateFileNewspaper}
page.2.marks.IMAGE < plugin.tx_dfgviewer_newspapercalendar
page.2.marks.GRIDSWITCHER >
page.2.marks.NAVIGATION >
page.2.marks.SRU < plugin.tx_dfgviewer_sru
page.2.marks.GUI = TEXT
[global]

[userFunc = user_dlf_docTypeCheck(issue)]
page.2.marks.GUI < plugin.tx_dlf_navigation
page.2.marks.IMAGE < plugin.tx_dlf_pageview
page.2.marks.NAVIGATION < plugin.tx_dlf_toc
page.2.marks.SRU < plugin.tx_dfgviewer_sru
[global]
