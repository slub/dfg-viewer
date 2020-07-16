# don't use wrapInBaseClass as it breaks the layout of new DFG-Viewer 4.0
config.disableWrapInBaseClass=1

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
  userFunc = Kitodo\Dlf\Plugin\Metadata->main
  excludeOther = 0
  linkTitle = 1
  getTitle = 1
  showFull = 0
  rootline = 1
  separator = #
  templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/MobileMetadata.tmpl
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
  userFunc = Kitodo\Dlf\Plugin\Navigation->main
  pages = {$config.storagePid}
  pageStep = 10
  templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/NavigationPageControl.tmpl
}

lib.navigation_viewfunction = USER
lib.navigation_viewfunction {
  userFunc = Kitodo\Dlf\Plugin\Navigation->main
  pages = {$config.storagePid}
  pageStep = 10
  templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/NavigationViewFunction.tmpl
}
lib.navigation_viewfunction_deactivated = USER
lib.navigation_viewfunction_deactivated {
  userFunc = Kitodo\Dlf\Plugin\Navigation->main
  pages = {$config.storagePid}
  pageStep = 10
  templateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/NavigationViewFunctionDeactivated.tmpl
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
  placeholder = EXT:dfgviewer/Resources/Public/Images/GridPlaceHolder.jpg
  targetPid = #
  templateFile = {$config.templateFileGrid}
}

plugin.tx_dlf_tableofcontents {
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
    1.NO.stdWrap.ifEmpty.field = type
    1.NO.stdWrap.ifEmpty.append = TEXT
    1.NO.stdWrap.ifEmpty.append.fieldRequired = volume
    1.NO.stdWrap.ifEmpty.append.field = volume
    1.NO.stdWrap.ifEmpty.append.wrap = &nbsp;|
    1.NO.stdWrap.dataWrap = | <span class="pagination">{field:pagination}</span>
    1.NO.doNotLinkIt.field = doNotLinkIt
    1.NO.ATagTitle.field = title // type
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

plugin.tx_dlf_pdfdownloadtool {
  # this file does not exist
  toolTemplateFile = EXT:dfgviewer/Resources/Private/Templates/Plugins/Kitodo/toolboxPdf.tmpl
}

plugin.tx_dlf_fulltexttool {
  templateFile = {$config.templateFileToolFulltext}
  pages = {$config.storagePid}
}

plugin.tx_dlf_imagemanipulationtool {
  templateFile = {$config.templateFileToolImageManipulation}
}

plugin.tx_dlf_audioplayer {
  pages = {$config.storagePid}
  excludeOther = 0
  elementId = tx-dlf-audio
#  templateFile = {$config.templateFilePage}
}

[userFunc = user_dlf_docTypeCheck(newspaper, {$config.storagePid})] || [userFunc = user_dlf_docTypeCheck(ephemera, {$config.storagePid})]
page.10.variables {
  isNewspaper = TEXT
  isNewspaper.value = newspaper_anchor
}
[global]

[userFunc = user_dlf_docTypeCheck(year, {$config.storagePid})]
page.10.variables {
  isNewspaper = TEXT
  isNewspaper.value = newspaper_year
}
[global]

[userFunc = user_dlf_docTypeCheck(issue, {$config.storagePid})]
page.10.variables {
  isNewspaper = TEXT
  isNewspaper.value = newspaper_issue
}
[global]
