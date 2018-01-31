jQuery.noConflict();
if(typeof(BTLJ)=='undefined') var BTLJ = jQuery;
if(typeof(btTimeOut)=='undefined') var btTimeOut;
if(typeof(requireRemove)=='undefined') var requireRemove = true;


BTLJ(document).ready(function() {
	
	BTLJ('#btl-content').appendTo('body');
	BTLJ(".btl-input #jform_profile_aboutme").attr("cols",21);
	BTLJ('.bt-scroll .btl-buttonsubmit').click(function(){		
		setTimeout(function(){
			if(BTLJ("#btl-registration-error").is(':visible')){
				BTLJ('.bt-scroll').data('jsp').scrollToY(0,true);
			}else{
				var position = BTLJ('.bt-scroll').find('.invalid:first').position();
				if(position) BTLJ('.bt-scroll').data('jsp').scrollToY(position.top-15,true);
			}
		},20);
	})
	//SET POSITION
	if(BTLJ('.btl-dropdown').length){
		setFPosition();
		BTLJ(window).resize(function(){
			setFPosition();
		})
	}
	
	BTLJ(btlOpt.LOGIN_TAGS).addClass("btl-modal");
	if(btlOpt.REGISTER_TAGS != ''){
		BTLJ(btlOpt.REGISTER_TAGS).addClass("btl-modal");
	}

	// Login event
	var elements = '#btl-panel-login';
	if (btlOpt.LOGIN_TAGS) elements += ', ' + btlOpt.LOGIN_TAGS;
	if (btlOpt.MOUSE_EVENT =='click'){ 
		BTLJ(elements).click(function (event) {
			return showLoginForm();
		});	
	}else{
		BTLJ(elements).hover(function () {
			return showLoginForm();
		},function(){});
	}

	// Registration/Profile event
	elements = '#btl-panel-registration';
	if (btlOpt.REGISTER_TAGS) elements += ', ' + btlOpt.REGISTER_TAGS;
	if (btlOpt.MOUSE_EVENT =='click'){ 
		BTLJ(elements).click(function (event) {
			return showRegistrationForm();
		});
		BTLJ("#btl-panel-profile").click(function(event){
			showProfile();
			event.preventDefault();
		});
	}else{
		BTLJ(elements).hover(function () {
			if(!BTLJ("#btl-integrated").length){
				return showRegistrationForm();
			}
		},function(){});
		BTLJ("#btl-panel-profile").hover(function () {
				showProfile();
		},function(){});
	}
	BTLJ('#register-link a').click(function (event) {
		if(BTLJ('.btl-modal').length){
			BTLJ.simpleModal.close();
			setTimeout(showRegistrationForm(),1000);
		}
		else{
			return showRegistrationForm();
		}

	});	
	
	// Close form
	BTLJ(document).click(function(event){
		if(requireRemove && event.which == 1) btTimeOut = setTimeout('BTLJ("#btl-content > div").slideUp();BTLJ(".btl-panel span").removeClass("active");',10);
		requireRemove =true;
	})
	BTLJ(".btl-content-block").click(function(){requireRemove =false;});	
	BTLJ(".btl-panel span").click(function(){requireRemove =false;});	
	
	// Modify iframe
	BTLJ('#btl-iframe').load(function (){
		//edit action form	
		oldAction=BTLJ('#btl-iframe').contents().find('form').attr("action");
		if(oldAction!=null){
			if(oldAction.search("tmpl=component")==-1){
				if(BTLJ('#btl-iframe').contents().find('form').attr("action").indexOf('?')!=-1){	
					BTLJ('#btl-iframe').contents().find('form').attr("action",oldAction+"&tmpl=component");
				}
				else{
					BTLJ('#btl-iframe').contents().find('form').attr("action",oldAction+"?tmpl=component");
				}
			}
		}
	});	
	
	//reload captcha click event
	BTLJ('span#btl-captcha-reload').click(function(){
		BTLJ.ajax({
						type: "post",
						url: btlOpt.BT_AJAX,
						data: 'bttask=reload_captcha',
						success: function(html){
							BTLJ('#recaptcha img').attr('src', html);
						}
					});
	});

});

