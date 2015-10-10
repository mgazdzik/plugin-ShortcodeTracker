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
use Piwik\Plugins\ShortcodeTracker\Settings;
use Piwik\Plugins\ShortcodeTracker\ShortcodeTracker;

/**
 * @group ShortcodeTracker
 * @group ShortcodeApi
 * @group Plugins
 */
class ShortcodeApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var API
     */
    private $api;

    /**
     * @var Model
     */
    private $modelMock;

    public function setUp()
    {
        $this->initializeModelMock();
        $this->api = new API();
        $this->api->setModel($this->modelMock);
        Access::getInstance()->setSuperUserAccess(true);
    }

    public function testGenerateShortenedUrl()
    {
        $expected = 'http://changeme.com/123abc';
        $this->api->setGenerator($this->getGeneratorMock('generateShortcode', '123abc'));

        /** @var Settings $pluginSettingsMock */
        $pluginSettingsMock = $this->getMock('Piwik\Plugins\ShortcodeTracker\Settings');

        $pluginSettingsMock->expects($this->once())
            ->method('getSetting')
            ->with(ShortcodeTracker::SHORTENER_URL_SETTING)
            ->willReturn(ShortcodeTracker::DEFAULT_SHORTENER_URL);

        $this->api->setPluginSettings($pluginSettingsMock);

        $actual = $this->api->generateShortenedUrl('http://foo.bar');
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
        $expected = 'ShortcodeTracker_unable_to_generate_shortcode';
        $this->api->setGenerator(new Generator($this->modelMock, new UrlValidator()));

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
        $expected = array('url' => 'http://foo.bar');
        $this->modelMock->expects($this->any())
            ->method('selectShortcodeByCode')
            ->willReturn(null);


        $actual = $this->api->getUrlFromShortcode('456zxc');

        $this->assertEquals('ShortcodeTracker_invalid_shortcode', $actual);
    }

    public function testPerformRedirectForShortcode()
    {
        $this->modelMock->expects($this->once())
            ->method('selectShortcodeByCode')
            ->with('123zxc')
            ->willReturn(array('url' => 'http://foo.bar'));

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
                                    'ShortcodeTracker_unable_to_perform_redirect');

        $this->api->performRedirectForShortcode('123zxc');
    }


    /**
     * @param $methodName
     * @param $returnValue
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
