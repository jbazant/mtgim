
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
  cardname = $('#cardname', this).val()
  $.mobile.changePage jsParams.baseUrl + '/index/search/card/' + encodeURIComponent cardname
  return


##
#  Callback po nacteni stranky pomoci jquery mobile
#
#  Zajistuje obsluhu vyhledavaciho formulare a inicializaci jednotlivych stranek
window.pageShowCallback = ->
  page = $.mobile.activePage
  pageId = page.attr 'id'

  if pageId != 'page-search'
    $('#searchform', page).on 'submit', searchFormCallback

  pageActions =
    'page-search': ->
      currentPage = new SearchPage page, activityTracker
    'page-contact': ->
      contactFormInit page
    'page-index': ->
      c = $('#cardname', page).focus()
      $('#cookies-agree', page).on 'click', ->
        window.activityTracker.trackEvent 'IndexPage', 'cookies-agree', 'click'
    'page-test': ->
      contactFormInit page

  fbPageInit()

  if pageActions[pageId]?
    pageActions[pageId]()

  return
