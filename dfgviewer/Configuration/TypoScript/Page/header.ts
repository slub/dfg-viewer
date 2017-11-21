###################################################
# Page header
###################################################

page = PAGE
page {
	includeCSS {
		style = EXT:dfgviewer/Resources/Public/Css/webStyles.css
	}
	# jQuery 3.x doesn't work with Kitodo.Presentation!
	# Kitodo.Presentation Pageview Plugin requires jQuery to be loaded in head
	includeJSlibs {
			jQuery = //code.jquery.com/jquery-2.2.4.min.js
			jQuery {
					external = 1
					disableCompression = 1
					# will work as of TYPO3 7.6
					integrity = sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=
					excludeFromConcatenation = 1
					allWrap = |<script type="text/javascript">if (typeof jQuery == 'undefined') { document.write(unescape("%3Cscript src='typo3conf/ext/slub_web_ldp/Resources/Public/Javascript/jquery-2.2.4.min.js' type='text/javascript'%3E%3C/script%3E"));}</script>
			}
	}
	includeJSFooterlibs {
		jqueryUiMouseSlider = EXT:dfgviewer/Resources/Public/Js/jquery-ui-mouse-slider.js
		dfgviewer = EXT:dfgviewer/Resources/Public/Js/webScripts.js
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
	includeCSS {
		style = EXT:dfgviewer/Resources/Public/Css/allStyles.css
	}
	includeJSlibs {
		# we include jquery by t3jquery on by ViewHelpers
		plugins = EXT:dfgviewer/Resources/Public/Js/allScripts.js
	}
	meta {
		# the object view must be excluded from index
		robots = noindex,nofollow
	}
}
[global]
