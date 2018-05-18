<?php
defined('_JEXEC') or die();
/**
 * @author Valérie Isaksen
 * @version $Id: cc_payment_page.php 9789 2018-03-12 13:27:14Z alatak $
 * @package VirtueMart
 * @subpackage vmpayment
 * @copyright Copyright (C) 2004-Copyright (C) 2004 - 2018 Virtuemart Team. All rights reserved.   - All rights reserved.
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
$doc->addStyleSheet(JURI::root(true) . '/plugins/vmpayment/eway/assets/css/eway.css');
vmJsApi::addJScript('/plugins/vmpayment/eway/assets/js/jquery.payform.min.js');
$maskedCard = $viewData['maskedCard'];

?>
<div id="eway-page">
	<div id="eway-payment-logo">
		<img src="<?php echo JURI::root() ?>/plugins/vmpayment/eway/assets/images/eway-logo.png"/>
	</div>
	<h1><?php echo $viewData['pageTitle'] ?></h1>

	<?php if ($viewData['sandbox']) {
		echo '<p><span style="color:red;font-weight:bold">Your payment is set in sandbox mode. No real money is transferred and this is not suitable for live sites.</span></p>';
		echo '<p><span style="color:red;font-weight:bold"><a href="https://go.eway.io/s/article/Test-Credit-Card-Numbers" target="_blank">Test Credit Card Numbers</a></span></p>';
	}
	?>
	<form method="POST" action="<?php echo $viewData['FormActionURL'] ?>" id="eway-payment-form" autocomplete="on" class="eway-payment-form">
		<input type="hidden" name="EWAY_ACCESSCODE" value="<?php echo $viewData['AccessCode'] ?>"/>

		<div id="payment">
			<div class="transactioncustomer">
				<div class="eway-form-group">
					<label for="EWAY_CARDNAME" class="eway-control-label">
						<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_CARD_HOLDER') ?></label>
					<input type="text" name="EWAY_CARDNAME" id="EWAY_CARDNAME"
						   class="eway-form-control"
						   placeholder="<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_CARD_HOLDER_PLACE') ?>"
						   value="<?php echo $maskedCard->Name ?>"/>
				</div>
				<div class="eway-form-group">
					<label for="EWAY_CARDNUMBER" class="eway-control-label">
						<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_CARD_NUMBER') ?></label>
					<input type="tel" name="EWAY_CARDNUMBER" id="EWAY_CARDNUMBER"
						   class="eway-form-control" autocomplete="cc-number"
						   placeholder="4444333322221111"
						<?php if ( $viewData['update_pay']=='update') { ?>
							readonly
						<?php } ?>
						   value="<?php echo $maskedCard->Number ?>"/>
				</div>
				<div class="eway-form-group eway-cardexpiry-group">
					<label for="EWAY_CARDEXPIRYMONTH" class="eway-control-label">
						<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_EXPIRY_DATE') ?></label>
					<div class="eway-cardexpiry-select">
						<select id="EWAY_CARDEXPIRYMONTH" name="EWAY_CARDEXPIRYMONTH" class="eway-cardexpirymonth">
							<?php
							$expiry_month = date('m');
							for ($i = 1; $i <= 12; $i++) {
								$month = sprintf('%02d', $i);
								$selected = '';
								if ($maskedCard->ExpiryMonth) {
									if ($maskedCard->ExpiryMonth == $i) {
										$selected = " selected='selected'";
									}
								} elseif ($expiry_month == $i) {
									$selected = " selected='selected'";
								}
								?>
								<option value="<?php echo $month ?>" <?php echo $selected ?>><?php echo $month ?></option>
							<?php } ?>
						</select>
						<select id="EWAY_CARDEXPIRYYEAR" name="EWAY_CARDEXPIRYYEAR" class="eway-cardexpiryyear">
							<?php
							$i = date("y");
							$j = $i + 11;
							for ($i; $i <= $j; $i++) {
								$selected = '';
								if ($maskedCard->ExpiryYear) {
									if ($maskedCard->ExpiryYear == $i) {
										$selected = " selected='selected'";
									}
								} elseif ($expiry_month == $i) {
									$selected = " selected='selected'";
								}
								?>
								<option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="eway-form-group eway-cardstart-group">
					<label for="EWAY_CARDSTARTMONTH" class="eway-control-label">
						<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_VALID_FROM_DATE') ?></label>
					<div class="eway-cardstart-select">
						<select id="EWAY_CARDSTARTMONTH" name="EWAY_CARDSTARTMONTH" class="eway-cardstartmonth">
							<option value=""><?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_VALID_FROM_DATE_SELECT_MONTH') ?></option>
							<?php
							$start_month = "";
							for ($i = 1; $i <= 12; $i++) {
								$month = sprintf('%02d', $i);
								$selected = '';
								if ($maskedCard->StartMonth) {
									if ($maskedCard->StartMonth == $i) {
										$selected = " selected='selected'";
									}
								} elseif ($start_month == $i) {
									$selected = " selected='selected'";
								}
								?>
								<option value="<?php echo $month ?>" <?php echo $selected ?>><?php echo $month ?></option>
							<?php } ?>
						</select>
						<select id="EWAY_CARDSTARTYEAR" name="EWAY_CARDSTARTYEAR" class="eway-cardstartyear">
							<?php
							$i = date("y");
							$j = $i - 11;
							?>
							<option value=""><?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_VALID_FROM_DATE_SELECT_YEAR') ?></option>
							<?php
							for ($i; $i >= $j; $i--) {
								$selected = '';
								if ($maskedCard->StartYear) {
									if ($maskedCard->StartYear == $i) {
										$selected = " selected='selected'";
									}
								}
								?>
								<option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="eway-form-group eway-cardissuenumber-group">
					<label for="EWAY_CARDISSUENUMBER" class="eway-control-label">
						<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_ISSUE_NUMBER') ?></label>
					<input type="text" name="EWAY_CARDISSUENUMBER" id="EWAY_CARDISSUENUMBER"
						   class="eway-form-control cc-cvc" autocomplete="off"
						   placeholder="22"
						   value="<?php echo $maskedCard->IssueNumber ?>"
						   maxlength="2"/>
					<!-- This field is optional but highly recommended -->
				</div>
				<div class="eway-form-group eway-cardcvn-group">
					<label for="EWAY_CARDCVN" class="eway-control-label">
						<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_CVN') ?></label>
					<input type="text" name="EWAY_CARDCVN" id="EWAY_CARDCVN" autocomplete="off"
						   class="eway-form-control cc-cvn"
						   placeholder="123"
						   value="<?php echo $maskedCard->CVN ?>" maxlength="4"
					/> <!-- This field is optional but highly recommended -->
				</div>
			</div>
			<div class="">
				<input type="submit" class="eway-button" id="btnSubmit" name="btnSubmit"
					   value="<?php echo vmText::_('VMPAYMENT_EWAY_PAYMENT_' . $viewData['update_pay']) ?>"/>
			</div>
		</div>
	</form>
</div>


<script type="text/javascript" src="https://api.ewaypayments.com/JSONP/v3/js"></script>


<script>
    jQuery(document).ready(function ($) {
        $("#EWAY_CARDNUMBER").payform("formatCardNumber");
        $("#EWAY_CARDCVN").payform("formatCardCVC");
        $("#EWAY_CARDCVN").payform("formatNumeric");
        $("#EWAY_CARDISSUENUMBER").payform("formatNumeric");

        $.fn.toggleInputError = function (erred) {
            this.parent(".eway-form-group").toggleClass("eway-error", erred);
            return this;
        };
        $("#eway-payment-form").submit(function (e) {

            var cardType = $.payform.parseCardType($("#EWAY_CARDNUMBER").val());
            var validCardNumber=true;
			<?php if ( $viewData["update_pay"]=="pay") { ?>
            validCardNumber = $.payform.validateCardNumber($("#EWAY_CARDNUMBER").val());
	        <?php } ?>
            var validCardCVC = $.payform.validateCardCVC($("#EWAY_CARDCVN").val(), cardType);
            var validCardExpiry = $.payform.validateCardExpiry($("#EWAY_CARDEXPIRYMONTH").val(), $("#EWAY_CARDEXPIRYYEAR").val());

            $("#EWAY_CARDNUMBER").toggleInputError(!validCardNumber);
            $("#EWAY_CARDCVN").toggleInputError(!validCardCVC);
            $("#EWAY_CARDEXPIRYMONTH").toggleInputError(!validCardExpiry);
           // $("#EWAY_CARDEXPIRYYEAR").toggleInputError(!validCardExpiry);

            if (!validCardNumber || !validCardCVC || !validCardExpiry) {
                // Stop the form from submitting
                e.preventDefault();
                return false;
            }

            $("#EWAY_CARDNUMBER").val($("#EWAY_CARDNUMBER").val().replace(/\s/g, ""));

            Virtuemart.ewayAjax();
        });


        Virtuemart.ewayAjax = function () {

            var form = $("#eway-payment-form");

            form.ewayAjax();

            function ewayAjax(e) {

                // call eWAY to process the request
                eWAY.process(
                    $("#eway-payment-form"),
                    {
                        autoRedirect: false,
                        onComplete: function (data) {
                            // this is a callback to hook into when the requests completes
                            window.location.replace(data.RedirectUrl);
                        },
                        onError: function (e) {
                            // this is a callback you can hook into when an error occurs
                            alert("There was an error processing the request");
                        },
                        onTimeout: function (e) {
                            // this is a callback you can hook into when the request times out
                            alert("The request has timed out.");
                        }
                    }
                );
                // Stop the form from submitting
                e.preventDefault();
            }
        }


    });
</script>
