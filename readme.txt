=== noprofeed.org ===
Contributors: camaleo
Donate link:
Tags: social, help, update, comments, links, plugin, plugins, rss, sidebar, google, facebook, twitter, widget, wordpress
Requires at least: 2.8
Tested up to: 3.9.*
Stable tag: trunk

Help non-profit organizations to spread the word about their activities on the largest possible number of WordPress blogs/sites.

== Description ==

How it works.

1) participating non-profit organizations register for free at noprofeed.org - they only need a site able to send an RSS feed with their news;

2) bloggers and site owners - based on WordPress - looking to help non-profit organizations, install the freely available noprofeed.org plugin; then they can easily configure how the widget used to display the feeds should look like on their blog, including colors and size. Finally the blogger sets a number of parameters about what kind of contents will be presented by the widget, including the country where the non-profit organization operates, the language of the feed, one or more tags (such as: education, environment, etc.). From that moment on the plugin will take care to download the relevant data once a day and to feed it to the widget.

What a better way to help non-profit organizations?

Related Links:

* The <a href="http://noprofeed.org/" title="noprofeed.org: the official site">noprofeed.org</a> site
* For the latest news about our activities please follow us on <a href="http://twitter.com/noprofeed" title="noprofeed.org news">Twitter</a>

Planned for the next release:

* noprofeed.php: automatically upgrade the db structure when releasing a new version (if needed)
* widget: handling ratings
* widget: handling report abuse
* help system, is it needed?
* do you have a good idea you wish to see in this plugin? Please <a href="http://noprofeed.org/contact/">let us know</a>!

Thanks to:

* Andrew Kurtis <a href="http://www.webhostinghub.com/">webhostinghub.com</a> for the Spanish translation

== Installation ==
This section describes how to install the plugin and get it working.

1. Upload the full directory into your `wp-content/plugins` directory.
1. Pending on your server setting you may need to manually copy one file from the plugins directory to your main WordPress installation directory; the plugin will let you know what to copy and where if it will not be able to do it itself.
1. Activate the plugin through the 'Plugins' menu in the WordPress Administration page.
1. Open the "Settings | noprofeed.org" menu and set the widget look the way you want it.
1. Click on the "Update options" button &ndash; or the "Update options and reload the feeds cache" if you changed any of the contents filters
1. Open the "Appearance | Widgets" menu and drag the noprofeed.org widget in the area of your theme you better like


== Frequently Asked Questions ==
= If I need help with this plugin, where can I get support? =

For an updated list of FAQ, please check the <a href="http://noprofeed.org/faq/">the FAQ page</a> &ndash; if you cannot find your answer there, please send us an email through the <a href="http://noprofeed.org/contact/">contact page</a>


== Screenshots ==
1. The plugin settings allow to configure the widget graphical look like, for example: width, colour of each element, how many feeds and what type of content to show, etc. Changing the options will immediately reflect the sample widget look so you can easily configure the widget before publishing it.
2. When adding the widget you can change its title
3. Example of how the widget looks like on the default home page


== Changelog ==
= 1.2.1 (28 April 2014) =
Added the Spanish translation, thanks to Andrew Kurtis http://www.webhostinghub.com/

= 1.1.2 (17 December 2012) =
Added some missing images and fixed a minor bug.

= 1.1.1 (22 November 2011) =
Added some missing images and fixed a minor bug.

= 1.1 (21 November 2011) =
Added the Italian translation - Please help us by translating the plugin in your language!

= 1.0.8.1 (24 July 2011) =
Replaced few lines of a Creative Commons licensed code used to handle the mailing list subscription as per kind request from wordpress.org

= 1.0.8 (23 July 2011) =
All the images and javascript code is now loaded from the same server where the plugin is installed.
Last year I tought it might be useful to have the myeasy common images and code loaded from a CDN to avoid having to update all the plugins in the series each time an image changes and to load pages faster; so I moved all the common items to a CDN.
Today I received a kind email from wordpress.org letting me know that "there a potential malicious intent issue here as you {me} could change the files to embed malicious code and nobody would be the wiser" and asking me to change the code.
I promptly reacted to show everyone that I am 101% in bona fide and here is a new version.

= 1.0.1 (13 July 2011) =
Limiting the number of words to 20 in the widget feed content - even when the "The title and the entire feed content" option is selected.
Minor fixes in the code.

= 1.0.0 =
This is the first release.

== Upgrade Notice ==
Upgrade ad usual, you might need to reactivate the plugin after the upgrade.
