=== tweetdis ===
Tags: tweetdis, tweet, wordpress plugin, tweetable quotes, content
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 4.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

TweetDis creates click-to-tweet quotes in your articles from existing [tweet_box], [tweet_dis], and [tweet_dis_img] shortcodes.

== Installation ==
**To install the plugin manually in WordPress:**

1. Login as Admin on your WordPress blog.
2. Click on the "Plugins" tab in the left menu.
3. Select "Add New"
4. Click on "Upload" at the top of the page.
5. Select 'tweetdis.zip' on your computer, and upload. Activate the plugin once it is uploaded.

**To install the plugin manually with FTP:**

1. Unzip 'tweetdis.zip' file. Upload that folder to the '/wp-content/plugins/' directory.
2. Login to your WordPress dashboard and activate the plugin through the "Plugins" tab in the left menu.

== Frequently Asked Questions ==
= Does this plugin still require a license key? =

No. Version 4.0.0 is a local, license-free renderer for existing TweetDis shortcodes.

= Does it post to X / Twitter automatically? =

No. Click-to-tweet links open the X / Twitter web compose window. There is no API integration.

== Changelog ==
= 4.0.0 =
* PHP 8.3 compatibility (fixes mb_strrpos TypeError)
* Removed license activation and remote update checks
* Removed Twitter OAuth / API 1.1 image upload
* Removed Bitly and TinyURL shorteners
* Shortcodes always register without a purchase code
* Admin AJAX now requires a nonce and manage_options capability

= 3.5.4 =
* Max character count increased
* Compatibility with WordPress 4.9.1

= 3.5.3 =
* Compatibility with PHP 7
* Compatibility with WordPress 4.6.1

= 3.5.2 =
* Minor fixes: auto update and css

= 3.5.1 =
* Twitter handle with "via", "by" and "none" prefixes moved to the end of a tweet
* Minor performance optimizations

= 3.5 =
* increased performance speed
* improved compatibility with major page builders
* full compatibility with WordPress 4.4
* optimized css and js
* general bug fixes
