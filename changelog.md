## Changelog
* 1.2.0
    * add proper LICENSE file to codebase 
    
* 1.1.8
    * Fix Apache .htaccess example file

* 1.1.5, 1.1.4, 1.1.3

    * minor tweaks in plugin compatibility with Matomo plugin Marketplace

* 1.1.2
    * Force update of plugin in Matomo plugin Marketplace

* 1.1.1
    * Fix generating reports and shortcodes for admin users

* 1.1.0
    * Attempt to limit shortcode generation to only admin access users
    
* 1.0.0
    * Updated plugin to work with Piwik 3.x

* 0.7.0
    * Handled html-encoded entities in URLs (for ex. http://example.com?param1=foo&param2=bar was html encoded and therefore it was breaking redirects)
    * Added some screenshots for Marketplace

* 0.6.2
    * Sort out mistakenly pushed tag


* 0.6.0
    * Shortcode usage report added link to shortened page to for easier recognition of what is being shortened and used most,
    * Display summarized report displaying which URLs were visited most via shortcode redirects,


* 0.5.0
    * Add statistics collection for redirects to pages not tracked with Piwik (external pages)
         * collect redirect statistics into Site you choose in interface,
         * aggregate and display report for external shortcodes in separate view


* 0.4.5
    * fix README formating for sake of Plugin market


* 0.4.4
    * add license to plugin.json


* 0.4.3
    * fix plugin.json structure for Plugin market


* 0.4.2
    * Added missing changelog


* 0.4.0
    * Piwik Plugin market release


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