###################################################
# Page header
###################################################

page = PAGE
page {
	includeCSS {
		style = EXT:dfgviewer/Resources/Public/Css/allStyles.css
	}
	includeJSlibs {
		# we include jquery by t3jquery on by ViewHelpers
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

[globalVar = TSFE:id = {$config.kitodoPageView}]
page {
	meta {
		# the object view must be excluded from index
		robots = noindex,nofollow
	}
}
[global]
