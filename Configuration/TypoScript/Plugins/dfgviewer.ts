plugin.tx_dfgviewer_gridpager {
  userFunc = Slub\Dfgviewer\Plugins\GridPager->main
  pages = {$config.storagePid}
  limit = 24
  pageStep = 10
  templateFile = {$config.templateFilePager}
}

plugin.tx_dfgviewer_newspapercalendar {
  userFunc = Slub\Dfgviewer\Plugins\NewspaperCalendar->main
  pages = {$config.storagePid}
  targetPid = #
  templateFile = {$config.templateFileNewspaperCalendar}
}

plugin.tx_dfgviewer_newspaperyears {
  userFunc = Slub\Dfgviewer\Plugins\NewspaperYears->main
  pages = {$config.storagePid}
  targetPid = #
  templateFile = {$config.templateFileNewspaperYears}
}

plugin.tx_dfgviewer_uri {
  userFunc = Slub\Dfgviewer\Plugins\Uri->main
  pages = {$config.storagePid}
  templateFile = {$config.templateFileUri}
}

plugin.tx_dfgviewer_sru {
  userFunc = Slub\Dfgviewer\Plugins\Sru\Sru->main
  pages = {$config.storagePid}
  templateFile = {$config.templateFileSru}
  targetPid.data = TSFE:page|uid
}
