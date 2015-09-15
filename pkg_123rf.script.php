<?php
/**
 * @package      123RF
 *
 * @author       JoomlArt
 * @copyright    Copyright (C) 2012-2013. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
 */

defined('_JEXEC') or die();


class pkg_123rfInstallerScript
{
    /**
     * Called before any type of action
     *
     * @param     string              $route      Which action is happening (install|uninstall|discover_install)
     * @param     jadapterinstance    $adapter    The object responsible for running this script
     *
     * @return    boolean                         True on success
     */
    public function preflight($route, JAdapterInstance $adapter)
    {
        return true;
    }


    /**
     * Called after any type of action
     *
     * @param     string              $route      Which action is happening (install|uninstall|discover_install)
     * @param     jadapterinstance    $adapter    The object responsible for running this script
     *
     * @return    boolean                         True on success
     */
    public function postflight($route, JAdapterInstance $adapter)
    {
        // We only need to perform this if the extension is being installed, not updated
        if ( $route == 'install' || $route == 'update' )
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $fields = array(
                $db->quoteName('enabled') . ' = ' . (int) 1
            );

            $conditions = array(
                $db->quoteName('element') . ' like ' . $db->quote('%123rf%'),
                $db->quoteName('type') . ' = ' . $db->quote('plugin')
            );

            $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

            $db->setQuery($query);
            $db->execute();
        }
        return true;
    }
}
