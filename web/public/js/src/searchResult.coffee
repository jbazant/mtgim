##
#  Trida pro obsluhu collapsible s vysledky hledani
class window.SearchResult
  ##
  # Konstruktor
  # @param holder html collapsible div
  # @param form html form element
  # @param Tracking activityHolder
  constructor: (@holder, @form, @activityTracker) ->
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
    # vzdy trackuju
    @activityTracker.trackEvent 'SearchPage', 'Display results', @adapter + '-' + @foil

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
          @activityTracker.trackEvent 'SearchPage', 'Display results data error', @adapter  + '-' + @foil + '-' + @searchText
          @list.append @createLiDivider "Chyba! Opakujte požadavek později."

        @list.listview 'refresh'

        if data.success
          @list.trigger 'resultsLoaded', @list

      ).fail =>
        @activityTracker.trackEvent 'SearchPage', 'Display results connection error', @adapter  + '-' + @foil + '-' + @searchText
        @list.empty()
        @list.append @createLiDivider "Chyba! Opakujte požadavek později."
        @list.listview 'refresh'
        @loaded = false
        return

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
      'data-cardname': item.name
      'data-cardset': item.expansion
      'alt': 'Zobrazit obrázek karty'
      'title': 'Zobrazit obrázek karty'
      'text': 'Zobrazit kartu'
      'class': 'detail-button ui-btn ui-btn-icon-notext ui-icon-info ui-mini ui-corner-all ui-btn-b'

    $('<li />')
      .append $('<div />', 'class': 'cardActions').append cardActions
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
      return

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
    return


  ##
  # Zrusi priznak "aktivni" pro tento vypis vysledku
  resetActive: () =>
    @isActive = false
    return



