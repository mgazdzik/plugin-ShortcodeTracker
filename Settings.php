<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Access;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\API as SitesManagerAPI;
use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;

/**
 * @codeCoverageIgnore
 */
class Settings extends \Piwik\Plugin\Settings
{
    /**
     * @var UserSetting
     */
    public $shortenerUrl;

    /**
     * @var UserSetting
     */
    public $shortenerExternalShortcodeIdsite;

    protected function init()
    {
        $this->setIntroduction(Piwik::translate('ShortcodeTracker_settings_introduction'));

        $this->createShortenerUrlSetting();
        $this->setShortenerIdsiteForExternalShortcodes();
    }

    public function getSlashedSetting($name)
    {
        $setting = parent::getSetting($name);
        $value = $setting->getValue();
        if (substr($value, -1) !== '/') {
            return $value . "/";
        }

        return $value;
    }

    public function isSettingUnspecified($settingName)
    {
        $setting = $this->getSetting($settingName);

        return ($setting->defaultValue === $setting->getValue());

    }

    private function createShortenerUrlSetting()
    {
        $this->shortenerUrl = new SystemSetting(ShortcodeTracker::SHORTENER_URL_SETTING,
                                                Piwik::translate('ShortcodeTracker_settings_shortener_url_setting_name'));
        $this->shortenerUrl->readableByCurrentUser = true;
        $this->shortenerUrl->uiControlType = static::CONTROL_TEXT;
        $this->shortenerUrl->description =
            Piwik::translate('ShortcodeTracker_settings_shortener_url_setting_description');
        $this->shortenerUrl->defaultValue = ShortcodeTracker::DEFAULT_SHORTENER_URL;

        $this->addSetting($this->shortenerUrl);
    }

    private function setShortenerIdsiteForExternalShortcodes()
    {
        $this->shortenerExternalShortcodeIdsite = new SystemSetting(
            ShortcodeTracker::SHORTENER_EXTERNAL_SHORTCODES_IDSITE,
            Piwik::translate('ShortcodeTracker_settings_shortener_external_shortcodes_idsite_setting_name'));
        $sitesManagerApi = SitesManagerAPI::getInstance();
        $this->shortenerExternalShortcodeIdsite->readableByCurrentUser = true;
        $this->shortenerExternalShortcodeIdsite->defaultValue = "0";
        $this->shortenerExternalShortcodeIdsite->uiControlType = static::CONTROL_SINGLE_SELECT;
        $this->shortenerExternalShortcodeIdsite->availableValues =
            Access::doAsSuperUser(function () use ($sitesManagerApi) {
                $sites = $sitesManagerApi->getAllSites();
                $siteIds = array('0' => 'Do not collect external shortcode redirects');
                foreach ($sites as $site) {
                    $siteIds[$site['idsite']] = $site['name'];
                }

                return $siteIds;
            });
        $this->shortenerExternalShortcodeIdsite->description =
            Piwik::translate('ShortcodeTracker_settings_shortener_external_shortcodes_idsite_setting');

        $this->addSetting($this->shortenerExternalShortcodeIdsite);
    }
}
