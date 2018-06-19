function Marquee (speed) {
  // default scroll speed
  var speed = speed || 50
  // fetch relative jq dom elements
  var $marqueeContainer = $('#real-time-news')
  var marquee = $marqueeContainer.find('.marquee')[0]
  var begin = $marqueeContainer.find('.begin')[0]
  var end = $marqueeContainer.find('.end')[0]

  function startMarquee () {
    // set the same value as the begin to the end
    $(end).text($(begin).text())

    function marqueeLoop () {
      if (begin.offsetWidth - marquee.scrollLeft <= 0) {
        marquee.scrollLeft -= begin.offsetWidth
      } else {
        marquee.scrollLeft++
      }
    }

    var marqueeIntervalId = setInterval(marqueeLoop, speed)
    $(marquee).hover(function () {
      clearInterval(marqueeIntervalId)
    }, function () {
      marqueeIntervalId = setInterval(marqueeLoop, speed)
    })
  }
  return startMarquee
}
