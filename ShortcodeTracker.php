<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\Plugins\ShortcodeTracker\Model\Model;

class ShortcodeTracker extends \Piwik\Plugin
{

    const DEFAULT_SHORTENER_URL = 'http://changeme.com/';
    const SHORTENER_URL_SETTING = 'shortener_url';

    public function install()
    {
        $model = new Model();
        $model->install();
    }

    public function getListHooksRegistered()
    {
        return array(
            'API.DocumentationGenerator.@hideForAll' => 'hideForAll',
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
        );
    }

    public function hideForAll(&$hide)
    {
        $hide = true;
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "plugins/ShortcodeTracker/javascripts/rowaction.js";
        $jsFiles[] = "plugins/ShortcodeTracker/javascripts/shortcodeGenerating.js";
    }

    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'ShortcodeTracker_generated_shortcode_is';
        $translationKeys[] = 'ShortcodeTracker_rowaction_tooltip_title';
        $translationKeys[] = 'ShortcodeTracker_rowaction_tooltip';
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/ShortcodeTracker/stylesheets/styles.less";
    }
}
