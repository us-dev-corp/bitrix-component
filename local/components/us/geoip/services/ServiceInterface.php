<?php

namespace components\us\geoip\services;

interface ServiceInterface
{
    public function __construct(mixed $client);

    public function setIp(string $ip): ServiceInterface;

    public function getResponse(): array;

    public function checkApiKey(): bool;

    public function sendRequest(): ServiceInterface;

    public function getInfoFromHighLoad(): ServiceInterface;

    public function writeResultToHighLoad(array $result): void;
}