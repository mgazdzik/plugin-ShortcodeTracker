<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;


class NoCache extends ShortcodeCache
{
    /**
     * @param string $code
     * @return void
     */
    public function getShortcodeFromCache($code)
    {
        return;
    }

    /**
     * @param string $code
     * @param string $shortcode
     * @return void
     */
    protected function storeShortcodeInCache($code, $shortcode)
    {
        return;
    }


}