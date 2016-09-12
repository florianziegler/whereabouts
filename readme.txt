=== Whereabouts ===
Contributors: florianziegler
Tags: location, timezone, travel, digitalnomad, nomad, dashboard, widget, user, users, usermeta, meta, shortcode
Requires at least: 3.9
Tested up to: 4.5
Stable tag: 0.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Users can set their current location via the WordPress dashboard. A widget displays the location and the corresponding time (zone).

== Description ==

Each user can save his/her current location and the corresponding time (zone). The information is stored as user meta data.

The Whereabouts widget displays the location and time (zone) of a specified user (select user in the widget options).

You can - of course - add multiple widgets to show more than one user/location.

= Dashboard-Widget =
Each user can comfortably set her/his current location directly on the WordPress dashboard.

= A little help from Google =
Activate the use of the Google Geocoding and Timezone API in the settings:

You can then use the browser's geolocation API to determine your location, and the Plugin will automatically fetch the time zone of your whereabouts.

Want to use another location? Just type in a location name and the plugin will get all the relevant information for you.

You can also set the language in which the results of the api requests are returned.

= Requirements =
* PHP 5.3
* WordPress 3.9.2
* In some modern browsers your website needs to have a working SSL-certificate in order to use the geolocation api.

= Support =
* [Open a new topic here](https://wordpress.org/support/plugin/whereabouts)

= Website =
* [Whereabouts](https://where.abouts.io/)
* [Github](https://github.com/florianziegler/whereabouts)

= Author =
* [Website](https://florianziegler.de/)
* [Twitter](https://twitter.com/damndirty)


== Installation ==

1. Upload the `whereabouts` folder to your `/wp-content/plugins` directory.

2. Activate the "Whereabouts" plugin in the WordPress administration interface.

3. Go to "Settings" -> "Whereabouts" and activate "Use Google to get location data", enter you API key and set the "API Request Language".

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

= Filter =

There is a filter available to change the html output of the widget/shortcode:

**whab_widget_output**

Three argument variables are availabe:

- `$output` The html Whereabouts generates by default as a string
- `$args` The widget settings as an array
- `$location` The location values as an array

You could use it in your theme's `functions.php` like this:

`
add_filter( 'whab_widget_output', 'my_function_to_change_location_widget', 10, 3 );

function my_function_to_change_location_widget( $output, $args, $location ) {

    $output = '<p class="my-location">' . $location['location_name'] . ', ';

    $output .= date( $args['time_format'], time() + $location['utc_difference'] );
    if ( $args['show_tz'] ) {
        $output .= ' (' . $location['timezone_name'] . ')';
    }
    return $output . '</p>';
}
`

This will change the html output to:

`<p class="my-location">Paris, France, 12:34 (Central European Standard Time)</p>`


== Frequently Asked Questions ==

= Why am I not getting any results when I use my browser's geolocation api? =

Usually it is a privacy issue: Make sure your browser and your website are allowed to use the location functionality of your device (eg. your smartphone). In general these settings are found in your device's privacy or location settings.

= Upgrade from version 0.3.0 =

When you upgrade from 0.3.0 to a newer version, you have to (re) enter you location and (re) add the Whereabouts widget.

Go to "Appearance > Widgets" and drag the Whereabouts widget to the sidebar of your choosing.

From version 0.4.0 (or newer) the location is saved _per user_. You can choose the user, whose location you want to display, in the widget's options.



== Screenshots ==

1. Use the Whereabouts dashboard widget to enter your location. If activated, Google will fill out the time zone information for you.
2. Customize the Whereabouts widget


== Changelog ==

= 0.7.0 =

* Adapt to changes in Google's APIs
* Important: You now need a Google API key to use the geolocation feature!
* Also: In most modern browser your website needs a working SSL-certificate to use the geolocation feature!

= 0.6.1 =

* Update Google Maps API language selector
* Fix PHP 7 issues

= 0.6.0 =

* You can now use the browser's geolocation API to set your current location.

= 0.5.6 =

* Bugfix

= 0.5.5 =

* Added a filter function, which you can use to change the widget's html output to your heart's content.

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