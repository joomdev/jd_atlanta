<?php
/**
 * @package 	mod_bt_login - BT Login Module
 * @version		2.6.0
 * @created		April 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2011 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="btl">
	<!-- Panel top -->	
	<div class="btl-panel">
		<?php if($type == 'logout') : ?>
		<!-- Profile button -->
		<span id="btl-panel-profile" class="btl-dropdown">
			
			<?php
			echo JText::_("BTL_WELCOME").", ";
			if($params->get('name')) : {
				echo $user->get('name');
			} else : {
				echo $user->get('username');
			} endif;
			?>
		</span> 
		<?php else : ?>
			<!-- Login button -->
			<?php
			if($params->get('enabled_login_tab', 1)){
			?>
			<span id="btl-panel-login" class="<?php echo $effect;?>"><?php echo JText::_('JLOGIN');?></span>
			<?php }?>
			<!-- Registration button -->
			<?php
			if($enabledRegistration && $params->get('enabled_registration_tab')){
				$option = JRequest::getCmd('option');
				$task = JRequest::getCmd('task');
				if($option!='com_user' && $task != 'register' ){
			?>
			<span id="btl-panel-registration" class="<?php echo $effect;?>"><?php echo JText::_('JREGISTER');?></span>
			<?php }
			} ?>
			
			
		<?php endif; ?>
	</div>
	<!-- content dropdown/modal box -->
	<div id="btl-content">
		<?php if($type == 'logout') { ?>
		<!-- Profile module -->
		<div id="btl-content-profile" class="btl-content-block">
			<?php if($loggedInHtml): ?>
			<div id="module-in-profile">
				<?php echo $loggedInHtml; ?>
			</div>
			<?php endif; ?>
			<?php if($showLogout == 1):?>
			<div class="btl-buttonsubmit">
				<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="logoutForm">
					<button name="Submit" class="btl-buttonsubmit" onclick="document.logoutForm.submit();"><?php echo JText::_('JLOGOUT'); ?></button>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.logout" />
					<input type="hidden" name="return" value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
				
				</form>
			</div>
			<?php endif;?>
		</div>
		<?php }else{ ?>	
		<!-- Form login -->	
		<div id="btl-content-login" class="btl-content-block">
			<?php if(JPluginHelper::isEnabled('authentication', 'openid')) : ?>
				<?php JHTML::_('script', 'openid.js'); ?>
			<?php endif; ?>
			
			<!-- if not integrated any component -->
			<?php if($integrated_com==''|| $moduleRender == ''){?>
			<form name="btl-formlogin" class="btl-formlogin" action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
				<div id="btl-login-in-process"></div>	
				<h3><?php echo JText::_('LOGIN_TO_YOUR_ACCOUNT') ?></h3>
				<?php if ($enabledRegistration) : ?>
					<div id="register-link">
						<?php echo sprintf(JText::_('DONT_HAVE_AN_ACCOUNT_YET'),'<a href="'.JRoute::_('index.php?option=com_users&view=registration').'">','</a>');?>
					</div>
				<?php else: ?>
					<div class="spacer"></div>
				<?php endif; ?>
				<div class="btl-error" id="btl-login-error"></div>
				<div class="btl-field">
					<div class="btl-label"><?php echo JText::_('MOD_BT_LOGIN_USERNAME') ?></div>
					<div class="btl-input">
						<input id="btl-input-username" type="text" name="username"	/>
					</div>
				</div>
				<div class="btl-field">
					<div class="btl-label"><?php echo JText::_('MOD_BT_LOGIN_PASSWORD') ?></div>
					<div class="btl-input">
						<input id="btl-input-password" type="password" name="password" alt="password" />
					</div>
				</div>
				<div class="clear"></div>
				<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
				<div class="btl-field">				
					
					<div class="btl-input" id="btl-input-remember">
						<input id="btl-checkbox-remember"  type="checkbox" name="remember"
							value="yes" />
							<?php echo JText::_('BT_REMEMBER_ME'); ?>
					</div>	
				</div>
				<div class="clear"></div>
				<?php endif; ?>
				<div class="btl-buttonsubmit">
					<input type="submit" name="Submit" class="btl-buttonsubmit" onclick="return loginAjax()" value="<?php echo JText::_('JLOGIN') ?>" /> 
					<input type="hidden" name="bttask" value="login" /> 
					<input type="hidden" name="return" id="btl-return"	value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token');?>
				</div>
			</form>	
			<ul id ="bt_ul">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo JText::_('BT_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo JText::_('BT_FORGOT_YOUR_USERNAME'); ?></a>
				</li>				
			</ul>
			
		<!-- if integrated with one component -->
			<?php }else{ ?>
				<h3><?php echo JText::_('JLOGIN') ?></h3>
				<div id="btl-wrap-module"><?php  echo $moduleRender; ?></div>
				<?php }?>			
		</div>
		
		<?php if($enabledRegistration ){ ?>			
		<div id="btl-content-registration" class="btl-content-block">			
			<!-- if not integrated any component -->
			<?php if($integrated_com==''){?>	
						
				<form name="btl-formregistration" class="btl-formregistration"  autocomplete="off">
					<div id="btl-register-in-process"></div>	
					<h3><?php echo JText::_('CREATE_AN_ACCOUNT') ?></h3>
					<div id="btl-success"></div>
					<div class="btl-note"><span><?php echo JText::_("BTL_REQUIRED_FIELD"); ?></span></div>
					<div id="btl-registration-error" class="btl-error"></div>
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_LOGIN_NAME' ); ?></div>
						<div class="btl-input">
							<input id="btl-input-name" type="text" name="jform[name]" />
						</div>
					</div>			
					<div class="clear"></div>
					
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_LOGIN_USERNAME' ); ?></div>
						<div class="btl-input">
							<input id="btl-input-username1" type="text" name="jform[username]"  />
						</div>
					</div>
					<div class="clear"></div>
					
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_LOGIN_PASSWORD' ); ?></div>
						<div class="btl-input">
							<input id="btl-input-password1" type="password" name="jform[password1]"  />
						</div>
					</div>		
					<div class="clear"></div>
					
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_VERIFY_PASSWORD' ); ?></div>
						<div class="btl-input">
							<input id="btl-input-password2" type="password" name="jform[password2]"  />
						</div>
					</div>
					<div class="clear"></div>
					
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_EMAIL' ); ?></div>
						<div class="btl-input">
							<input id="btl-input-email1" type="text" name="jform[email1]" />
						</div>
					</div>
					<div class="clear"></div>
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_VERIFY_EMAIL' ); ?></div>
						<div class="btl-input">
							<input id="btl-input-email2" type="text" name="jform[email2]" />
						</div>
					</div>
					<div class="clear"></div>			
					<!-- add captcha-->
					<?php if($enabledRecaptcha){?>
					<div class="btl-field">
						<div class="btl-label"><?php echo JText::_( 'MOD_BT_CAPTCHA' ); ?></div>
						<div id="recaptcha"><?php echo $reCaptcha;?></div>
					</div>
					<div id="btl-registration-captcha-error" class="btl-error-detail"></div>
					<div class="clear"></div>
					<!--  end add captcha -->
					<?php }?>
				
					<div class="btl-buttonsubmit">						
						<button type="submit" class="btl-buttonsubmit" onclick="return registerAjax()" >
							<?php echo JText::_('JREGISTER');?>							
						</button>
						 
						<input type="hidden" name="bttask" value="register" /> 
						<?php echo JHtml::_('form.token');?>
					</div>
			</form>
			<!-- if  integrated any component -->
			<?php }else{ ?>
				<input type="hidden" name="integrated" value="<?php echo $linkOption?>" value="no" id="btl-integrated"/>		
			<?php }?>
		</div>
						
		<?php } ?>
	<?php } ?>
	
	</div>
	<div class="clear"></div>
</div>

<script type="text/javascript">
/*<![CDATA[*/
var btlOpt = 
{
	BT_AJAX					:'<?php echo addslashes(JURI::getInstance()->toString()); ?>',
	BT_RETURN				:'<?php echo addslashes($return_decode); ?>',
	RECAPTCHA				:'<?php echo $enabledRecaptcha ;?>',
	LOGIN_TAGS				:'<?php echo $loginTag?>',
	REGISTER_TAGS			:'<?php echo $registerTag?>',
	EFFECT					:'<?php echo $effect?>',
	ALIGN					:'<?php echo $align?>',
	BG_COLOR				:'<?php echo $bgColor ;?>',
	MOUSE_EVENT				:'<?php echo $params->get('mouse_event','click') ;?>',
	TEXT_COLOR				:'<?php echo $textColor;?>',
	MESSAGES 				: {
		E_LOGIN_AUTHENTICATE 		: '<?php echo addslashes(JText::_('E_LOGIN_AUTHENTICATE'))?>',
		REQUIRED_NAME				: '<?php echo addslashes(JText::_('REQUIRED_NAME'))?>',
		REQUIRED_USERNAME			: '<?php echo addslashes(JText::_('REQUIRED_USERNAME'))?>',
		REQUIRED_PASSWORD			: '<?php echo addslashes(JText::_('REQUIRED_PASSWORD'))?>',
		REQUIRED_VERIFY_PASSWORD	: '<?php echo addslashes(JText::_('REQUIRED_VERIFY_PASSWORD'))?>',
		PASSWORD_NOT_MATCH			: '<?php echo addslashes(JText::_('PASSWORD_NOT_MATCH'))?>',
		REQUIRED_EMAIL				: '<?php echo addslashes(JText::_('REQUIRED_EMAIL'))?>',
		EMAIL_INVALID				: '<?php echo addslashes(JText::_('EMAIL_INVALID'))?>',
		REQUIRED_VERIFY_EMAIL		: '<?php echo addslashes(JText::_('REQUIRED_VERIFY_EMAIL'))?>',
		EMAIL_NOT_MATCH				: '<?php echo addslashes(JText::_('EMAIL_NOT_MATCH'))?>',
		CAPTCHA_REQUIRED			: '<?php echo addslashes(JText::_('CAPTCHA_REQUIRED'))?>'
	}
}
if(btlOpt.ALIGN == "center"){
	BTLJ(".btl-panel").css('textAlign','center');
}else{
	BTLJ(".btl-panel").css('float',btlOpt.ALIGN);
}
BTLJ("input.btl-buttonsubmit,button.btl-buttonsubmit").css({"color":btlOpt.TEXT_COLOR,"background":btlOpt.BG_COLOR});
BTLJ("#btl .btl-panel > span").css({"color":btlOpt.TEXT_COLOR,"background-color":btlOpt.BG_COLOR,"border":btlOpt.TEXT_COLOR});
/*]]>*/
</script>
