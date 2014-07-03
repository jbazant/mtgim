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
        @list.append @createLiDivider "Nalezeno #{data.total} záznamů."

        $.each data.results, (index, value) =>
          @list.append @createLiItem value

        @list.listview 'refresh'

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

    $('<li />')
      .append(
        $('<p />')
          .append($('<span />',
            'class': 'price'
            'text': item.value + ' Kč' # todo format number
          )).append($('<span />',
            'class': 'name'
            'text': displayedName
          ))
      ).append(
        $('<p />')
          .append($('<span />',
            'class': 'count ' + colorClass
            'text': item.amount + ' ks' # todo format number
          )).append($('<span />',
            'class': 'expansion'
            'text': item.expansion
          ))
      )


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
