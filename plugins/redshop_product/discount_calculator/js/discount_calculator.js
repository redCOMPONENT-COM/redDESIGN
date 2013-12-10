var rsjQuery;

// Check for akeeba availability
if ("undefined" === typeof akeeba)
{
    rsjQuery = jQuery;
}
else
{
    rsjQuery = akeeba.jQuery;
}

var lookupData = [
    {"size": 0,"price": 25}, {"size": 51,"price": 29}, {"size": 101,"price": 34},
    {"size": 201,"price": 43}, {"size": 301,"price": 48}, {"size": 401,"price": 64},
    {"size": 601,"price": 81}, {"size": 801,"price": 95}, {"size": 1001,"price": 107},
    {"size": 1301,"price": 121}, {"size": 1601,"price": 140}, {"size": 1901,"price": 160},
    {"size": 2201,"price": 171}, {"size": 2501,"price": 176}, {"size": 2801,"price": 186},
    {"size": 3101,"price": 192}, {"size": 3401,"price": 198}, {"size": 3701,"price": 208},
    {"size": 4001,"price": 216}, {"size": 4501,"price": 227}, {"size": 5001,"price": 249},
    {"size": 5501,"price": 267}, {"size": 6001,"price": 286}, {"size": 6501,"price": 305},
    {"size": 7001,"price": 323}, {"size": 7501,"price": 341}, {"size": 8001,"price": 358},
    {"size": 8501,"price": 376}, {"size": 9001,"price": 392}, {"size": 9501,"price": 408},
    {"size": 10001,"price": 417}, {"size": 10501,"price": 427}, {"size": 11001,"price": 436},
    {"size": 11501,"price": 445}, {"size": 12001,"price": 454}, {"size": 12501,"price": 463},
    {"size": 13001,"price": 473}, {"size": 13501,"price": 482}, {"size": 14001,"price": 491},
    {"size": 14501,"price": 500}, {"size": 15001,"price": 509}
];

var elementData = [
    {"size": 1,"price": 0.65}, {"size": 8,"price": 0.85}, {"size": 35,"price": 1},
    {"size": 70,"price": 1.3}, {"size": 125,"price": 1.6}
];

rsjQuery(document).ready(function () {

    rsjQuery('[id^="rs_sticker_element"]').hide();

    // Set Discount Price On Load
    rsjQuery.setDiscountPrice();

    // Set Discount Price on Demand
    rsjQuery('input[id^="plg_dimension_base_input_"],select[id^="plg_dimension_base_"]').bind('change', rsjQuery.validateDimension);

    rsjQuery('input[id^="plg_dimension_base_input_"]').click(function(event) {
        rsjQuery(this).val('');
    });

    // Set Trigger for add to cart button
    redShopJsTrigger.push(rsjQuery.setDiscountPrice);
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
    // finalWH /= 10000;

    var finaldata       = rsjQuery.vlookup(finalWH, lookupData, false);
    var stickerElement = parseInt(rsjQuery('#rs_sticker_element').html());
    finaldata.element   = rsjQuery.vlookup(stickerElement, elementData, true);

    rsjQuery.updatePrice(pid, finaldata);
};

/**
 * Update price in HTML View
 *
 * @param   {number}  pid         Current Product Id
 * @param   {json}  priceData    Product data Array JSON
 *
 * @return  {number}              calculated price
 */
rsjQuery.updatePrice = function (pid, priceData) {

    var main_price  = rsjQuery('#main_price' + pid).val();
    var priceValue = priceData.price * priceData.element.price;
    var price       = priceValue;

    // Set QUantity Based Discount
    discountPrices = rsjQuery.setQuantityDiscount(pid, priceValue);

    if ('object' === typeof discountPrices)
    {
        priceValue = discountPrices.basePrice;
        price       = discountPrices.price;
    }

    if (SHOW_PRICE == '1' && ( DEFAULT_QUOTATION_MODE != '1' || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE)))
    {
        // Set price changes in HTML fields
        var priceExclVat = price * 0.8;
        var formattedMainPrice = number_format(priceExclVat, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#display_product_price_no_vat' + pid).html(formattedMainPrice);

        // VAT Applied Price
        formattedMainPrice = number_format(price, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#produkt_kasse_hoejre_pris_indre' + pid).html(formattedMainPrice);

        // Set price changes in hidden fields
        rsjQuery('#product_price_no_vat' + pid).val(priceExclVat);
        rsjQuery('#main_price' + pid).val(price);
    }

    // Set Calculated product price into hidden input type
    // Send Excluding VAT price
    rsjQuery('#plg_product_price_' + pid).val(priceValue * 0.8);

    // redSHOP Price Calculations
    calculateTotalPrice(pid, 0);

    // Set Plugin Product Price for add to cart
    getExtraParamsArray.plg_product_price = rsjQuery('input[id^="plg_product_price_"]').val();

    return priceValue;
};

/**
 * Set QUantity Based Discount
 *
 * @param  {number}  pid    Product Id
 * @param  {number}  price  Product Price
 */
rsjQuery.setQuantityDiscount = function(pid, price){

    if (rsjQuery('.quantity_discount_radio').length <= 0)
    {
        return [];
    }

    var discountedPrice = 0, qtydiscountedPrice = 0;

    rsjQuery('.quantity_discount_radio').each(function(index, el) {

        discountedPrice = parseFloat(price) - parseFloat(price * Math.abs(rsjQuery(this).attr('percentage')));

        // Multiply with Quantity
        qtydiscountedPrice = discountedPrice * parseInt(rsjQuery(this).val());

        //Add price from attribute
        qtydiscountedPriceShow = qtydiscountedPrice + parseFloat(rsjQuery("#tmp_product_old_price").val());

        // Set Base Price
        rsjQuery(this).attr('base-price', discountedPrice);
        rsjQuery(this).attr('price', qtydiscountedPrice);

        // Set price changes in HTML fields
        var formattedMainPrice = number_format(qtydiscountedPriceShow, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#price_quantity' + rsjQuery(this).attr('index')).html(formattedMainPrice);

    });

    // Quantity Based Discount Calculations
    var quantityDiscountRadio = rsjQuery('.quantity_discount_radio:checked');

    discountedPrice = parseFloat(price) - parseFloat(price * Math.abs(quantityDiscountRadio.attr('percentage')));

    // Multiply with Quantity
    qtydiscountedPrice = discountedPrice * parseInt(quantityDiscountRadio.val());

    // Set Base Price
    quantityDiscountRadio.attr('base-price', discountedPrice);
    quantityDiscountRadio.attr('price', qtydiscountedPrice);

    // Prepare Object to return
    discountPrices           = {};
    discountPrices.basePrice = discountedPrice;
    discountPrices.price     = qtydiscountedPrice;

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
