<?php

namespace Sprint\Migration;

/**
 * Удаляет ключ `order_success.signature` из HL «Translations». Подпись «Ваш Gree»
 * под success-сообщением заказа убрана по запросу из шаблона
 * (local/views/order/success.blade.php) — оставлять «висящий» перевод в БД нет
 * смысла.
 *
 * Идемпотентно: если записи уже нет, миграция спокойно завершается.
 */
class Version20260609000001 extends Version
{
    protected $description = "HL Translations: удалить order_success.signature (подпись «Ваш Gree»)";

    private const CODE = 'order_success.signature';

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('HL «Translations» не найден');
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $row = $dataClass::query()->where('UF_CODE', self::CODE)->setSelect(['ID'])->exec()->fetch();
        if (!$row) {
            $this->outSuccess('Ключ %s уже отсутствует', self::CODE);
            return;
        }

        $dataClass::delete((int) $row['ID']);
        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/hl/');
        $this->outSuccess('Ключ %s удалён', self::CODE);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — подпись больше не используется в шаблоне success');
    }
}
