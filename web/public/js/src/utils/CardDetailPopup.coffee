##
# Trida pro zobrazovani popupu s nahledem karty
class window.CardDetailPopup

  ##
  # Konstruktor
  # Zachytava kliky na link s tridou '.detail-button'
  constructor: (page) ->
    # ulozim si self, je to pouzito v callbacku definovanem nize
    t = @

    #informace o prave nactene karte
    @cardname = null
    @cardset  = null

    # token zabranujici casove zavisle chybe pri rychlem klikane a pomalem nacitani obrazku
    @loadToken = 0

    # Tag zobrazeny pred nactenim obrazku
    @loadingTag = $ '<p />',
      'text': 'Nahrávám ...'

    # Tag zobrazeny, kdyz se nepodari nacist obrazek
    @errorTag = $ '<p />',
      'text': 'Kartu nelze zobrazit'

    @popup = new Popup()

    # definice callbacku kliku na tlacitko pro zobrazeni nahledu
    onLinkClick = (e) ->
      e.preventDefault()
      l = $ @
      cardname = l.data 'cardname'
      cardset  = l.data 'cardset'

      if (cardname != t.cardname or cardset != t.cardset)
        t.cardname = cardname
        t.cardset  = cardset
        t.showCard cardname, cardset
        # card is already loaded
      else
        t.popup.show()

    # poslouchani eventu nahrani vysledku a nasledna inicializace tlacitek
    page.on 'resultsLoaded', (e, list) =>
      $ '.detail-button', list
      .on 'click', onLinkClick
  #e.preventDefault()


  ##
  # Logika nacitani a zobrazovani obrazku karty
  # pres gatherer nejsem schopen nahravat podle edice - logika odstranena
  showCard: (@cardname, @cardset) =>
    @popup.setContent @loadingTag
    @popup.show()

    # zkusim nahrat presnou moznost
    $.when @loadCard ++@loadToken, @cardname
    .then null, =>
      @popup.setContent @errorTag


  ##
  # Samotne sestaveni url nacitane karty a jeji nacteni
  # @return $.Deferred.promise
  loadCard: (token, name) ->
    p = new $.Deferred()

    encName = encodeURIComponent name.trim().replace '´', '\''
    img = $ '<img />'
    .load =>
      if (token == @loadToken)
        @popup.setContent img
      p.resolve()
    .error =>
      p.reject()
    .attr 'src', 'http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name=' + encName

    p.promise()
