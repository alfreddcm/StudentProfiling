$(document).ready(function() {
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('active');
        $('.main-content').toggleClass('active');
    });

    if ($(window).width() < 768) {
        $('#sidebar').addClass('active');
        $('.main-content').addClass('active');
    }

    $(window).resize(function() {
        if ($(window).width() < 768) {
            $('#sidebar').addClass('active');
            $('.main-content').addClass('active');
        } else {
            $('#sidebar').removeClass('active');
            $('.main-content').removeClass('active');
        }
    });
});
