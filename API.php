<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Piwik;
use Piwik\Plugins\ShortcodeTracker\Component\Generator;
use Piwik\Plugins\ShortcodeTracker\Component\NoCache;
use Piwik\Plugins\ShortcodeTracker\Component\ShortcodeCache;
use Piwik\Plugins\ShortcodeTracker\Component\ShortcodeValidator;
use Piwik\Plugins\ShortcodeTracker\Component\UrlValidator;
use Piwik\Plugins\ShortcodeTracker\Exception\UnableToRedirectException;
use Piwik\Plugins\ShortcodeTracker\Model\Model;
use Piwik\Plugins\SitesManager\API as SitesManagerAPI;

/**
 * API for plugin ShortcodeTracker
 *
 * @method static \Piwik\Plugins\ShortcodeTracker\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * @var Model
     */
    private $model = null;

    /**
     * @var UrlValidator
     */
    private $urlValidator = null;

    /**
     * @var ShortcodeCache
     */
    private $cache = null;

    /**
     * @var Generator
     */
    private $generator = null;

    /**
     * @var SitesManagerAPI
     */
    private $sitesManagerAPI = null;

    /**
     * @var Settings
     */
    private $pluginSettings = null;


    /**
     * @hideForAll
     *
     * @return Model
     */
    public function getModel()
    {
        if ($this->model === null) {
            $this->model = new Model();
        }

        return $this->model;
    }

    /**
     * @hideForAll
     *
     * @param Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @hideForAll
     *
     * @return UrlValidator
     */
    public function getUrlValidator()
    {
        if ($this->urlValidator === null) {
            $this->urlValidator = new UrlValidator();
        }

        return $this->urlValidator;
    }

    /**
     * @hideForAll
     *
     * @param UrlValidator $urlValidator
     */
    public function setUrlValidator($urlValidator)
    {
        $this->urlValidator = $urlValidator;
    }

    /**
     * @hideForAll
     *
     * @return ShortcodeCache
     */
    public function getCache()
    {
        if ($this->cache === null) {
            $this->cache = new NoCache($this->getModel());
        }

        return $this->cache;
    }

    /**
     * @hideForAll
     *
     * @param Cache @cache
     */
    public function setCache(ShortcodeCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @hideForAll
     * @return Generator
     */
    public function getGenerator()
    {
        if ($this->generator === null) {
            $this->generator = new Generator($this->getModel(), $this->getUrlValidator(), $this->gerSitesManagerAPI());
        }

        return $this->generator;
    }

    /**
     * @hideForAll
     *
     * @param Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * @hideForAll
     *
     * @return Settings
     */
    public function getPluginSettings()
    {
        if ($this->pluginSettings === null) {
            $this->pluginSettings = new Settings('ShortcodeTracker');
        }

        return $this->pluginSettings;
    }

    /**
     * @hideForAll
     *
     * @param Settings $pluginSettings
     */
    public function setPluginSettings($pluginSettings)
    {
        $this->pluginSettings = $pluginSettings;
    }


    /**
     * @hideForAll
     *
     * @return SitesManagerAPI
     */
    public function getSitesManagerAPI()
    {
        if ($this->sitesManagerAPI === null) {
            $this->sitesManagerAPI = SitesManagerAPI::getInstance();
        }

        return $this->sitesManagerAPI;
    }

    /**
     * @hideForAll
     *
     * @param SitesManagerAPI $sitesManagerAPI
     */
    public function setSitesManagerAPI($sitesManagerAPI)
    {
        $this->sitesManagerAPI = $sitesManagerAPI;
    }

    /**
     * @param $url
     * @param $useExistingCodeIfAvailable
     *
     * @return bool|string
     */
    public function generateShortenedUrl($url, $useExistingCodeIfAvailable = false)
    {
        $settings = $this->getPluginSettings();
        $baseUrl = $settings->getSetting(ShortcodeTracker::SHORTENER_URL_SETTING);

        $response = $this->generateShortcodeForUrl($url, $useExistingCodeIfAvailable);

        $shortcodeValidator = new ShortcodeValidator();
        if ($shortcodeValidator->validate($response)) {
            return $baseUrl . $response;
        }

        return $response;
    }

    /**
     * @param            $url
     * @param bool|false $useExistingCodeIfAvailable
     *
     * @return bool|string
     */
    public function generateShortcodeForUrl($url, $useExistingCodeIfAvailable = false)
    {
        $shortcode = false;

        if ($useExistingCodeIfAvailable === "true") {
            $shortcode = $this->getModel()->selectShortcodeByUrl($url);
        }

        if ($shortcode === false) {
            $generator = $this->getGenerator();
            $shortcode = $generator->generateShortcode($url);
            $isShortcodeInternal = $generator->isUrlInternal($url);

            if ($shortcode === false) {
                return Piwik::translate('ShortcodeTracker_unable_to_generate_shortcode');
            }

            $this->getModel()->insertShortcode($shortcode, $url, false);
        }

        return $shortcode;

    }

    /**
     * @param $code
     *
     * @return string
     */
    public function getUrlFromShortcode($code)
    {
        $shortcode = $this->getModel()->selectShortcodeByCode($code);

        return $shortcode ? $shortcode['url'] : Piwik::translate('ShortcodeTracker_invalid_shortcode');
    }

    /**
     * @param $code
     *
     * @throws UnableToRedirectException
     */
    public function performRedirectForShortcode($code)
    {
        $targetUrl = $this->getCache()->getRedirectUrlForShortcode($code);

        if ($targetUrl === null) {
            throw new UnableToRedirectException(Piwik::translate('ShortcodeTracker_unable_to_perform_redirect'));
        }

        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $targetUrl);
    }

}
