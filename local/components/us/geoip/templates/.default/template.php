<?php

/**
 * @var array $arParams
 * @var array $arResult
 */

?>
<section class="ip-finder">
    <div class="header">Поиск информации о IP-адресе</div>
    <form name="findIp" class="form-grid" id="formFindIp">
        <input type="text" name="ip" id="ip-input" placeholder="Введите IP-адрес" required>
        <input type="hidden" id="service" value="<?=$arResult['SERVICE']?>">
        <input type="submit" value="Поиск">
    </form>

    <div class="success hide" id="success"></div>
    <div class="errors hide" id="error"></div>
</section>
