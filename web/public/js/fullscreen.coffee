# todo dodelat

##
##  Spusti rezim cele obrazovky
##
launchFullScreen = (element) ->
  if element.requestFullscreen
    element.requestFullscreen()
  else if element.mozRequestFullScreen
    element.mozRequestFullScreen()
  else if element.webkitRequestFullscreen
    element.webkitRequestFullscreen()
  else if element.msRequestFullscreen
    element.msRequestFullscreen()



##
##  Vypne rezim cele obrazovky
##
exitFullscreen = ->
  if document.exitFullscreen
    document.exitFullscreen()
  else if document.mozCancelFullScreen
    document.mozCancelFullScreen()
  else if document.webkitExitFullscreen
    document.webkitExitFullscreen()



##
##  Inicializace logiky pro full screen
##
initFullScreen = ->
  h1 = $('.enter-full-screen').not '.fs-initialized'
  h2 = $('.exit-full-screen').not '.fs-initialized'
  h1.on 'click', ->
    launchFullScreen document.documentElement
    return

  h2.on 'click', ->
    exitFullscreen()
    return

  h1.addClass 'fs-initialized'
  h2.addClass 'fs-initialized'
