<?php

require_once __DIR__.'/../vendor/autoload.php';

$client = new \LangleyFoxall\PressAssociationTvApi\Client('***REMOVED***');

$schedule = $client->getScheduleForToday('da015cc7-a71e-3137-a110-30dc51262eef');

$items = $schedule->all();

$items = $schedule->getByGenre('Boxing');

var_dump($items);
