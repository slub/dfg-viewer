# -------------------------------
# PAGE SETUP
# -------------------------------
page {
  typeNum = 0
  bodyTag = <body class="dfgviewer">

  adminPanelStyles = 0
  shortcutIcon = EXT:dfgviewer/Resources/Public/Images/favicon.png

	10 = FLUIDTEMPLATE
	10 {
	  file = EXT:dfgviewer/Resources/Private/Templates/Main.html
	  layoutRootPath = EXT:dfgviewer/Resources/Private/Layouts/
	  partialRootPath = EXT:dfgviewer/Resources/Private/Partials/

		variables {

			pageTitle = TEXT
			pageTitle.data = page:title

			productName = TEXT
			productName.value = {$config.productName}

			pageHideInMenu = TEXT
			pageHideInMenu.data = page:nav_hide

			content < styles.content.get
			contentRight < styles.content.getRight

			rootPageId = TEXT
			rootPageId.value = {$config.rootPid}

      kitodoPageView = TEXT
			kitodoPageView.value = {$config.kitodoPageView}

      piwik_hostname = TEXT
			piwik_hostname.value = {$config.piwik_hostname}

      piwik_idsite = TEXT
			piwik_idsite.value = {$config.piwik_idsite}

      piwik_domains = TEXT
			piwik_domains.value = {$config.piwik_domains}

      gp-page = TEXT
      gp-page.data = GP:tx_dlf|page
      gp-page.ifEmpty = 1

      gp-double = TEXT
      gp-double.data = GP:tx_dlf|double

      gp-id = TEXT
      gp-id.data = GP:tx_dlf|id

      gp-pagegrid = TEXT
      gp-pagegrid.data = GP:tx_dlf|pagegrid

		}
	}

}

# -------------------------------
# Diverses
# -------------------------------
[globalVar = TSFE:id = {$config.kitodoPageView}]
page {
  10 {
    file = EXT:dfgviewer/Resources/Private/Templates/Kitodo.html
  }
}
[global]
