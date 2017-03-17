###################################################
# Page header
###################################################

page = PAGE
page {

	includeCSS {
		style = EXT:dfgviewer/Resources/Public/Css/allStyles.css
	}

	includeJSlibs {
    # we include jquery by t3jquery on page.9 below
    plugins = EXT:dfgviewer/Resources/Public/Js/allScripts.js
  }

	meta {
				keywords.field = keywords
				description.field = description
				author.field = author
				robots = all
				viewport = width=device-width, initial-scale=1
		}
}

# include t3jquery
includeLibs.t3jquery = EXT:t3jquery/class.tx_t3jquery.php
page.9 = USER_INT
page.9.userFunc = tx_t3jquery->addJqJS


[globalVar = TSFE:id = {$config.kitodoPageView}]
page {
  meta {
    # the object view must be excluded from index
    robots = noindex,nofollow
  }
}
[global]
