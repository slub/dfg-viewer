plugin.tx_dlf.useragent = {$config.useragent}

# map GET parameter set[mets] --> tx_dlf[id]
[globalString = GP:set|mets = http*]
plugin.tx_dlf._DEFAULT_PI_VARS.id.stdWrap.data = GP:set|mets
[global]
# map GET parameter set[image] --> tx_dlf[page]
[globalString = GP:set|image != /^$/]
plugin.tx_dlf._DEFAULT_PI_VARS.page.stdWrap.data = GP:set|image
[global]
# map GET parameter set[double] --> tx_dlf[double]
[globalVar = GP:set|double > 0]
plugin.tx_dlf._DEFAULT_PI_VARS.double.stdWrap.data = GP:set|double
[global]

lib.metadata = USER
lib.metadata {
	includeLibs = typo3conf/ext/dlf/plugins/metadata/class.tx_dlf_metadata.php
	userFunc = tx_dlf_metadata->main
	excludeOther = 0
	linkTitle = 1
	getTitle = 1
	showFull = 0
	rootline = 1
	separator = #
	templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/mobile-metadata.tmpl
}

plugin.tx_dlf_metadata {
	pages = {$config.storagePid}
	excludeOther = 0
	linkTitle = 0
	getTitle = 0
	showFull = 1
	rootline = 1
	separator = #
	templateFile = {$config.templateFileMeta}
}

lib.navigation_pagecontrol = USER
lib.navigation_pagecontrol {
	includeLibs = typo3conf/ext/dlf/plugins/navigation/class.tx_dlf_navigation.php
	userFunc = tx_dlf_navigation->main
	pages = {$config.storagePid}
	pageStep = 10
	templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/navigation-pagecontrol.tmpl
}

lib.navigation_viewfunction = USER
lib.navigation_viewfunction {
	includeLibs = typo3conf/ext/dlf/plugins/navigation/class.tx_dlf_navigation.php
	userFunc = tx_dlf_navigation->main
	pages = {$config.storagePid}
	pageStep = 10
	templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/navigation-viewfunction.tmpl
}
lib.navigation_viewfunction_deactivated = USER
lib.navigation_viewfunction_deactivated {
	includeLibs = typo3conf/ext/dlf/plugins/navigation/class.tx_dlf_navigation.php
	userFunc = tx_dlf_navigation->main
	pages = {$config.storagePid}
	pageStep = 10
	templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/navigation-viewfunction-deactivated.tmpl
}

plugin.tx_dlf_navigation {
	pages = {$config.storagePid}
	pageStep = 5
	templateFile = {$config.templateFileNav}
}

plugin.tx_dlf_pageview {
	pages = {$config.storagePid}
	excludeOther = 0
	features =
	elementId = tx-dfgviewer-map
	templateFile = {$config.templateFilePage}
}

plugin.tx_dlf_pagegrid {
	pages = {$config.storagePid}
	limit = 24
	placeholder = EXT:dfgviewer/res/images/placeholder.jpg
	targetPid = #
	templateFile = {$config.templateFileGrid}
}

plugin.tx_dlf_toc {
	pages = {$config.storagePid}
	excludeOther = 0
	targetPid.data = TSFE:page|uid
	templateFile = {$config.templateFileToc}
	menuConf {
		expAll = 0
		1 = TMENU
		1.noBlur = 1
		1.wrap = <ul class="toc">|</ul>
		1.NO = 1
		1.NO.stdWrap.crop = 55 | &nbsp;... | 1
		1.NO.stdWrap.ifEmpty.field = tyoe
		1.NO.stdWrap.ifEmpty.append = TEXT
		1.NO.stdWrap.ifEmpty.append.fieldRequired = volume
		1.NO.stdWrap.ifEmpty.append.field = volume
		1.NO.stdWrap.ifEmpty.append.wrap = &nbsp;|
		1.NO.stdWrap.dataWrap = | <span class="pagination">{field:pagination}</span>
		1.NO.doNotLinkIt.field = doNotLinkIt
		1.NO.ATagTitle.field = title
		1.NO.allWrap = <span class="a">|</span>
		1.NO.allWrap.fieldRequired = doNotLinkIt
		1.NO.wrapItemAndSub = <li>|</li>
		1.IFSUB < .1.NO
		1.IFSUB.wrapItemAndSub = <li class="submenu">|</li>
		1.CUR < .1.NO
		1.CUR.wrapItemAndSub = <li class="current">|</li>
		1.CURIFSUB < .1.NO
		1.CURIFSUB.wrapItemAndSub = <li class="current submenu">|</li>
		1.ACT < .1.NO
		1.ACT.wrapItemAndSub = <li class="active">|</li>
		1.ACTIFSUB < .1.NO
		1.ACTIFSUB.wrapItemAndSub = <li class="active submenu">|</li>
		2 < .1
		2.wrap = <ul>|</ul>
		3 < .2
		4 < .3
		5 < .4
		6 < .5
		7 < .6
	}
}
plugin.tx_dlf_toolbox {
	pages = {$config.storagePid}
	tools = tx_dlf_toolsPdf,tx_dlf_toolsFulltext,tx_dlf_toolsImagemanipulation
	templateFile = {$config.templateFileToolbox}
}

plugin.tx_dlf_toolsPdf {
	toolTemplateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/toolboxPdf.tmpl
}

lib.tools.toolsFulltext = USER
lib.tools.toolsFulltext {
	includeLibs = typo3conf/ext/dlf/plugins/toolbox/class.tx_dlf_toolbox.php
	userFunc = tx_dlf_toolbox->main
	pages = {$config.storagePid}
	tools = tx_dlf_toolsFulltext
	templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/toolbox.tmpl
}
plugin.tx_dlf_toolsFulltext {
	toolTemplateFile = {$config.templateFileToolFulltext}
}

lib.tools.toolsImagemanipulation = USER
lib.tools.toolsImagemanipulation {
	includeLibs = typo3conf/ext/dlf/plugins/toolbox/class.tx_dlf_toolbox.php
	userFunc = tx_dlf_toolbox->main
	pages = {$config.storagePid}
	tools = tx_dlf_toolsImagemanipulation
	templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/toolbox.tmpl
}

plugin.tx_dlf_toolsImagemanipulation {
	toolTemplateFile = {$config.templateFileToolImageManipulation}
}


plugin.tx_dlf_audioplayer {
	pages = {$config.storagePid}
	excludeOther = 0
	elementId = tx-dlf-audio
#	templateFile = {$config.templateFilePage}
}


[userFunc = user_dlf_docTypeCheck(newspaper)]
page.10.variables {
	isNewspaper = TEXT
	isNewspaper.value = newspaper_anchor
}
[global]

[userFunc = user_dlf_docTypeCheck(year)]
page.10.variables {
	isNewspaper = TEXT
	isNewspaper.value = newspaper_year
}
[global]

[userFunc = user_dlf_docTypeCheck(issue)]
page.10.variables {
	isNewspaper = TEXT
	isNewspaper.value = newspaper_issue
}
[global]
