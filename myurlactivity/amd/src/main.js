define(['jquery'], function($) {
    return {
        loader:function() {
            var url = $(this).attr('url');
            $(".loader_main_overlay").show();
            $.ajax({
                type: "GET",
                url: url,

                success: function(data) {
                    $(".loader_main_overlay").hide();
                },
                error: function (xhr, status) {
                    $(".loader_main_overlay").hide();
                    alert('Unknown error ' + status);
                }
            });
        }
    }
}); 
