/*
* @Author: AlanWang
* @Date:   2017-09-04 09:22:48
 * @Last modified by:
 * @Last modified time: 2018-01-30T16:50:51+08:00
*/

export function getViewPortSize () {
  return {
    width: window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
    height: window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
  }
}

export function requestAnimationFrame () {
  let lastTime = 0
  let vendors = ['webkit', 'moz']
  let func
  if (window.requestAnimationFrame) func = window.requestAnimationFrame

  for (var i = 0; i < vendors.length && !window.requestAnimationFrame; i++) {
    func = window[vendors[i] + 'RequestAnimationFrame']
  }

  if (!window.requestAnimationFrame) {
    func = function (callback, element) {
      let currTime = new Date().getTime()
      let timeToCall = Math.max(0, 16.7 - (currTime - lastTime))
      let id = window.setTimeout(function () {
        callback(currTime + timeToCall)
      }, timeToCall)
      lastTime = currTime + timeToCall
      return id
    }
  }

  return func
}

export function cancelAnimationFrame (id) {
  let vendors = ['webkit', 'moz']
  let func
  if (window.cancelAnimationFrame) func = window.cancelAnimationFrame
  for (let i = 0; i < vendors.length && !window.requestAnimationFrame; i++) {
    func = window[vendors[i] + 'CancelAnimationFrame'] ||    // Webkit中此取消方法的名字变了
            window[vendors[i] + 'CancelRequestAnimationFrame']
  }

  if (!window.cancelAnimationFrame) {
    func = function (id) {
      clearTimeout(id)
    }
  }

  return func
}
