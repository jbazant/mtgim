
##
# Trida pro trackovani aktivity uzivatele na strankach
class window.Tracking
  constructor: ->
    _gaq.push(['_setAccount', jsParams.gaCode]);
    $(document).bind 'pageshow', @trackPage
    $('a.external:not(.trackInitialized)')
      .addClass '.trackInitialized'
      .click @trackExternalLinkFunc()

  ##
  # Zaznamena zmenu stranky
  trackPage: ->
    _gaq.push ['_trackPageview', $.mobile.activePage.data 'url']

  ##
  # Vraci funkci, ktera
  # zaznamena kliknuti na link oznaceny jako external
  trackExternalLinkFunc: ->
    t = @
    ->
      l = $ @
      t.trackEvent 'External link', l.attr('title'), l.attr('href')

  ##
  # zaznamena custom event
  trackEvent: (category, action, label, value = 0) ->
    _gaq.push ['_trackEvent', category, action, label, value]
