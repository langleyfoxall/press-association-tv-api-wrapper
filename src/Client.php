<?php
namespace LangleyFoxall\PressAssociationTvApi;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Carbon\Carbon;
use DateTime;
use LangleyFoxall\PressAssociationTvApi\Objects\Channel;
use LangleyFoxall\PressAssociationTvApi\Objects\Schedule;
use LangleyFoxall\PressAssociationTvApi\Objects\ScheduleItem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

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
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * Client constructor.
     * @param string $apiKey
     * @param CacheItemPoolInterface $cacheItemPool
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

        $this->setupCache();
    }

    private function setupCache()
    {
        $filesystemAdapter = new Local('/tmp/press-association-tv-api-wrapper');
        $filesystem        = new Filesystem($filesystemAdapter);

        $pool = new FilesystemCachePool($filesystem);

        $this->cache = $pool;
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
        $cacheKey = sha1(serialize(func_get_args()));

        try {
            $cacheItem = $this->cache->getItem($cacheKey);
        } catch (InvalidArgumentException $e) {
            exit('Unable to store retrieve cache item. Cache key is unsupported.');
        }

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $params['apikey'] = $this->apiKey;

        $response = $this->client->request($method, $endpoint, [
            'query' => $params,
        ]);

        $body = (string) $response->getBody();
        $decoded = json_decode($body);

        $cacheItem->set($decoded);
        $cacheItem->expiresAt((new DateTime())->modify('+1 month'));

        $this->cache->save($cacheItem);

        return $decoded;
    }

    /**
     * @param string $id
     * @return Channel
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannel(string $id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Channel ID is required.');
        }

        $data = $this->request('get', 'channel/'.$id);

        $channel = new Channel();
        $channel->id = $data->id;
        $channel->title = $data->title;
        $channel->images = collect($data->media);

        return $channel;
    }

    /**
     * @param array $ids
     * @return \Illuminate\Support\Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannels(array $ids)
    {
        if (!$ids) {
            throw new \InvalidArgumentException('Channel IDs is required.');
        }

        $channels = collect();

        foreach($ids as $id) {

            if (!$id) {
                continue;
            }

            $channels->push($this->getChannel($id));

        }

        return $channels;
    }

    /**
     * @param array $channelIds
     * @return Schedule
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getScheduleForToday(array $channelIds)
    {
        return $this->getScheduleForDay($channelIds, Carbon::now());
    }

    public function getScheduleForDay(array $channelIds, Carbon $date)
    {
        if (!$channelIds) {
            throw new \InvalidArgumentException('Channel IDs is required.');
        }

        $scheduleItems = collect();

        foreach($channelIds as $channelId) {

            if (!$channelId) {
                continue;
            }

            $params = [
                'channelId' => $channelId,
                'start' => $date->format('Y-m-d'),
                'end' => $date->copy()->addDay()->format('Y-m-d'),
                'limit' => 1000,
            ];

            $data = $this->request('get', 'schedule', $params);

            foreach ($data->items as $item) {

                $scheduleItem = new ScheduleItem();

                $scheduleItem->title = $item->title;
                $scheduleItem->episodeTitle = $item->asset->title;
                $scheduleItem->dateTime = new Carbon($item->dateTime);

                $scheduleItem->genres = collect();

                foreach ($item->asset->tag as $tag) {
                    if (str_contains($tag->id, 'genre:')) {
                        $genreParts = explode(':', $tag->id);
                        $scheduleItem->genres->push($genreParts[1]);
                    }
                }

                $scheduleItem->channel = $this->getChannel($item->channel->id);

                $scheduleItems->push($scheduleItem);
            }
        }

        return new Schedule($scheduleItems);
    }
}
