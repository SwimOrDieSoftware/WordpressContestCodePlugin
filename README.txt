=== Contest Code Checker ===
Contributors: mdedev
Tags: contest codes, contests, contest code checkers
Requires at least: 4.0
Tested up to: 4.7.0
Stable tag: 1.1.4
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

= 1.1.4 =
* Fixed php short-code openings that don't work with some PHP configurations

= 1.1.3 =
* Implemented boolval if the function does not exist which only happens in old versions of PHP

= 1.1.2 =
* Another small fix for older PHP versions dealing with the empty function

= 1.1.1 =
* Small fix for older PHP versions where the empty function can't be a recipient of a functions return value

= 1.1.0 =
* Improved the export all functionality so it is quicker

= 1.0.9 =
* Made it so deleting and exporting of a large set of contest codes (30,000+) worked

= 1.0.8 =
* Fixed a bug where if you entered a code that did not have a prize you would get a message saying the code was already used when it wasn't
* Set the execution time limit for deleting and exporting of contest codes to be unlimited to allow for large number of contest codes

= 1.0.7 =
* Set the execution time limit for import files to be unlimited to allow for large contest codes to be imported

= 1.0.6 =
* Made it so even anonymous users can use the ajax calls

= 1.0.5 =
* Set email to winners to be text/html instead of a plain-text email

= 1.0.4 =
* Included the generic prize information in the email to winners

= 1.0.3 =
* Small label changes on the front-end form
* Added ability to export contest codes
* Added the ability to delete all contest codes
* Added the option to show a message if the code is invalid, if nothing is specified the losing message will be displayed
* Added the option to show a message if the code is already used, if nothing is specified the losing message will be displayed
* Added the ability to specify the pop-up width and height in pixels
* Split the contestant's name into two fields first and last

= 1.0.2 =
* Added the ability to create generic prize information that could be associated with multiple prizes
* Fixed a problem where the "has this code been used" form field was not properly checked on the admin side
* Added the option to have the prize information show as a modal instead of on the page

= 1.0.1 =
* Added options and functionality to email winners with their prize information

= 1.0.0 =
* Initial release
