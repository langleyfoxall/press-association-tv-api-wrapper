# Press Association TV API Wrapper

This package provides a client for the Press Association TV API.

## Installation

To install, add the following to your `composer.json`.

```json
"repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:langleyfoxall/press-association-tv-api-wrapper.git"
        }
    ],
```

When done, just run the following Composer command from the root of your project.

```bash
composer require langleyfoxall/press-association-tv-api-wrapper
```

## Usage

First instantiate the client using your API key.

```php
$client = new \LangleyFoxall\PressAssociationTvApi\Client('API_KEY');
```

Retrieve the schedule for today by calling the `getScheduleForToday` method, passing it an array of channel IDs. This will return a `Schedule` object.

You may wish to surround this method call with a try catch block to catch any networking exceptions.

```php
$schedule = $client->getScheduleForToday([
    'da015cc7-a71e-3137-a110-30dc51262eef',
    '3b205a49-a866-32a0-b391-c727d52b1e79',
    '7cd38a6c-bb1f-306a-a6c6-00c7f7558432',
]);
```

You can then retrieve all schedule items, by calling the `Schedule` object's `all` method.

$items = $schedule->all();

Alternatively, you can retrieve schedule items filtered by genre, by calling the `Schedule` object's `getByGenre` method.
This method accepts a genre or sport name, such as `Football`, `Tennis`, or `Boxing`.

```php
$items = $schedule->getByGenre('Football');
```

## Example `ScheduleItem` object

The example below shows the available public properties that are available with a `ScheduleItem` object.

```php
object(LangleyFoxall\PressAssociationTvApi\Objects\ScheduleItem)#412 (5) {
  ["title"]=>
  string(16) "Arsenal Classics"
  ["episodeTitle"]=>
  string(16) "Arsenal Classics"
  ["dateTime"]=>
  string(24) "2018-07-26T03:00:00+0000"
  ["channel"]=>
  object(LangleyFoxall\PressAssociationTvApi\Objects\Channel)#398 (2) {
    ["title"]=>
    string(10) "BT Sport 1"
    ["images"]=>
    object(Illuminate\Support\Collection)#399 (1) {
      ["items":protected]=>
      array(1) {
        [0]=>
        object(stdClass)#396 (2) {
          ["kind"]=>
          string(12) "picture:logo"
          ["rendition"]=>
          object(stdClass)#395 (3) {
            ["default"]=>
            object(stdClass)#393 (1) {
              ["href"]=>
              string(43) "http://tv.static.press.net/logo/p388706.png"
            }
            ["transparent-light"]=>
            object(stdClass)#392 (1) {
              ["href"]=>
              string(50) "http://tv.static.press.net/logo/p2201805121804.png"
            }
            ["transparent-dark"]=>
            object(stdClass)#391 (1) {
              ["href"]=>
              string(50) "http://tv.static.press.net/logo/p1201805121804.png"
            }
          }
        }
      }
    }
  }
  ["genres"]=>
  object(Illuminate\Support\Collection)#397 (1) {
    ["items":protected]=>
    array(2) {
      [0]=>
      string(5) "sport"
      [1]=>
      string(13) "football-club"
    }
  }
}
```

## Limitations

This library does not currently deal with API pagination. 
This should not be a problem, as channels do not tend to have over 1000 schedule items per day.
