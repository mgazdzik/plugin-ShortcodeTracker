<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Component;

use Guzzle\Http\Url;
use Piwik\UrlHelper;

/**
 * Class UrlValidator
 *
 * @package Piwik\Plugins\ShortcodeTracker\Component
 */
class UrlValidator extends Validator
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
       if($this->isValidUrl($value)){
           return true;
       }

        return false;
    }

    /**
     * @param $url
     * @return bool
     */
    public function isValidUrl($url) {
        // First check: is the url just a domain name? (allow a slash at the end)
        $_domain_regex = "|^[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,})/?$|";
        if (preg_match($_domain_regex, $url)) {
            return true;
        }

        // Second: Check if it's a url with a scheme and all
        $_regex = '#^([a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))$#';
        if (preg_match($_regex, $url, $matches)) {
            // pull out the domain name, and make sure that the domain is valid.
            $_parts = parse_url($url);
            if (!in_array($_parts['scheme'], array( 'http', 'https' )))
                return false;

            // Check the domain using the regex, stops domains like "-example.com" passing through
            if (!preg_match($_domain_regex, $_parts['host']))
                return false;

            return true;
        }

        return false;
    }
}