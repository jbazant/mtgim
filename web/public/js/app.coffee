
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
#  Inicializace zobrazeni stranky
$(document).bind 'pageshow', ->
  fbPageInit()

  page = $.mobile.activePage
  switch page.attr 'id'
    when 'page-search'
      currentPage = new SearchPage page

    when 'page-contact'
      contactFormInit page

    when 'page-index'
      $('#cardname', page).focus()

    # todo pryc
    when 'page-test'
      contactFormInit page
  return
