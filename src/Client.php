<?php
namespace LangleyFoxall\PressAssociationTvApi;
use LangleyFoxall\PressAssociationTvApi\Objects\Channel;
use LangleyFoxall\PressAssociationTvApi\Objects\Schedule;
use LangleyFoxall\PressAssociationTvApi\Objects\ScheduleItem;

/**
 * Class Client
 * @package LangleyFoxall\PressAssociationTvApi
 */
class Client
{
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Client constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        if (!$apiKey) {
            throw new \InvalidArgumentException('API key is required.');
        }

        $this->apiKey = $apiKey;

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://tv.api.press.net/v1/',
            'timeout' => 3.0,
        ]);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $method, string $endpoint, array $params = [])
    {
        $params['apikey'] = $this->apiKey;

        $response = $this->client->request($method, $endpoint, [
            'query' => $params,
        ]);

        $body = (string) $response->getBody();

        return json_decode($body);
    }

    /**
     * @param string $id
     * @return Channel
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannel(string $id)
    {
        $data = $this->request('get', 'channel/'.$id);

        $channel = new Channel();
        $channel->title = $data->title;
        $channel->images = collect($data->media);

        return $channel;
    }

    /**
     * @param string $channelId
     * @return Schedule
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getScheduleForToday(string $channelId)
    {
        $params = [
            'channelId' => $channelId,
            'start' => date('Y-m-d'),
        ];

        $data = $this->request('get', 'schedule', $params);

        $scheduleItems = [];

        foreach($data->items as $item) {

            $scheduleItem = new ScheduleItem();

            $scheduleItem->title = $item->title;
            $scheduleItem->episodeTitle = $item->asset->title;
            $scheduleItem->dateTime = $item->dateTime;

            $scheduleItem->genres = collect();

            foreach($item->asset->tag as $tag) {
                if (str_contains($tag->id, 'genre:')) {
                    $genreParts = explode(':', $tag->id);
                    $scheduleItem->genres->push($genreParts[1]);
                }
            }

            $scheduleItem->channel = $this->getChannel($item->channel->id);

            $scheduleItems[] = $scheduleItem;
        }

        return new Schedule(collect($scheduleItems));
    }
}