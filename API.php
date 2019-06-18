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
use Piwik\Plugins\Events\API as EventsAPI;
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
    private $model;

    /**
     * @var UrlValidator
     */
    private $urlValidator;

    /**
     * @var ShortcodeValidator
     */
    private $shortCodeValidator;

    /**
     * @var ShortcodeCache
     */
    private $cache;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var SitesManagerAPI
     */
    private $sitesManagerAPI;

    /**
     * @var SystemSettings
     */
    private $pluginSettings;

    /**
     * @hideForAll
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     *
     * @param Model $model
     */
    public function setModel($model)
    {
        $this->checkUserNotAnonymous();
        $this->model = $model;
    }

    /**
     * @hideForAll
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     *
     * @param UrlValidator $urlValidator
     */
    public function setUrlValidator($urlValidator)
    {
        $this->checkUserNotAnonymous();
        $this->urlValidator = $urlValidator;
    }

    /**
     * @return ShortcodeValidator
     */
    public function getShortCodeValidator()
    {
        if ($this->shortCodeValidator === null) {
            $this->shortCodeValidator = new ShortcodeValidator();
        }

        return $this->shortCodeValidator;
    }

    /**
     * @param ShortcodeValidator $shortCodeValidator
     */
    public function setShortCodeValidator($shortCodeValidator)
    {
        $this->checkUserNotAnonymous();
        $this->shortCodeValidator = $shortCodeValidator;
    }


    /**
     * @hideForAll
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     *
     * @param Cache @cache
     */
    public function setCache(ShortcodeCache $cache)
    {
        $this->checkUserNotAnonymous();
        $this->cache = $cache;
    }

    /**
     * @hideForAll
     * @codeCoverageIgnore
     * @return Generator
     */
    public function getGenerator()
    {
        $this->checkUserNotAnonymous();
        if ($this->generator === null) {
            $this->generator = new Generator(
                $this->getModel(),
                $this->getUrlValidator(),
                $this->getShortcodeValidator(),
                $this->getSitesManagerAPI());
        }

        return $this->generator;
    }

    /**
     * @hideForAll
     * @codeCoverageIgnore
     *
     * @param Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->checkUserNotAnonymous();
        $this->generator = $generator;
    }

    /**
     * @hideForAll
     * @codeCoverageIgnore
     * @return SystemSettings
     */
    public function getPluginSettings()
    {
        if ($this->pluginSettings === null) {
            $this->pluginSettings = new SystemSettings();
        }

        return $this->pluginSettings;
    }

    /**
     * @hideForAll
     * @codeCoverageIgnore
     *
     * @param SystemSettings $pluginSettings
     */
    public function setPluginSettings($pluginSettings)
    {
        $this->checkUserNotAnonymous();
        $this->pluginSettings = $pluginSettings;
    }


    /**
     * @hideForAll
     * @codeCoverageIgnore
     * @return SitesManagerAPI
     */
    public function getSitesManagerAPI()
    {
        $this->checkUserNotAnonymous();

        if ($this->sitesManagerAPI === null) {
            $this->sitesManagerAPI = SitesManagerAPI::getInstance();
        }

        return $this->sitesManagerAPI;
    }

    /**
     * @hideForAll
     * @codeCoverageIgnore
     *
     * @param SitesManagerAPI $sitesManagerAPI
     */
    public function setSitesManagerAPI($sitesManagerAPI)
    {
        $this->checkUserNotAnonymous();
        $this->sitesManagerAPI = $sitesManagerAPI;
    }

    /**
     * @return string
     */
    public function checkMinimalRequiredAccess()
    {
        Piwik::checkUserIsNotAnonymous();
    }

    /**
     * @param $url
     * @param $useExistingCodeIfAvailable
     *
     * @return bool|string
     */
    public function generateShortenedUrl($url, $useExistingCodeIfAvailable = false)
    {
        $this->checkMinimalRequiredAccess();

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
        $sanitizedUrl = html_entity_decode($url);
        $generator = $this->getGenerator();
        $shortcodeIdsite = $generator->getIdSiteForUrl($sanitizedUrl);
        $this->checkUserHasWriteAccess($shortcodeIdsite);

        $shortcode = false;

        if ($useExistingCodeIfAvailable === "true") {
            $shortcode = $this->getModel()->selectShortcodeByUrl($sanitizedUrl);
        }

        if ($shortcode === false) {
            $shortcode = $generator->generateShortcode($sanitizedUrl);
            if ($shortcode === false) {
                return Piwik::translate('ShortcodeTracker_unable_to_generate_shortcode');
            }

            $this->getModel()->insertShortcode($shortcode, $sanitizedUrl, $shortcodeIdsite);
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
        $this->checkUserNotAnonymous();

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
        $shortCode = $this->getCache()->getShortcode($code);

        Piwik::postEvent(ShortcodeTracker::TRACK_REDIRECT_VISIT_EVENT, array($shortCode));

        if ($shortCode === null) {
            throw new UnableToRedirectException(Piwik::translate('ShortcodeTracker_unable_to_perform_redirect'));
        }

        header('Location: ' . $shortCode['url']);
    }


    public function getShortcodeUsageReport($idSite, $period, $date, $segment = false, $columns = false)
    {
        $this->checkUserNotAnonymous();
        $eventsApi = EventsAPI::getInstance();

        $eventReport = $eventsApi
            ->getCategory($idSite, $period, $date, $segment);

        if ($eventReport->getRowsCount() === 0) {
            return new DataTable();
        }

        $shortcodeReportIdSubtable = $eventReport
            ->getRowFromLabel(ShortcodeTracker::REDIRECT_EVENT_CATEGORY)
            ->getIdSubDataTable();

        if ($shortcodeReportIdSubtable) {
            $report = $eventsApi->getNameFromCategoryId($idSite, $period, $date, $shortcodeReportIdSubtable);
            $enrichedReport = $this->enrichReportWithShortcodeUrls($report);

            return $enrichedReport;
        }

        return false;
    }

    public function getExternalShortcodeUsageReport($idSite, $period, $date, $segment = false, $columns = false)
    {
        $this->checkUserNotAnonymous();
        /** @var SystemSettings $settings */
        $settings = $this->getPluginSettings();
        $idSite = $settings->getSetting(ShortcodeTracker::SHORTENER_EXTERNAL_SHORTCODES_IDSITE)->getValue();
        $eventsApi = EventsAPI::getInstance();

        $eventReport = $eventsApi
            ->getCategory($idSite, $period, $date, $segment);

        if ($eventReport->getRowsCount() === 0) {
            return new DataTable();
        }

        $shortcodeReportIdSubtable = $eventReport
            ->getRowFromLabel(ShortcodeTracker::REDIRECT_EVENT_CATEGORY)
            ->getIdSubDataTable();

        if ($shortcodeReportIdSubtable) {
            $report = $eventsApi->getNameFromCategoryId($idSite, $period, $date, $shortcodeReportIdSubtable);
            $enrichedReport = $this->enrichReportWithShortcodeUrls($report);

            return $enrichedReport;
        }

        return false;
    }

    public function getShortenedPagesReport($idSite, $period, $date, $segment = false, $columns = false)
    {
        $report = $this->getShortcodeUsageReport($idSite, $period, $date, $segment, $columns);

        if ($report !== false) {
            $report = $this->summarizeByShortenedUrl($report);
        }

        return $report;
    }

    public function getShortenedExternalPagesReport($idSite, $period, $date, $segment = false, $columns = false)
    {
        $report = $this->getExternalShortcodeUsageReport($idSite, $period, $date, $segment, $columns);
        if ($report !== false) {
            $report = $this->summarizeByShortenedUrl($report);
        }

        return $report;

    }

    protected function checkUserNotAnonymous()
    {
        Piwik::checkUserIsNotAnonymous();
    }

    protected function checkUserHasWriteAccess($idSite = null)
    {
        if ($idSite != false) {
            Piwik::checkUserHasWriteAccess($idSite);
        }
        Piwik::checkUserHasSuperUserAccess();
    }

    /**
     * @param DataTable $report
     *
     * @return DataTable
     */
    protected function enrichReportWithShortcodeUrls(DataTable $report)
    {
        $model = $this->getModel();
        $filter = new DataTable\Filter\ColumnCallbackAddColumn($report, 'label', 'shortcode_url', (function ($code) use ($model) {
            $shortcode = $model->selectShortcodeByCode($code);

            return $shortcode['url'];
        }));

        $filter->filter($report);

        foreach ($report->getRows() as $row) {
            $row->setMetadata('url', $row['shortcode_url']);
        }

        return $report;
    }

    protected function summarizeByShortenedUrl(DataTable $report)
    {
        $report->renameColumn('shortcode_url', 'label');
        $filter = new DataTable\Filter\GroupBy($report, 'label');
        $filter->filter($report);

        return $report;
    }


    /**
     * Another example method that returns a data table.
     * @param int $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getShortcodes($idSite, $period, $date, $segment = false)
    {
        $table = new DataTable();

        $table->addRowFromArray(array(Row::COLUMNS => array('nb_visits' => 5)));

        return $table;
    }

}
