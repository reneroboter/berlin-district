<?php

declare(strict_types=1);

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

require __DIR__ . '/vendor/autoload.php';

$class = new class() {
    public const CACHE_LIMIT_ONE_WEEK = 604800;
    protected CacheInterface $cache;
    protected array $districts = [
        'Mitte',
        'Friedrichshain-Kreuzberg',
        'Pankow',
        'Charlottenburg-Wilmersdorf',
        'Spandau',
        'Steglitz-Zehlendorf',
        'Tempelhof-Schöneberg',
        'Neukölln',
        'Treptow-Köpenick',
        'Marzahn-Hellersdorf',
        'Lichtenberg',
        'Reinickendorf'
    ];

    public function __construct()
    {
        // todo redis cache adapter
        $this->cache = new FilesystemAdapter();
    }

    public function findDistrictByAddress(string $address): string
    {
        $district = $this->findBy($address)['address']['borough'] ?? throw new RuntimeException(
                'Could not found district'
            );
        if (!\in_array($district, $this->districts, true)) {
            throw new RuntimeException('Could not found Berlin district');
        }
        return (string)$district;
    }

    public function findSuburbByAddress(string $address): string
    {
        return (string)$this->findBy($address)['address']['suburb'];
    }

    protected function findBy(string $address): array
    {
        $key = md5($address);
        return $this->cache->get($key, function (ItemInterface $item) use ($address) {
            $item->expiresAfter(self::CACHE_LIMIT_ONE_WEEK);

            $client = new GuzzleHttp\Client();
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
};

$addresses = [
    'Reinickendorfer Straße, 50 a, 13347, Berlin',
    'Zimmerstraße, 50, 10117, Berlin',
    'Axel-Springer-Straße, 65, 10888, Berlin',
    'Am Treptower Park, 14, 12435, Berlin',
    'Carl-Schurz-Str., 2, 13597, Berlin',
    //'Frachtstraße, 21, 33602, Bielefeld',
];
foreach ($addresses as $address) {
    var_dump($class->findDistrictByAddress($address));
    // var_dump($class->findSuburbByAddress($address));
}