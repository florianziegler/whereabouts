# Whereabouts

**A user location plugin for WordPress**

Users can set their current location via the WordPress dashboard. A widget displays the location and the corresponding time (zone).

* * *

## Description

Each user can save his/her current location and the corresponding time (zone). The information is stored as user meta data.

The Whereabouts widget displays the location and time (zone) of a specified user (select user in the widget options).

You can - of course - add multiple widgets to show more than one location.

### Dashboard-Widget
Each user can comfortably set her/his current location directly on the WordPress dashboard.

### A little help from Google
Activate the use of the Google Geocoding and Timezone API in the settings: The Plugin will then set the time zone of your whereabouts automatically, when entering a new location.

You can also set the language in which the results of the api requests are returned.

* * *

## Dev

The individual locations are stored in the respective user meta data with the key "whab_location_data", as an array in the following format:

```
{
    'location_name'  => 'Paris, France',
    'utc_difference' => '3600', // offset to UTC in milliseconds
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

* * *

## Changelog

### 0.4.0
* Per user location: Each user can now set her/his individual location via the WordPress dashboard.
* You can also choose which user's location to display per widget.

### 0.3.0
* You can now choose the time format in which the local time is displayed

###0.2.0
* Whereabouts is live

###0.1.0
* Somehow this got skipped...