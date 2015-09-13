<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Model;

interface ModelInterface {

    /**
     * @return mixed
     */
    public function install();

    /**
     * @param string $shortcode
     * @param string $url
     * @param string $is_locally_tracked
     * @return bool
     * @throws ShortcodeDuplicateException
     */
    public function insertShortcode($shortcode, $url, $is_locally_tracked);

    /**
     * @param $shortcode
     * @return string
     */
    public function selectShortcodeByCode($shortcode);

    /**
     * @param $url
     * @return string
     */
    public function selectShortcodeByUrl($url);

    /**
     * @return array
     */
    public function selectShortcodesTrackedLocally();

    /**
     * @throws /Exception
     * @return bool
     */
    public function deleteShortcode();
}