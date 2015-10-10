<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\tests\Unit;

use Piwik\Plugins\ShortcodeTracker\Component\Generator;
use Piwik\Plugins\ShortcodeTracker\Component\UrlValidator;

/**
 * @group ShortcodeTracker
 * @group ShortcodeGenerator
 * @group Plugins
 */
class ShortcodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    private $component;

    /**
     * @param $url
     * @param $expected
     *
     * @dataProvider generateShortcodeProvider
     */
    public function testGenerateShortcode($url, $modelMockValue)
    {
        $modelMock = $this->getModelMockWithResponse($modelMockValue);
        $urlValidator = new UrlValidator();
        $sitesManagerAPI = $this->getSitesManagerAPIMock();
        $this->component = new Generator($modelMock, $urlValidator, $sitesManagerAPI);
        $actual = $this->component->generateShortcode($url);
        $this->assertNotEmpty($actual);
        $this->assertEquals(6, strlen($actual));
    }


    /**
     * @return array
     */
    public function generateShortcodeProvider()
    {
        return array(
            array('http://www.piwik.org', false),
            array('http://www.johndoe.com', false),
        );
    }

    public function testCheckIfUnique()
    {
        $modelMock = $this->getModelMockForUniqueTest();
        $urlValidator = new UrlValidator();
        $sitesManagerAPI = $this->getSitesManagerAPIMock();
        $this->component = new Generator($modelMock, $urlValidator, $sitesManagerAPI);

        $actual = array();
        $actual[] = $this->component->generateShortcode('http://www.piwik.org');
        $actual[] = $this->component->generateShortcode('http://www.piwik.org');

        $this->assertNotEquals($actual[0], $actual[1]);
    }

    public function testIsUrlInternal()
    {
        $modelMock = $this->getModelMockWithResponse('');
        $urlValidator = new UrlValidator();
        $sitesManagerAPI = $this->getSitesManagerAPIMock();
        $this->component = new Generator($modelMock, $urlValidator, $sitesManagerAPI);

        $this->component->isUrlInternal('http://foo.bar');
        $this->assertTrue(1);
    }

    private function getModelMockForUniqueTest()
    {
        $mock = $this->getMockBuilder('Piwik\Plugins\ShortcodeTracker\Model\Model')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->at(0))
            ->method('selectShortcodeByCode')
            ->will($this->returnValue(true));

        $mock->expects($this->at(1))
            ->method('selectShortcodeByCode')
            ->will($this->returnValue(false));

        $mock->expects($this->at(2))
            ->method('selectShortcodeByCode')
            ->will($this->returnValue(true));

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getModelMockWithResponse($response)
    {
        $mock = $this->getMockBuilder('Piwik\Plugins\ShortcodeTracker\Model\Model')
            ->disableOriginalConstructor()
            ->setMethods(array('selectShortcodeByCode'))
            ->getMock();

        $mock->expects($this->any())
            ->method('selectShortcodeByCode')
            ->will($this->returnValue($response));

        return $mock;
    }

    private function getSitesManagerAPIMock()
    {
        $mock = $this->getMockBuilder('Piwik\Plugins\SitesManager\API')
            ->disableOriginalConstructor()
            ->getMock();

        $sitesCollection = array(
            array('main_url' => 'http://foo.bar'),
            array('main_url' => 'https://bar.foo'),
        );

        $mock->expects($this->any())
            ->method('getAllSites')
            ->willReturn($sitesCollection);

        return $mock;
    }

}
