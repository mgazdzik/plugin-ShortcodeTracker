<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Menu\MenuReporting;

/**
 * @codeCoverageIgnore
 * @package Piwik\Plugins\ShortcodeTracker
 */
class Menu extends \Piwik\Plugin\Menu
{
    public function configureReportingMenu(MenuReporting $menu)
    {
        $menu->addItem('Shortcodes', 'ShortcodeTracker_menu_generateShortcode', $this->urlForDefaultAction(), $orderId = 30);
        $menu->addItem('Shortcodes', 'Shortcode usage', $this->urlForAction('getShortcodeUsageReport'), $orderId = 40);
        $menu->addItem('Shortcodes', 'Most shortened pages', $this->urlForAction('getShortenedPagesReport'), $orderId = 50);
        $menu->addItem('Shortcodes', 'External shortcode usage', $this->urlForAction('getExternalShortcodeUsageReport'), $orderId = 60);
        $menu->addItem('Shortcodes', 'Most shortened external pages', $this->urlForAction('getShortenedExternalPagesReport'), $orderId = 70);
    }

}
