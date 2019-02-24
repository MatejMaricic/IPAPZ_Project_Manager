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

$(document).ready(function () {
    $('.removeTask').on('click', function (e) {
        e.preventDefault();
        var link = $(e.currentTarget);
        $.ajax({
            method: 'POST',
            url: link.attr('href')
        }).done(function (data) {
            jQuery( '#task-'+data.deletedTask ).remove();
        })
    });
});

$(document).ready(function () {
    $('.js-status-change').on('change', function (e) {
        e.preventDefault();
        let link = $(this).find(':selected').data('change_status');
        $.ajax({
            method: 'POST',
            url: link
        }).done(function (data) {
            console.log(data);

            var rowHtml =  $('#task-'+data.taskID).prop('outerHTML');
            $('#task-'+data.taskID).remove();
            $('#js-table-status-'+data.newStatusID ).append(rowHtml);
        })
    });
});

$(function() {
    $('.pop').on('click', function() {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });
});