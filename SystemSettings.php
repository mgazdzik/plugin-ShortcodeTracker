<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Access;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\API as SitesManagerAPI;
use Piwik\Settings\FieldConfig;
use Piwik\Settings\Setting;

/**
 * Defines Settings for ShortcodeTracker.
 *
 * Usage like this:
 * $settings = new SystemSettings();
 * $settings->metric->getValue();
 * $settings->description->getValue();
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /**
     * @var Setting
     */
    public $shortenerUrl;

    /**
     * @var Setting
     */
    public $shortenerExternalShortcodeIdsite;

    protected function init()
    {
        $this->shortenerUrl = $this->createShortenerUrlSetting();
        $this->shortenerExternalShortcodeIdsite = $this->getShortenerIdsiteForExternalShortcodesSetting();
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

    private function createShortenerUrlSetting()
    {
        return $this->makeSetting(
            ShortcodeTracker::SHORTENER_URL_SETTING,
            $default = null,
            FieldConfig::TYPE_STRING,
            function (FieldConfig $field) {
                $field->title = Piwik::translate('ShortcodeTracker_settings_shortener_url_setting_name');
                $field->description = Piwik::translate('ShortcodeTracker_settings_shortener_url_setting_description');
                $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
                $field->description = 'If enabled, the value will be automatically refreshed depending on the specified interval';
            }
        );

    }


    private function getShortenerIdsiteForExternalShortcodesSetting()
    {
        return $this->makeSetting(
            ShortcodeTracker::SHORTENER_EXTERNAL_SHORTCODES_IDSITE,
            null,
            FieldConfig::TYPE_INT,
            function (FieldConfig $field) {
                $field->title = Piwik::translate('ShortcodeTracker_settings_shortener_external_shortcodes_idsite_setting_name');
                $field->description = Piwik::translate('ShortcodeTracker_settings_shortener_external_shortcodes_idsite_setting');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $sitesManagerApi = SitesManagerAPI::getInstance();
                $field->availableValues = Access::doAsSuperUser(function () use ($sitesManagerApi) {
                    $sites = $sitesManagerApi->getAllSites();
                    $siteIds = array('0' => 'Do not collect external shortcode redirects');
                    foreach ($sites as $site) {
                        $siteIds[$site['idsite']] = $site['name'];
                    }

                    return $siteIds;
                });
            }
        );
    }
}
