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

class acyeditorHelper{

	var $width = '95%';

	var $height = '600';

	var $cols = 100;

	var $rows = 30;

	var $editor = null;

	var $name = '';

	var $content = '';

	var $editorConfig = array();

	var $editorContent = '';

	function __construct(){

		$config = acymailing_config();
		$this->editor = $config->get('editor',null);
		if(empty($this->editor)) $this->editor = null;

		$this->myEditor = JFactory::getEditor($this->editor);
		$this->myEditor->initialise();

		$name = $this->myEditor->get('_name');
		if(ACYMAILING_J16){
			if($name == 'tinymce'){
				$this->editorConfig['extended_elements'] = 'table[background|cellspacing|cellpadding|width|align|bgcolor|border|style|class|id],tr[background|width|bgcolor|style|class|id|valign],td[background|width|align|bgcolor|valign|colspan|rowspan|height|style|class|id|nowrap]';
			}
		}
	}

	function setTemplate($id){
		if(empty($id)) return;

		$cssurl = rtrim(acymailing_rootURI(),'/').'/'.(acymailing_isAdmin() ? 'administrator/index.php?option=com_acymailing&ctrl=template':'index.php?option=com_acymailing&ctrl=fronttemplate').'&task=load&tempid='.$id.'&time='.time();

		$name = $this->myEditor->get('_name');

		$classTemplate = acymailing_get('class.template');
		$filepath = $classTemplate->createTemplateFile($id);

		if($name == 'tinymce'){
			$this->editorConfig['content_css_custom'] = $cssurl;
			$this->editorConfig['content_css'] = '0';
		}elseif($name=='jckeditor' || $name=='fckeditor'){
			$this->editorConfig['content_css_custom'] = $filepath;
			$this->editorConfig['content_css'] = '0';
			$this->editorConfig['editor_css'] = '0';
		}else{
			$fileurl = 'media/com_acymailing/templates/css/template_'.$id.'.css?time='.time();
			$this->editorConfig['custom_css_url'] = $cssurl;
			$this->editorConfig['custom_css_file'] = $fileurl;
			$this->editorConfig['custom_css_path'] = $filepath;
			acymailing_setVar('acycssfile', $fileurl);
		}
	}

	function prepareDisplay(){
		$this->content = htmlspecialchars($this->content, ENT_COMPAT, 'UTF-8');
		ob_start();
		if(!ACYMAILING_J16){
			echo $this->myEditor->display( $this->name,  $this->content ,$this->width, $this->height, $this->cols, $this->rows,array('pagebreak', 'readmore'),$this->editorConfig ) ;
		}else{
			echo $this->myEditor->display( $this->name,  $this->content ,$this->width, $this->height, $this->cols, $this->rows,array('pagebreak', 'readmore'),null,'com_content',null,$this->editorConfig ) ;
		}

		$this->editorContent = ob_get_clean();
	}


	function setDescription(){
		$this->width = 700;
		$this->height = 200;
		$this->cols = 80;
		$this->rows = 10;
	}

	function setContent($var){
		$name = $this->myEditor->get('_name');
		if(method_exists($this->myEditor,'setContent')){
			$function = "try{ Joomla.editors.instances['".$this->name."'].setValue(".$var."); }catch(err){alert('Error using the setContent function of the wysiwyg editor')} ";
			$function = "try{".$this->myEditor->setContent($this->name,$var)." }catch(err){".$function."}";
		}else{
			$function = "alert('There is no setContent method defined for this editor');";
		}

		if(!empty($name)){
			if($name == 'jce'){
				return " try{JContentEditor.setContent('".$this->name."', $var ); }catch(err){try{WFEditor.setContent('".$this->name."', $var )}catch(err){".$function."} }";
			}
			if($name == 'fckeditor'){
				return " try{FCKeditorAPI.GetInstance('".$this->name."').SetHTML( $var ); }catch(err){".$function."} ";
			}
			if($name == 'jckeditor'){
				return " try{oEditor.setData(".$var.");}catch(err){(!oEditor) ? CKEDITOR.instances.".$this->name.".setData($var) : oEditor.insertHtml = " .  $var.'}';
			}
			if($name == 'ckeditor'){
				return " try{CKEDITOR.instances.".$this->name.".setData( $var ); }catch(err){".$function."} ";
			}
			if($name == 'artofeditor'){
				return " try{CKEDITOR.instances.".$this->name.".setData( $var ); }catch(err){".$function."} ";
			}
			if($name == 'tinymce'){
				return ' try{ Joomla.editors.instances["'.$this->name.'"].setValue('.$var.'); }catch(err){'.$function.'} ';
			}
		}

		return $function;
	}

	function setEditorStylesheet($tempid){
		if(acymailing_isAdmin()) $cssurl = 'administrator/index.php?option=com_acymailing&ctrl=template';
		else $cssurl = 'index.php?option=com_acymailing&ctrl=fronttemplate';

		$cssurl = rtrim(acymailing_rootURI(),'/').'/'.$cssurl.'&task=load&time='.time().'&tempid=';

		$function = 'if('.$tempid.' !== 0){
						try{
							setEditorStylesheet(\''.$this->name.'\',\''.$cssurl.'\'+'.$tempid.',\'media/com_acymailing/templates/css/template_\'+'.$tempid.'+\'.css\');
						}catch(err){
							var iframe = document.getElementById("'.$this->name.'_ifr");
							if(typeof iframe != undefined && iframe){
								var css = iframe.contentDocument.querySelector(\'link[href*="media/com_acymailing/templates/css/template_"]\');
								if(typeof css != undefined && css){
									css.href = css.href.replace(/template_\d{1,10}.css/, "template_"+'.$tempid.'+".css");
								}else{
									var css = iframe.contentDocument.querySelector(\'link[href*="com_acymailing&ctrl=template&task=load&tempid="]\');
									if(typeof css != undefined && css){
										css.href = css.href.replace(/&tempid=\d{1,10}&time/, "&tempid="+'.$tempid.'+"&time");
									}
								}
							}
						}
					}';

		return $function;
	}

	function getContent(){
		return $this->myEditor->getContent($this->name);
	}

	function display(){
		if(empty($this->editorContent)) $this->prepareDisplay();
		return $this->editorContent;
	}

	function jsCode(){
		return $this->myEditor->save( $this->name );
	}

}//endclass
