# ShortcodeTracker Plugin

| Branch | Status |
| --- | --- |
| Master | [![Build Status](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker.svg?branch=master)](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker) |
| Develop | [![Build Status](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker.svg?branch=develop)](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker) |

## Description

Plugin allows to turn Piwik instance into URL Shortener.

Basic features:

* easily create shortcode from any page you track in Piwik (integration with Actions report UI),
* create shortcode for any custom URL you want,
* perform redirects using your Piwik instance,
* get usage statistics for shortcodes handled by your instance
    * get best performing URL's on websites you track,
    * external URLs redirect statistics,
* see which URLs are being shortened and visited most often - also for external URLs not tracked in your Piwik.

Goodness coming:

* for redirect performance improvement, store your shortcodes in storage like Memcache or Redis,
* attributing shortcode redirects with actual visits on your page,
* more advanced reports,

Before using, please read content in [`Setup`](https://github.com/mgazdzik/plugin-ShortcodeTracker#setup) section 
as it contains steps required to make plugin work with your Piwik instance!

### Usage

After correctly setting up this plugin (please see section below), you are ready for shortening your Urls.

There is one new section in top reporting menu called "Shortcodes".

This view gives you possibility to shorten any URL you want and operate with shortcode retrieved.

Additionally this plugin integrates with Page URL's report - hover over URL you want to shorten and click scissors icon.

This will call popup with appropriate shortcode, so you don't need to manually shorten any URL you already track with your
Piwik instance.

Enjoy!

### Setup

#### Webserver
Besides of functional Piwik instance with this plugin enabled you will also need special configuration for your webserver.

It's purpose is to redirect any short url hitting your server to proper API method doing the magic.

Below you can find example configurations

* [for NGINX webserver vhost](https://github.com/mgazdzik/plugin-ShortcodeTracker/blob/master/docs/nginx_config.txt)
* [for Apache2 webserver .htaccess file](https://github.com/mgazdzik/plugin-ShortcodeTracker/blob/master/docs/apache_config.txt)

**Please be aware that in your case this configuration may be different, so please contact your system/webserver
admin for advisory!**


#### Plugin

Before you can start shortening your URLs you need to perform following steps:

* go to Administration -> Plugins,
* find "ShortcodeTracker" plugin and click `enable`,

After you confirm that plugin has been enabled:
* go to Administration -> Plugin Settings,
* go to ShortcodeTracker section,
* fill in Shortener URL input,
* if you want to track external sites, you need to decide to which Piwik page those actions will be attributed (see
External redirects tracking section below),
* click 'save',
* **additionally you have to make Shortener URL a trusted host for Piwik by entering it in settings section**,

This is necessary to perform, as otherwise you will not be able to generate shortened URLs or use them with Piwik.

#### External redirects tracking

It is possible to also track redirect actions for external URLs (i.e. which URL doesn't match any page tracked within
your Piwik instance). However, it is required to decide to which site this traffic will be attributed to.

It is recommended to create a separate Website in Piwik instance only dedicated to this traffic, so that other websites
reports won't be affected by redirect events.

To select which site should collect redirects:

* go to `Plugin Settings` section,
* from dropdown you can select site for external redirects,
* alternatively you can select not to track external redirects by setting `Do not collect external shortcode redirects`,
* click save



### Backlog

* Add advanced report for each shortcode
    * stitch every redirect event with following action,
    * add new referrer type (shortcode),
    * aggregate statistics,
    * add segment for referrer,
* Refactor plugin so it's possible to cover Model.php with tests,
* Add queue system for tracking redirect events to improve performance of redirect feature,
* Add integration test for redirect tracking,
* Add support for at least one caching system (redis/memcache),
* Improve HTML elements designs/styles,
* Throw exception/signal in UI in case Shortener URL is not changed,
* Introduce Shortener base URL validation (in Settings section),
* introduce value object to store Shortcode,
* handle case when given idsite has multiple domains assigned (currently it's only for main domain URL),


### Support

Please direct any feedback regarding plugin to Github repository issue tracker available at
[https://github.com/mgazdzik/plugin-ShortcodeTracker/issues](https://github.com/mgazdzik/plugin-ShortcodeTracker/issues).


### Credits
Scissors icon visible in Actions report is originating from
[https://icons8.com/](https://icons8.com/).

