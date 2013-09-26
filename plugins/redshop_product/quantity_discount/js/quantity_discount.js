akeeba.jQuery(function(){

    var quantity_elm = $('input[id^="quantity"]');
    quantity_elm.hide();

    akeeba.jQuery('.quantity_discount_radio').click(function(){

        var nq = $(this).val();
        var pid = $(this).attr('product_id');

        quantity_elm.val(nq);
        calculateTotalPrice(pid, 0);
    });
});
