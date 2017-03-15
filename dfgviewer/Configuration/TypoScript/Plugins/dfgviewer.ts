plugin.tx_dfgviewer_amd {
	pages = {$plugin.tx_dfgviewer.storagePid}
	templateFile = {$plugin.tx_dfgviewer.templateFileAmd}
}

plugin.tx_dfgviewer_gridpager {
	pages = {$plugin.tx_dfgviewer.storagePid}
	limit = 24
	pageStep = 5
	templateFile = {$plugin.tx_dfgviewer.templateFilePager}
}

plugin.tx_dfgviewer_newspapercalendar {
	pages = {$plugin.tx_dfgviewer.storagePid}
	targetPid = #
	templateFile = {$plugin.tx_dfgviewer.templateFileNewspaperCalendar}
}

plugin.tx_dfgviewer_newspaperyears {
	pages = {$plugin.tx_dfgviewer.storagePid}
	targetPid = #
	templateFile = {$plugin.tx_dfgviewer.templateFileNewspaperYears}
}

plugin.tx_dfgviewer_uri {
	pages = {$plugin.tx_dfgviewer.storagePid}
	templateFile = {$plugin.tx_dfgviewer.templateFileUri}
}

plugin.tx_dfgviewer_sru {
	pages = {$plugin.tx_dfgviewer.storagePid}
	templateFile = {$plugin.tx_dfgviewer.templateFileSru}
	targetPid.data = TSFE:page|uid
}
