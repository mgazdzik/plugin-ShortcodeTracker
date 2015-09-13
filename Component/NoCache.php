<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;


class NoCache extends Cache
{
    /**
     * @param string $code
     * @return void
     */
    public function getUrlFromCache($code)
    {
        return;
    }

    /**
     * @param string $code
     * @param string $url
     * @return void
     */
    protected function storeUrlForCodeInCache($code, $url)
    {
        return;
    }


}