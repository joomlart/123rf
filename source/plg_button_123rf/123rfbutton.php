<?php
/**
 * ------------------------------------------------------------------------
 * Plugin Button JA 123rf
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Editor Article buton
 *
 * @package Joomla.Plugin
 * @subpackage Editors-xtd.article
 * @since 1.5
 */
class plgButton123rfbutton extends JPlugin {

	/**
	 * Constructor
	 *
	 * @access protected
	 * @param object $subject
	 *        	The object to observe
	 * @param array $config
	 *        	An array that holds the plugin configuration
	 * @since 1.5
	 */
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage ();
	}

	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	function onDisplay($name) {
		$app = JFactory::getApplication ();
		if (! $app->isAdmin ()) {
			return false;
		}


		$doc = JFactory::getDocument();
        $doc->addStyleSheet(JURI::root() .'plugins/editors-xtd/123rfbutton/assets/css/style.css');


		$base_url = JURI::base ();
		if ($app->isAdmin ()) {
			$base_url = dirname ( $base_url );
		}

		// $current_url = 'index.php?'.$_SERVER['QUERY_STRING'];
		$option = JRequest::getVar ( 'option' );
		$view = JRequest::getVar ( 'view', 'form' );
		$layout = JRequest::getVar ( 'layout', 'edit', '' );

		if ($app->isAdmin ()) {
			$view = 'article';
		} else {
			if ($option != 'com_content') {
				$view = 'form';
				$layout = 'edit';
			}
		}
		$url = '?option=com_ajax&plugin=123rf&format=html';

		JHtml::_ ( 'behavior.modal' );

		$button = new JObject ();
		$button->class = 'btn';
		$button->set ( 'text', '123rf' );
		$button->set ( 'link', $url );
		$button->set ( 'name', 'picture' );
		$button->set ( 'modal', true );
		$button->set ( 'options', "{handler: 'iframe', size: {x: 1000, y: 600}, classWindow: 'ja123rf-window'}" );
		return $button;
	}
}
