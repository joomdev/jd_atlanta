<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

define('ACYMAILING_COMPONENT', 'com_acymailing');
define('ACYMAILING_ROOT', rtrim(JPATH_ROOT, DS).DS);
define('ACYMAILING_FRONT', rtrim(JPATH_SITE, DS).DS.'components'.DS.ACYMAILING_COMPONENT.DS);
define('ACYMAILING_BACK', rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS.ACYMAILING_COMPONENT.DS);
define('ACYMAILING_HELPER', ACYMAILING_BACK.'helpers'.DS);
define('ACYMAILING_CLASS', ACYMAILING_BACK.'classes'.DS);
define('ACYMAILING_TYPE', ACYMAILING_BACK.'types'.DS);
define('ACYMAILING_CONTROLLER', ACYMAILING_BACK.'controllers'.DS);
define('ACYMAILING_CONTROLLER_FRONT', ACYMAILING_FRONT.'controllers'.DS);
define('ACYMAILING_DBPREFIX', '#__acymailing_');
define('ACYMAILING_NAME', 'AcyMailing');
define('ACYMAILING_MEDIA', ACYMAILING_ROOT.'media'.DS.ACYMAILING_COMPONENT.DS);
define('ACYMAILING_TEMPLATE', ACYMAILING_MEDIA.'templates'.DS);
define('ACYMAILING_LANGUAGE', ACYMAILING_ROOT.'language'.DS);
define('ACYMAILING_UPDATEURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=update&task=');
define('ACYMAILING_SPAMURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=spamsystem&task=');
define('ACYMAILING_HELPURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=doc&component='.ACYMAILING_NAME.'&page=');
define('ACYMAILING_REDIRECT', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=redirect&page=');
define('ACYMAILING_INC', ACYMAILING_FRONT.'inc'.DS);

$jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
define('ACYMAILING_J16', version_compare($jversion, '1.6.0', '>=') ? true : false);
define('ACYMAILING_J25', version_compare($jversion, '2.5.0', '>=') ? true : false);
define('ACYMAILING_J30', version_compare($jversion, '3.0.0', '>=') ? true : false);

if(ACYMAILING_J16 && file_exists(ACYMAILING_ROOT.'media'.DS.'system'.DS.'js'.DS.'core.js')) acymailing_addScript(false, rtrim(acymailing_rootURI(), '/').'/media/system/js/core.js?v='.filemtime(ACYMAILING_ROOT.'media'.DS.'system'.DS.'js'.DS.'core.js'));

function acymailing_fileGetContent($url, $timeout = 10){
    ob_start();
    $data = '';
    if(class_exists('JHttpFactory') && method_exists('JHttpFactory', 'getHttp')) {
        $http = JHttpFactory::getHttp();
        try {
            $response = $http->get($url, array(), $timeout);
        } catch (RuntimeException $e) {
            $response = null;
        }

        if ($response !== null && $response->code === 200) $data = $response->body;
    }

    if(empty($data) && function_exists('curl_exec')){
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($conn, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($timeout)){
            curl_setopt($conn, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, $timeout);
        }

        $data = curl_exec($conn);
        curl_close($conn);
    }

    if(empty($data) && function_exists('file_get_contents')){
        if(!empty($timeout)){
            ini_set('default_socket_timeout', $timeout);
        }
        $streamContext = stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false)));
        $data = file_get_contents($url, false, $streamContext);
    }

    if(empty($data) && function_exists('fopen') && function_exists('stream_get_contents')){
        $handle = fopen($url, "r");
        if(!empty($timeout)){
            stream_set_timeout($handle, $timeout);
        }
        $data = stream_get_contents($handle);
    }
    $warnings = ob_get_clean();

    if(defined('JDEBUG') AND JDEBUG) echo $warnings;

    return $data;
}

function acymailing_formToken(){
    return JHTML::_('form.token');
}

function acymailing_checkToken(){
    if(!JRequest::checkToken() && !JRequest::checkToken('get')) {
        if (!ACYMAILING_J16) die('Invalid Token');
        JSession::checkToken() || JSession::checkToken('get') || die('Invalid Token');
    }
}