function setFPosition(){
	if(btlOpt.ALIGN == "center"){
		BTLJ("#btl-content > div").each(function(){
			var panelid = "#"+this.id.replace("content","panel");
			var left = BTLJ(panelid).offset().left + BTLJ(panelid).width()/2 - BTLJ(this).width()/2;
			if(left < 0) left = 0;
			BTLJ(this).css('left',left);
		});
	}else{
		if(btlOpt.ALIGN == "right"){
			BTLJ("#btl-content > div").css('right',BTLJ(document).width()-BTLJ('.btl-panel').offset().left-BTLJ('.btl-panel').width());
		}else{
			BTLJ("#btl-content > div").css('left',BTLJ('.btl-panel').offset().left);
		}
	}	
	BTLJ("#btl-content > div").css('top',BTLJ(".btl-panel").offset().top+BTLJ(".btl-panel").height()+2);	
}

// SHOW LOGIN FORM
function showLoginForm(){
	if(BTLJ('#btl-content-login').size() == 0){
		return;
	}
	BTLJ('.btl-panel span').removeClass("active");
	var el = '#btl-panel-login';
	if (btlOpt.LOGIN_TAGS) el += ', ' + btlOpt.LOGIN_TAGS;
	BTLJ.simpleModal.close();
	var containerWidth = 0;
	var containerHeight = 0;
	containerHeight = 371;
	containerWidth = 357;
	
	if(containerWidth>BTLJ(window).width()){
		containerWidth = BTLJ(window).width()-50;
	}
	if(btlOpt.EFFECT == "btl-modal"){
		BTLJ(el).addClass("active");
		BTLJ("#btl-content > div").slideUp();
		BTLJ("#btl-content-login").simpleModal({
			overlayClose:true,
			persist :true,
			autoPosition:true,
			fixed: BTLJ(window).width()>500,
			onOpen: function (dialog) {
				dialog.overlay.fadeIn();
				dialog.container.show();
				dialog.data.show();		
			},
			onClose: function (dialog) {
				dialog.overlay.fadeOut(function () {
					dialog.container.hide();
					dialog.data.hide();		
					BTLJ.simpleModal.close();
					BTLJ('.btl-panel span').removeClass("active");
				});
			},
			containerCss:{
				height:containerHeight,
				width:containerWidth
			}
		})			 
	}
	else
	{	
		setFPosition();
		BTLJ("#btl-content > div").each(function(){
			if(this.id=="btl-content-login")
			{
				if(BTLJ(this).is(":hidden")){
					BTLJ(el).addClass("active");
					BTLJ(this).slideDown();
					}
				else{
					BTLJ(this).slideUp();
					BTLJ(el).removeClass("active");
				}						
					
			}
			else{
				if(BTLJ(this).is(":visible")){						
					BTLJ(this).slideUp();
					BTLJ('#btl-panel-registration').removeClass("active");
				}
			}
			
		})
	}
	return false;
}

// SHOW REGISTRATION FORM
function showRegistrationForm(){
	if(BTLJ("#btl-integrated").length){
		window.location.href=BTLJ("#btl-integrated").val();
		return;
	}
	if(BTLJ('#btl-content-registration').size() == 0){
		return;
	}
	BTLJ('.btl-panel span').removeClass("active");
	BTLJ.simpleModal.close();
	var el = '#btl-panel-registration';
	var containerWidth = 0;
	var containerHeight = 0;
	containerHeight = "auto";
	containerWidth = "auto";
	if(containerWidth>BTLJ(window).width()){
		containerWidth = BTLJ(window).width();
	}
	if(btlOpt.EFFECT == "btl-modal"){
		BTLJ(el).addClass("active");
		BTLJ("#btl-content > div").slideUp();
		BTLJ("#btl-content-registration").simpleModal({
			overlayClose:true,
			persist :true,
			autoPosition:true,
			fixed: BTLJ(window).width()>500,
			onOpen: function (dialog) {
				dialog.overlay.fadeIn();
				dialog.container.show();
				dialog.data.show();		
			},
			onClose: function (dialog) {
				dialog.overlay.fadeOut(function () {
					dialog.container.hide();
					dialog.data.hide();		
					BTLJ.simpleModal.close();
					BTLJ('.btl-panel span').removeClass("active");
				});
			},
			containerCss:{
				height:containerHeight,
				width:containerWidth
			}
		})
	}
	else
	{	
		setFPosition();
		BTLJ("#btl-content > div").each(function(){
			if(this.id=="btl-content-registration")
			{
				if(BTLJ(this).is(":hidden")){
					BTLJ(el).addClass("active");
					BTLJ(this).slideDown();
					}
				else{
					BTLJ(this).slideUp();								
					BTLJ(el).removeClass("active");
					}
			}
			else{
				if(BTLJ(this).is(":visible")){						
					BTLJ(this).slideUp();
					BTLJ('#btl-panel-login').removeClass("active");
				}
			}
			
		})
	}
	return false;
}

