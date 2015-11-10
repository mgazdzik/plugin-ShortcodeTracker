<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;

use Piwik\Plugins\ShortcodeTracker\Model\Model;

abstract class ShortcodeCache
{

    /**
     * @var Model
     */
    protected $shortcodeModel;

    public function __construct(Model $shortcodeModel)
    {
        $this->shortcodeModel = $shortcodeModel;
    }

    /**
     * @param string $code
     *
     * @return string|null
     */
    abstract protected function getShortcodeFromCache($code);

    /**
     * @param string $code
     * @param string $url
     */
    abstract protected function storeShortcodeInCache($code, $url);

    /**
     * @param string $code
     *
     * @return array
     */
    public function getShortcode($code)
    {
        $shortcode = $this->getShortcodeFromCache($code);
        if ($shortcode === null) {
            $shortcode = $this->getUrlFromModel($code);

            if (isset($shortcode['url'])) {
                $this->storeShortcodeInCache($code, $shortcode);
            }
        }

        return $shortcode;
    }

    protected function getUrlFromModel($code)
    {
        return $this->shortcodeModel->selectShortcodeByCode($code);
    }

}