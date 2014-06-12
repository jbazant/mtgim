

##
#  Inicializace FB tak, aby se zobrazoval na vsech strankach
##
fbPageInit = ->
  try
    FB.XFBML.parse()
  catch err

  return


##
#  Inicializace kontaktniho formulare
##
contactFormInit = ->
  $('#page-contact #contactform .anti_spam_holder')
    .hide()
    .find('#anti_spam_check')
    .val('Anti-spam check')

  return


##
# Stranka vyhledavani
##
searchPageInit = (page) ->
  form = $ '#searchform', page

  $('#searchResults', page).tabs
    'active': 0
    'show':
      'effect': 'fadeIn'
      'duration': 'fast'
    'hide':
      'effect': 'fadeOut'
      'duration': 'fast'

  tabResults = []
  $('.result-holder').each () ->
    tabResults.push new SearchResult @, form

  # pozor tahle nula je i natvrdo v template views/scripts/index/search.phtml
  tabResults[0].setActive()

  $('#searchResults', page).on 'tabsactivate', (e, ui) ->
    if (ui.oldTab)
      tabResults[ui.oldTab.index()].resetActive()
    if (ui.newTab)
      tabResults[ui.newTab.index()].setActive()
    return


  form.on 'submit', ->
    $('#cardname').blur()
    $('#searchResults').focus()
    return
  return


##
#  Inicializace zobrazeni stranky
##
$(document).bind 'pageshow', ->
  fbPageInit()

  page = $.mobile.activePage
  switch page.attr('id')
    when 'page-search'
      searchPageInit page

    when 'page-contact'
      contactFormInit page

  return
