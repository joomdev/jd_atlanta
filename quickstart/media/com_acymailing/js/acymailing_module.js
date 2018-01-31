/**
 * @package    AcyMailing for Joomla!
 * @version    5.8.1
 * @author     acyba.com
 * @copyright  (C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

function submitacymailingform(task, formName, allowSpecialChars){
	var varform = document[formName];
	if(allowSpecialChars == 0) {
		var filterEmail = /^([a-z0-9_'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+$/i;
	}else{
		var filterEmail = /\@/i;
	}

	if(!varform.elements){
		if(varform[0].elements['user[email]'] && varform[0].elements['user[email]'].value && filterEmail.test(varform[0].elements['user[email]'].value)){
			varform = varform[0];
		}else{
			varform = varform[varform.length - 1];
		}
	}

	if(task != 'optout'){
		nameField = varform.elements['user[name]'];
		if(nameField && typeof acymailing != 'undefined' && (((typeof acymailing['level'] == 'undefined' || acymailing['level'] != 'enterprise') && ((nameField.value == acymailing['NAMECAPTION'] || (typeof acymailing['excludeValues' + formName] != 'undefined' && typeof acymailing['excludeValues' + formName]['name'] != 'undefined' && nameField.value == acymailing['excludeValues' + formName]['name'])) || nameField.value.replace(/ /g, "").length < 2)) || (typeof acymailing['level'] != 'undefined' && acymailing['level'] == 'enterprise' && typeof acymailing['reqFields' + formName] != 'undefined' && acymailing['reqFields' + formName].indexOf('name') >= 0 && ((nameField.value == acymailing['NAMECAPTION'] || (typeof acymailing['excludeValues' + formName] != 'undefined' && typeof acymailing['excludeValues' + formName]['name'] != 'undefined' && nameField.value == acymailing['excludeValues' + formName]['name'])) || nameField.value.replace(/ /g, "").length < 2)))){
			alert(acymailing['NAME_MISSING']);
			nameField.className = nameField.className + ' invalid';
			return false;
		}
	}

	var emailField = varform.elements['user[email]'];
	if(emailField){
		if(typeof acymailing == 'undefined' || emailField.value != acymailing['EMAILCAPTION']) emailField.value = emailField.value.replace(/ /g, "");
		if(!emailField || (typeof acymailing != 'undefined' && (emailField.value == acymailing['EMAILCAPTION'] || (typeof acymailing['excludeValues' + formName] != 'undefined' && typeof acymailing['excludeValues' + formName]['email'] != 'undefined' && emailField.value == acymailing['excludeValues' + formName]['email']))) || !filterEmail.test(emailField.value)){
			if(typeof acymailing != 'undefined'){
				alert(acymailing['VALID_EMAIL']);
			}
			emailField.className = emailField.className + ' invalid';
			return false;
		}
	}

	if(varform.elements['hiddenlists'].value.length < 1){
		var listschecked = false;
		var alllists = varform.elements['subscription[]'];
		if(alllists && (typeof alllists.value == 'undefined' || alllists.value.length == 0)){
			for(b = 0; b < alllists.length; b++){
				if(alllists[b].checked) listschecked = true;
			}
			if(!listschecked){
				alert(acymailing['NO_LIST_SELECTED']);
				return false;
			}
		}
	}


	if(task != 'optout' && typeof acymailing != 'undefined'){
		if(typeof acymailing['reqFields' + formName] != 'undefined' && acymailing['reqFields' + formName].length > 0){

			for(var i = 0; i < acymailing['reqFields' + formName].length; i++){
				elementName = 'user[' + acymailing['reqFields' + formName][i] + ']';
				elementToCheck = varform.elements[elementName];
				if(elementToCheck){
					var isValid = false;
					if(typeof elementToCheck.value != 'undefined'){
						if(elementToCheck.value == ' ' && typeof varform[elementName + '[]'] != 'undefined'){
							if(varform[elementName + '[]'].checked){
								isValid = true;
							}else{
								for(var a = 0; a < varform[elementName + '[]'].length; a++){
									if((varform[elementName + '[]'][a].checked || varform[elementName + '[]'][a].selected) && varform[elementName + '[]'][a].value.length > 0) isValid = true;
								}
							}
						}else{
							if(elementToCheck.value.replace(/ /g, "").length > 0){
								if(typeof acymailing['excludeValues' + formName] == 'undefined' || typeof acymailing['excludeValues' + formName][acymailing['reqFields' + formName][i]] == 'undefined' || acymailing['excludeValues' + formName][acymailing['reqFields' + formName][i]] != elementToCheck.value) isValid = true;
							}
						}
					}else{
						for(var a = 0; a < elementToCheck.length; a++){
							if(elementToCheck[a].checked && elementToCheck[a].value.length > 0) isValid = true;
						}
					}
					if((elementToCheck.length >= 1 && (elementToCheck[0].parentElement.parentElement.style.display == 'none' || elementToCheck[0].parentElement.parentElement.parentElement.style.display == 'none')) || (typeof elementToCheck.length == 'undefined' && (elementToCheck.parentElement.parentElement.style.display == 'none' || elementToCheck.parentElement.parentElement.parentElement.style.display == 'none'))){
						isValid = true;
					}
					if(!isValid){
						elementToCheck.className = elementToCheck.className + ' invalid';
						alert(acymailing['validFields' + formName][i]);
						return false;
					}
				}else{
					if((varform.elements[elementName + '[day]'] && varform.elements[elementName + '[day]'].value < 1) || (varform.elements[elementName + '[month]'] && varform.elements[elementName + '[month]'].value < 1) || (varform.elements[elementName + '[year]'] && varform.elements[elementName + '[year]'].value < 1902)){
						if(varform.elements[elementName + '[day]'] && varform.elements[elementName + '[day]'].value < 1) varform.elements[elementName + '[day]'].className = varform.elements[elementName + '[day]'].className + ' invalid';
						if(varform.elements[elementName + '[month]'] && varform.elements[elementName + '[month]'].value < 1) varform.elements[elementName + '[month]'].className = varform.elements[elementName + '[month]'].className + ' invalid';
						if(varform.elements[elementName + '[year]'] && varform.elements[elementName + '[year]'].value < 1902) varform.elements[elementName + '[year]'].className = varform.elements[elementName + '[year]'].className + ' invalid';
						alert(acymailing['validFields' + formName][i]);
						return false;
					}

					if((varform.elements[elementName + '[country]'] && varform.elements[elementName + '[country]'].value < 1) || (varform.elements[elementName + '[num]'] && (varform.elements[elementName + '[num]'].value < 3 || (typeof acymailing['excludeValues' + formName] != 'undefined' && typeof acymailing['excludeValues' + formName][acymailing['reqFields' + formName][i]] != 'undefined' && acymailing['excludeValues' + formName][acymailing['reqFields' + formName][i]] == varform.elements[elementName + '[num]'].value)))){
						if((varform.elements[elementName + '[country]'] && varform.elements[elementName + '[country]'].parentElement.parentElement.style.display != 'none') || (varform.elements[elementName + '[num]'] && varform.elements[elementName + '[num]'].parentElement.parentElement.style.display != 'none')){
							if(varform.elements[elementName + '[country]'] && varform.elements[elementName + '[country]'].value < 1) varform.elements[elementName + '[country]'].className = varform.elements[elementName + '[country]'].className + ' invalid';
							if(varform.elements[elementName + '[num]'] && (varform.elements[elementName + '[num]'].value < 3 || (typeof acymailing['excludeValues' + formName] != 'undefined' && typeof acymailing['excludeValues' + formName][acymailing['reqFields' + formName][i]] != 'undefined' && acymailing['excludeValues' + formName][acymailing['reqFields' + formName][i]] == varform.elements[elementName + '[num]'].value))) varform.elements[elementName + '[num]'].className = varform.elements[elementName + '[num]'].className + ' invalid';
							alert(acymailing['validFields' + formName][i]);
							return false;
						}
					}
				}
			}
		}

		if(typeof acymailing != 'undefined' && typeof acymailing['checkFields' + formName] != 'undefined' && acymailing['checkFields' + formName].length > 0){
			for(var i = 0; i < acymailing['checkFields' + formName].length; i++){
				elementName = 'user[' + acymailing['checkFields' + formName][i] + ']';
				elementtypeToCheck = acymailing['checkFieldsType' + formName][i];
				elementToCheck = varform.elements[elementName].value;
				if(typeof acymailing['excludeValues' + formName] != 'undefined'){
					var excludedValues = acymailing['excludeValues' + formName][acymailing['checkFields' + formName][i]];
					if(typeof excludedValues != 'undefined' && elementToCheck == excludedValues){
						continue;
					}
				}
				switch(elementtypeToCheck){
					case 'number':
						myregexp = new RegExp('^[0-9]*$');
						break;
					case 'letter':
						myregexp = new RegExp('^[A-Za-z\u00C0-\u017F ]*$');
						break;
					case 'letnum':
						myregexp = new RegExp('^[0-9a-zA-Z\u00C0-\u017F ]*$');
						break;
					case 'regexp':
						myregexp = new RegExp(acymailing['checkFieldsRegexp' + formName][i]);
						break;
				}
				if(!myregexp.test(elementToCheck)){
					alert(acymailing['validCheckFields' + formName][i]);
					return false;
				}
			}
		}
	}

	var captchaField = varform.elements['acycaptcha'];
	if(captchaField){
		if(captchaField.value.length < 1){
			if(typeof acymailing != 'undefined'){
				alert(acymailing['CAPTCHA_MISSING']);
			}
			captchaField.className = captchaField.className + ' invalid';
			return false;
		}
	}

	if(task != 'optout'){
		var termsandconditions = varform.terms;
		if(termsandconditions && !termsandconditions.checked){
			if(typeof acymailing != 'undefined'){
				alert(acymailing['ACCEPT_TERMS']);
			}
			termsandconditions.className = termsandconditions.className + ' invalid';
			return false;
		}

		if(typeof acymailing != 'undefined' && typeof acymailing['excludeValues' + formName] != 'undefined'){
			for(var fieldName in acymailing['excludeValues' + formName]){
				if(!acymailing['excludeValues' + formName].hasOwnProperty(fieldName)) continue;
				if(!varform.elements['user[' + fieldName + ']'] || varform.elements['user[' + fieldName + ']'].value != acymailing['excludeValues' + formName][fieldName]) continue;

				varform.elements['user[' + fieldName + ']'].value = '';
			}
		}
	}

	if(typeof ga != 'undefined' && task != 'optout'){
		ga('send', 'pageview', 'subscribe');
	}else if(typeof ga != 'undefined'){
		ga('send', 'pageview', 'unsubscribe');
	}

	taskField = varform.task;
	taskField.value = task;

	if(!varform.elements['ajax'] || !varform.elements['ajax'].value || varform.elements['ajax'].value == '0'){
		varform.submit();
		return false;
	}

	var form = document.getElementById(formName);

	var formData = new FormData(form);
	form.className += ' acymailing_module_loading';
	form.style.filter = "alpha(opacity=50)";
	form.style.opacity = "0.5";

	var xhr = new XMLHttpRequest();
	xhr.open('POST', form.action);
	xhr.onload = function(){
		var message = 'Ajax Request Failure';
		var type = 'error';

		if (xhr.status === 200){
			var response = JSON.parse(xhr.responseText);
			message = response.message;
			type = response.type;
		}
		acymailingDisplayAjaxResponse(decodeURIComponent(message), type, formName);
	};
	xhr.send(formData);

	return false;
}

function acymailingDisplayAjaxResponse(message, type, formName){
	var toggleButton = document.getElementById('acymailing_togglemodule_' + formName);

	if(toggleButton && toggleButton.className.indexOf('acyactive') > -1){
		var wrapper = toggleButton.parentElement.parentElement.childNodes[1];
		wrapper.style.height = '';
	}

	var responseContainer = document.querySelectorAll('#acymailing_fulldiv_' + formName + ' .responseContainer')[0];

	if(typeof responseContainer == 'undefined'){
		responseContainer = document.createElement('div');
		var fulldiv = document.getElementById('acymailing_fulldiv_' + formName);

		if(fulldiv.firstChild){
			fulldiv.insertBefore(responseContainer, fulldiv.firstChild);
		}else{
			fulldiv.appendChild(responseContainer);
		}

		oldContainerHeight = '0px';
	}else{
		oldContainerHeight = responseContainer.style.height;
	}

	responseContainer.className = 'responseContainer';

	var form = document.getElementById(formName);

	var elclass = form.className;
	var rmclass = 'acymailing_module_loading';
	var res = elclass.replace(' '+rmclass, '', elclass);
	if(res == elclass) res = elclass.replace(rmclass+' ', '', elclass);
	if(res == elclass) res = elclass.replace(rmclass, '', elclass);
	form.className = res;

	responseContainer.innerHTML = message;

	if(type == 'success'){
		responseContainer.className += ' acymailing_module_success';
	}else{
		responseContainer.className += ' acymailing_module_error';
		form.style.opacity = "1";
	}

	newContainerHeight = responseContainer.style.height;

	form.style.display = 'none';
	responseContainer.className += ' slide_open';
}

