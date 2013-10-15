// Check for akeeba availability
if (typeof akeeba == "undefined")
{
    var rsjQuery = jQuery;
}
else
{
    var rsjQuery = akeeba.jQuery;
}

rsjQuery(document).ready(function () {

    rsjQuery('[id^="rs_sticker_element_"]').hide();

    // Set Discount Price On Load
    rsjQuery.setDiscountPrice();

    // Set Discount Price on Demand
    rsjQuery('input[id^="plg_dimention_base_input_"],select[id^="plg_dimention_base_"]').bind('change keyup', rsjQuery.setDiscountPrice);
});

/**
 * Set Discount Price Calculations
 */
rsjQuery.setDiscountPrice = function(){

    var elm = null, pid = 0;

    pid = rsjQuery('#product_id').val();
    elm = rsjQuery('#plg_dimention_base_input_' + pid);

    var pdb = rsjQuery('#plg_dimention_base_' + pid).val();
    var pdbi = parseFloat(elm.val()).round(2);

    var h = newH = elm.attr('default-height'), w = newW = elm.attr('default-width');

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
    rsjQuery('#plg_dimention_log_' + pid).html(newW + ' X ' + newH + rsjQuery('#plg_default_volume_unit_' + pid).html());
    rsjQuery('.discount-calculator-plugin-width').html(newW);
    rsjQuery('.discount-calculator-plugin-height').html(newH);

    rsjQuery.getJSON('plugins/redshop_product/discount_calculator/json/lookup.json', {}, function (json, textStatus) {

        // Convert finalWH into "meter" from "centimeter"
        // finalWH /= 10000;

        var finaldata = rsjQuery.vlookup(finalWH, json, false);

        rsjQuery.getJSON('plugins/redshop_product/discount_calculator/json/element.json', {}, function(json, textStatus) {

            var sticker_element = parseInt(rsjQuery('#rs_sticker_element_' + pid).html());
            finaldata.element = rsjQuery.vlookup(sticker_element, json, true);

            rsjQuery.updatePrice(pid, finaldata);
        });

    });
};

/**
 * Update price in HTML View
 *
 * @param   {number}  pid         Current Product Id
 * @param   {json}  price_data    Product data Array JSON
 *
 * @return  {number}              calculated price
 */
rsjQuery.updatePrice = function (pid, price_data) {

    var main_price  = rsjQuery('#main_price' + pid).val();
    var price_value = price_data.price * price_data.element.price;

    // Set QUantity Based Discount
    discountPrices = rsjQuery.setQuantityDiscount(pid, price_value);

    price_value = discountPrices.basePrice;

    if (SHOW_PRICE == '1' && ( DEFAULT_QUOTATION_MODE != '1' || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE))) {

        // Set price changes in HTML fields
        var formatted_main_price = number_format(discountPrices.price, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#display_product_price_no_vat' + pid + ', #produkt_kasse_hoejre_pris_indre' + pid).html(formatted_main_price);

        // Set price changes in hidden fields
        rsjQuery('#product_price_no_vat' + pid).val(discountPrices.price);
        rsjQuery('#main_price' + pid).val(discountPrices.price);
    }

    // Set Calculated product price into hidden input type
    rsjQuery('#plg_product_price_' + pid).val(price_value);

    // redSHOP Price Calculations
    calculateTotalPrice(pid, 0);

    return price_value;
};

/**
 * Set QUantity Based Discount
 *
 * @param  {number}  pid    Product Id
 * @param  {number}  price  Product Price
 */
rsjQuery.setQuantityDiscount = function(pid, price){

    var discountedPrice = 0, qtydiscountedPrice = 0;

    rsjQuery('.quantity_discount_radio').each(function(index, el) {

        discountedPrice = parseFloat(price) + parseFloat(price * rsjQuery(this).attr('percentage'));

        // Multiply with Quantity
        qtydiscountedPrice = discountedPrice * parseInt(rsjQuery(this).val());

        // Set Base Price
        rsjQuery(this).attr('base-price', discountedPrice);
        rsjQuery(this).attr('price', qtydiscountedPrice);

        // Set price changes in HTML fields
        var formatted_main_price = number_format(qtydiscountedPrice, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
        rsjQuery('#price_quantity' + rsjQuery(this).attr('index')).html(formatted_main_price);

    });

    // Quantity Based Discount Calculations
    var quantityDiscountRadio = rsjQuery('.quantity_discount_radio:checked');

    discountedPrice = parseFloat(price) + parseFloat(price * quantityDiscountRadio.attr('percentage'));

    // Multiply with Quantity
    qtydiscountedPrice = discountedPrice * parseInt(quantityDiscountRadio.val());

    // Set Base Price
    quantityDiscountRadio.attr('base-price', discountedPrice);
    quantityDiscountRadio.attr('price', qtydiscountedPrice);

    // Prepare Object to return
    discountPrices           = new Object();
    discountPrices.basePrice = discountedPrice;
    discountPrices.price     = qtydiscountedPrice;

    return discountPrices;
};

/**
 * vlookup function checks the best match value from array
 *
 * @param   {integer}  needle  input from which we get best match
 * @param   {json}  data    json string
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
