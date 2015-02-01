##
# Simple modal popup layer
class window.Popup

  ##
  # Create popup markup, or use already created one
  constructor: ->
    @holder = $ '#popup-holder'

    # if popup markup exists,
    # make sure that no other event is bind
    if 0 < @holder.length
      @popup = $ '#popup'
      @holder.unbind()

      # if markup do not exists,
      # create one
    else
      @holder = $ '<div />',
        'id': 'popup-holder'
      @popup = $ '<div />',
        'id': 'popup'

      @holder.append @popup
      $('body').prepend @holder

    # bind hide event
    @holder.on 'click', (e) =>
      e.preventDefault()
      @hide()


  ##
  # show popup callback
  show: ->
    @holder.show()


  ##
  # hide popup callback
  hide: ->
    @holder.hide()


  ##
  # set popup markup
  setContent: (content) ->
    @popup.empty()
    @popup.append content



