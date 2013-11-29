var rsjQuery;

// Check for akeeba availability
if (typeof akeeba == "undefined")
{
    rsjQuery = jQuery;
}
else
{
    rsjQuery = akeeba.jQuery;
}

rsjQuery(function(){

    var quantity_elm = rsjQuery('input[id^="quantity"]');
    quantity_elm.hide();

    rsjQuery('.printedStickerPrice_radio').click(function(){

        var nq  = rsjQuery(this).val();
        var pid = rsjQuery(this).attr('product_id');

        rsjQuery('#main_price' + pid).val(rsjQuery(this).attr('price'));

        // Set Calculated product price into hidden input type
        rsjQuery('#plg_product_price_' + pid).val(rsjQuery(this).attr('base-price'));

        quantity_elm.val(nq);
        calculateTotalPrice(pid, 0);
    });
});

var lookupData = [{"size": 0,"price": 703.5}, {"size": 1,"price": 646.8}, {"size": 2,"price": 617.4}, {"size": 3,"price": 580.3}, {"size": 4,"price": 536.2}, {"size": 5,"price": 492.1}, {"size": 10,"price": 387.8}, {"size": 20,"price": 385.7}, {"size": 50,"price": 378}, {"size": 999,"price": 349.3}];
var elementData = [{"size": 0,"price": 1}, {"size": 5,"price": 0.90}, {"size": 10,"price": 0.85}, {"size": 25,"price": 0.8}, {"size": 50,"price": 0.75}, {"size": 500,"price": 0.70}];

rsjQuery(document).ready(function () {

    rsjQuery('[id^="rs_sticker_element"]').hide();

    // Set Discount Price On Load
    rsjQuery.setDiscountPrice();

    // Set Discount Price on Demand
    rsjQuery('input[id^="plg_dimention_base_input_"],select[id^="plg_dimention_base_"]').bind('change', rsjQuery.validateDimension);

    rsjQuery('input[id^="plg_dimention_base_input_"]').click(function(event) {
        rsjQuery(this).val('');
    });
});

rsjQuery.validateDimension = function(){

    var pid  = rsjQuery('#product_id').val();
    var elm  = rsjQuery('#plg_dimention_base_input_' + pid);
    var pdb  = rsjQuery('#plg_dimention_base_' + pid).val();

    var h = elm.attr('default-height'), w = elm.attr('default-width');

    if(pdb == 'w' && parseFloat(elm.val()) < w)
    {
       alert('Minimum påkrævede bredde er ' + w + 'cm');
    }
    else if(pdb == 'w' && parseFloat(elm.val()) > parseFloat(elm.attr('max-width')))
    {
       alert('Maksimale tilladte bredde er ' + elm.attr('max-width') + 'cm');
    }
    else if(pdb == 'h' && parseFloat(elm.val()) < h)
    {
       alert('Minimum påkrævede højde er ' + h + 'cm');
    }
    else if(pdb == 'h' && parseFloat(elm.val()) > parseFloat(elm.attr('max-height')))
    {
       alert('Maksimale tilladte højde er ' + elm.attr('max-height') + 'cm');
    }

    // Set Discount Price On Load
    rsjQuery.setDiscountPrice();
};

/**
 * Set Discount Price Calculations
 */
