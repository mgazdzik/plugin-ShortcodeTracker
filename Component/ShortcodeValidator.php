<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;

use Piwik\Plugins\ShortcodeTracker\Exception\InvalidShortcodeException;

class ShortcodeValidator extends Validator
{

    /**
     * @param string $value
     * @return bool
     */
    public function validate($value)
    {
        try {
            $this->checkLength($value);
            $this->checkAlphaNumerical($value);
        } catch (InvalidShortcodeException $e) {
            return false;
        }

        return true;
    }

    private function checkLength($value)
    {
        if (strlen($value) !== Generator::$SHORTCODE_LENGTH) {
            throw new InvalidShortcodeException(sprintf('Invalid shortcode length %s, expected %s',
                                                        strlen($value), Generator::$SHORTCODE_LENGTH));
        }

        return true;
    }

    private function checkAlphaNumerical($value)
    {
        if (ctype_digit($value)) {
            throw new InvalidShortcodeException('Invalid shortcode - contains only numerical characters');
        }

        if (preg_match('/^[a-zA-Z0-9._]+$/', $value)) {
            return true;
        }
        throw new InvalidShortcodeException('Invalid shortcode - contains non alphanumerical characters');
    }
}