<?php
defined('_JEXEC') or die();


/**
 *
 * Realex User Field plugin
 *
 * @author Valerie Isaksen
 * @version $Id: eway.php 9788 2018-03-12 13:23:51Z alatak $
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

if (!class_exists('vmUserfieldPlugin')) {
	require(VMPATH_PLUGINLIBS . DS . 'vmuserfieldtypeplugin.php');
}


class plgVmUserfieldEway extends vmUserfieldPlugin {

	var $varsToPush = array();

	const EWAY_FOLDERNAME = "eway_";

	function _construct(& $subject, $config) {

		parent::_construct($subject, $config);

		$this->_loggable = TRUE;
		$this->tableFields = array_keys($this->getTableSQLFields());
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		$this->setConfigParameterable('params', $this->varsToPush);
		$this->_userFieldName = 'eway_';
	}


	function plgVmDeclarePluginParamsUserfield($type, $name, $id, &$data) {

		return $this->declarePluginParams($type, $name, $id, $data);
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPluginTable($type, $data) {

		return $this->onStoreInstallPluginTable($type, $data->name);
	}


	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the shipment-specific data.
	 *
	 * @param integer $order_number The order Number
	 * @return mixed Null for shipments that aren't active, text (HTML) otherwise
	 * @author Valérie Isaksen
	 */

	public function plgVmOnUserfieldDisplay($_prefix, $field, $userId, &$return) {

		if ('plugin' . $this->_name != $field->type) {
			return;
		}
		$html = $this->onShowUserDisplayUserfield($userId, $field->name);
		if ($html) {
			$return['fields'][$field->name]['formcode'] .= $html;
		}
		return '';

	}


	function onShowUserDisplayUserfield($userId, $fieldName) {
		if ($userId == 0) {
			return;
		}
		$html = '';
		if (!class_exists('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		$view = vRequest::getString('view', '');
		$this->loadJLangThis('plg_vmpayment_eway_', 'vmpayment');
		if (($view == 'user')) {
			$maskedCards = '';

			JPluginHelper::importPlugin('vmpayment');

			$app = JFactory::getApplication();
			$app->triggerEvent('plgVmOnEwayGetCreditCards', array('eway', $userId, &$maskedCards));

			$html = $this->renderByLayout("creditcards", array(
				"creditCards" => $maskedCards,
			));
		}

		return $html;
	}


	function deleteCard($userId, $cardToDelete) {
		if (!class_exists('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		$view = vRequest::getString('view', '');
		$this->loadJLangThis('plg_vmpayment_eway_', 'vmpayment');
		if ($view != 'plugin') {
			$result['error'] = true;
			$result['msg'] = 'Programming error: View is wrong';
			return $result;
		}
		$maskedCards = '';
		$msg = '';
		JPluginHelper::importPlugin('vmpayment');

		$app = JFactory::getApplication();
		$return = $app->triggerEvent('plgVmOnEwayDeleteCreditCard', array('eway', $userId, $cardToDelete, &$maskedCards, &$msg));
		$result['error'] = !$return[0];
		$result['msg'] = $msg;

		$html = $this->renderByLayout("creditcards", array(
			"creditCards" => $maskedCards,
			"eway_card_selected" => NULL,
			"action" => 'delete',
			"js" => false,
		));

		$result['html'] = $html;

		return $result;
	}

	function updateCard($userId, $cardToUpdate) {
		if (!class_exists('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		$view = vRequest::getString('view', '');
		$this->loadJLangThis('plg_vmpayment_eway_', 'vmpayment');
		if ($view != 'plugin') {
			$result['error'] = true;
			$result['msg'] = 'Programming error: View is wrong';
			return $result;
		}

		$html = '';
		JPluginHelper::importPlugin('vmpayment');

		$app = JFactory::getApplication();
		$return = $app->triggerEvent('plgVmOnEwayUpdateCreditCard', array('eway', $userId, $cardToUpdate, &$html));
		$result['error'] = !$return[0];
		$result['html'] = $html;

		return $html;
	}



	function plgVmOnSelfCallFE($type, $name, &$render) {
		if ($type != $this->_type) {
			return;
		}
		if ($name != $this->_name) {
			return;
		}

		$token = JSession::getFormToken();
		$jinput = JFactory::getApplication()->input;
		$call_token = $jinput->get('token', 0, 'ALNUM');
		$render['error'] = false;
		if ($token != $call_token) {
			//$render['error'] = true;
			//$render['msg'] = 'Action not allowed (' . __LINE__ . ')';
			//return;
		}
		$user = JFactory::getUser();
		if (!$user->id) {
			$render['error'] = true;
			$render['msg'] = 'Action not allowed (' . __LINE__ . ')';
			return;
		}

		$action = vRequest::getCmd('action');

		switch ($action) {
			case 'deleteCard':
				$cardToDelete = vRequest::getVar('cardToDelete', array());
				$render = $this->deleteCard($user->id, $cardToDelete);
				echo json_encode($render);
				jexit();
				break;
			case 'updateCard':
				$cardToUpdate = vRequest::getVar('cardToUpdate', array());
				$render = $this->updateCard($user->id, $cardToUpdate);
				break;
			default:
				$render['error'] = true;
				$render['msg'] = 'Action not allowed (' . __LINE__ . ')'.$action;
		}

		return;
	}

}

// No closing tag