function acymailing_getFormToken() {
    if(ACYMAILING_J30) return JSession::getFormToken().'=1';
    return JUtility::getToken().'=1';
}

function acymailing_translation($key, $jsSafe = false, $interpretBackSlashes = true){
    return JText::_($key, $jsSafe, $interpretBackSlashes);
}

function acymailing_translation_sprintf(){
    $args = func_get_args();
    $return = "return JText::sprintf('".array_shift($args)."'";
    foreach($args as $oneArg){
        $return .= ",'".str_replace("'", "\\'", $oneArg)."'";
    }
    $return .= ');';
    return eval($return);
}

function acymailing_route($url, $xhtml = true, $ssl = null){
    return JRoute::_($url, $xhtml, $ssl);
}

function acymailing_getVar($type, $name, $default = null, $hash = 'default', $mask = 0){
    return JRequest::getVar($name, $default, $hash, $type, $mask);
}

function acymailing_setVar($name, $value = null, $hash = 'method', $overwrite = true){
    return JRequest::setVar($name, $value, $hash, $overwrite);
}

function acymailing_raiseError($level, $code, $msg, $info = null){
    return JError::raise($level, $code, $msg, $info);
}

function acymailing_getGroupsByUser($userid, $recursive){
    if(ACYMAILING_J16){
        jimport('joomla.access.access');
        return JAccess::getGroupsByUser($userid, $recursive);
    }

    $my = JFactory::getUser();
    return array($my->gid);
}

function acymailing_getGroups(){
    $groups = acymailing_loadObjectList('SELECT a.*, a.title as text, a.id as value  FROM #__usergroups AS a ORDER BY a.lft ASC', 'id');
    return $groups;
}

function acymailing_getLanguages($installed = false){
    $result = array();

    $path = acymailing_getLanguagePath(ACYMAILING_ROOT);
    $dirs = acymailing_getFolders($path);

    foreach($dirs as $dir){
        if(strlen($dir) != 5 || $dir == "xx-XX") continue;
        $xmlFiles = acymailing_getFiles($path.DS.$dir, '^([-_A-Za-z]*)\.xml$');
        $xmlFile = reset($xmlFiles);
        if(empty($xmlFile)){
            if($installed) continue;
            $data = array();
        }else{
            $data = JApplicationHelper::parseXMLLangMetaFile(ACYMAILING_LANGUAGE.$dir.DS.$xmlFile);
        }

        $lang = new stdClass();
        $lang->language = strtolower($dir);
        $lang->name = empty($data['name']) ? $dir : $data['name'];
        $lang->exists = file_exists(ACYMAILING_LANGUAGE.$dir.DS.$dir.'.com_acymailing.ini');

        $result[$dir] = $lang;
    }

    return $result;
}

function acymailing_languageFolder($code){
    return ACYMAILING_LANGUAGE.$code.DS;
}

function acymailing_cleanSlug($slug){
    $jconfig = JFactory::getConfig();
    $method = $jconfig->get('unicodeslugs', 0) == 1 ? 'stringURLUnicodeSlug' : 'stringURLSafe';
    return JFilterOutput::$method(trim($slug));
}

function acymailing_punycode($email, $method = 'emailToPunycode'){
    if(empty($email) || version_compare(JVERSION, '3.1.2', '<')) return $email;
    $email = JStringPunycode::$method($email);
    return $email;
}

function acymailing_extractArchive($archive, $destination){
    return JArchive::extract($archive, $destination);
}

function acymailing_selectOption($value, $text = '', $optKey = 'value', $optText = 'text', $disable = false){
    return JHTML::_('select.option', $value, $text, $optKey, $optText, $disable);
}

function acymailing_gridSort($title, $order, $direction = 'asc', $selected = '', $task = null, $new_direction = 'asc', $tip = ''){
    return JHTML::_('grid.sort', $title, $order, $direction, $selected, $task, $new_direction, $tip);
}

