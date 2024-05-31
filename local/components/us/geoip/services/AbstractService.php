<?php

namespace components\us\geoip\services;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

abstract class AbstractService implements ServiceInterface
{
    protected mixed $client;
    protected string $apiUrl = '';
    protected string $ip;
    protected bool $needApiKey = false;
    protected string $apiKey = '';
    protected mixed $response = null;
    protected mixed $hlBlock = null;
    protected array $resultFromHl = [];

    const HL_NAME = 'ip_directory';

    /**
     * AbstractService constructor.
     * 
     * @param mixed $client
     */
    public function __construct(mixed $client)
    {
        $this->client = $client;
        $this->hlBlock = $this->getHighLoadBlock();
        
        if (!$this->checkApiKey()) {
            throw new SystemException('API-ключ не установлен!');
        }
    }

    /**
     * Установить IP
     *
     * @param string $ip
     * @return ServiceInterface
     */
    public function setIp(string $ip): ServiceInterface
    {
        $this->ip = $ip;

        return $this;
    }


    /**
     * Метод отправки запроса
     *
     * @return ServiceInterface
     */
    public function sendRequest(): ServiceInterface
    {
        return $this;
    }

    /**
     * Метод получения ответа в общем формате
     *
     * @return array
     */
    public function getResponse(): array
    {
        return [];
    }

    /**
     * Проверка наличия API-ключа
     * 
     * @return bool
     */
    public function checkApiKey(): bool
    {
        if ($this->needApiKey) {
            return (bool)strlen($this->apiKey);
        } else {
            return true;
        }
    }

    /**
     * Получить DataManager HL
     *
     * @return mixed
     */
    protected function getHighLoadBlock(): mixed
    {
        $dataManager = null;

        Loader::includeModule('highloadblock');
        $highLoadData = HighloadBlockTable::getList([
            'filter' => [
                'TABLE_NAME' => self::HL_NAME
            ]
        ])->fetch();

        if ($highLoadData) {
            $entity = HighloadBlockTable::compileEntity($highLoadData);
            $dataManager = $entity->getDataClass();
        } else {
            throw new SystemException('HighLoadBlock не найден!');
        }

        return $dataManager;
    }

    /**
     * Получить информацию о IP из HighLoad блока
     *
     * @return ServiceInterface
     */
    public function getInfoFromHighLoad(): ServiceInterface
    {
        $result = $this->hlBlock::getList([
            'filter' => [
                'UF_IP_ADDRESS' => $this->ip,
            ]
        ])->fetch();

        if (isset($result) && is_array($result) && !empty($result)) {
            $this->resultFromHl = [
                'IP' => $result['UF_IP_ADDRESS'],
                'CITY' => $result['UF_CITY'],
                'REGION' => $result['UF_REGION'],
                'TIMEZONE' => $result['UF_COUNTRY'],
                'COUNTRY' => $result['UF_TIMEZONE'],
            ];
        }

        return $this;
    }

    /**
     * Добавить запись в HL
     *
     * @param array $result
     */
    public function writeResultToHighLoad(array $result): void
    {
        $this->hlBlock::add([
            'UF_IP_ADDRESS' => $result['IP'],
            'UF_CITY' => $result['CITY'] ?? 'не найдено',
            'UF_REGION' => $result['REGION'] ?? 'не найдено',
            'UF_COUNTRY' => $result['TIMEZONE'] ?? 'не найдено',
            'UF_TIMEZONE' => $result['COUNTRY'] ?? 'не найдено',
        ]);
    }
}