<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="page-acl">
	<?php if(ACYMAILING_J16 && acymailing_authorised('core.admin', 'com_acymailing')){ ?>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo acymailing_translation('ACY_JOOMLA_PERMISSIONS'); ?></span>
			<a class="acymailing_button_grey" style="color:#666;" target="_blank" href="index.php?option=com_config&view=component&component=com_acymailing"><?php echo acymailing_translation('JTOOLBAR_OPTIONS'); ?></a><br/>
		</div>
	<?php } ?>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo acymailing_translation('ACY_ACL'); ?></span>
		<?php
		if(!acymailing_level(3)){
			echo '<a target="_blank" href="'.ACYMAILING_REDIRECT.'acymailing-features#mail">'.acymailing_translation('ONLY_FROM_ENTERPRISE').'</a>';
		}else{ ?>
			<table class="acymailing_table" cellspacing="1">
				<?php
				$acltable = acymailing_get('type.acltable');
				$aclcats['campaign'] = array('manage', 'delete', 'copy');
				$aclcats['configuration'] = array('manage');
				$aclcats['extra_fields'] = array('import');
				$aclcats['cpanel'] = array('manage');
				$aclcats['distribution'] = array('manage', 'copy', 'delete');
				$aclcats['lists'] = array('manage', 'delete', 'filter');
				$aclcats['newsletters'] = array('manage', 'delete', 'send', 'schedule', 'spam_test', 'copy', 'lists', 'attachments', 'sender_informations', 'meta_data', 'abtesting', 'inbox_actions');
				$aclcats['queue'] = array('manage', 'delete', 'process');
				$aclcats['simple_sending'] = array('manage');
				$aclcats['autonewsletters'] = array('manage', 'delete');
				$aclcats['tags'] = array('view');
				$aclcats['templates'] = array('view', 'manage', 'delete', 'copy');
				$aclcats['statistics'] = array('manage', 'delete');
				$aclcats['subscriber'] = array('view', 'manage', 'delete', 'export', 'import', 'zohoimport');
				foreach($aclcats as $category => $actions){ ?>
					<tr>
						<td width="185" class="acykey" valign="top">
							<?php $trans = acymailing_translation('ACY_'.strtoupper($category));
							if($trans == 'ACY_'.strtoupper($category)) $trans = acymailing_translation(strtoupper($category));
							echo $trans;
							?>
						</td>
						<td>
							<?php echo $acltable->display($category, $actions) ?>
						</td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>
	</div>
</div>
