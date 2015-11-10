# ShortcodeTracker Plugin

| Branch | Status |
| --- | --- |
| Master | [![Build Status](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker.svg?branch=master)](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker) |
| Develop | [![Build Status](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker.svg?branch=develop)](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker) |

## Description

Plugin allows to turn Piwik instance into URL Shortener.

Basic features:
* easily create shortcode from any page you track in Piwik (integration with UI),
* create shortcode for any custom URL you want,
* perform redirects using your Piwik instance,

Goodness comming:
* for redirect peformance improvement, store your shortcodes in storage like Memcache or Redis
* get statistics for shortcodes handled by your instance
    * get best performing URL's on websites you track,
    * external URLs redirect statistics

## Usage

After correctly setting up this plugin (please see section below), you are ready for shortening your Urls.

There is one new section in top reporting menu called "Shortcodes".

This view gives you possibility to shorten any URL you want and operate with shortcode retrieved.

Additionally this plugin integrates with Page URL's report - hover over URL you want to shorten and click scissors icon.

This will call popup with appropriate shortcode, so you don't need to manually shorten any URL you already track with your
Piwik instnace.

Enjoy!

## Setup

### Webserver
Besides of functional Piwik instance with this plugin enabled you will also need special configuration for your webserver.

It's purpose is to redirect any short url's hitting your server to proper API method doing all the magic.

Below you can find example configurations

* [for NGINX webserver](docs/nginx_config.md)
* [for Apache2 webserver](docs/apache_config.md) - content for .htaccess file of your webserver docroot

**Please be aware that in your case this configuration may be different, so please contact your system/webserver
admin for advisory!**

### Plugin

Before you can start shorteing your URLs you need to perform following steps:

* go to Administration -> Plugins,
* find "ShortcodeTracker" plugin and click `enable`,

After you confirm that plugin has been enabled:
* go to Administration -> Plugin Settings,
* go to ShortcodeTracker section,
* fill in Shortener URL input,
* add more tests and integrate with CI environment
* click 'save',
* additionally you have to make Shortener URL a trusted host for Piwik by entering it in settings section,

This is necessary to perform, as otherwise you will not be able to generate shortened URLs or use them with Piwik.

## Changelogs

* 0.3.0
    * Tuned travis build file
    * Mark Shortcodes as internal during creating
    * Track custom event with "redirect" category upon each redirect for internal Shortcode
    * Secure API methods from anonymous user usage
    * Add shortcode report for internally tracked URLs:
        * Create new visit during redirect (store referrer)
        * Add Shortcode usage report based on Custom Events plugin API

    
* 0.2.0
    * added Travis build badges for master and develop branches
    * fixed existing unit tests
    * slight refactor in terms of class naming
    * added integration test for API methods

* 0.1.0
    * API allowing to create and retrieve shortcodes,
    * basic storage in MySQL, but possible to add other caching layers - for ex. Memcache, Redis,
    * unit tests covering core logic,
    * redirect API method that will preform appropriate redirects for incoming shortcode requests,
    * basic setup guide involving Apache2 and Nginx configs,
    * settings section allowing user to configure Shortener base URL (which may and should be different than Piwik instance)

## Backlog

* Display shortened URL in shortcode usage report, make it more user-friendly
* Migrate plugin to work with Piwik 2.15 LTS version
* Add advanced report for each shortcode
    * stitch every redirect event with following action
    * add new referrer type (shortcode)
    * aggregate statistics
    * add segment for referrer
* Add statistics for redirects to pages not tracked with Piwik (external pages)
    * collect redirect statistics
    * aggregate and display report
* Add queue system for tracking redirect events to improve performance of redirect feature
* Add integration test for redirect tracking
* Add support for at least one caching system (redis/memcache)
* Improve HTML elements designs/styles
* Throw exception/signal in UI in case Shortener URL is not changed
* Introduce Shortener base URL validation (in Settings section)
* introduce value object to store Shortcode
* handle case when given idsite has multiple domains assigned (currently it's only for main domain URL)


## Support

Please direct any feedback regarding plugin to Github repository issue tracker available at
[https://github.com/mgazdzik/plugin-ShortcodeTracker/issues](https://github.com/mgazdzik/plugin-ShortcodeTracker/issues).

## Credits
Scissors icon visible in Actions report is originating from
[https://icons8.com/](https://icons8.com/).
