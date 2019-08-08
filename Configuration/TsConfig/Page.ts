<INCLUDE_TYPOSCRIPT: source="FILE:EXT:dfgviewer/Configuration/TsConfig/Rte.ts">

# PAGE DEFAULT PERMISSIONS
TCEMAIN.permissions {

  # Configure default permission for new created pages.
  # do anything (default):
  #user = 31

  # do anything (normally "delete" is disabled)
  #group = 31

  # (normally everybody can do nothing)
  #everybody =

  # user: default user
  # userid = 6

  # group _Users
  # group: schrank-zwei == 30
  #groupid = 30
}

TCEFORM.tt_content {

  // Full screen for bodytext (tt_content)
  bodytext.RTEfullScreenWidth= 100%

  header_layout {
      disabled = 0
      altLabels.0 = Standard
      altLabels.1 = H1 Überschrift
      altLabels.2 = H2 Überschrift
      altLabels.3 = H3 Überschrift
      altLabels.4 = H4 Überschrift
      altLabels.5 = H5 Überschrift
      removeItems = 0,1,2,5
    }

  section_frame {
    removeItems = 1,5,6,10,11,12,20,21
    addItems.101 = DFG-Viewer: Mission
    addItems.102 = DFG-Viewer: Demonstrator
    addItems.103 = DFG-Viewer: Metadaten
    addItems.104 = DFG-Viewer: Mailingliste
  }

}
