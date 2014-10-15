
##
# Trida obsluhy stranky pro vyhledavani (index/search)
class window.SearchPage

  ##
  # Konstruktor
  # Nacte zajimave elementy z DOM
  # @param HtmlObject page
  # @param Tracking activityHolder
  constructor: (@page, @activityTracker) ->
    # Inicializace elementu
    @form = $ '#searchform', @page
    @shopSelect = $ '#shop-select', @page
    @shopResultsHolders = $ '.shop-results', @page
    @typeResultsHolders = $ '.result-holder', @page

    # inicializace ostatnich vlastnosti objektu
    @tabResults = {}
    @lastActiveTab = null
    @lastActiveTabIndex = 0
    @lastShopResultHolder = null
    @fadeSpeed = 'fast'

    # inicializace formulare, vcetne nacteni hashe
    @initForm()

    # inicializace stavu stranky
    # inicializace seznamu s vysledky
    @initResultHolders @activityTracker
    # inicializace zalozek jednotlivych metod obchodu
    @initTabs()
    # inicializace vyberu obchodu a prislusnych zalozek
    @initShopSelect()

    #inicializace nahledu karty
    @cardPreview = new CardDetailPopup $('#cardImgPopup'), @page

  ##
  # inicializace udalosti nad formularem
  initForm: ->
    at = @activityTracker

    # prectu hash a vlozim jej do formulare,
    # pokud neni specifikovan presmeruji uzivatele na index
    cardmatch = window.location.href.match /\/card\/(.*)$/
    if cardmatch and cardmatch[1]
      @form.find('#cardname', @page).val decodeURIComponent cardmatch[1]
    else
      $.mobile.changePage jsParams.baseUrl

    # pri odeslani chci zrusit focus na vyhledavacim inputu
    # tim se zavre softwarova klavesnice
    # a take chci trackovat samotne odeslani formulare
    @form.on 'submit', ->
      c = $('#cardname', @)
      card = c.val()
      at.trackEvent 'SearchPage', 'Form submit', card
      window.history.pushState  {id: card}, '', encodeURIComponent card
      c.blur()
      return


  ##
  # Inicializace vyberu obchodu vcetne otevreni vysledku
  # Nacte soucasnou volbu
  # a pripravi sledovani zmeny obchodu
  initShopSelect: ->
    @shopSelect.change @shopChanged
    @shopChanged()


  ##
  # Vlastni callback pro obsluhu zmeny obchodu
  shopChanged: =>
    # nactu prislusnou sekci s vysledky daneho obchodu
    holder = $ '#' + @shopSelect.val(), @page
    # vcetne navigace pro zalozky
    tabLinks = holder.find 'li a'

    # obsluha skryti stareho vysledku
    oldCallback = =>
      # vytvorim deferred, abych mohl navazovat obsluhy
      p = new $.Deferred

      # skryji prave zobrazeny vysledek
      # ale pouze pokud jej znam a pokud je jiny nez soucasny
      if @lastShopResultHolder != null and holder != @lastShopResultHolder
        @lastShopResultHolder.fadeOut @fadeSpeed, p.resolve
      else
        # splnim slib rovnou
        p.resolve()

      # vratim slib
      p.promise()

    # obsluha zobrazeni noveho vysledku
    newCallback = =>
      # nastavim novy div jako prave aktivni
      @lastShopResultHolder = holder

      # aktivni zalozku n-tou podle toho jaka zalozka byla nastavena predtim
      tabLinks.removeClass 'ui-btn-active'
      tabLinks.eq(@lastActiveTabIndex).addClass 'ui-btn-active'

      # zobrazim tab podle aktivni zalozky
      holder.tabs 'option', 'active', @lastActiveTabIndex
      @tabsactivateCallback holder, @lastActiveTabIndex

      # zobrazim prislusnou sekci
      holder.fadeIn @fadeSpeed

    # spusteni obsluh postupne
    $
      .when oldCallback()
      .then newCallback



  ##
  # Vlastni inicializace zalozek s typy vysledku pro jednotlive obchody
  initTabs: ->
    callback = @tabsactivateCallback

    # inicializace zalozek
    @shopResultsHolders.each ->
      $(@).tabs
        'show':
          'effect': 'fadeIn'
          'duration': 'fast'
        'hide':
          'effect': 'fadeOut'
          'duration': 'fast'

    # sledovani zmeny prave zobrazene zalozky
    @shopResultsHolders.on 'tabsactivate', (e, ui) ->
      # pokud byla otevrena nova zalozka, tak zmen (tzn pripadne donacti) prave
      # aktivni sekci s vysledky
      if (ui.newTab)
        callback $(@), ui.newTab.index()
      return


  ##
  # Inicializace jednotlivych seznamu s vysledky
  # @param Tracking activityHolder
  initResultHolders: (activityTracker) ->
    callback = @initResultHolder
    @typeResultsHolders.each ->
      callback @, activityTracker


  ##
  # Vlastni callback pro inicializaci jednoho seznamu s vysledkem vyhledavani
  # @param HtmlObject holder
  # @param Tracking activityHolder
  initResultHolder: (holder, activityTracker) =>
    adapter = $(holder).data 'shop-id'
    # objekt pro obsluhu daneho seznamu
    sr = new SearchResult holder, @form, activityTracker

    # pridani objektu do prislusneho pole vysledku
    if typeof @tabResults[adapter] == 'undefined' then @tabResults[adapter] = []
    @tabResults[adapter].push sr


  ##
  # Vlastni callback pri zmene zalozky
  # a to vcetne prepnuti mezi obchody
  tabsactivateCallback: (holder, index) =>
    newTab = @tabResults[holder.data 'shop-id'][index]

    # pokud je event vyvolan pro stejnou zalozku jako je jiz prave zobrazena,
    # tak nic nedelam
    if @lastActiveTab
      if @lastActiveTab == newTab
        return
      else
        # pripadne deaktivuji soucasnou
        @lastActiveTab.resetActive()

    # aktivuji novou zalozku a poznamenam si ji jako prave aktivni
    @lastActiveTab = newTab
    @lastActiveTab.setActive()
    @lastActiveTabIndex = index
