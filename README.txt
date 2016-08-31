=== Contest Code Checker ===
Contributors: mdedev
Tags: contest codes, contests, contest code checkers
Requires at least: 4.0
Tested up to: 4.5.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to allow for people to enter in a contest code and see if they won a prize

== Description ==

This plugin was created for people who have contest codes on a physical or electronic product that are tied to a prize. The user who receives the contest code can go to a page or post and enter in their name, email and code and get back if they have won or lost.

The following capabilies exist:

* User-facing form to check contest codes
* Admin area to manage contest codes with the ability to import codes
* Admin area to view contestants, also export contestants

== Installation ==

1. Upload the `contest-code-checker` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add and/or import your contest codes via the admin area
1. Set the general options like winning and losing text and date ranges for when the contest is active
1. Add the short code [contest_code_checker] to any page or post

== Frequently Asked Questions ==

= Why can't this plugin do X? =

Good question, maybe I didn't think about having this feature or didn't feel anyone would use it.  Contact me at mikede@mde-dev.com and
I will see if I can get it added for you.

= Is there a GitHub Repository? =

Yes, that is where the main development is done - https://github.com/SwimOrDieSoftware/WordpressContestCodePlugin

== Screenshots ==

1. Contest submission form
1. Contestants admin listing
1. Contest code admin listing
1. Contest code admin import area
1. Contest code checker settings area

== Changelog ==

= 1.0.2 =
* Added the ability to create generic prize information that could be associated with multiple prizes
* Fixed a problem where the "has this code been used" form field was not properly checked on the admin side
* Added the option to have the prize information show as a modal instead of on the page

= 1.0.1 =
* Added options and functionality to email winners with their prize information

= 1.0.0 =
* Initial release
