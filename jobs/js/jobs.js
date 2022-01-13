jQuery(document).ready(function($) {
    $('.job-click').click(function() {

        $.ajax({
            url: jfs_vars.ajaxurl,
            type: 'post',
            data: {
                action     : 'job_click',
                security   : jfs_vars.security,
                dataType   : 'html',
                job_id: $(this).data('job-id'),
                post_id: $(this).data('post-id')
            },
            success: function ( html ){
                //$('#ajax_results_html').html(html);
            }
        });

        /*
        var data = {
            action: 'job_click',
            security : MyAjax.security,
            job_id: $(this).data('job-id'),
            post_id: $(this).data('post-id')
        };
        $.post(MyAjax.ajaxurl, data, function(response) {

        });
        */
    });
});