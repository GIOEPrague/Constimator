<?php
/**
 * @package	Joomla.Site
 * @subpackage	mod_escope_currency_converter
 * @copyright	Copyright (C) Jan Linhart aka escope.cz. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$currencies = array('CZK','EUR','USD');
$session = JFactory::getSession();
$selectedCurrency = $session->get('currency');
var_dump($selectedCurrency);
?>
<script language="Javascript" type="text/javascript">
jQuery(document).ready(function($) {
 
    $('select#toCurrency').change(function(){

        var from = $('.priceContainer .currency').text();

        var amount = $('.priceContainer .result').text().replace(/ /g,'');
        
        var to = $('select#toCurrency').val();

        var url = 'index.php?option=com_openmart&task=esticalc.getprice';

        var requestData = "&amount=" + amount + '&from=' + from + "&to=" + to;
        console.log(requestData);
        $.ajax({
            type: "POST",
            url: url+requestData,
            data: requestData,

            success: function(data){
                $('.priceContainer .result').html(data);
                $('.priceContainer .currency').html(to);
            }

        });
    });
});
    </script>
    <form>
        <label id="toCurrency"><?php echo JText::_('MOD_ESCOPE_CURRENCY_CONVERTER_LABEL'); ?></label>
        <select name="toCurrency"  id="toCurrency">
            <?php 
            foreach($currencies as $currency){
                $selected = '';
                if($currency == $selectedCurrency){
                    $selected = 'selected="selected"';
                }
            ?>
                <option value="<?php echo $currency ?>" <?php echo $selected ?>><?php echo $currency ?></option>
            <?php } ?>
        </select>
    </form>
