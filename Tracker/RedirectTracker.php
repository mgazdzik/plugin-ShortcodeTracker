<?php

namespace Piwik\Plugins\ShortcodeTracker\Tracker;


use Piwik\Log;
use Piwik\Plugins\ShortcodeTracker\SystemSettings;
use Piwik\Plugins\ShortcodeTracker\ShortcodeTracker;
use Piwik\SettingsPiwik;

class RedirectTracker
{
    protected $shortcodeTrackerSettings;

    public function __construct()
    {
        $this->shortcodeTrackerSettings = new SystemSettings('ShortcodeTracker');
    }

    public function recordRedirectAction($shortcode)
    {
        $baseUrl = $this->shortcodeTrackerSettings->getSlashedSetting(ShortcodeTracker::SHORTENER_URL_SETTING);

        $idSite = $this->getIdsiteForShortcode($shortcode);
        if ($idSite !== "0") {
            $piwikTracker = $this->getPiwikTracker($idSite);
            $piwikTracker->setUrl($shortcode['url']);
            $piwikTracker->setUrlReferrer($baseUrl . $shortcode['code']);
            $result = $piwikTracker->doTrackEvent(ShortcodeTracker::REDIRECT_EVENT_CATEGORY,
                                                  ShortcodeTracker::REDIRECT_EVENT_NAME,
                                                  $shortcode['code']);

            Log::debug($result);
        }
    }

    protected function getPiwikTracker($idSite)
    {
        require_once PIWIK_INCLUDE_PATH . '/libs/PiwikTracker/PiwikTracker.php';
        \PiwikTracker::$URL = SettingsPiwik::getPiwikUrl();

        return new \PiwikTracker($idSite);
    }

    protected function getIdsiteForShortcode($shortcode)
    {
        if ($shortcode['idsite'] !== "0") {
            return $shortcode['idsite'];
        } else {
            return $this->shortcodeTrackerSettings
                ->getSetting(ShortcodeTracker::SHORTENER_EXTERNAL_SHORTCODES_IDSITE)->getValue();
        }
    }

}