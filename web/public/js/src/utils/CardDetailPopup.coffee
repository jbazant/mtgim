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
  showCard: (@cardname, @cardset) =>
    @popup.setContent @loadingTag
    @popup.show()

    # zkusim nahrat presnou moznost
    $.when @loadCard ++@loadToken, @cardname, @cardset
    .then null, =>
      # zkusim nahrat jen podle nazvu
      @loadCard(@loadToken, @cardname)
    .then null, =>
      @popup.setContent @errorTag


  ##
  # Samotne sestaveni url nacitane karty a jeji nacteni
  # @return $.Deferred.promise
  loadCard: (token, name, set) ->
    p = new $.Deferred()

    # pokud to je pulena karta, tak budu obrazek vyhledavat podle nazvu prni z nich
    # pokud to pulena karta neni, tak mam nazev take v prvni polozce
    # dale prekoduji spatne apostrofy na spravne
    encName = encodeURIComponent name.replace('´', "'").split('//')[0].trim()

    if set
      encSet = encodeURIComponent set
      uri = "setname/#{encSet}/#{encName}.jpg"
    else
      uri = "card/#{encName}.jpg"

    img = $ '<img />'
    .load =>
      if (token == @loadToken)
        @popup.setContent img
      p.resolve()
    .error =>
      p.reject()
    .attr('src', 'http://mtgimage.com/' + uri)

    p.promise()
