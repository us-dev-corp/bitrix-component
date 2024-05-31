<?php

namespace components\us\geoip\services;

class IpStackService extends AbstractService
{
    protected string $apiUrl = 'https://api.ipstack.com/';
    protected bool $needApiKey = true;
    protected string $apiKey = '';

    /**
     * Метод отправки запроса
     *
     * @return ServiceInterface
     */
    public function sendRequest(): ServiceInterface
    {
        if (empty($this->resultFromHl)) {
            $this->response = $this->client->get($this->apiUrl . $this->ip . '?access_key=' . $this->apiKey);
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
                'CITY' => $response['city'],
                'REGION' => $response['region_name'],
                'TIMEZONE' => $response['time_zone']['id'],
                'COUNTRY' => $response['country_name'],
            ];

            $this->writeResultToHighLoad($result);
            return $result;
        } else {
            return [];
        }
    }
}