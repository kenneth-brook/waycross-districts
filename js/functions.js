; (function ($) {
    "use strict";

    $("[class*='cushycms']").each(function () {
        $(this).attr({'title': ''});
    });

    var $headerContact = $('.header-contact > *');
    var $headerContactLanding = $('.site-header-left');
    $headerContactLanding.addClass('beside-logo' ===  $headerContact.data('position') ? 'item-column' : '');
    $headerContact.parent().remove();
    $headerContact.addClass('header-contact').appendTo($headerContactLanding);
    
    $('.sidenav-button').sideNav();

    $('#mobile-navigation .menu > li > a').click(function (e) {
        if ($(this).siblings('.submenu').length) {
            e.stopPropagation();
            e.preventDefault();
            $(this).parent().siblings().removeClass('submenu-active');
            $(this).parent().siblings().find('.submenu').slideUp('fast');
            $(this).parent().toggleClass('submenu-active');
            $(this).siblings('.submenu').slideToggle('fast')
        }
    });

})(jQuery);
