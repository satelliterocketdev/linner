$(document).ready(function () {

    // $(".sidebar").mCustomScrollbar({
    //     theme: "minimal"
    // });

    $('.sidebarCollapse').on('click', function () {
        // open or close navbar
        $('.sidebar, #content, #content-header').toggleClass('active');
        // close dropdowns
        $('.collapse.in').toggleClass('in');
        // and also adjust aria-expanded attributes we use for the open/closed arrows
        // in our CSS
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });

    $('.sidebar .btn-circle').click(function(){
        $('.sidebar').addClass('opened')
        $('.sidebar .btn-circle').removeClass('active')
        $('.sidebar .second ul').removeClass('active')
        $(this).addClass('active')
        $($(this).attr('href')).addClass('active')
        return false;
    })

    $('.sidemenu-close').click(function(){
        $('.sidebar').removeClass('opened')
        $('.sidebar .btn-circle').removeClass('active')
        $('.sidebar .second ul').removeClass('active')
        return false
    })
});
