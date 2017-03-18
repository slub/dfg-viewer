# ----------------------------------------
# Header Secondary Navigation
# ----------------------------------------

lib.menu {
  secondary = COA
	secondary {
    20 = HMENU
	  20 {
  	    special = directory
  	    special.value = 143
        #{$config.headNavPid}
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
}