rsjQuery.setDiscountPrice = function(){

    var pid  = rsjQuery('#product_id').val();
    var elm  = rsjQuery('#plg_dimention_base_input_' + pid);
    var pdb  = rsjQuery('#plg_dimention_base_' + pid).val();
    var pdbi = rsjQuery.clearPriceString(elm.val());

    var h = elm.attr('default-height'), newH = h, w = elm.attr('default-width'), newW = w;

    if (!isNaN(pdbi)) {

        var ratio_h2w = w > 0 && (h / w), ratio_w2h = h > 0 && (w / h);

        if (pdb == 'w') {
            newW = pdbi;
            newH = (newW * ratio_h2w).round(2);

            // Check for valid height and width
            dpAllow = (w <= pdbi);

        } else {
            newH = pdbi;
            newW = (newH * ratio_w2h).round(2);

            // Check for valid height and width
            dpAllow = (h <= pdbi);
        }
    } else {
        elm.val('');
    }

    var finalWH = newW * newH;

    rsjQuery('#plg_dimention_width_' + pid).val(newW);
    rsjQuery('#plg_dimention_height_' + pid).val(newH);

    rsjQuery('#plg_dimention_log_' + pid).html(rsjQuery.clearPriceString(newW) + ' X ' + rsjQuery.clearPriceString(newH) + rsjQuery('#plg_default_volume_unit_' + pid).html());
    rsjQuery('.discount-calculator-plugin-width').html(newW);
    rsjQuery('.discount-calculator-plugin-height').html(newH);

    // Convert finalWH into "meter" from "centimeter". @todo: This will need confirmation from client.
    finalWH /= 10000;

    // Quantity Based Discount Calculations

     rsjQuery('.printedStickerPrice_radio').each(function(index, el) {

        var quantity = parseInt(rsjQuery(this).val());

        var finalWHTotal = finalWH * quantity;

        var finaldata       = rsjQuery.vlookup(finalWHTotal, lookupData, false);
        var sticker_element = 39;//parseInt(rsjQuery('#rs_sticker_element').html());

        finaldata.element   = rsjQuery.vlookup(sticker_element, elementData, true);

        var tppm = finaldata.price * finalWHTotal;

        var price = tppm / quantity * finaldata.element.price + (sticker_element / quantity);


        var discountedPrice = parseFloat(price) + parseFloat(price * rsjQuery(this).attr('percentage'));

        // Multiply with Quantity
        var qtydiscountedPrice = discountedPrice * quantity;

        // Set Base Price
        rsjQuery(this).attr('base-price', discountedPrice);
        rsjQuery(this).attr('price', qtydiscountedPrice);

        if (rsjQuery(this).attr('checked'))
        {
            rsjQuery.updatePrice(pid, price);
        }

        var discountedPrice = parseFloat(price) + parseFloat(price * rsjQuery(this).attr('percentage'));

        // Multiply with Quantity
        var qtydiscountedPrice = discountedPrice * quantity;

        // Set Base Price
        rsjQuery(this).attr('base-price', discountedPrice);
        rsjQuery(this).attr('price', qtydiscountedPrice);

        // Set price changes in HTML fields
        var formatted_main_price = number_format(qtydiscountedPrice, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);

        rsjQuery('#price_quantity' + rsjQuery(this).attr('index')).html(formatted_main_price);

    });


};

/**
 * Update price in HTML View
 *
 * @param   {number}  pid         Current Product Id
 * @param   {json}  price_value    Product data Array JSON
 *
 * @return  {number}              calculated price
 */
rsjQuery.updatePrice = function (pid, price_value) {

    if (SHOW_PRICE == '1' && ( DEFAULT_QUOTATION_MODE != '1' || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE))){

        // Set price changes in HTML fields
        var formatted_main_price = number_format(price_value, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#display_product_price_no_vat' + pid + ', #produkt_kasse_hoejre_pris_indre' + pid).html(formatted_main_price);

        // Set price changes in hidden fields
        rsjQuery('#product_price_no_vat' + pid).val(price_value);
        rsjQuery('#main_price' + pid).val(price_value);
    }

    // Set Calculated product price into hidden input type
    rsjQuery('#plg_product_price_' + pid).val(price_value);

    // redSHOP Price Calculations
    calculateTotalPrice(pid, 0);

    return price_value;
};

/**91*
 * Set QUantity Based Discount
 *
 * @param  {number}  pid    Product Id
 * @param  {number}  price  Product Price
 */
rsjQuery.setQuantityDiscount = function(pid, price){

    var discountedPrice = 0;
    var qtyDiscountedPrice = 0;

    // Quantity Based Discount Calculations
    var quantityDiscountRadio = rsjQuery('.printedStickerPrice_radio:checked');

    discountedPrice = parseFloat(price) + parseFloat(price * quantityDiscountRadio.attr('percentage'));

    // Multiply with Quantity
    qtyDiscountedPrice = discountedPrice * parseInt(quantityDiscountRadio.val());

    // Set Base Price
    quantityDiscountRadio.attr('base-price', discountedPrice);
    quantityDiscountRadio.attr('price', qtyDiscountedPrice);

    // Prepare Object to return
    var discountPrices           = [];
    discountPrices.basePrice = discountedPrice;
    discountPrices.price     = qtyDiscountedPrice;

    return discountPrices;
};

/**
 * vlookup function checks the best match value from array
 *
 * @param   {integer}  needle     input from which we get best match
 * @param   {json}     data       json string
 * @param   {boolean}  isElement  Check for JSON data is for elements or not.
 *
 * @return  {json}          JSON String object contains final values
 */
rsjQuery.vlookup = function (needle, data, isElement) {

    var size = [], price = [];

    rsjQuery.each(data, function (key, val) {

        if(isElement){

            if (val.size >= needle) {

                size.push(val.size);
                price.push(val.price);
            }
        }else{

            if (val.size <= needle) {

                size.push(val.size);
                price.push(val.price);
            }
        }

    });

    if (isElement) {
        return {'size': size[0], 'price': price[0]};
    }else{
        return {'size': size[size.length - 1], 'price': price[price.length - 1]};
    }

};

/**
 * Clear Input string into valid 2 decimal price format
 *
 * @param   {string}  str  Price String e.g. 22,31
 *
 * @return  {float}       Price e.g. 22.31
 */
rsjQuery.clearPriceString = function(str){
    str = String(str);
    return parseFloat(str.replace(/\s/g, "").replace(",", ".")).round(2);
};
