<?php
/**
 * @package	Joomla.Site
 * @subpackage	mod_escope_currency_converter
 * @copyright	Copyright (C) Jan Linhart aka escope.cz. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<script language="Javascript" type="text/javascript">
jQuery(document).ready(function($) {
 
    //$('#convert').click(function(){

        //Get all the values
//        var amount = $('#amount').val();
//        var from = $('#fromCurrency').val();
//        var to = $('#toCurrency').val();
        console.log('test');
        var amount = 200;
        var from = 'CZK';
        var to = 'EUR';
        //var url = 'http://www.google.com/ig/calculator?hl=en';
        var url = 'index.php?option=com_helloworld&task=getprice';
        //Make data string
        var requestData = "&amount=" + amount + '$from=' + from + "&to=" + to;
//http://www.google.com/ig/calculator?hl=en&q=1GBP=?USD
        $.ajax({
            type: "POST",
            url: url+requestData,
            data: requestData,

            success: function(data){
                console.log(data);
//            //Show results div
//            $('#results').show();
//
//            //Put received response into result div
//            $('#results').html(data);
            }

        });
    //});
});
    </script>
    <form>
        <label id="escopeCC"><?php echo JText::_('MOD_ESCOPE_CURRENCY_CONVERTER_LABEL'); ?></label>
        <select name="escopeCC"  id="escopeCC">
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
        </select>
    </form>
