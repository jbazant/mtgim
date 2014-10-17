##
# Trida zajistujici obsluhu uvodni stranky
class window.IndexPage

  ##
  #  Inicializace stranky
  #
  # focus na formular
  # inicializace schovavani bloku
  constructor: (@page, @activityTracker) ->
    c = $('#cardname', @page).focus()

    @closeBoxInit 'submitcookies', 'cookies-agree', 'cookies-accept-block'
    @closeBoxInit 'hidenews', 'news-hide-1', 'news-block'
    @closeBoxInit 'hidenews', 'news-hide-2', 'news-block'


  ##
  #  Obecny callback pro schovani bloku
  #  a zaslani informace (na pozadi) na server
  closeBoxInit: (action, evokerId, boxId) ->
    $('#' + evokerId, @page).on 'click', (e) =>
      e.preventDefault()
      @activityTracker.trackEvent 'IndexPage', 'closeBox', evokerId
      $('#' + boxId).fadeOut()

      # post event to server silently
      $.ajax
        url: jsParams.baseUrl + '/json/' + action
        method: 'POST'
        dataType: 'json'

      return
