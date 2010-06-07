=== Kino Events Calendar ===
Contributors: Richard Telford, Brad Brighton, Simon Paul, Seth Ridley
Donate link: http://www.kinocreative.co.uk/wordpress-plugins/kino-events-calendar-plugin-for-wordpress/
Tags: calendar, events
Requires at least: 2.0.2
Tested up to: 2.9.2
Stable tag: 1.4.3

The Kino Events Calendar is a simple yet flexible calendar plugin for WordPress enabling you to easily add event information manually.

== Description ==


Once installed the calendar will sit in the sidebar. You can also easily select an Events page which shows all current and future events in a logical order.

The admin area enables you to add, edit and remove events quickly and easily, and changing colours is a breeze. The Kino Events Calendar does not use blog posts to handle events but rather lets you add events manually. This flexibility makes integrating the calendar into client sites much more feasible, and the colour changing options allow for simple branding too.

See http://www.toonfood.co.uk for a working demo


== Installation ==

Install the plugin as you would any other Wordpress plugin by uploading the ZIP via Wordpress. Once installed you will see a new tab in the CMS called Events, underneath which are two buttons for Settings and Events.

- Settings - change the colours of the events and select which page you would like a list of current and future events to be displayed.
- Events - Add the events to the calendar. It is self explanatory, but make them active if you want them to show up on your website.

Once you have input all the event information you want then go Appearance>Widgets and move the Kino Events widget into the sidebar. Your event calendar is now live!


== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg
3. screenshot-3.jpg

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
