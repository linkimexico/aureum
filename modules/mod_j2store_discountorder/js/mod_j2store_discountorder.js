
function applycoupon(id,new_name) {
    (function($) {
        var form_data = $('#'+id).serializeArray();
        var button = $('#'+id+' #coupon_button');
        console.log(button);
        $.ajax({
            url : j2storeURL,
            type : 'post',
            cache : false,
            data : form_data,
            beforeSend: function() {
                $(button).after('<span class="wait"><img src="'+j2storeURL+'media/j2store/images/loader.gif" alt="" /></span>');
                $(button).attr('disabled',true);
                $(button).val(new_name);
            },
            success : function(json) {
                window.location.reload();
            }

        });

    })(jQuery);
}

function applyvoucher(id,new_name){
    (function($) {
        var form_data = $('#'+id).serializeArray();
        var button = $('#'+id+' #voucher_button');
        console.log(button);
        $.ajax({
            url : j2storeURL,
            type : 'post',
            cache : false,
            data : form_data,
            beforeSend: function() {
                $(button).after('<span class="wait"><img src="'+j2storeURL+'media/j2store/images/loader.gif" alt="" /></span>');
                $(button).attr('disabled',true);
                $(button).val(new_name);
            },
            success : function(json) {
                window.location.reload();
            }

        });

    })(jQuery);
}