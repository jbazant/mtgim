
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
# Standardni obsluha vyhledavaciho formulare
searchFormCallback = (e) ->
  e.preventDefault()
  cardname = $('#cardname', page).val()
  $.mobile.changePage $(this).attr('action') + '#find-card-' + encodeURIComponent(cardname)
  return


##
#  Callback po nacteni stranky pomoci jquery mobile
#
#  Zajistuje obsluhu vyhledavaciho formulare a inicializaci jednotlivych stranek
pageShowCallback = ->
  page = $.mobile.activePage
  pageId = page.attr 'id'

  if pageId != 'page-index'
    $('#searchform', page).on 'submit', pageShowCallback

  pageActions =
    'page-search': ->
      currentPage = new SearchPage page, activityTracker
    'page-contact': ->
      contactFormInit page
    'page-index': ->
      c = $('#cardname', page).focus()
    'page-test': ->
      contactFormInit page

  fbPageInit()

  if pageActions[pageId]?
    pageActions[pageId]()

  return


##
#  Uprava nastaveni jqm
mobileInitCallback = ->
  $.mobile.ajaxFormsEnabled = false;
  $.mobile.hashListeningEnabled = false;
  #$.mobile.pushStateEnabled = false;
  return

