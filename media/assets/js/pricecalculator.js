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
                akeeba.jQuery('.price-modifier:checked').each(function () {
                    total += parseFloat(akeeba.jQuery(this).val());
                });
                akeeba.jQuery('#total').html(total);
            }
        );
        // Calculate default price
        var total = 0;
        akeeba.jQuery('.price-modifier:checked').each(function () {
            total += parseFloat(akeeba.jQuery(this).val());
        });
        akeeba.jQuery('#total').html(total);
    }
);