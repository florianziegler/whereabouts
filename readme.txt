=== Whereabouts ===
Contributors: florianziegler
Tags: location, timezone, travel, digitalnomad, nomad, dashboard, widget, user, users, usermeta, meta, shortcode
Requires at least: 3.9
Tested up to: 4.0
Stable tag: 0.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Users can set their current location via the WordPress dashboard. A widget displays the location and the corresponding time (zone).

== Description ==

**Please read the FAQ for information about updating from version 0.3.0**

Each user can save his/her current location and the corresponding time (zone). The information is stored as user meta data.

The Whereabouts widget displays the location and time (zone) of a specified user (select user in the widget options).

You can - of course - add multiple widgets to show more than one user/location.

= Dashboard-Widget =
Each user can comfortably set her/his current location directly on the WordPress dashboard.

= A little help from Google =
Activate the use of the Google Geocoding and Timezone API in the settings: The Plugin will then set the time zone of your whereabouts automatically, when entering a new location.

You can also set the language in which the results of the api requests are returned.

= Requirements =
* PHP 5.3
* WordPress 3.9.2

= Support =
Send a friendly email?

= Website =
* [Whereabouts](http://florianziegler.de/whereabouts)
* [Github](https://github.com/florianziegler/whereabouts)

= Author =
* [Website](http://florianziegler.de)
* [Twitter](https://twitter.com/damndirty)


== Installation ==

1. Upload the `whereabouts` folder to your `/wp-content/plugins` directory.

2. Activate the "Whereabouts" plugin in the WordPress administration interface.

3. Go to "Settings" -> "Whereabouts" and activate "Use Google to get location data" and set the "API Request Language".

4. On the dashboard, set your location.

5. Go to "Appearance" -> "Widgets" and add a Wherebouts widget to a sidebar of your choosing. (You have the options to chose the user, whose location you want to display, link the location to Google Maps and display the time zone name.)

**Please note:** The widget will only be displayed if the specified user has set his/her location.

= Styling =

There is **no extra styling** for the widget. You can however do it yourself, in your theme. This is what the HTML looks like:

`<dl class="whab-info">
    <dt class="whab-label whab-label-location">Current Location:</dt>
    <dd class="whab-location">...</dd>
    <dt class="whab-label whab-label-time">Local Time:</dt>
    <dd class="whab-time">12:34 <span class="whab-timezone-name">...</span></dd>
</dl>`

= Shortcode =

You can also generate this HTML code anywhere in your theme by using this shortcode:

`[whereabouts user="2" link_location="1" time_format="H:i" show_tz="1"]`

You need to enter a valid user id and the specified user must have saved his/her location for the widget to be displayed.

== Frequently Asked Questions ==

= Upgrade from version 0.3.0 =

When you upgrade from 0.3.0 to a newer version, you have to (re) enter you location and (re) add the Whereabouts widget.

Go to "Appearance > Widgets" and drag the Whereabouts widget to the sidebar of your choosing.

From version 0.4.0 (or newer) the location is saved _per user_. You can choose the user, whose location you want to display, in the widget's options.


== Screenshots ==

1. Use the Whereabouts dashboard widget to enter your location. If activated, Google will fill out the time zone information for you.
2. Customize the Whereabouts widget


== Changelog ==

= 0.5.0 =

* The admin can now allow/deny access to the dashboard widget by user role
* Display the location anywhere in your theme by using the shortcode [whereabouts]

= 0.4.0 =

* Per user location: Each user can now set her/his individual location via the WordPress dashboard.
* You can also choose which user's location to display per widget.

= 0.3.0 =
* You can now choose the time format in which the local time is displayed

= 0.2.0 =
* Whereabouts is live

= 0.1.0 =
* Somehow this got skipped...