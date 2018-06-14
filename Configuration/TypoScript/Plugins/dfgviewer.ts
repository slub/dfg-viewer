plugin.tx_dfgviewer_amd {
	pages = {$config.storagePid}
	templateFile = {$config.templateFileAmd}
}

plugin.tx_dfgviewer_gridpager {
	pages = {$config.storagePid}
	limit = 24
	pageStep = 10
	templateFile = {$config.templateFilePager}
}

plugin.tx_dfgviewer_newspapercalendar {
	pages = {$config.storagePid}
	targetPid = #
	templateFile = {$config.templateFileNewspaperCalendar}
}

plugin.tx_dfgviewer_newspaperyears {
	pages = {$config.storagePid}
	targetPid = #
	templateFile = {$config.templateFileNewspaperYears}
}

plugin.tx_dfgviewer_uri {
	pages = {$config.storagePid}
	templateFile = {$config.templateFileUri}
}

plugin.tx_dfgviewer_sru {
	pages = {$config.storagePid}
	templateFile = {$config.templateFileSru}
	targetPid.data = TSFE:page|uid
}
