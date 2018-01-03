<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Common;
use Piwik\Plugins\ShortcodeTracker\Component\ShortcodeValidator;
use Piwik\View;

/**
 * @codeCoverageIgnore
 */
class Controller extends \Piwik\Plugin\Controller
{
    public function showShortcodePopup()
    {
        $shortcode = Common::getRequestVar('shortcode');
        $pluginSettings = new SystemSettings();
        $baseUrl = $pluginSettings->getSlashedSetting(ShortcodeTracker::SHORTENER_URL_SETTING);

        $view = new View('@ShortcodeTracker/singleShortcode');
        $codeValidator = new ShortcodeValidator();
        if ($codeValidator->validate($shortcode) === true) {
            $view->header = 'ShortcodeTracker_generated_shortcode_is';
            $view->text = $baseUrl . $shortcode;
            $view->containerClass = 'alert-success';
        } else {
            $view->header = 'ShortcodeTracker_unable_to_generate_shortcode';
            $view->text = 'ShortcodeTracker_provide_valid_url';
            $view->containerClass = 'alert-warning';
        }

        return $view->render();
    }
}
