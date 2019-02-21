$(document).ready(function () {
    $('.removeProject').on('click', function (e) {
        e.preventDefault();
        var link = $(e.currentTarget);
        $.ajax({
            method: 'POST',
            url: link.attr('href')
        }).done(function (data) {
            jQuery( '#project-'+data.deletedProject ).remove();
        })
    });
});