// SHOW PROFILE (LOGGED MODULES)
function showProfile(){
	setFPosition();
	var el = '#btl-panel-profile';
	BTLJ("#btl-content > div").each(function(){
		if(this.id=="btl-content-profile")
		{
			if(BTLJ(this).is(":hidden")){
				BTLJ(el).addClass("active");
				BTLJ(this).slideDown();
				}
			else{
				BTLJ(this).slideUp();	
				BTLJ('.btl-panel span').removeClass("active");
			}				
		}
		else{
			if(BTLJ(this).is(":visible")){						
				BTLJ(this).slideUp();
				BTLJ('.btl-panel span').removeClass("active");	
			}
		}
		
	})
}

// AJAX REGISTRATION
function registerAjax(){

	BTLJ("#btl-registration-error").hide();
	 BTLJ(".btl-error-detail").hide();
	if(BTLJ("#btl-input-name").val()==""){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.REQUIRED_NAME).show();
		BTLJ("#btl-input-name").focus();
		return false;
	}
	if(BTLJ("#btl-input-username1").val()==""){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.REQUIRED_USERNAME).show();
		BTLJ("#btl-input-username1").focus();
		return false;
	}
	if(BTLJ("#btl-input-password1").val()==""){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.REQUIRED_PASSWORD).show();
		BTLJ("#btl-input-password1").focus();
		return false;
	}
	if(BTLJ("#btl-input-password2").val()==""){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.REQUIRED_VERIFY_PASSWORD).show();
		BTLJ("#btl-input-password2").focus();
		return false;
	}
	if(BTLJ("#btl-input-password2").val()!=BTLJ("#btl-input-password1").val()){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.PASSWORD_NOT_MATCH).show();
		BTLJ("#btl-input-password2").focus().select();
		BTLJ("#btl-registration-error").show();
		return false;
	}
	if(BTLJ("#btl-input-email1").val()==""){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.REQUIRED_EMAIL).show();
		BTLJ("#btl-input-email1").focus();
		return false;
	}
	var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;
	if(!emailRegExp.test(BTLJ("#btl-input-email1").val())){		
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.EMAIL_INVALID).show();
		BTLJ("#btl-input-email1").focus().select();
		return false;
	}
	if(BTLJ("#btl-input-email2").val()==""){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.REQUIRED_VERIFY_EMAIL).show();
		BTLJ("#btl-input-email2").focus().select();
		return false;
	}
	if(BTLJ("#btl-input-email1").val()!=BTLJ("#btl-input-email2").val()){
		BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.EMAIL_NOT_MATCH).show();;
		BTLJ("#btl-input-email2").focus().select();
		return false;
	}
	if(btlOpt.RECAPTCHA =="1"){
		if(BTLJ('#recaptcha_response_field').length && BTLJ('#recaptcha_response_field').val()==''){
			BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.CAPTCHA_REQUIRED).show();
			BTLJ('#recaptcha_response_field').focus();
			return false;
		}	
	}else if(btlOpt.RECAPTCHA =="2"){
		if(BTLJ('#btl-captcha').length && BTLJ('#btl-captcha').val()==''){
			BTLJ("#btl-registration-error").html(btlOpt.MESSAGES.CAPTCHA_REQUIRED).show();
			BTLJ('#btl-captcha').focus();
			return false;
		}	
	}	
	 
	
	var datasubmit = BTLJ('#btl-content-registration form').serialize();
	
	
	BTLJ.ajax({
		   type: "POST",
		   beforeSend:function(){
			   BTLJ("#btl-register-in-process").show();			   
		   },
		   url: btlOpt.BT_AJAX,
		   data: datasubmit,
		   success: function(html){				  
			   //if html contain "Registration failed" is register fail
			  BTLJ("#btl-register-in-process").hide();	
			  if(html.indexOf('$error$')!= -1){
				  BTLJ("#btl-registration-error").html(html.replace('$error$',''));  
				  BTLJ("#btl-registration-error").show();
				  if(btlOpt.RECAPTCHA =="1"){
					  if(typeof(Recaptcha) != 'undefined'){
						Recaptcha.reload();
					  }else if(typeof(grecaptcha) != 'undefined'){
						  grecaptcha.reset('bt-login-recaptcha');
					  }
				  }else if(btlOpt.RECAPTCHA =="2"){
					BTLJ.ajax({
						type: "post",
						url: btlOpt.BT_AJAX,
						data: 'bttask=reload_captcha',
						success: function(html){
							BTLJ('#recaptcha img').attr('src', html);
						}
					});
				  }
				  
			   }else{				   
				   BTLJ(".btl-formregistration").children("div").hide();
				   BTLJ("#btl-success").html(html);	
				   BTLJ("#btl-success").show();	
				   setTimeout(function() {window.location.reload();},7000);

			   }
		   },
		   error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus + ': Ajax request failed');
		   }
		});
		return false;
}

