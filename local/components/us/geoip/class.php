<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\HttpClient;
use components\us\geoip\services\ServiceInterface;
use components\us\geoip\services\SypexGeoService;
use components\us\geoip\services\IpStackService;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class UsGeoIpSearch extends CBitrixComponent implements Controllerable {

    private string $ajaxErrors = '';

    /**
     * Проверка подгрузки модулей
     */
    private function checkModules(): void
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль Iblock не загружен');
        }
    }

    /**
     * Получить экземпляр объекта используемого сервиса
     *
     * @param string $class
     * @return ServiceInterface|null
     */
    private function getService(string $class): ?ServiceInterface
    {
        $object = null;

        try {
            switch ($class) {
                case 'SypexGeoService':
                    $object = new SypexGeoService($this->getHttpClient());
                    break;
                case 'IpStackService':
                    $object = new IpStackService($this->getHttpClient());
                    break;
                default:
                    $this->ajaxErrors .= 'Сервис не поддерживается!' . PHP_EOL;
                    break;
            }

        } catch (SystemException $e) {
            $this->ajaxErrors .= $e->getMessage() . PHP_EOL;
            ShowError($e->getMessage());
        }

        return $object;
    }

    /**
     * Получить экземпляр HTTP клиента
     *
     * @return HttpClient
     */
    private function getHttpClient(): HttpClient
    {
        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);

        return $httpClient;
    }

    /**
     * Ответ от сервиса
     *
     * @param string $ip
     * @param string $service
     * @return array
     */
    private function getServiceResponse(string $service, string $ip): array
    {
        return $this->getService($service)?->setIp($ip)->getInfoFromHighLoad()->sendRequest()->getResponse() ?? [];
    }

    /**
     * Подготовка параметров компонента
     *
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        return $arParams;
    }

    /**
     * Возвращает массив результатов компонента
     */
    public function getResult(): void
    {
        $this->arResult = $this->arParams;
    }

    /**
     * Выполнение компонента
     */
    public function executeComponent(): void
    {
        try {
            $this->checkModules();
            $this->getResult();
            $this->includeComponentTemplate();
        }
        catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    /**
     * Конфигурация экшенов
     *
     * @return array[][]
     */
    public function configureActions(): array
    {
        return [
            'callIp' => [
                'prefilters' => [],
                'postfilters' => []
            ]
        ];
    }

    /**
     * Экшен запроса информации IP
     *
     * @param string $ip
     * @param string $service
     * @return array
     */
    public function callIpAction(string $ip, string $service)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return [
                'result' => $this->getServiceResponse($service, $ip),
                'ajaxErrors' => $this->ajaxErrors,
            ];
        } else {
            return [
                'result' => [],
                'ajaxErrors' => 'Не валидный IP',
            ];
        }

    }
}