function acymailing_gridID($rowNum, $recId, $checkedOut = false, $name = 'cid', $stub = 'cb'){
    return JHTML::_('grid.id', $rowNum, $recId, $checkedOut, $name, $stub);
}

function acymailing_select($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false){
    return JHTML::_('select.genericlist', $data, $name, $attribs, $optKey, $optText, $selected, $idtag, $translate);
}

function acymailing_radio($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false, $vertical = false){
    $element = class_exists('JHtmlAcyselect') ? 'acyselect' : 'select';
    return JHTML::_($element.'.radiolist', $data, $name, $attribs, $optKey, $optText, $selected, $idtag, $translate, $vertical);
}

function acymailing_calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null){
    return JHTML::_('calendar', $value, $name, $id, $format, $attribs);
}

function acymailing_date($input = 'now', $format = null, $tz = true, $gregorian = false){
    return JHTML::_('date', $input, $format, $tz, $gregorian);
}

function acymailing_boolean($name, $attribs = null, $selected = null, $yes = 'JOOMEXT_YES', $no = 'JOOMEXT_NO', $id = false){
    $element = class_exists('JHtmlAcyselect') ? 'acyselect' : 'select';
    return JHTML::_($element.'.booleanlist', $name, $attribs, $selected, $yes, $no, $id);
}

function acymailing_addScript($raw, $script, $type = "text/javascript", $defer = false, $async = false){
    global $acyDocument;
    if($acyDocument === null){
        $acyDocument = JFactory::getDocument();
    }

    if($raw){
        $acyDocument->addScriptDeclaration($script, $type);
    }else{
        $acyDocument->addScript($script, $type, $defer, $async);
    }
}

function acymailing_addStyle($raw, $style, $type = 'text/css', $media = null, $attribs = array()){
    global $acyDocument;
    if($acyDocument === null){
        $acyDocument = JFactory::getDocument();
    }

    if($raw){
        $acyDocument->addStyleDeclaration($style, $type);
    }else{
        $acyDocument->addStyleSheet($style, $type, $media, $attribs);
    }
}

function acymailing_addMetadata($meta, $data){
    global $acyDocument;
    if($acyDocument === null){
        $acyDocument = JFactory::getDocument();
    }

    $acyDocument->setMetaData($meta, $data);
}

function acymailing_trigger($method, $args = null){
    global $acydispatcher;
    if($acydispatcher === null){
        $acydispatcher = JDispatcher::getInstance();
    }

    if(empty($args)) return $acydispatcher->trigger($method);

    return @$acydispatcher->trigger($method, $args);
}

function acymailing_isAdmin(){
    global $acyapp;
    if($acyapp === null){
        $acyapp = JFactory::getApplication();
    }

    return $acyapp->isAdmin();
}

function acymailing_getUserVar($key, $request, $default = null, $type = 'none'){
    global $acyapp;
    if($acyapp === null){
        $acyapp = JFactory::getApplication();
    }

    return $acyapp->getUserStateFromRequest($key, $request, $default, $type);
}

function acymailing_getCMSConfig($varname, $default = null){
    global $acyapp;
    if($acyapp === null){
        $acyapp = JFactory::getApplication();
    }

    return $acyapp->getCfg($varname, $default);
}

function acymailing_redirect($url, $msg = '', $msgType = 'message'){
    global $acyapp;
    if($acyapp === null){
        $acyapp = JFactory::getApplication();
    }

    return $acyapp->redirect($url, $msg, $msgType);
}

function acymailing_getLanguageTag(){
    global $acylanguage;
    if($acylanguage === null){
        $acylanguage = JFactory::getLanguage();
    }

    return $acylanguage->getTag();
}

function acymailing_getLanguageLocale(){
    global $acylanguage;
    if($acylanguage === null){
        $acylanguage = JFactory::getLanguage();
    }

    return $acylanguage->getLocale();
}

function acymailing_setLanguage($lang){
    global $acylanguage;
    if($acylanguage === null){
        $acylanguage = JFactory::getLanguage();
    }

    $acylanguage->setLanguage($lang);
}

function acymailing_baseURI($pathonly = false){
    return JURI::base($pathonly);
}

