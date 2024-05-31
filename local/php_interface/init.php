<?php

use Bitrix\Main\Application;

spl_autoload_register(function ($className) {
    $baseDir = Application::getInstance()->getContext()->getServer()->getDocumentRoot() . '/local/';
    $filePath = $baseDir . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($filePath)) {
        include_once $filePath;
    }
});
