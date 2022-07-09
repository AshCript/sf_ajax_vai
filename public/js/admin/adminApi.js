const inactiveButton = () => {
  $(".item").css(
    "background-color", "rgba(0, 0, 0, 0.0)"
  )
}

const hideModal = () => {
  $('#curtain').css('display', 'none')
  $('#modal').fadeOut("fast")
}

const  hideWiwiBan = () =>{
  var wiwiMirror = document.getElementById('wiwi-mirror')
  wiwiMirror.style.opacity = 0
  setInterval(() =>{
    wiwiMirror.style.zIndex -= 5
    if(wiwiMirror.style.zIndex == -5)
      document.getElementById("messageBox").style.zIndex = -5
    clearInterval()
  }, 500)
}