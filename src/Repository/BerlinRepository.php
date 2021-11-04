<?php

declare(strict_types=1);

namespace ReneRoboter\BerlinDistrict\Repository;

use ReneRoboter\BerlinDistrict\Client\ClientInterface;
use RuntimeException;

class BerlinRepository implements RepositoryInterface
{
    protected ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

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

    public function findDistrictByAddress(string $address): string
    {
        $district = $this->client->findBy($address)['address']['borough'] ?? throw new RuntimeException(
                'Could not found district'
            );
        if (!\in_array($district, $this->districts, true)) {
            throw new RuntimeException('Could not found Berlin district');
        }
        return (string)$district;
    }

    public function findSuburbByAddress(string $address): string
    {
        return (string)$this->client->findBy($address)['address']['suburb'];
    }
}