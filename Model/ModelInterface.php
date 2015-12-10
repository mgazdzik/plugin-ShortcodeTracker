<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Model;

use Piwik\Plugins\ShortcodeTracker\Exception\ShortcodeDuplicateException;

interface ModelInterface
{

    /**
     * @return mixed
     */
    public function install();

    /**
     * @param $shortcode
     * @param $url
     * @param $is_locally_tracked
     *
     * @throws ShortcodeDuplicateException
     *
     * @return mixed
     */
    public function insertShortcode($shortcode, $url, $is_locally_tracked);

    /**
     * @param $shortcode
     *
     * @return string
     */
    public function selectShortcodeByCode($shortcode);

    /**
     * @param $url
     *
     * @return string
     */
    public function selectShortcodeByUrl($url);

    /**
     * @throws /Exception
     * @return bool
     */
    public function deleteShortcode();
}