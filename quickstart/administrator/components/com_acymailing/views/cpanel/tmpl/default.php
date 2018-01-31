<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<div id="iframedoc"></div>
	<form action="<?php echo acymailing_route('index.php?option=com_acymailing&ctrl=cpanel'); ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
		<?php acymailing_formOptions();

		echo '<div class="acytabsystem">';
		echo $this->tabs->startPane('config_tab');

		echo $this->tabs->startPanel(acymailing_translation('MAIL_CONFIG'), 'config_mail');
		include(dirname(__FILE__).DS.'mail.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->startPanel(acymailing_translation('QUEUE_PROCESS'), 'config_queue');
		include(dirname(__FILE__).DS.'queue.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->startPanel(acymailing_translation('SUBSCRIPTION'), 'config_subscription');
		include(dirname(__FILE__).DS.'subscription.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->startPanel(acymailing_translation('INTERFACE'), 'config_interface');
		include(dirname(__FILE__).DS.'interface.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->startPanel(acymailing_translation('SECURITY'), 'config_security');
		include(dirname(__FILE__).DS.'security.php');
		echo $this->tabs->endPanel();

		if(file_exists(dirname(__FILE__).DS.'others.php')){
			echo $this->tabs->startPanel(acymailing_translation('OTHERS'), 'config_others');
			include(dirname(__FILE__).DS.'others.php');
			echo $this->tabs->endPanel();
		}

		echo $this->tabs->startPanel(acymailing_translation('ACCESS_LEVEL'), 'config_acl');
		include(dirname(__FILE__).DS.'acl.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->startPanel(acymailing_translation('PLUGINS'), 'config_plugins');
		include(dirname(__FILE__).DS.'plugins.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->startPanel(acymailing_translation('LANGUAGES'), 'config_languages');
		include(dirname(__FILE__).DS.'languages.php');
		echo $this->tabs->endPanel();

		echo $this->tabs->endPane();

		echo '</div>';
		?>

		<div class="clr"></div>

	</form>
</div>
