
##
#  Inicializace FB tak, aby se zobrazoval na vsech strankach
fbPageInit = ->
  try
    FB.XFBML.parse()
  catch err


##
#  Inicializace kontaktniho formulare
contactFormInit = (page) ->
  $ '#contactform .anti_spam_holder', page
    .hide()
    .find '#anti_spam_check'
    .val 'Anti-spam check'


##
#  Callback po nacteni stranky pomoci jquery mobile
pageShowCallback = ->
  page = $.mobile.activePage
  pageId = page.attr 'id'

  pageActions =
    'page-search': ->
      currentPage = new SearchPage page, activityTracker
    'page-contact': ->
      contactFormInit page
    'page-index': ->
      c = $('#cardname', page)
      c.attr('type', 'search')
      c.focus()
    'page-test': ->
      contactFormInit page

  fbPageInit()

  if pageActions[pageId]?
    pageActions[pageId]()

  return


# ----- END OF DEFINITIONS -----


# inicializace trackovani
# POZOR Nejaka logika je i v init.js, toto musi byt volano az pote!
window.activityTracker = new Tracking()

#  Inicializace zobrazeni stranky
$(document).bind 'pageshow', pageShowCallback
