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
    // prevent the default event of the element 'a'
    e.preventDefault()

    // remove class 'active' of all of elements 'li' which classname are 'nav-link'
    $navLinks.removeClass('active')

    // add class 'active' to the element nav link which was clicked
    $(this).addClass('active')

    return false
  })


  // fetch the nav dropdown list
  var $linkKey = 0;

  // bind the mouseover event to 'li' which id like 'link-*'
  $('.nav-wrap .container .nav').on('mouseenter', 'li[id^="link-"]', function () {
    $linkKey = $(this).attr('id').split('-')[1];
    $('div[id^="dropdown-list-"]').each(function(){
      if ($(this).attr('id').split('-')[2] == $linkKey) {
        $(this).show();
        return false;
      }
    });
  });
  $('.nav-wrap .container .nav').on('mouseleave', 'li[id^="link-"]', function () {
    $linkKey = $(this).attr('id').split('-')[1];
    $('div[id^="dropdown-list-"]').each(function(){
      if ($(this).attr('id').split('-')[2] == $linkKey) {
        $(this).hide();
        return false;
      }
    });
  });
  // bind the mouseover event to 'div' which id like 'dropdown-list-*'
  $('.nav-wrap .container').on('mouseenter', 'div[id^="dropdown-list-"]', function () {
    $(this).show()
  });
  $('.nav-wrap .container').on('mouseleave', 'div[id^="dropdown-list-"]', function () {
    $(this).hide()
  });
})
