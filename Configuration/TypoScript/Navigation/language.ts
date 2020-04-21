# ----------------------------------------
# Header Language Navigation
# ----------------------------------------

lib.menu {
  language = COA
  language {
    10 = HMENU
    10 {
      special = language
      # show only Default and English
      special.value = 0,1

      addQueryString = 1
      addQueryString.method = GET
      addQueryString.exclude = cHash

      1 = TMENU
      1 {
        wrap = <ul class="language-nav">|</ul>
        noBlur = 1
        NO = 1
        NO {
          linkWrap=<li>|</li>
          stdWrap.override = DE || EN
          ATagTitle.override = Sprache: Deutsch || Language: English
        }

        # active language
        ACT < .NO
        ACT {
          #doNotLinkIt = 1
          ATagParams=class="actlang"
        }

        # NO + Translation doesn't exist
        USERDEF1 < .NO
        # USERDEF1.doNotLinkIt = 1

        # ACT + Translation doesn't exist
        USERDEF2 < .ACT
        # USERDEF2.doNotLinkIt = 1
      }
    }
  }
}
