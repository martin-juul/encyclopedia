import * as $ from 'jquery';

$(function ($) {

  $('.sidebar-dropdown > a').click(function () {
    $('.sidebar-submenu').slideUp(200);
    if (
      $(this)
        .parent()
        .hasClass('active')
    ) {
      $('.sidebar-dropdown').removeClass('active');
      $(this)
        .parent()
        .removeClass('active');
    } else {
      $('.sidebar-dropdown').removeClass('active');
      $(this)
        .next('.sidebar-submenu')
        .slideDown(200);
      $(this)
        .parent()
        .addClass('active');
    }
  });

  $('#close-sidebar').click(function () {
    $('.content').removeClass('toggled');
  });
  $('#show-sidebar').click(function () {
    $('.content').addClass('toggled');
  });


});
