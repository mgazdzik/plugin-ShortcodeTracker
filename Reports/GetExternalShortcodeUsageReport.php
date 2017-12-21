<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Reports;

use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;

use Piwik\View;

/**
 * This class defines a new report.
 *
 * See {@link http://developer.piwik.org/api-reference/Piwik/Plugin/Report} for more information.
 */
class GetExternalShortcodeUsageReport extends Base
{
    protected function init()
    {
        parent::init();

        $this->name          = 'Shortcodes';
        $this->dimension     = null;
        $this->documentation = Piwik::translate('');

        // This defines in which order your report appears in the mobile app, in the menu and in the list of widgets
        $this->order = 4;


         $this->subcategoryId = 'External shortcode usage';
    }

    /**
     * Here you can configure how your report should be displayed. For instance whether your report supports a search
     * etc. You can also change the default request config. For instance change how many rows are displayed by default.
     *
     * @param ViewDataTable $view
     */
    public function configureView(ViewDataTable $view)
    {
        if (!empty($this->dimension)) {
            $view->config->addTranslations(array('label' => $this->dimension->getName()));
        }

        // $view->config->show_search = false;
        // $view->requestConfig->filter_sort_column = 'nb_visits';
        // $view->requestConfig->filter_limit = 10';

        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);
    }

    /**
     * Here you can define related reports that will be shown below the reports. Just return an array of related
     * report instances if there are any.
     *
     * @return \Piwik\Plugin\Report[]
     */
    public function getRelatedReports()
    {
        return array(); // eg return array(new XyzReport());
    }

    /**
     * A report is usually completely automatically rendered for you but you can render the report completely
     * customized if you wish. Just overwrite the method and make sure to return a string containing the content of the
     * report. Don't forget to create the defined twig template within the templates folder of your plugin in order to
     * make it work. Usually you should NOT have to overwrite this render method.
     *
     * @return string
    public function render()
    {
        $view = new View('@ShortcodeTracker/getShortcodes');
        $view->myData = array();

        return $view->render();
    }
    */

}
