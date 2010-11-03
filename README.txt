=== Kino Events Calendar ===
Contributors: Richard Telford, Brad Brighton, Simon Paul, Seth Ridley
Donate link: http://www.kinocreative.co.uk/wordpress-plugins/kino-events-calendar-plugin-for-wordpress/
Tags: calendar, events
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 1.4.3

== Description ==

This event calendar ONLY WORKS ON WORDPRESS 3.0 and above, and not on MU sites (yet - we're working on it though).

For an earlier version of the calendar please visit http://plugins.svn.wordpress.org/kino-event-calendar-plugin/tag/v1.4/kino-event-calendar-plugin.zip


Once installed the calendar must be dragged into the side bar from Appearance > Widgets. The calendar will then show in the sidebar of every page.

You can easily select an Events page which shows all current and future events by entering the smartcode [events] into whatever page you like.

The admin area enables you to add, edit and remove events quickly and easily, and changing colours is a breeze. The Kino Events Calendar does not use blog posts to handle events but rather lets you add events manually. This flexibility makes integrating the calendar into client sites much more feasible, and the colour changing options allow for simple branding too.

See http://www.8bitbard.co.uk for a working demo.

The main updates are as follows:

1. Added option to "Add End Date".
2. Added Fully featured TinyMCE Editor to allow links, and wide ranging copy editing.
3. Search Engine Friendly URLs (SEF) on all event pages.
4. Added option to choose a thumbnail for a post using the Featured Post utility.
5. Events can now be set to be recurring.
6. Events can now be set to be "All Day Events".
7. Events can be placed into categories and given individual category slug and descriptions.
8. You can now pick a master colour and rollover colour for events, and individual colours for categorised events.
9. Events can be given 'intro text' using the Excerpt functionality.
10. Place the main Events page which lists all future events wherever you want in your site using the shortcode [events].
11. Events now use custom post types and are much easier work with.


== Installation ==
NEW INSTALL:
Install the plugin as you would any other Wordpress plugin by uploading the ZIP via Wordpress or installing from the plugin console. Once installed you will see a new area in the CMS called Events and another called Event Calendar under Settings.

- Settings> Event Calendar: Change the colours of the events and category colours here. Also change date and time format.
- Events> Events: Lists all the events you've added.
- Events> Add New: Add new events.
- Events> Cartegories: Add categories with to organise events under.


UPGRADING
If you have an existing Kino Events Calendar plugin then we would suggest you follow these instructions to ensure an easy upgrade:

1) Deactivate the existing plugin.
2) Delete the existing kino-event-calendar-plugin, preferably using FTP.
3) Install the latest plugin via Wordpress.




== Screenshots ==

See http://www.8bitbard.co.uk for a working demo.

== Changelog ==

= 1.0 =
Vanilla plugin launched

= 1.1 =

Sorted install problems where the plugin was looking in the wrong directory for certain files.

= 1.2 =

Fixed path problem when making changes to Events or Settings.

= 1.3 =

Significant changes to internal path handling. (BMB)
1. Added ke-location.php
2. js/admin.js is now js/admin.js.php
3. Modified many files to utilize ke-location. 

Benefits:

1. Plugin can now be used in WP installs other than DOCUMENT_ROOT
2. Plugin directory can now vary in name without breakage (does NOT yet incorporate any form of multi-install setting protection)

= 2.0 = 
Major updates! This is a complete rewrite of the existing plugin to take advantage of the new WP3 functionality, specifically custom post types. The following are the key updates:

1. Added option to "Add End Date".
2. Added Fully featured TinyMCE Editor to allow links, and wide ranging copy editing.
3. Search Engine Friendly URLs (SEF) on all event pages.
4. Added option to choose a thumbnail for a post using the Featured Post utility.
5. Events can now be set to be recurring.
6. Events can now be set to be "All Day Events".
7. Events can be placed into categories and given individual category slug and descriptions.
8. You can now pick a master colour and rollover colour for events, and individual colours for categorised events.
9. Events can be given 'intro text' using the Excerpt functionality.
10. Place the main Events page which lists all future events wherever you want in your site using the shortcode [events].
11. Events now use custom post types and are much easier work with.





