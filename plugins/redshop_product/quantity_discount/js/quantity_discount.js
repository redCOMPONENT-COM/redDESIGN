/**
 * Created with JetBrains PhpStorm.
 * User: gunjan
 * Date: 3/29/13
 * Time: 10:33 AM
 * To change this template use File | Settings | File Templates.
 */
$(function(){

    var quantity_elm = $('input[id^="quantity"]');
    quantity_elm.hide();

    $('.quantity_discount_radio').click(function(){

        var nq = $(this).val();
        var pid = $(this).attr('product_id');

        quantity_elm.val(nq);
        calculateTotalPrice(pid, 0);
    });
});