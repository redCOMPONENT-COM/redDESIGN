// Check for akeeba availability
if (typeof akeeba == "undefined")
{
    var rsjQuery = jQuery;
}
else
{
    var rsjQuery = akeeba.jQuery;
}

rsjQuery(function(){

    var quantity_elm = rsjQuery('input[id^="quantity"]');
    quantity_elm.hide();

    rsjQuery('.quantity_discount_radio').click(function(){

        var nq  = rsjQuery(this).val();
        var pid = rsjQuery(this).attr('product_id');

        rsjQuery('#main_price' + pid).val(rsjQuery(this).attr('price'));

        // Set Calculated product price into hidden input type
        rsjQuery('#plg_product_price_' + pid).val(rsjQuery(this).attr('base-price'));

        quantity_elm.val(nq);
        calculateTotalPrice(pid, 0);
    });
});
