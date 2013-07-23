/**
 * Design product price calculator
 *
 * @package     RedDesign.Component
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
akeeba.jQuery(document).ready(
    function () {
        // onClick calculate current product price adding all price modifiers: frames and accessories
        akeeba.jQuery(document).on('click', '.price-modifier', function () {
                var total = 0;
                var formatedTotal = 0;
                akeeba.jQuery('.price-modifier:checked').each(function () {
                    total += parseFloat(akeeba.jQuery(this).val());
                });
                formatedTotal = accounting.formatMoney(total);
                akeeba.jQuery('#total').html(formatedTotal);
            }
        );
    }
);