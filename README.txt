=== Run Contests, Raffles, and Giveaways with ContestsWP ===
Contributors: mdedev
Tags: contests, giveaways, sweepstakes, raffles
Requires at least: 4.0
Tested up to: 5.5.0
Stable tag: 1.1.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An easy to use WordPress plugin to do giveaways.

== Description ==

<a href="https://www.contestswp.com/" title="ContestsWP">ContestsWP</a> was developed so that you can easily build, launch, and operate contests and giveaways on your WordPress site. Whether you are running a promotion for a few dozen or a few hundred thousand contestants, ContestsWP is an effective and simple plugin which seamlessly integrates with your existing installation and plugins. 

Launched in 2017, ContestsWP is currently used by hundreds of site owners and developers. Below are some examples of how ContestsWP is currently deployed:

ABC Autos is an up and coming car dealership in Detroit, MI. Upon visiting the dealerships website, a user is encouraged to sign up for the dealerships monthly newsletter via the user facing ContestsWP form (ContestsWP Pro can integrate with Mailchimp) and enter their monthly drawing for free oil changes for one year. The user simply enters their name and email address. Once per month, it takes the dealership just a few seconds to randomly pick the winner using the ContestsWP plugin. Since deploying ContestsWP, ABC Autos has seen a 300% increase in newsletter sign ups and has dramatically reduced the staff time needed to run the giveaway.

DEF Store in the Netherlands wants to increase their online sales. In order to do this they send out an email to their mailing list saying that for every online order they make they will get a code to check for a prize. The customer can then use the code on a ContestsWP form to see what they have won.

The free version has the following features:

* Front-end contest form that can be easily customized
* Admin area to manage contest codes with the ability to import codes
* Admin area to view and export contestants

= Additional Features in the <a target="_new" href="https://www.contestswp.com/">Premium Version</a> =
* Set up and run multiple contests
* Add custom fields to user-facing form
* Only show additional fields for winner contest codes
* CAPTCHA support
* Mailchimp integration
* Personal support from the developer
* Run contests of any size with as many contestants as you want
* And much much more
* More info at <a target="_new" href="https://www.contestswp.com">https://www.contestswp.com</a>

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
1. ContestsWP settings area

== Changelog ==

= 1.1.9 =
* Fixed an issue in the uninstall script that was accidently deleting all posts, sorry about that.
* Renamed admin menu from "Contest Code Checker" to "ContestsWP" to be consistent

= 1.1.8 =
* Replaced the old import library with a newer Excel parsing library that is faster and has less bugs.

= 1.1.7 =
* Updated the pot file for translations
* Made some changes to the contestant export to try and speed it up for larger contests

= 1.1.6 =
* Fixed an issue where the contestant's first name wasn't recorded when using a non-AJAX form
* Added the submission date to the contestant's grid and export

= 1.1.5 =
* Added the ability to hide the first name, last name and email fields

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
