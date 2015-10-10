<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;

use Piwik\Plugins\ShortcodeTracker\Model\ModelInterface;
use Piwik\Plugins\SitesManager\API as SitesManagerAPI;

/**
 * Class ShortcodeGenerator
 *
 * @package Piwik\Plugins\ShortcodeTracker\Component
 */
class Generator
{
    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @var UrlValidator
     */
    private $urlValidator;

    /**
     * @var SitesManagerAPI;
     */
    private $sitesManager;

    /**
     * @var int
     */
    static $SHORTCODE_LENGTH = 6;

    /**
     * @var int
     * purpose of this value is to rather
     */
    static $GENERATIONS_MAX_ATTEMPT_NUMBER = 10;

    /**
     * @param ModelInterface $model
     */
    public function __construct(ModelInterface $model, UrlValidator $urlValidator, $sitesManagerAPI)
    {
        $this->model = $model;
        $this->urlValidator = $urlValidator;
        $this->sitesManager = $sitesManagerAPI;
    }

    /**
     * @param $url
     *
     * @return bool
     */
    public function generateShortcode($url)
    {
        if ($this->urlValidator->validate($url)) {
            $shortcode = $this->iterateToGetShortcode($url);

            return $shortcode;
        }

        return false;
    }

    /**
     * @param $url
     *
     * @return bool|void
     */
    public function isValidUrl($url)
    {
        return $this->urlValidator->validate($url);
    }

    /**
     * @return string
     */
    private function generate($url)
    {
        $code = substr(md5($url . uniqid()), 0, Generator::$SHORTCODE_LENGTH);

        return $code;
    }

    private function iterateToGetShortcode($url)
    {
        $attempts = 1;
        while ($attempts < self::$GENERATIONS_MAX_ATTEMPT_NUMBER) {
            $shortcode = $this->generate($url);
            if ($this->checkIfUnique($shortcode)) {
                return $shortcode;
            }
            $attempts++;
        }

        return false;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function checkIfUnique($code)
    {
        return ($this->model->selectShortcodeByCode($code) === false);
    }

    /**
     * @param $url
     *
     * @return bool
     */
    public function isUrlInternal($url)
    {
        $allSites = $this->sitesManager->getAllSites();
        foreach ($allSites as $site) {
            if (strpos($url, $site['main_url']) !== false) {
                return true;
            }
        }


        return false;
    }
}