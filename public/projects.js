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
    $(document).on('click','.removeTask', function (e) {
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
    $(document).on('change','.js-status-change', function (e) {
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
            $('#js-table-status-'+data.newStatusID+' option[value='+data.newStatusID+']' ).prop('selected', 'selected');
        })
    });
});

$(function() {
    $('.pop').on('click', function() {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });
});
