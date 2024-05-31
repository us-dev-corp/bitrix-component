<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!CModule::IncludeModule('iblock')) {
    return;
}

$arComponentParameters = [
    'PARAMETERS' => [
        'SERVICE' => [
            'NAME' => 'Сервис для поиска',
            'TYPE' => 'LIST',
            'VALUES' => [
                'SypexGeoService' => 'sypexgeo.net',
//                'GeoIpService' => 'geoip.top', - не работает
                'IpStackService' => 'ipstack.com',
            ],
            'REFRESH' => 'Y',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'SypexGeoService',
        ],
    ],
];