function acymailing_rootURI($pathonly = false, $path = null){
    return JURI::root($pathonly, $path);
}

function acymailing_generatePassword($length = 8){
    return JUserHelper::genrandompassword($length);
}

function acymailing_currentUserId(){
    global $acymy;
    if($acymy === null){
        $acymy = JFactory::getUser();
    }

    return $acymy->id;
}

function acymailing_currentUserName($userid = null){
    if(!empty($userid)){
        $special = JFactory::getUser($userid);
        return $special->name;
    }

    global $acymy;
    if($acymy === null){
        $acymy = JFactory::getUser();
    }

    return $acymy->name;
}

function acymailing_currentUserEmail($userid = null){
    if(!empty($userid)){
        $special = JFactory::getUser($userid);
        return $special->email;
    }

    global $acymy;
    if($acymy === null){
        $acymy = JFactory::getUser();
    }

    return $acymy->email;
}

function acymailing_authorised($action, $assetname = null){
    global $acymy;
    if($acymy === null){
        $acymy = JFactory::getUser();
    }

    return $acymy->authorise($action, $assetname);
}

function acymailing_loadLanguageFile($extension = 'joomla', $basePath = JPATH_BASE, $lang = null, $reload = false, $default = true){
    global $acylanguage;
    if($acylanguage === null){
        $acylanguage = JFactory::getLanguage();
    }

    $acylanguage->load($extension, $basePath, $lang, $reload, $default);
}

function acymailing_escapeDB($value){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    return $acydb->quote($value);
}

function acymailing_query($query){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    $acydb->setQuery($query);
    $acydb->query();
    return $acydb->getAffectedRows();
}

function acymailing_loadObjectList($query, $key = ''){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    $acydb->setQuery($query);
    return $acydb->loadObjectList($key);
}

function acymailing_loadObject($query){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    $acydb->setQuery($query);
    return $acydb->loadObject();
}

function acymailing_loadResult($query){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    $acydb->setQuery($query);
    return $acydb->loadResult();
}

function acymailing_loadResultArray(&$db){
    if(ACYMAILING_J30) return $db->loadColumn();
    return $db->loadResultArray();
}

function acymailing_getEscaped($value, $extra = false) {
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    if(ACYMAILING_J30) return $acydb->escape($value, $extra);
    return $acydb->getEscaped($value, $extra);
}

function acymailing_getDBError(){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    return $acydb->getErrorMsg();
}

function acymailing_insertObject($table, $element){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }
    $acydb->insertObject($table, $element);

    return $acydb->insertid();
}

function acymailing_updateObject($table, $element, $pkey){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    return $acydb->updateObject($table, $element, $pkey);
}

function acymailing_getColumns($table){
    global $acydb;
    if($acydb === null){
        $acydb = JFactory::getDBO();
    }

    if(ACYMAILING_J30) return $acydb->getTableColumns($table);
    $allfields = $acydb->getTableFields($table);
    return reset($allfields);
}

function acymailing_importPlugin($family, $name = null){
    if(!empty($name)) {
        JPluginHelper::importPlugin($family, $name);
    }else{
        JPluginHelper::importPlugin($family);
    }
}

function acymailing_completeLink($link, $popup = false, $redirect = false){
    if($popup) $link .= '&tmpl=component';
    return acymailing_route('index.php?option='.ACYMAILING_COMPONENT.'&ctrl='.$link, !$redirect);
}

function acymailing_cmsLoaded(){
    defined('_JEXEC') or die('Restricted access');
}

function acymailing_formOptions($order = null){
    echo '<input type="hidden" name="option" value="'.ACYMAILING_COMPONENT.'"/>';
    echo '<input type="hidden" name="task" value=""/>';
    echo '<input type="hidden" name="ctrl" value="'.acymailing_getVar('cmd', 'ctrl', '').'"/>';
    if($order) {
        echo '<input type="hidden" name="boxchecked" value="0"/>';
        echo '<input type="hidden" name="filter_order" value="'.$order->value.'"/>';
        echo '<input type="hidden" name="filter_order_Dir" value="'.$order->dir.'"/>';
    }
    echo acymailing_formToken();
}

