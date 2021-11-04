<?php

declare(strict_types=1);


use ReneRoboter\BerlinDistrict\Factory\RepositoryFactory;

require __DIR__ . '/vendor/autoload.php';

$berlinRepository = RepositoryFactory::create('Berlin');

$addresses = [
    'Reinickendorfer Straße, 50 a, 13347, Berlin',
    'Zimmerstraße, 50, 10117, Berlin',
    'Axel-Springer-Straße, 65, 10888, Berlin',
    'Am Treptower Park, 14, 12435, Berlin',
    'Carl-Schurz-Str., 2, 13597, Berlin',
    //'Frachtstraße, 21, 33602, Bielefeld',
];
foreach ($addresses as $address) {
    var_dump($berlinRepository->findDistrictByAddress($address));
    var_dump($berlinRepository->findSuburbByAddress($address));
}