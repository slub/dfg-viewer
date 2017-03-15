###################################################
# Page header
###################################################

page = PAGE
page {

	includeCSS {
		style = EXT:dfgviewer/Resources/Public/Css/allStyle.css
	}

	includeJSlibs {
    # we include jquery by t3jquery on page.9 below
    plugins = EXT:dfgviewer/Resources/Public/Js/allScripts.js
  }

}

# include t3jquery
includeLibs.t3jquery = EXT:t3jquery/class.tx_t3jquery.php
page.9 = USER_INT
page.9.userFunc = tx_t3jquery->addJqJS
