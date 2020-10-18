define(['core/str', 'core/notification'], function (str, notification) {
    return {
        search_func: function () {
            $(".search_user").keyup(function () {
                var search_key = $(this).val();
                var url_hit = $(this).attr('id');
                if (search_key != '' || search_key != null) {
                    $.ajax({
                        type: 'GET',
                        url: url_hit,
                        data: { search_key: search_key },
                        success: function (data) {
                            $('.previous_table').hide();
                            $(".show_data").html(data);
                        },
                    });
                }
            });
        }
    };
});