<?php

declare(strict_types=1);

namespace ReneRoboter\BerlinDistrict\Client;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use GuzzleHttp\Client as HttpClient;

class OpenStreetMapClient implements ClientInterface
{
    public const CACHE_LIMIT_ONE_WEEK = 604800;
    protected CacheInterface $cache;

    public function __construct()
    {
        // todo redis cache adapter
        $this->cache = new FilesystemAdapter();
    }

    public function findBy(string $address): array
    {
        $key = md5($address);
        return $this->cache->get($key, function (ItemInterface $item) use ($address) {
            $item->expiresAfter(self::CACHE_LIMIT_ONE_WEEK);

            $client = new HttpClient();
            $response = $client->get(
                $this->buildQueryUrl($address)
            );
            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException(
                    sprintf('Request %s and responses with %s', $address, $response->getStatusCode())
                );
            }
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)[0];
        });
    }

    protected function buildQueryUrl(string $address): string
    {
        $baseUrl = 'https://nominatim.openstreetmap.org/search?q=%s&format=json&polygon=1&addressdetails=1';
        return sprintf($baseUrl, urlencode($address));
    }
}