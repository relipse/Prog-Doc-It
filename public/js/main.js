/**
 * main javascript for whole site, enable hamburger menu
 * @author James Kinsman
 * @copyright 2021 Hampton Roads Transit
 */

$("body").click(function( event ) {
    // User clicked on hamburger menu
    if (event.target === $('a.box-shadow-menu')[0]){
        // if the menu is hidden, show it
        if ($('menu.top-bar-menu').is(':hidden')){
            $('menu.top-bar-menu').show();
        }else{
            // otherwise, hide it
            $('menu.top-bar-menu').hide();
        }
        return false;
    }else if ($(event.target).closest('menu.top-bar-menu').length){
        // if clicking inside of menu, do not hide
        // do nothing
    }else if ($('menu.top-bar-menu').is(':visible')){
        // hide the menu if clicking anywhere but the menu
        $('menu.top-bar-menu').hide();
    }
});
