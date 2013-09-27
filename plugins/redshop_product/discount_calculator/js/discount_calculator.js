akeeba.jQuery(function () {

    akeeba.jQuery('[id^="rs_sticker_element_"]').hide();

    akeeba.jQuery('input[id^="plg_dimention_base_input_"],select[id^="plg_dimention_base_"]').bind('change keyup', function () {

        var elm = null, pid = 0;

        if (akeeba.jQuery(this).is('input')) {
            pid = akeeba.jQuery(this).attr('id').split("_")[4];
            elm = akeeba.jQuery(this);
        } else {
            pid = akeeba.jQuery(this).attr('id').split("_")[3];
            elm = akeeba.jQuery('#plg_dimention_base_input_' + pid);
        }

        var pdb = akeeba.jQuery('#plg_dimention_base_' + pid).val();
        var pdbi = parseFloat(elm.val()).round(2);

        var h = newH = elm.attr('default-height'), w = newW = elm.attr('default-width');

        if (!isNaN(pdbi)) {

            var ratio_h2w = w > 0 && (h / w), ratio_w2h = h > 0 && (w / h);

            if (pdb == 'w') {
                newW = pdbi;
                newH = (newW * ratio_h2w).round(2);

            } else {
                newH = pdbi;
                newW = (newH * ratio_w2h).round(2);
            }
        } else {
            elm.val('');
        }

        var finalWH = newW * newH;

        akeeba.jQuery('#plg_dimention_width_' + pid).val(newW);
        akeeba.jQuery('#plg_dimention_height_' + pid).val(newH);
        akeeba.jQuery('#plg_dimention_log_' + pid).html(newW + ' X ' + newH + akeeba.jQuery('#plg_default_volume_unit_' + pid).html());

        akeeba.jQuery.getJSON('plugins/redshop_product/discount_calculator/json/lookup.json', {}, function (json, textStatus) {

            var finaldata = akeeba.jQuery.vlookup(finalWH, json, false);

            akeeba.jQuery.getJSON('plugins/redshop_product/discount_calculator/json/element.json', {}, function(json, textStatus) {

                var sticker_element = parseInt(akeeba.jQuery('#rs_sticker_element_' + pid).html());
                finaldata.element = akeeba.jQuery.vlookup(sticker_element, json, true);

                akeeba.jQuery.updatePrice(pid, finaldata);
            });

        });

    });

    /**
     * Update price in HTML View
     *
     * @param   {number}  pid         Current Product Id
     * @param   {json}  price_data    Product data Array JSON
     *
     * @return  {number}              calculated price
     */
    akeeba.jQuery.updatePrice = function (pid, price_data) {

        //console.log(price_data);

        var main_price = akeeba.jQuery('#main_price' + pid).val();
        var price_value = price_data.price * price_data.element.price;

        //calculateTotalPrice(pid, 0);

        if (SHOW_PRICE == '1' && ( DEFAULT_QUOTATION_MODE != '1' || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE))) {

            // Set price changes in HTML fields
            var formatted_main_price = number_format(price_value, PRICE_DECIMAL, PRICE_SEPERATOR, THOUSAND_SEPERATOR);
            akeeba.jQuery('#display_product_price_no_vat' + pid + ', #produkt_kasse_hoejre_pris_indre' + pid).html(formatted_main_price);

            // Set price changes in hidden fields
            akeeba.jQuery('#product_price_no_vat' + pid).val(price_value);
            akeeba.jQuery('#main_price' + pid).val(price_value);
        }

        // Set Calculated product price into hidden input type
        akeeba.jQuery('#plg_product_price_' + pid).val(price_value);

        return price_value;
    };

    /**
     * vlookup function checks the best match value from array
     *
     * @param   {integer}  needle  input from which we get best match
     * @param   {json}  data    json string
     *
     * @return  {json}          JSON String object contains final values
     */
    akeeba.jQuery.vlookup = function (needle, data, isElement) {

        var size = [], price = [];

        akeeba.jQuery.each(data, function (key, val) {

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
});