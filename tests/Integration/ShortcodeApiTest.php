<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\tests\Unit;

use Piwik\Access;
use Piwik\Plugins\ShortcodeTracker\API;
use Piwik\Plugins\ShortcodeTracker\Component\Generator;
use Piwik\Plugins\ShortcodeTracker\Component\UrlValidator;
use Piwik\Plugins\ShortcodeTracker\Model\Model;
use Piwik\Plugins\ShortcodeTracker\SystemSettings;
use Piwik\Plugins\ShortcodeTracker\ShortcodeTracker;
use Piwik\Plugins\SitesManager\API as SitesManagerAPI;
use Piwik\Tests\Framework\TestCase\SystemTestCase;

/**
 * @group ShortcodeTracker
 * @group ShortcodeApi
 * @group Plugins
 */
class ShortcodeApiTest extends SystemTestCase
{
    /**
     * @var API
     */
    private $api;

    /**
     * @var Model
     */
    private $modelMock;

    /**
     * @var SitesManagerAPI
     */
    private $sitesManagerApi;

    public function setUp()
    {
        Access::getInstance()->setSuperUserAccess(true);
        $this->initializeModelMock();
        $this->initializeSitesManagerAPI();
        $this->api = new API();
        $this->api->setModel($this->modelMock);
        $this->api->setSitesManagerAPI($this->sitesManagerApi);
    }

    public function testGenerateShortenedUrl()
    {
        $expected = 'http://changeme.com/123abc';
        try {
            $this->api->setGenerator($this->getGeneratorMock('generateShortcode', '123abc'));

            /** @var SystemSettings $pluginSettingsMock */
            $pluginSettingsMock = $this->getMockBuilder('Piwik\Plugins\ShortcodeTracker\SystemSettings')
                ->disableOriginalConstructor()
                ->getMock();

            $pluginSettingsMock->expects($this->once())
                ->method('getSetting')
                ->with(ShortcodeTracker::SHORTENER_URL_SETTING)
                ->willReturn(ShortcodeTracker::DEFAULT_SHORTENER_URL);

            $this->api->setPluginSettings($pluginSettingsMock);


            $actual = $this->api->generateShortenedUrl('http://foo.bar');
        } catch (\Zend_Db_Statement_Exception $e) {
            $this->markTestSkipped('No database connection, skip test');
        }

        $this->assertEquals($expected, $actual);
    }

    public function testGenerateShortcodeForUrl()
    {
        $expected = 'abc123';

        $this->api->setGenerator($this->getGeneratorMock('generateShortcode', $expected));

        $actual = $this->api->generateShortcodeForUrl('http://foo.bar');

        $this->assertEquals($expected, $actual);
    }


    /**
     * @dataProvider invalidUrlProvider
     */
    public function testGenerateShortcodeForUrlException($invalidUrl)
    {
        $expected = 'Unable to generate shortcode.';
        $this->api->setGenerator(new Generator($this->modelMock, new UrlValidator(), $this->sitesManagerApi));

        $actual = $this->api->generateShortcodeForUrl($invalidUrl);

        $this->assertEquals($expected, $actual);
    }


    public function invalidUrlProvider()
    {
        return array(
            array('invalidUrl'),
            array('http://'),
            array('file://c:/windows/myfile'),
            array('http://foo'),
        );
    }


    private function initializeModelMock()
    {
        $mock = $this->getMockBuilder('Piwik\Plugins\ShortcodeTracker\Model\Model')->disableOriginalConstructor()->getMock();

        $this->modelMock = $mock;
    }

    private function initializeSitesManagerAPI()
    {
        $mock = $this->getMockBuilder('Piwik\Plugins\SitesManager\API')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getAllSites')
            ->willReturn(array(
                array(
                    'idsite' => 1,
                    'main_url' => 'http://foo.bar',
                ),
            ));
        $this->sitesManagerApi = $mock;
    }

    public function testGetUrlFromShortcode()
    {
        $expected = array('url' => 'http://foo.bar');
        $this->modelMock->expects($this->once())
            ->method('selectShortcodeByCode')
            ->with('123abc')
            ->willReturn($expected);

        $actual = $this->api->getUrlFromShortcode('123abc');

        $this->assertEquals('http://foo.bar', $actual);
    }

    public function testGetUrlFromShortcodeFailure()
    {
        $this->modelMock->expects($this->any())
            ->method('selectShortcodeByCode')
            ->willReturn(null);


        $actual = $this->api->getUrlFromShortcode('456zxc');

        $this->assertEquals('Invalid shortcode.', $actual);
    }

    public function testPerformRedirectForShortcode()
    {
        $this->modelMock->expects($this->once())
            ->method('selectShortcodeByCode')
            ->with('123zxc')
            ->willReturn(
                [
                    'url' => 'http://foo.bar',
                    'idsite' => 1,
                    'code' => '123zxc'
                ]
            );

        try {
            $this->api->performRedirectForShortcode('123zxc');
        } catch (\Exception $e) {
            $this->assertContains('Cannot modify header information - headers already sent by', $e->getMessage());
        }

    }

    public function testPerformRedirectForShortcodeFailure()
    {
        $this->modelMock->expects($this->once())
            ->method('selectShortcodeByCode')
            ->willReturn(null);
        $this->setExpectedException('Piwik\Plugins\ShortcodeTracker\Exception\UnableToRedirectException',
            'Oops, couldn\'t complete redirect! Please try using valid shortcode.');

        $this->api->performRedirectForShortcode('123zxc');
    }

    public function testHtmlEncodedEntitiesStoredProperly()
    {
        $api = new API();
        $model = new Model();
        $api->setModel($model);
        $api->setSitesManagerAPI($this->sitesManagerApi);

        $url = 'http://example.com?param1=foo&amp;param2=bar';

        $code = $api->generateShortcodeForUrl($url);

        $resultUrl = $api->getUrlFromShortcode($code);

        $this->assertEquals(html_entity_decode($url), $resultUrl);
    }

    /**
     * @param $methodName
     * @param $returnValue
     *
     * @return Generator
     */
    private function getGeneratorMock($methodName, $returnValue)
    {
        $generatorMock = $this->getMockBuilder('Piwik\Plugins\ShortcodeTracker\Component\Generator')->disableOriginalConstructor()->getMock();
        $generatorMock->expects($this->once())
            ->method($methodName)
            ->willReturn($returnValue);

        return $generatorMock;
    }
}
