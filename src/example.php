<?php

require_once __DIR__.'/../vendor/autoload.php';

$client = new \LangleyFoxall\PressAssociationTvApi\Client('API_KEY');

try {
    $schedule = $client->getScheduleForToday([
        'da015cc7-a71e-3137-a110-30dc51262eef',
        '3b205a49-a866-32a0-b391-c727d52b1e79',
        '7cd38a6c-bb1f-306a-a6c6-00c7f7558432',
    ]);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    exit('Error retrieving schedule. Details: '.$e->getMessage());
}

$items = $schedule->all();

$items = $schedule->getByGenre('Football');

var_dump($items);
