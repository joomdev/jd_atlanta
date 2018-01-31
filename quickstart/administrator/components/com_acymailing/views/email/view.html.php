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


class EmailViewEmail extends acymailingView{
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();


		parent::display($tpl);
	}

	function form(){
		$mailid = acymailing_getCID('mailid');
		if(empty($mailid)) $mailid = acymailing_getVar('string', 'mailid');

		$mailClass = acymailing_get('class.mail');
		$mail = $mailClass->get($mailid);

		if(empty($mail)){
			$config = acymailing_config();

			$mail = new stdClass();
			$mail->created = time();
			$mail->fromname = $config->get('from_name');
			$mail->fromemail = $config->get('from_email');
			$mail->replyname = $config->get('reply_name');
			$mail->replyemail = $config->get('reply_email');
			$mail->subject = '';
			$mail->type = acymailing_getVar('string', 'type');
			$mail->published = 1;
			$mail->visible = 0;
			$mail->html = 1;
			$mail->body = '';
			$mail->altbody = '';
			$mail->tempid = 0;
			$mail->alias = '';
		};


		$values = new stdClass();
		$values->maxupload = (acymailing_bytes(ini_get('upload_max_filesize')) > acymailing_bytes(ini_get('post_max_size'))) ? ini_get('post_max_size') : ini_get('upload_max_filesize');


		$toggleClass = acymailing_get('helper.toggle');

		if(acymailing_isAdmin()) {
			$acyToolbar = acymailing_get('helper.toolbar');
			$acyToolbar->custom('', acymailing_translation('ACY_TEMPLATES'), 'template', false, 'displayTemplates(); return false;');
			$acyToolbar->custom('', acymailing_translation('TAGS'), 'tag', false, 'try{IeCursorFix();}catch(e){}; displayTags(); return false;');
			$acyToolbar->divider();
			$acyToolbar->custom('test', acymailing_translation('SEND_TEST'), 'send', false);
			$acyToolbar->custom('apply', acymailing_translation('ACY_APPLY'), 'apply', false);
			$acyToolbar->setTitle(acymailing_translation('ACY_EDIT'));
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}

		$editor = acymailing_get('helper.editor');
		$editor->setTemplate($mail->tempid);
		$editor->name = 'editor_body';
		$editor->content = $mail->body;

		$js = "function updateAcyEditor(htmlvalue){";
		$js .= 'if(htmlvalue == \'0\'){window.document.getElementById("htmlfieldset").style.display = \'none\'}else{window.document.getElementById("htmlfieldset").style.display = \'block\'}';
		$js .= '}';

		$script = '
		var attachmentNb = 1;
		function addFileLoader(){
			if(attachmentNb > 9) return;
			window.document.getElementById("attachmentsdiv"+attachmentNb).style.display = "";
			attachmentNb++;
		}';

		if(!ACYMAILING_J16){
			$script .= 'function submitbutton(pressbutton){
						if (pressbutton == \'cancel\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$script .= 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton == \'cancel\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}
		$script .= 'if(window.document.getElementById("subject").value.length < 2){alert(\''.acymailing_translation('ENTER_SUBJECT', true).'\'); return false;}';
		$script .= $editor->jsCode();
		if(!ACYMAILING_J16){
			$script .= 'submitform( pressbutton );} ';
		}else{
			$script .= 'Joomla.submitform(pressbutton,document.adminForm);}; ';
		}

		$script .= "var zoneToTag = 'editor';
		function insertTag(tag){
			if(zoneToTag == 'editor'){
				try{
					if(window.parent.tinymce){ parentTinymce = window.parent.tinymce; window.parent.tinymce = false; }
					jInsertEditorText(tag,'editor_body');
					if(typeof parentTinymce !== 'undefined'){ window.parent.tinymce = parentTinymce; }
					document.getElementById('iframetag').style.display = 'none';
					displayTags();
					return true;
				} catch(err){
					alert('Your editor does not enable AcyMailing to automatically insert the tag, please copy/paste it manually in your Newsletter');
					return false;
				}
			}else{
				try{
					simpleInsert(zoneToTag, tag);
					return true;
				} catch(err){
					alert('Error inserting the tag in the '+ zoneToTag + 'zone. Please copy/paste it manually in your Newsletter.');
					return false;
				}
			}
		}

		function simpleInsert(myField, myValue) {
			myField = document.getElementById(myField);
			if (document.selection) {
				myField.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
			} else if (myField.selectionStart || myField.selectionStart == '0') {
				var startPos = myField.selectionStart;
				var endPos = myField.selectionEnd;
				myField.value = myField.value.substring(0, startPos)
					+ myValue
					+ myField.value.substring(endPos, myField.value.length);
			} else if (myField.tagName == 'DIV') {
				myField.innerHTML += myValue;
				document.getElementById('subject').value += myValue;
			} else {
				myField.value += myValue;
			}
		}

		document.addEventListener('DOMContentLoaded', function(){
			setTimeout(function() {
				document.getElementById('htmlfieldset').addEventListener('click', function(){
					zoneToTag = 'editor';
				});	

				var ediframe = document.getElementById('htmlfieldset').getElementsByTagName('iframe');
				if(ediframe && ediframe[0]){
					var children = ediframe[0].contentDocument.getElementsByTagName('*');
					for (var i = 0; i < children.length; i++) {
						children[i].addEventListener('click', function(){
							zoneToTag = 'editor';
						});			
					}
				}		
			}, 1000);
		});";

		$typeMail = 'news';
		if(strpos($mail->alias, 'notification') !== false){
			$typeMail = 'notification';
		}

		$iFrame = "'<iframe src=\'index.php?option=com_acymailing&ctrl=".(acymailing_isAdmin() ? '' : 'front')."tag&task=tag&type=".$typeMail."&tmpl=component\' width=\'100%\' height=\'100%\' scrolling=\'auto\'></iframe>'";
		$script .= "var openTag = true;
					function displayTags(){
						var box = document.getElementById('iframetag');
						if(openTag){
							box.innerHTML = ".$iFrame.";
							box.style.display = 'block';
						}else{
							box.style.display = 'none';
						}

						if(openTag){
							box.className = 'slide_open';
						}else{
							box.className = box.className.replace('slide_open', '');
						}
						openTag = !openTag;
					}";

		$iFrame = "'<iframe src=\'index.php?option=com_acymailing&ctrl=".(acymailing_isAdmin() ? '' : 'front')."template&task=theme&tmpl=component\' width=\'100%\' height=\'100%\' scrolling=\'auto\'></iframe>'";
		$script .= "var openTemplate = true;
					function displayTemplates(){
						var box = document.getElementById('iframetemplate');
						if(openTemplate){
							box.innerHTML = ".$iFrame.";
							box.style.display = 'block';
						}else{
							box.style.display = 'none';
						}

						if(openTemplate){
							box.className = 'slide_open';
						}else{
							box.className = box.className.replace('slide_open', '');
						}
						openTemplate = !openTemplate;
					}";

		$script .= "function changeTemplate(newhtml,newtext,newsubject,stylesheet,fromname,fromemail,replyname,replyemail,tempid){
			if(newhtml.length>2){".$editor->setContent('newhtml')."}
			var vartextarea = document.getElementById('altbody');
			if(newtext.length>2) vartextarea.innerHTML = newtext;
			document.getElementById('tempid').value = tempid;

			if(fromname.length>1){document.getElementById('fromname').value = fromname;}
			if(fromemail.length>1){document.getElementById('fromemail').value = fromemail;}
			if(replyname.length>1){document.getElementById('replyname').value = replyname;}
			if(replyemail.length>1){document.getElementById('replyemail').value = replyemail;}
			if(newsubject.length>1){
				var subjectObj = document.getElementById('subject');
				if(subjectObj.tagName.toLowerCase() == 'input'){
					subjectObj.value = newsubject;
				}else{
				    subjectObj.innerHTML = newsubject;
				}
			}

			".$editor->setEditorStylesheet('tempid')."
			document.getElementById('iframetemplate').style.display = 'none';
			displayTemplates();
		}";

		acymailing_addScript(true, $js.$script);

		$this->toggleClass = $toggleClass;
		$this->editor = $editor;
		$this->values = $values;
		$this->mail = $mail;
		$tabs = acymailing_get('helper.acytabs');
		$tabs->setOptions(array('useCookie' => true));
		$this->tabs = $tabs;
	}
}
