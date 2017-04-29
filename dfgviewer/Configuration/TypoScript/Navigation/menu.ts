# ----------------------------------------
# Navigation
# 1. + 2. Level
# ----------------------------------------
lib.menu {
	main = HMENU
	main {
		excludeUidList =
		maxItems = 6
		entryLevel = 0
	  1 = TMENU
	  1 {
	    expAll = 1
	    wrap (
	       <nav class="mainNav">
	           <div class="navContainer">
	              <div class="sizeContainer">
	                 <ul>|</ul>
	                 <div class="clearfix"></div>
	              </div>
	           </div>
	       </nav>
	    )
	    noBlur = 1
	    accessKey = 1
	    NO {
	      wrapItemAndSub = <li>|<div class="clearfix"></div></li>

	      ATagParams =
	      stdWrap.wrap =
	      stdWrap = upper
	      ATagTitle.field = description // title
	    }

	    #ACT = 1
	    ACT < .1.NO
	    ACT {
	      #wrapItemAndSub = <li class="act">|</li>
	      stdWrap = upper
	      ATagParams =
	      stdWrap.wrap =
	      #after = <div class="navact"></div>
	      ATagTitle.field = description // title
	        }

	    #CURIFSUB < .1.NO
	    #CURIFSUB.allWrap = <li id="curifsub">|

	    #ACTIFSUB < .1.CURIFSUB

	    }
	  2 = TMENU
	  2 {
	    noBlur = 1
	    accessKey = 1
	    wrap = <ul>|</ul><div class="clearfix"></div>

	    NO = 1
	    NO.ATagTitle.field = description // title
	    NO.allWrap = <li>|</li>
	  }
	}
  secondary = COA
	secondary {
    20 = HMENU
	  20 {
  	    special = directory
  	    special.value = {$config.headNavPid}
        #special.value.current = 1
  	    1 = TMENU
  	    1 {
  	      expAll = 1
  	      noBlur = 1
  	      NO = 1
  	      NO {
  	        wrapItemAndSub = <li>|</li>
  	        ATagTitle.field = description // title
  	      }
  	    }
	  }
	  wrap = <ul class="secondary-nav">|</ul>
  }
	# ----------------------------------------
	# Breadcrumb Navigation (on Subsites only)
	# ----------------------------------------
	breadCrumbNavigation = HMENU
	breadCrumbNavigation {
	  special = rootline
	  special.range = 1|-1
	  1 = TMENU
	  1 {
	    expAll = 1
	    noBlur = 1
      accessKey = 1
	    wrap = <ol class="breadcrumb">|</ol>
	    NO = 1
	    NO {
	      wrapItemAndSub = <li>|</li>
        ATagTitle.field = title
	    }

	    CUR = 1
	    CUR < .NO
	    #CUR.doNotLinkIt = 1
	  }
	}
}