// AJAX LOGIN
function loginAjax(){
	if(BTLJ("#btl-input-username").val()=="") {
		showLoginError(btlOpt.MESSAGES.REQUIRED_USERNAME);
		return false;
	}
	if(BTLJ("#btl-input-password").val()==""){
		showLoginError(btlOpt.MESSAGES.REQUIRED_PASSWORD);
		return false;
	}
	var token = BTLJ('.btl-buttonsubmit input:last').attr("name");
	var value_token = encodeURIComponent(BTLJ('.btl-buttonsubmit input:last').val()); 
	var datasubmit= "bttask=login&username="+encodeURIComponent(BTLJ("#btl-input-username").val())
	+"&passwd=" + encodeURIComponent(BTLJ("#btl-input-password").val())
	+ "&"+token+"="+value_token
	+"&return="+ encodeURIComponent(BTLJ("#btl-return").val());
	
	if(BTLJ("#btl-checkbox-remember").is(":checked")){
		datasubmit += '&remember=yes';
	}
	
	BTLJ.ajax({
	   type: "POST",
	   beforeSend:function(){
		   BTLJ("#btl-login-in-process").show();
		   BTLJ("#btl-login-in-process").css('height',BTLJ('#btl-content-login').outerHeight()+'px');
		   
	   },
	   url: btlOpt.BT_AJAX,
	   data: datasubmit,
	   success: function (html, textstatus, xhrReq){
		  if(html == "1" || html == 1){
			   window.location.href=btlOpt.BT_RETURN;
		   }else{
			   if(html.indexOf('</head>')==-1){		   
				   showLoginError(btlOpt.MESSAGES.E_LOGIN_AUTHENTICATE);
				}
				else
				{
					if(html.indexOf('btl-panel-profile')==-1){ 
						showLoginError('Another plugin has redirected the page on login, Please check your plugins system');
					}
					else
					{
						window.location.href=btlOpt.BT_RETURN;
					}
				}
		   }
	   },
	   error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert(textStatus + ': Ajax request failed!');
	   }
	});
	return false;
}
function showLoginError(notice,reload){
	BTLJ("#btl-login-in-process").hide();
	BTLJ("#btl-login-error").html(notice);
	BTLJ("#btl-login-error").show();
	if(reload){
		setTimeout(function() {window.location.reload();},5000);
	}
}

