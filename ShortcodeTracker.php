<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\ShortcodeTracker\Model\Model;
use Piwik\Plugins\ShortcodeTracker\Tracker\RedirectTracker;

class ShortcodeTracker extends \Piwik\Plugin
{
    const DEFAULT_SHORTENER_URL = 'http://changeme.com/';
    const SHORTENER_URL_SETTING = 'shortener_url';
    const TRACK_REDIRECT_VISIT_EVENT = 'ShortcodeTracker.trackRedirectAction';
    const REDIRECT_EVENT_CATEGORY = 'shordcode';
    const REDIRECT_EVENT_NAME = 'redirect';

    public function install()
    {
        $model = new Model();
        $model->install();
    }

    /**
     * @return array
     */
    public function getListHooksRegistered()
    {
        return array(
            'API.DocumentationGenerator.@hideForAll' => 'hideForAll',
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            self::TRACK_REDIRECT_VISIT_EVENT => 'trackRedirectAction',
            'ViewDataTable.configure' => 'dataTableConfigure',
        );
    }

    /**
     * @param bool $hide
     */
    public function hideForAll(&$hide)
    {
        $hide = true;
    }

    /**
     * @param array $jsFiles
     */
    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "plugins/ShortcodeTracker/javascripts/rowaction.js";
        $jsFiles[] = "plugins/ShortcodeTracker/javascripts/shortcodeGenerating.js";
    }

    /**
     * @param array $translationKeys
     */
    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'ShortcodeTracker_generated_shortcode_is';
        $translationKeys[] = 'ShortcodeTracker_rowaction_tooltip_title';
        $translationKeys[] = 'ShortcodeTracker_rowaction_tooltip';
    }

    /**
     * @param array $stylesheets
     */
    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/ShortcodeTracker/stylesheets/styles.less";
    }

    /**
     * @param array $shortcode
     */
    public function trackRedirectAction($shortcode)
    {
        if ($shortcode['idsite'] !== 0) {
            $tracker = new RedirectTracker();
            $tracker->recordRedirectAction($shortcode);
        }
    }

    public function dataTableConfigure(ViewDataTable $view)
    {
        if ($view->requestConfig->apiMethodToRequestDataTable === 'ShortcodeTracker.getShortcodeUsageReport') {
            $view->config->show_insights = false;
        }
    }
}
