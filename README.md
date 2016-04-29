# Whereabouts

**A user location plugin for WordPress**

Users can set their current location via the WordPress dashboard. A widget displays the location and the corresponding time (zone).

* * *

## Description

Each user can save his/her current location and the corresponding time (zone). The information is stored as user meta data.

The Whereabouts widget displays the location and time (zone) of a specified user (select user in the widget options).

You can - of course - add multiple widgets to show more than one user/location.

### Dashboard-Widget

Each user can comfortably set her/his current location directly on the WordPress dashboard.

### A little help from Google

Activate the use of the Google Geocoding and Timezone API in the settings:

You can then use the browser's geolocation API to determine your location, and the Plugin will automatically fetch the time zone of your whereabouts.

Want to use another location? Just type in a location name and the plugin will get all the relevant information for you.

You can also set the language in which the results of the api requests are returned.

### Requirements
* PHP 5.3
* WordPress 3.9.2

* * *

## Displaying the location

### Widget

You can use a standard widget to display the location for a specified user.

There is **no extra styling** for the widget. You can however do it yourself, in your theme. This is what the HTML looks like:

```
<dl class="whab-info">
    <dt class="whab-label whab-label-location">Current Location:</dt>
    <dd class="whab-location">Paris, France</dd>
    <dt class="whab-label whab-label-time">Local Time:</dt>
    <dd class="whab-time">12:34 <span class="whab-timezone-name">Central European Standard Time</span></dd>
</dl>
```

### Shortcode 

You can also generate this HTML anywhere in your theme by using the shortcode:

`[whereabouts user="2" link_location="1" time_format="H:i" show_tz="1"]`

You need to enter a valid user id and the specified user must have saved his/her location for the widget to be displayed.

* * *

## Dev

### User Meta

The individual locations are stored in the respective user meta data with the key "whab_location_data", as an array in the following format:

```
{
    'location_name'  => 'Paris, France',
    'utc_difference' => '3600', // offset to UTC in seconds
    'timezone_name'  => 'Central European Standard Time',
    'geo'            => '48.856614, 2.3522219' // latitude, longitude
}
``` 
   
You can easily access it via the following function:

```php
$location = get_user_meta( $user_id, 'whab_location_data', true );
```

Example, how you can get the geo location:

```php
$geo_location = $location['geo'];
```

### Filter

There is a filter available to change the html output of the widget/shortcode:

**whab_widget_output**

Three argument variables are availabe:

- ```$output``` The html Whereabouts generates by default as a string
- ```$args``` The widget settings as an array
- ```$location``` The location values as an array

You could use it in your theme's ```functions.php``` like this:

```php
add_filter( 'whab_widget_output', 'my_function_to_change_location_widget', 10, 3 );

function my_function_to_change_location_widget( $output, $args, $location ) {

    $output = '<p class="my-location">' . $location['location_name'] . ', ';
    
    $output .= date( $args['time_format'], time() + $location['utc_difference'] );
    if ( $args['show_tz'] ) {
        $output .= ' (' . $location['timezone_name'] . ')';
    }
    return $output . '</p>';
}
```

This will change the html output to:

```<p class="my-location">Paris, France, 12:34 (Central European Standard Time)</p>```

* * *

## Changelog

### 0.6.1

* Update Google Maps API language selector
* Fix PHP 7 issues

### 0.6.0

* You can now use the browser's geolocation API to set your current location.

### 0.5.6

* Bugfix

### 0.5.5

* Added a filter function, which you can use to change the widget's html output to your heart's content.

### 0.5.0

* The admin can now allow/deny access to the dashboard widget by user role
* Display the location anywhere in your theme by using the shortcode [whereabouts]

### 0.4.0
* Per user location: Each user can now set her/his individual location via the WordPress dashboard.
* You can also choose which user's location to display per widget.

### 0.3.0
* You can now choose the time format in which the local time is displayed

###0.2.0
* Whereabouts is live

###0.1.0
* Somehow this got skipped...