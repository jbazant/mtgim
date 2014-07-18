##
#  Trida pro obsluhu collapsible s vysledky hledani
class window.SearchResult
  ##
  # Konstruktor
  # @param holder html collapsible div
  # @param form html form element
  constructor: (@holder, @form) ->
    ## pomocna promenna pro nastaveni rychlosti efektu
    @fadeSpeed = 'fast'

    ## collapsible jquery div
    @jHolder = $ @holder

    ## typ adapteru
    @adapter = @jHolder.attr 'data-shop-id'

    ## foil typ
    @foil = @jHolder.attr 'data-foil-id'

    ## listview s vysledky jquery ul
    @list = @jHolder.find '.results'

    ## priznak, zda-li je collapsible nactene
    @loaded = false

    ## input pole s vyhledavanym retezcem
    @searchInput = @form.find '#cardname'

    ## hledany text
    @searchText = @searchInput.val()

    # navesit se na formular a pri submitu nacist hodnotu, sbalit se a mazat @list
    @form.on 'submit', @formSubmitCallback

    # priznak, ze je prave zobrazen
    @isActive = false

    return


  ##
  #  Obsluha pri rozbaleni collapsible
  expandCallback: () =>
    # pouze pokud jiz neni nacteny
    if not @loaded
      @loaded = true

      # zobrazim uzivateli at si pocka
      @list.append @createLiDivider 'Načítám ...'
      @list.listview 'refresh'

      # donactu data
      $.ajax(
        url: jsParams.baseUrl + '/search/basic/format/json'
        type: 'POST'
        dataType: 'json'
        data:
          cardname: @searchText
          adapter:  @adapter
          foil:     @foil

      ).done((data) =>
        @list.empty()

        if data.success
          @list.append @createLiDivider "Nalezeno #{data.total} záznamů."

          $.each data.results, (index, value) =>
            @list.append @createLiItem value

        else
          @list.append @createLiDivider "Chyba! Opakujte požadavek později."

        @list.listview 'refresh'

        if data.success
          @list.trigger 'resultsLoaded', @list

      ).fail =>
        @list.empty()
        @list.append @createLiDivider "Chyba! Opakujte požadavek později."
        @list.listview 'refresh'
        @loaded = false

      return


  ##
  #  pomocna funkce pro vytvareni listview divideru
  createLiDivider: (text) ->
    $ '<li />',
      'data-role': 'list-divider'
      'text': text


  ##
  #  pomocna funkce pro vytvareni jedne polozky vysledku
  createLiItem: (item) ->
    colorClass = if item.amount is 0
        'empty'
      else if item.amount < 4
        'low'
      else
        'ok'

    displayedName = item.name + if item.quality then ' - ' + item.quality else ''

    cardInfoLine1 = $ '<p />'
      .append($ '<span />',
        'class': 'price'
        'text': item.value + ' Kč' # todo format number
      ).append($ '<span />',
          'class': 'name'
          'text': displayedName
      )

    cardInfoLine2 = $ '<p />'
      .append($ '<span />',
        'class': 'count ' + colorClass
        'text': item.amount + ' ks' # todo format number
      ).append($ '<span />',
        'class': 'expansion'
        'text': item.expansion
      )

    # todo tohle by bylo o moc hezci, kdyby si to popup pridaval sam
    cardActions = $ '<a />',
      'href': '#cardImgPopup'
      'data-rel': 'popup'
      'data-cardname': item.name
      'data-cardset': item.expansion
      'alt': 'Zobrazit obrázek karty'
      'title': 'Zobrazit obrázek karty'
      'text': 'Zobrazit kartu'
      'class': 'detail-button ui-btn ui-btn-icon-notext ui-icon-info ui-mini ui-corner-all ui-btn-b'

    $('<li />')
      .append($ '<div />',
          'class': 'cardActions'
        .append cardActions
      )
      .append($ '<div />',
          'class': 'cardInfo'
        .append cardInfoLine1
        .append cardInfoLine2
      )
      .append $ '<div />',
        'class': 'clear'

  ##
  # Akce divu vysledku pri znovuodeslani formulare pro vyhledavani
  formSubmitCallback: (e) =>
    @loaded = false
    e.preventDefault()

    formSubmitBaseAction = () =>
      @loaded = false
      @list.empty()
      @searchText = @searchInput.val()

    if @isActive
      @jHolder.fadeOut @fadeSpeed, () =>
        formSubmitBaseAction()
        @expandCallback()
        @jHolder.fadeIn @fadeSpeed
    else
      formSubmitBaseAction()

    return

  ##
  # Nastavi tento vypis vysledku jako aktivni
  setActive: () =>
    @isActive = true
    @expandCallback()


  ##
  # Zrusi priznak "aktivni" pro tento vypis vysledku
  resetActive: () =>
    @isActive = false



##
# Trida pro zobrazovani popupu s nahledem karty
class window.CardDetailPopup

  constructor: (@holder, page) ->
    t = @
    @cardname = null
    @cardset  = null
    @loadToken = 0

    @loadingTag = $ '<p />',
      'text': 'Nahrávám ...'

    @errorTag = $ '<p />',
      'text': 'Kartu nelze zobrazit'


    @holder.popup 'option', 'positionTo', 'window'

    onLinkClick = () ->
      l = $ @
      cardname = l.data 'cardname'
      cardset  = l.data 'cardset'

      if (cardname != t.cardname or cardset != t.cardset)
        t.cardname = cardname
        t.cardset  = cardset
        t.showCard cardname, cardset

    page.on 'resultsLoaded', (e, list) =>
      $ '.detail-button', list
        .on 'click', onLinkClick
      #e.preventDefault()



  showCard: (@cardname, @cardset) =>
    @popupSetData @loadingTag
    #@holder.popup 'open'

    # zkusim nahrat presnou moznost
    $.when @loadCard ++@loadToken, @cardname, @cardset
      .then null, =>
        # zkusim nahrat jen podle nazvu
        @loadCard(@loadToken, @cardname)
      .then null, =>
        @popupSetData @errorTag



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
          @popupSetData img
        p.resolve()
      .error =>
        p.reject()
    .attr('src', 'http://mtgimage.com/' + uri)

    p.promise()

  popupSetData: (data) ->
    # vycistim obsah
    old = @holder.children().eq(1)
    if old
      old.remove()

    #nastavim novy obsah
    @holder.append data
    @holder.popup 'reposition', y: '20px'

