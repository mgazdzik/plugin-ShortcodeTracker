<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Piwik;
use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;

/**
 * Defines Settings for ShortcodeTracker.
 *
 * Usage like this:
 * $settings = new Settings('ShortcodeTracker');
 * $settings->autoRefresh->getValue();
 * $settings->metric->getValue();
 */
class Settings extends \Piwik\Plugin\Settings
{
    /** @var UserSetting */
    public $shortenerUrl;

    protected function init()
    {
        $this->setIntroduction(Piwik::translate('ShortcodeTracker_settings_introduction'));

        $this->createShortenerUrlSetting();
    }

    private function createShortenerUrlSetting()
    {
        $this->shortenerUrl = new SystemSetting(ShortcodeTracker::SHORTENER_URL_SETTING, Piwik::translate('ShortcodeTracker_settings_setting_name'));
        $this->shortenerUrl->readableByCurrentUser = true;
        $this->shortenerUrl->uiControlType = static::CONTROL_TEXT;
        $this->shortenerUrl->description = Piwik::translate('ShortcodeTracker_settings_setting_description');
        $this->shortenerUrl->defaultValue = ShortcodeTracker::DEFAULT_SHORTENER_URL;

        $this->addSetting($this->shortenerUrl);
    }

    public function getSetting($name)
    {
        $setting = parent::getSetting($name);
        $value = $setting->getValue();
        if (substr($value, -1) !== '/') {
            return $value . "/";
        }

        return $value;
    }
}
