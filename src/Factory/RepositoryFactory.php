<?php

namespace ReneRoboter\BerlinDistrict\Factory;

use ReneRoboter\BerlinDistrict\Client\OpenStreetMapClient;
use ReneRoboter\BerlinDistrict\Repository\RepositoryInterface;

class RepositoryFactory
{
    public static function create(string $city): RepositoryInterface
    {
        $fqcn = 'ReneRoboter\\BerlinDistrict\\Repository\\' . ucfirst($city) . 'Repository';
        if (!\class_exists($fqcn)) {
            throw new \InvalidArgumentException('Could not instanciate class!');
        }
        // todo check if class has interface ..
        return new $fqcn(new OpenStreetMapClient());
    }
}