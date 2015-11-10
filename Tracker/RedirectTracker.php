<?php

namespace Piwik\Plugins\ShortcodeTracker\Tracker;


use Piwik\Log;
use Piwik\Plugins\ShortcodeTracker\Settings;
use Piwik\Plugins\ShortcodeTracker\ShortcodeTracker;
use Piwik\SettingsPiwik;

class RedirectTracker
{

    public function recordRedirectAction($shortcode)
    {
        $pluginSettings = new Settings('ShortcodeTracker');
        $baseUrl = $pluginSettings->getsetting(ShortcodeTracker::SHORTENER_URL_SETTING);

        $piwikTracker = $this->getPiwikTracker($shortcode['idsite']);
        $piwikTracker->setUrl($shortcode['url']);
        $piwikTracker->setUrlReferrer($baseUrl . $shortcode['code']);
        $result = $piwikTracker->doTrackEvent(ShortcodeTracker::REDIRECT_EVENT_CATEGORY,
                                              ShortcodeTracker::REDIRECT_EVENT_NAME,
                                              $shortcode['code']);
        Log::debug($result);
    }

    protected function getPiwikTracker($idSite)
    {
        require_once PIWIK_INCLUDE_PATH . '/libs/PiwikTracker/PiwikTracker.php';
        \PiwikTracker::$URL = SettingsPiwik::getPiwikUrl();

        return new \PiwikTracker($idSite);
    }
}