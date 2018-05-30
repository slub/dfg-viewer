# ----------------------------------------
# Navigation
# 1. + 2. Level
# ----------------------------------------
lib.menu {
  main = HMENU
  main {
    excludeUidList =
    maxItems = 8
    entryLevel = 0
    1 = TMENU
    1 {
      expAll = 1
      noBlur = 1
      accessKey = 1
      wrap (
         <ul class="main-nav">|</ul>
      )
      NO {
        #wrapItemAndSub = <li class="submenu">|</li>
        allWrap = <li>|</li>
        ATagParams =
        ATagTitle.field = description // title
      }

      ACT = 1
      ACT {
        allWrap = <li class="active">|</li>
        ATagTitle.field = description // title
      }

      CUR = 1
      CUR {
        allWrap = <li class="active">|</li>
        ATagParams = class=current
        ATagTitle.field = description // title
      }

      IFSUB = 1
      IFSUB {
        wrapItemAndSub = <li class="submenu">|</li>
        ATagTitle.field = description // title
      }

      ACTIFSUB = 1
      ACTIFSUB {
        wrapItemAndSub = <li class="submenu active">|</li>
        ATagTitle.field = description // title
      }

      CURIFSUB = 1
      CURIFSUB {
        wrapItemAndSub = <li class="submenu active">|</li>
        ATagParams = class="current"
        ATagTitle.field = description // title
      }
    }

    2 = TMENU
    2 {
      noBlur = 1
      accessKey = 1
      wrap = <ul>|</ul>

      NO = 1
      NO {
        allWrap = <li>|</li>
        ATagTitle.field = description // title
      }
      CUR = 1
      CUR {
        allWrap = <li>|</li>
        ATagParams = class="current"
        ATagTitle.field = description // title
      }
    }
  }
  secondary = COA
  secondary {
    20 = HMENU
    20 {
        special = directory
        special.value = {$config.headNavPid}
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
  viewernav = COA
  viewernav {
    20 = HMENU
    20 {
        special = list
        special.value = {$config.viewerNavPids}
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
    wrap = <ul class="viewer-nav">|</ul>
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
