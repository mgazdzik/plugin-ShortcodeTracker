<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\tests\Unit;

use Piwik\Access;
use Piwik\Plugins\ShortcodeTracker\Component\UrlValidator;

/**
 * @group ShortcodeTracker
 * @group UrlValidator
 * @group Plugins
 */
class ShortcodeUrlValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlValidator
     */
    private $component;

    public function setUp()
    {
        $this->component = new UrlValidator();
    }

    /**
     * @dataProvider lengthValidationProvider
     */
    public function testValidator($value, $expected)
    {
        $actual = $this->component->validate($value);
        $this->assertEquals($expected, $actual);
    }

    public function lengthValidationProvider()
    {
        return array(
            array('aaa', false),
            array('http://.', false),
            array('http://www', false),
            array('http://www.johndoe.com', true),
            array('http://johndoe.com', true),
            array('http://subdomain.johndoe.com', true),
            array('www.subdomain.johndoe.com', true),
            array('subdomain.johndoe.com', true),
        );
    }
}