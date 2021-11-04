<?php

namespace ReneRoboter\BerlinDistrict\Client;

interface ClientInterface
{
    public function findBy(string $address): array;
}