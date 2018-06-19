/**
 * @Author: AlanWang
 * @Date:   2018-03-16T11:26:53+08:00
 * @Filename: nav-bar.js
 * @Last modified by:   AlanWang
 * @Last modified time: 2018-03-16T17:09:16+08:00
 */

$(function () {
  // fetch the nav links
  var $navLinks = $('#nav-bar').find('.nav-wrap .container .nav .nav-item .nav-link')

  // bind the click events to every nav links
  $navLinks.click(function (e) {
    // if ($(this).hasClass(''))

    // prevent the default event of the element 'a'
    // e.preventDefault()

    // remove class 'active' of all of elements 'li' which classname are 'nav-link'
    $navLinks.removeClass('active')

    // add class 'active' to the element nav link which was clicked
    $(this).addClass('active')

    // return false
  })

  // fetch the nav link which id is 'link-product-center'
  var $linkProductCenter = $('#link-product-center')
  // fetch the product dropdown list
  var $dropdownListProductCenter = $('#dropdown-list-product-center')

  // bind the mouseover event to $linkProductCenter
  $linkProductCenter.mouseenter(function () {
    $dropdownListProductCenter.show()
  }).mouseleave(function () {
    $dropdownListProductCenter.hide()
  })
  // bind the mouseover event to $dropdownListProductCenter
  $dropdownListProductCenter.mouseenter(function () {
    $(this).show()
  }).mouseleave(function () {
    $(this).hide()
  })
})
