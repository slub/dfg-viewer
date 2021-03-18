###################################################
# Page header
###################################################

page = PAGE
page {
  includeCSS {
    style = EXT:dfgviewer/Resources/Public/Css/webStyles.css
  }
  includeJSFooterlibs {
    jQuery = EXT:dfgviewer/Resources/Public/Javascript/jQuery/jquery-3.5.1.min.js
    jQuery.forceOnTop = 1
    jqueryUiMouseSlider = EXT:dlf/Resources/Public/Javascript/jQueryUI/jquery-ui-mouse-slider-resizable-autocomplete.js
    dfgviewer = EXT:dfgviewer/Resources/Public/Javascript/webScripts.js
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
  includeJSFooterlibs {
    dfgviewer = EXT:dfgviewer/Resources/Public/Javascript/allScripts.js
  }
  meta {
    # the object view must be excluded from index
    robots = noindex,nofollow
  }
}
[global]
