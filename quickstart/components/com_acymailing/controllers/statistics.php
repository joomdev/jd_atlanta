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
defined('_JEXEC') or die('Restricted access');

class StatisticsController extends acymailingController{

    function listing(){
        acymailing_setVar('tmpl','component');

        $statsClass = acymailing_get('class.stats');
        $statsClass->saveStats();

        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        header("Expires: Wed, 17 Sep 1975 21:32:10 GMT");

        ob_end_clean();

        acymailing_importPlugin('acymailing');
        $results = acymailing_trigger('acymailing_getstatpicture');

        $picture = reset($results);
        if(empty($picture)) $picture = 'media/com_acymailing/images/statpicture.png';

        $picture = ltrim(str_replace(array('\\','/'),DS,$picture),DS);

        $imagename = ACYMAILING_ROOT.$picture;
        $handle = fopen($imagename, 'r');
        if(!$handle) exit;

        header("Content-type: image/png");
        $contents = fread($handle, filesize($imagename));
        fclose($handle);
        echo $contents;
        exit;
    }

    function detecttimeout(){
        $config = acymailing_config();
        if($config->get('security_key') != acymailing_getVar('string', 'seckey')) die('wrong key');
        $db = JFactory::getDBO();
        $db->setQuery("REPLACE INTO `#__acymailing_config` (`namekey`,`value`) VALUES ('max_execution_time','5'), ('last_maxexec_check','".time()."')");
        $db->query();
        @ini_set('max_execution_time',600);
        @ignore_user_abort(true);
        $i = 0;
        while($i < 480){
            sleep(8);
            $i += 10;
            $db->setQuery("UPDATE `#__acymailing_config` SET `value` = '".intval($i)."' WHERE `namekey` = 'max_execution_time'");
            $db->query();
            $db->setQuery("UPDATE `#__acymailing_config` SET `value` = '".time()."' WHERE `namekey` = 'last_maxexec_check'");
            $db->query();
            sleep(2);
        }
        exit;
    }
}