function acymailing_enqueueMessage($message, $type = 'success'){
    $result = is_array($message) ? implode('<br/>', $message) : $message;

    if(acymailing_isAdmin()){
        if(ACYMAILING_J30){
            $type = str_replace(array('notice', 'message'), array('info', 'success'), $type);
        }else{
            $type = str_replace(array('message', 'notice', 'warning'), array('info', 'warning', 'error'), $type);
        }
    }else{
        if(ACYMAILING_J30){
            $type = str_replace(array('success', 'info'), array('message', 'notice'), $type);
        }else{
            $type = str_replace(array('success', 'error', 'warning', 'info'), array('message', 'warning', 'notice', 'message'), $type);
        }
    }

    global $acyapp;
    if($acyapp === null){
        $acyapp = JFactory::getApplication();
    }

    $acyapp->enqueueMessage($result, $type);
}

function acymailing_displayMessages(){
    $app = JFactory::getApplication();
    $messages = $app->getMessageQueue(true);
    if(empty($messages)) return;

    $sorted = array();
    foreach ($messages as $oneMessage) {
        $sorted[$oneMessage['type']][] = $oneMessage['message'];
    }

    foreach ($sorted as $type => $message) {
        acymailing_display($message, $type);
    }
}

function acymailing_editCMSUser($userid){
    return acymailing_route('index.php?option=com_users&view=user&layout=edit&id='.$userid);
}

function acymailing_prepareAjaxURL($url){
    return htmlspecialchars_decode(acymailing_completeLink($url)).'&tmpl=component';
}

function acymailing_cmsACL(){
    if(!acymailing_authorised('core.admin', 'com_acymailing')) return '';

    $return = urlencode(base64_encode((string)JUri::getInstance()));
    return '<div class="onelineblockoptions">
        <span class="acyblocktitle">'.acymailing_translation('ACY_JOOMLA_PERMISSIONS').'</span>
        <a class="acymailing_button_grey" style="color:#666;" target="_blank" href="index.php?option=com_config&view=component&component=com_acymailing&path=&return='.$return.'">'.acymailing_translation('JTOOLBAR_OPTIONS').'</a><br/>
    </div>';
}

function acymailing_isDebug(){
    return defined('JDEBUG') && JDEBUG;
}

jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

if(ACYMAILING_J16) {
    class acyParameter extends JRegistry {

        function get($path, $default = null){
            $value = parent::get($path, 'noval');
            if($value === 'noval') $value = parent::get('data.'.$path,$default);
            return $value;
        }
    }

    if(ACYMAILING_J30){
        class acymailingBridgeController extends JControllerLegacy{}
        class acymailingView extends JViewLegacy{
            var $chosen = true;

            function display($tpl = null){
                if($this->chosen && acymailing_isAdmin()){
                    JHtml::_('formbehavior.chosen', 'select');
                }

                return parent::display($tpl);
            }
        }
    }else{
        class acymailingBridgeController extends JController{}
        class acymailingView extends JView{}
    }
}else{
    jimport( 'joomla.html.parameter' );
    class acyParameter extends JParameter{}
    class acymailingBridgeController extends JController{}
    class acymailingView extends JView{}
}

if(acymailing_isAdmin()){
    define('ACYMAILING_IMAGES', '../media/'.ACYMAILING_COMPONENT.'/images/');
    define('ACYMAILING_CSS', '../media/'.ACYMAILING_COMPONENT.'/css/');
    define('ACYMAILING_JS', '../media/'.ACYMAILING_COMPONENT.'/js/');
}else{
    define('ACYMAILING_IMAGES', acymailing_baseURI(true).'/media/'.ACYMAILING_COMPONENT.'/images/');
    define('ACYMAILING_CSS', acymailing_baseURI(true).'/media/'.ACYMAILING_COMPONENT.'/css/');
    define('ACYMAILING_JS', acymailing_baseURI(true).'/media/'.ACYMAILING_COMPONENT.'/js/');
}
