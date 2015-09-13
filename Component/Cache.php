<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;

use Piwik\Plugins\ShortcodeTracker\Model\Model;

abstract class Cache
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
     * @return string|null
     */
    abstract protected function getUrlFromCache($code);

    /**
     * @param string $code
     * @param string $url
     */
    abstract protected function storeUrlForCodeInCache($code, $url);

    /**
     * @param string $code
     * @return string $url
     */
    public function getRedirectUrlForShortcode($code)
    {
        $url = $this->getUrlFromCache($code);
        if ($url === null) {
            $modelUrl = $this->getUrlFromModel($code);

            if ($modelUrl !== null) {
                $this->storeUrlForCodeInCache($code, $modelUrl);
                $url = $modelUrl;
            }
        }


        return $url;
    }

    protected function getUrlFromModel($code)
    {
        $result = $this->shortcodeModel->selectShortcodeByCode($code);

        return $result['url'];
    }

}