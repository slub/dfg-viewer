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

		}
	}

}

# -------------------------------
# Diverses
# -------------------------------
