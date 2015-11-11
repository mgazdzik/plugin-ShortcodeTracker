<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\tests\Unit;

use Piwik\Access;
use Piwik\Plugins\ShortcodeTracker\Component\ShortcodeValidator;

/**
 * @group ShortcodeTracker
 * @group ShortcodeValidator
 * @group Plugins
 */
class ShortcodeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShortcodeValidator
     */
    private $component;

    public function setUp()
    {
        $this->component = new ShortcodeValidator();
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
            array('aaaa', false),
            array('aaaaa', false),
            array('aaa!@#', false),
            array('aaaaaaa', false),
            array('aaaa   ', false),
            array('123456', false),
            array('aaaaaa', true),
            array('abc123', true),

        );
    }
}
