<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuReporting;
use Piwik\Menu\MenuTop;
use Piwik\Menu\MenuUser;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureReportingMenu(MenuReporting $menu)
    {
        $menu->addItem('Shortcodes', 'ShortcodeTracker_menu_generateShortcode', $this->urlForDefaultAction(), $orderId = 30);
        $menu->addItem('Shortcodes', 'Shortcode usage', $this->urlForAction('getShortcodeUsageReport'), $orderId = 40);
    }

}
