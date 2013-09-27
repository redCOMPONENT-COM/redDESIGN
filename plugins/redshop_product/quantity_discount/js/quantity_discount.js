// Check for akeeba availability
if (typeof akeeba == "undefined")
{
	var rsjQuery = jQuery;
}
else
{
	console.log('redshop');
	var rsjQuery = akeeba.jQuery;
}

rsjQuery(function(){

    var quantity_elm = rsjQuery('input[id^="quantity"]');
    quantity_elm.hide();

    rsjQuery('.quantity_discount_radio').click(function(){

        var nq = rsjQuery(this).val();
        var pid = rsjQuery(this).attr('product_id');

        quantity_elm.val(nq);

        calculateTotalPrice(pid, 0);
    });
});