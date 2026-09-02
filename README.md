# TweetDis

A WordPress plugin that turns highlighted quotes into click-to-tweet boxes, inline hints, and shareable images.

This is a maintained, license-free fork of the original TweetDis plugin. It keeps the shortcodes already embedded in older posts working on modern WordPress and PHP 8.3.

**Maintained by [Abundant Designs](https://www.abundantdesigns.com)**  
**Source:** [github.com/robertstaddon/tweetdis](https://github.com/robertstaddon/tweetdis)

Originally created by [Tim Soulo](https://www.tweetdis.com/).

## Why this fork exists

The original plugin stopped receiving updates around WordPress 4.9. It also depended on a remote license server, Twitter API 1.1 OAuth, and old URL shorteners. Those pieces no longer work reliably and caused PHP 8 fatals on live sites.

Version 4.0.0 keeps the designs and shortcodes, and drops everything that required a vendor, an API key, or a purchase code.

## What it does

Visitors click a quote, hint, or image. X / Twitter opens a compose window with the quote and a link back to the post. The plugin does not post on anyone’s behalf and does not call the X API.

| Shortcode | What it renders |
| --- | --- |
| `[tweet_box]…[/tweet_box]` | Styled quote box with a click-to-tweet action |
| `[tweet_dis]…[/tweet_dis]` | Inline highlighted “hint” in the paragraph |
| `[tweet_dis_img]…[/tweet_dis_img]` | Image overlay with a tweet button |

Existing attributes such as `design`, `float`, `url`, `inject`, and `excerpt` still work. You do not need to edit old posts.

## Requirements

- WordPress 5.8 or later
- PHP 8.0 or later (tested with PHP 8.3)
- No license key
- No X / Twitter developer app

## Installation

1. Download the repository as a ZIP, or clone it into `wp-content/plugins/tweetdis`.
2. In WordPress, go to **Plugins** and activate **Tweet Dis**.
3. Open **Tweet Dis** in the admin menu to adjust box, hint, image, and account settings.

If you are replacing an older licensed copy, overwrite `wp-content/plugins/tweetdis/` with this version and keep the plugin activated. Saved settings and post shortcodes stay in place.

## Settings

The settings screen still lets you choose:

- Box, hint, and image templates
- Call-to-action text and colors
- A default X / Twitter handle and preposition (`via`, `by`, `RT`, or none)

Share links always use the post permalink, or the `url` attribute on the shortcode if you set one.

## What changed in 4.0.1

- Maintainer is now [Abundant Designs](https://www.abundantdesigns.com)
- Default call to action is **Click to share**
- Twitter bird icon replaced with the X logo

## What changed in 4.0.0

- PHP 8.3 compatibility, including the `mb_strrpos()` TypeError that crashed some posts
- License activation and remote update checks removed
- Twitter OAuth / API 1.1 image upload removed
- Bitly and TinyURL shorteners removed
- Shortcodes always register, even without a purchase code
- Admin AJAX requires a nonce and the `manage_options` capability

## Credits

- **Original plugin:** [Tim Soulo / TweetDis](https://www.tweetdis.com/)
- **This fork:** [Abundant Designs](https://www.abundantdesigns.com)
- **Repository:** [github.com/robertstaddon/tweetdis](https://github.com/robertstaddon/tweetdis)

TweetDis is not affiliated with, endorsed by, or sponsored by X or Twitter.

## License

GPL-2.0-or-later. See the plugin header and [GNU GPL 2.0](http://www.gnu.org/licenses/gpl-2.0.html).
