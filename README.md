# 📺⌚️ Press Association TV API Wrapper

[![Packagist](https://img.shields.io/packagist/dt/langleyfoxall/press-association-tv-api-wrapper.svg)](https://packagist.org/packages/langleyfoxall/press-association-tv-api-wrapper/stats)

This package provides a client for the [Press Association TV API](http://developer.press.net/).

It provides access to information regarding TV channels, schedules and schedule items.

> The Press Association (PA) is the national news agency for the UK and Ireland and a leading multimedia content provider across web, mobile, broadcast and print. For the last 145 years PA has been providing fast, accurate feeds of text, data, photos and video. Today the business is increasingly focused on the delivery of complete products for both digital and print clients.

## Installation

To install, just run the following Composer command from the root of your project.

```bash
composer require langleyfoxall/press-association-tv-api-wrapper
```

## Usage

First instantiate the client using your API key.

```php
$client = new \LangleyFoxall\PressAssociationTvApi\Client('API_KEY');
```

### Channels

To retrieve details regarding a channel, call the `getChannel` method, passing it a channel ID. If you wish to retrieve
details on multiple channels at once, you can call the `getChannels` method instead, passing it an array of channel IDs.

These methods will, respectively, return a `Channel` object or a collection of `Channel` objects.

You may wish to surround this method call with a try catch block to catch any networking exceptions.

```php
$channel = $client->getChannel('da015cc7-a71e-3137-a110-30dc51262eef');

$channels = $client->getChannels([
    'da015cc7-a71e-3137-a110-30dc51262eef',
    '3b205a49-a866-32a0-b391-c727d52b1e79',
    '7cd38a6c-bb1f-306a-a6c6-00c7f7558432',
]);
```

#### Example `Channel` object

The example below shows the available public properties that are available with a `Channel` object.

```php
object(LangleyFoxall\PressAssociationTvApi\Objects\Channel)#1804 (2) {
  ["title"]=>
  string(20) "Sky Sports Action HD"
  ["images"]=>
  object(Illuminate\Support\Collection)#1803 (1) {
    ["items":protected]=>
    array(1) {
      [0]=>
      object(stdClass)#1800 (2) {
        ["kind"]=>
        string(12) "picture:logo"
        ["rendition"]=>
        object(stdClass)#1801 (3) {
          ["default"]=>
          object(stdClass)#1798 (1) {
            ["href"]=>
            string(43) "http://tv.static.press.net/logo/p329131.png"
          }
          ["transparent-light"]=>
          object(stdClass)#1794 (1) {
            ["href"]=>
            string(50) "http://tv.static.press.net/logo/p2201707181414.png"
          }
          ["transparent-dark"]=>
          object(stdClass)#1795 (1) {
            ["href"]=>
            string(50) "http://tv.static.press.net/logo/p1201707181414.png"
          }
        }
      }
    }
  }
}
```

### Schedules

You can retrieve the schedule for today by calling the `getScheduleForToday` method, passing it an array of channel IDs. 
Similarly, you can also retrieve the schedule for a specific day using the `getScheduleForDay` method, passing it an
array of channel IDs and a `Carbon` date object. 

Both of these method will return a `Schedule` object.

You may wish to surround this method call with a try catch block to catch any networking exceptions.

```php
$schedule = $client->getScheduleForToday([
    'da015cc7-a71e-3137-a110-30dc51262eef',
    '3b205a49-a866-32a0-b391-c727d52b1e79',
    '7cd38a6c-bb1f-306a-a6c6-00c7f7558432',
]);

$tomorrowSchedule = $client->getScheduleForDay([
        'da015cc7-a71e-3137-a110-30dc51262eef',
        '3b205a49-a866-32a0-b391-c727d52b1e79',
        '7cd38a6c-bb1f-306a-a6c6-00c7f7558432',
    ], \Carbon\Carbon::now()->addDay());
```

You can then retrieve all schedule items, by calling the `Schedule` object's `all` method.

```php
$items = $schedule->all();
```

Alternatively, you can retrieve schedule items filtered by genre, by calling the `Schedule` object's `getByGenre` method.
This method accepts a genre or sport name, such as `Football`, `Tennis`, or `Boxing`.

```php
$items = $schedule->getByGenre('Football');
```

#### Example `ScheduleItem` object

The example below shows the available public properties that are available with a `ScheduleItem` object.

```php
object(LangleyFoxall\PressAssociationTvApi\Objects\ScheduleItem)#1542 (5) {
  ["title"]=>
  string(27) "Hockey: China v Netherlands"
  ["episodeTitle"]=>
  string(27) "Hockey: China v Netherlands"
  ["dateTime"]=>
  object(Carbon\Carbon)#1538 (3) {
    ["date"]=>
    string(26) "2018-07-27 23:00:00.000000"
    ["timezone_type"]=>
    int(1)
    ["timezone"]=>
    string(6) "+00:00"
  }
  ["channel"]=>
  object(LangleyFoxall\PressAssociationTvApi\Objects\Channel)#1533 (2) {
    ["title"]=>
    string(10) "BT Sport 2"
    ["images"]=>
    object(Illuminate\Support\Collection)#1534 (1) {
      ["items":protected]=>
      array(1) {
        [0]=>
        object(stdClass)#1531 (2) {
          ["kind"]=>
          string(12) "picture:logo"
          ["rendition"]=>
          object(stdClass)#1532 (3) {
            ["default"]=>
            object(stdClass)#1529 (1) {
              ["href"]=>
              string(43) "http://tv.static.press.net/logo/p388708.png"
            }
            ["transparent-light"]=>
            object(stdClass)#1530 (1) {
              ["href"]=>
              string(50) "http://tv.static.press.net/logo/p2201805121806.png"
            }
            ["transparent-dark"]=>
            object(stdClass)#1506 (1) {
              ["href"]=>
              string(50) "http://tv.static.press.net/logo/p1201805121806.png"
            }
          }
        }
      }
    }
  }
  ["genres"]=>
  object(Illuminate\Support\Collection)#1535 (1) {
    ["items":protected]=>
    array(1) {
      [0]=>
      string(6) "hockey"
    }
  }
}
```

## Limitations

This library does not currently deal with API pagination. 
This should not be a problem, as channels do not tend to have over 1000 schedule items per day.
