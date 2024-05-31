<?php

namespace components\us\geoip\services;

class SypexGeoService extends AbstractService
{
    protected string $apiUrl = 'https://api.sypexgeo.net/json/';

    /**
     * Метод отправки запроса
     *
     * @return ServiceInterface
     */
    public function sendRequest(): ServiceInterface
    {
        if (empty($this->resultFromHl)) {
            $this->response = $this->client->get($this->apiUrl . $this->ip);
        }

        return $this;
    }

    /**
     * Получить ответ в общем формате
     *
     * @return array
     */
    public function getResponse(): array
    {
        if (!empty($this->resultFromHl)) {
            return $this->resultFromHl;
        }

        if ($this->response) {
            $response = json_decode($this->response, true);
            $result = [
                'IP' => $response['ip'],
                'CITY' => $response['city']['name_ru'],
                'REGION' => $response['region']['name_ru'],
                'TIMEZONE' => $response['region']['timezone'],
                'COUNTRY' => $response['country']['name_ru'],
            ];

            $this->writeResultToHighLoad($result);
            return $result;
        } else {
            return [];
        }
    }
}