<?php
/**
 *
 * Eway User Field plugin
 *
 * @author Valerie Isaksen
 * @version $Id: creditcards.php 9788 2018-03-12 13:23:51Z alatak $
 * @package VirtueMart
 * @subpackage userfield
 * Copyright (C) 2004 - 2018 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('_JEXEC') or die();
$doc = JFactory::getDocument();
//$doc->addScript(JURI::root(true) . '/plugins/vmuserfield/eway/eway/assets/js/site.js');
$doc->addStyleSheet(JURI::root(true) . '/plugins/vmuserfield/eway/eway/assets/css/eway.css');
$doc->addStyleSheet(JURI::root(true) . '/plugins/vmpayment/eway/assets/css/eway.css');
$creditCards = $viewData['creditCards'];
vmJsApi::addJScript('/plugins/vmpayment/eway/assets/js/jquery.payform.min.js');
 if (!$creditCards) {
	//echo vmText::_('VMUSERFIELD_EWAY_NO_CARDS');
	return;
}

?>
	<div class="eway-cards">
		<table>
			<tr>
				<th><?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_CARD_HOLDER') ?></th>
				<th><?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_CARD_NUMBER') ?></th>
				<th><?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_EXPIRY_DATE') ?></th>
				<th></th>
				<th></th>
			</tr>
			<?php

			foreach ($creditCards as $creditCard) {
				?>
				<tr>
					<td><?php echo $creditCard->Name ?> </td>
					<td><?php echo $creditCard->Number ?> </td>
					<td>
						<?php echo $creditCard->ExpiryMonth ?>/<?php echo $creditCard->ExpiryYear ?>
					</td>
					<td><span
							data-eway='<?php echo vmCrypt::encrypt(json_encode($creditCard)) ?>'
							class="eway-edit-card button"><?php echo vmText::_('VMPAYMENT_EWAY_EDIT_CREDIT_CARD') ?>
						</span>
					</td>
					<td><span data-eway='<?php echo vmCrypt::encrypt(json_encode($creditCard)) ?>'
							class="eway-delete-card button"><?php echo vmText::_('VMPAYMENT_EWAY_DELETE_CREDIT_CARD') ?></span>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>

	<div class="eway-cards-msg"></div>
	<input type="hidden"
		   name="eway_card_selected"
		   id="eway_card_selected"
		   value="">


	<script type="text/javascript" src="https://api.ewaypayments.com/JSONP/v3/js"></script>

	<script>
        jQuery(document).ready(function ($) {
            jQuery(".eway-delete-card").click(function () {
                var eway_card_selected = $(this).data("eway");

                if (eway_card_selected !== undefined) {
                    $("#eway_card_selected").val(eway_card_selected);
                    $('.eway-cards-error').removeClass('eway-error').html();
                    request = {
                        'option': 'com_virtuemart',
                        'view': 'plugin',
                        'type': 'vmuserfield',
                        'tmpl': 'raw',
                        'name': 'eway',
                        'action': 'deleteCard',
                        'cardToDelete': eway_card_selected,
                        'token': "<?php echo JSession::getFormToken() ?>",
                    };
                    ewayDoAjax(request);
                }
            });
            jQuery(".eway-edit-cardxx").click(function () {
                jQuery('.eway-edit-card').toggle();
                jQuery('.eway-delete-card').toggle();
            });

            jQuery(".eway-edit-card").click(function () {
                var eway_card_selected = $(this).data("eway");

                if (eway_card_selected !== undefined) {

                    $('.eway-cards-error').removeClass('eway-error').html();

                    request = {
                        'option': 'com_virtuemart',
                        'view': 'plugin',
                        'type': 'vmuserfield',
                        'tmpl': 'raw',
                        'name': 'eway',
                        'action': 'updateCard',
                        'cardToUpdate': eway_card_selected,
                        'redirectURL':  Virtuemart.vmSiteurl + "<?php echo vmURI::getCurrentUrlBy() ?>",
                        'token': "<?php echo JSession::getFormToken() ?>",
                    };
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        data: request,
                        url: Virtuemart.vmSiteurl,
                        beforeSend: function() {
                            var object = {
                                data: {
                                    msg: ''
                                }
                            };
                            Virtuemart.startVmLoading(object);
                        },
                        success: function (response) {
                            Virtuemart.stopVmLoading();
                            $.fancybox(response);
                        },
                        error: function (e, t, n) {
                            console.log(e);
                            console.log(t);
                            console.log(n);
                            Virtuemart.stopVmLoading();
                        }
                    });

                }
            });

            var ewayDoAjax = function (request) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    data: request,
                    url: Virtuemart.vmSiteurl,
                    beforeSend: function() {
                        var object = {
                            data: {
                                msg: ''
               				 }
        				};
						Virtuemart.startVmLoading(object);
                    },
                    success: function (json) {
                        Virtuemart.stopVmLoading();
                        var response = json;
                        if ((response.error)) {
                            var msg;
                            if ((response.msg.length > 0)) {
                                msg = response.msg;
                            }
                            else {
                                msg = action + ' the CC returns an unknown error';
                            }
                            $('.eway-cards-msg').addClass('eway-error').html(msg);
                            // update
                            return;
                        }

                        $('.eway-cards-msg').addClass('eway-success').html(msg);
                        $('.eway-cards').html(response.html);
                    },
                    error: function (e, t, n) {
                        console.log(e);
                        console.log(t);
                        console.log(n);
                        Virtuemart.stopVmLoading();
                    }
                });
            }
        });
	</script>
