<?php
/**
 * ------------------------------------------------------------------------
 * Plugin Ajax JA 123rf
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.Jacontenttype
 * @since       1.5
 */
class PlgAjax123rf extends JPlugin
{
	protected $layoutBasePath;
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->layoutBasePath = JPATH_ROOT . '/plugins/ajax/123rf/layouts';
	}

	public function onAjax123rf() {
		$app = JFactory::getApplication();
		$view = $app->input->getCmd('view', 'index');
		$format = $app->input->getCmd('format', '');
		$output = $this->loadTemplate($view);

		if ($format == 'json') {
			return $output;
		} else {
			echo $output;
			JFactory::getApplication()->close();
		}
	}

	function loadTemplate($tmpl) {
		$template = dirname(__FILE__) . '/layouts/' . $tmpl . '.php';
		if (! is_file ( $template ))
			return '';
		$buffer = ob_get_clean ();
		ob_start ();
		include ($template);
		$content = ob_get_clean ();
		ob_start ();
		return $content;
	}

    function getOptions($option){
        if(!empty($_POST[$option])){
            foreach($_POST[$option] as $value){
                $options[] = $value;
            }
        return implode(',', $options);
        }else{
            return false;
        }
    }

}
