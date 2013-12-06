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

rsjQuery(document).ready(function () {

    rsjQuery('[id^="rs_sticker_element"]').hide();

    // Set Discount Price On Load
    rsjQuery.setDiscountPrice();

    // Set Discount Price on Demand
    rsjQuery('input[id^="plg_dimension_base_input_"],select[id^="plg_dimension_base_"]').bind('change', rsjQuery.validateDimension);

    rsjQuery('input[id^="plg_dimension_base_input_"]').click(function(event) {
        rsjQuery(this).val('');
    });
});

rsjQuery.validateDimension = function(){

    var pid  = rsjQuery('#product_id').val();
    var pluginBaseInput  = rsjQuery('#plg_dimension_base_input_' + pid);
    var pdb  = rsjQuery('#plg_dimension_base_' + pid).val();

    var h = pluginBaseInput.attr('default-height'), w = pluginBaseInput.attr('default-width');

    if(pdb == 'w' && parseFloat(pluginBaseInput.val()) < w)
    {
       pluginBaseInput.val(w);
    }
    else if(pdb == 'w' && parseFloat(pluginBaseInput.val()) > parseFloat(pluginBaseInput.attr('max-width')))
    {
       alert('Maksimale tilladte bredde er ' + pluginBaseInput.attr('max-width') + 'cm');
       pluginBaseInput.val(pluginBaseInput.attr('max-width'));
    }
    else if(pdb == 'h' && parseFloat(pluginBaseInput.val()) < h)
    {
       pluginBaseInput.val(h);
    }
    else if(pdb == 'h' && parseFloat(pluginBaseInput.val()) > parseFloat(pluginBaseInput.attr('max-height')))
    {
       alert('Maksimale tilladte højde er ' + pluginBaseInput.attr('max-height') + 'cm');
       pluginBaseInput.val(pluginBaseInput.attr('max-height'));
    }

    // Set Discount Price On Load
    rsjQuery.setDiscountPrice();
};

/**
 * Set Discount Price Calculations
 */
rsjQuery.setDiscountPrice = function(){

    var pid  = rsjQuery('#product_id').val();
    var pluginBaseInput  = rsjQuery('#plg_dimension_base_input_' + pid);
    var pdb  = rsjQuery('#plg_dimension_base_' + pid).val();
    var pdbi = rsjQuery.clearPriceString(pluginBaseInput.val());

    var h = pluginBaseInput.attr('default-height'), newH = h, w = pluginBaseInput.attr('default-width'), newW = w;

    if (!isNaN(pdbi)) {

        var ratio_h2w = w > 0 && (h / w), ratio_w2h = h > 0 && (w / h);
        var dpAllow;

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
        pluginBaseInput.val('');
    }

    var finalWH = newW * newH;

    rsjQuery('#plg_dimension_width_' + pid).val(newW);
    rsjQuery('#plg_dimension_height_' + pid).val(newH);

    rsjQuery('#plg_dimension_log_' + pid).html(rsjQuery.clearPriceString(newW) + ' X ' + rsjQuery.clearPriceString(newH) + rsjQuery('#plg_default_volume_unit_' + pid).html());
    rsjQuery('.discount-calculator-plugin-width').html(newW);
    rsjQuery('.discount-calculator-plugin-height').html(newH);

    // Convert finalWH into "meter" from "centimeter". @todo: This will need confirmation from client.
    finalWH /= 10000;

    // Quantity Based Discount Calculations

     rsjQuery('.printedStickerPrice_radio').each(function(index, el) {

        var quantity = parseInt(rsjQuery(this).val());

        var finalWHTotal = finalWH * quantity;

        var finaldata       = rsjQuery.vlookup(finalWHTotal, lookupData, false);
        var stickerElement = 39;

        var tppm = finaldata.price * finalWHTotal;

        var discountPercentage = 1 - Math.abs(rsjQuery(this).attr('percentage'));

        var price = tppm / quantity * discountPercentage + (stickerElement / quantity);

        // Apply VAT
        var priceVat = price * 0.25;
        var priceInclVat = price + priceVat;

        if (rsjQuery(this).attr('checked'))
        {
            rsjQuery.updatePrice(pid, price, priceVat);
        }

        // Multiply with Quantity
        var qtydiscountedPrice = priceInclVat * quantity;

        // Set Base Price
        rsjQuery(this).attr('base-price', price);
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
 * @param   {json}  priceValue    Product data Array JSON
 *
 * @return  {number}              calculated price
 */
rsjQuery.updatePrice = function (pid, priceValue, priceVat) {

    if (SHOW_PRICE == '1' && ( DEFAULT_QUOTATION_MODE != '1' || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE))){

        // Set price changes in HTML fields
        var formatted_main_price = number_format(priceValue, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#display_product_price_no_vat' + pid).html(formatted_main_price);

        // Apply VAT
        var priceInclVat = priceValue + priceVat;
        formatted_main_price = number_format(priceInclVat, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#produkt_kasse_hoejre_pris_indre' + pid).html(formatted_main_price);

        // Set price changes in hidden fields
        rsjQuery('#product_price_no_vat' + pid).val(priceValue);
        rsjQuery('#main_price' + pid).val(priceInclVat);
    }

    // Set Calculated product price into hidden input type
    rsjQuery('#plg_product_price_' + pid).val(priceValue);

    // redSHOP Price Calculations
    calculateTotalPrice(pid, 0);

    return priceValue;
};

/**91*
 * Set QUantity Based Discount
 *
 * @param  {number}  pid    Product Id
 * @param  {number}  price  Product Price
 */
rsjQuery.setQuantityDiscount = function(pid, price){

    var discountedPrice;
    var qtyDiscountedPrice;

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
