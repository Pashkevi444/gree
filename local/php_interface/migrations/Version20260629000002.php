<?php

namespace Sprint\Migration;

/**
 * Orders.UF_DELIVERY_CITY: string-slug → hlblock-привязка на запись HL «Cities»
 * (single, USER_TYPE_ID='hlblock', MULTIPLE='N'). В админке менеджер видит
 * выпадашку с городами, отображается через UF_NAME_RU.
 *
 * Алгоритм:
 *   1. snapshot текущих значений (orderId → slug) в RAM ДО удаления поля;
 *   2. пересоздать поле как hlblock-привязку (тип менять нельзя);
 *   3. backfill: slug → city.ID через UF_CODE справочника Cities.
 *
 * Идемпотентно: получает текущий тип поля и пересоздаёт только если string.
 */
class Version20260629000002 extends Version
{
    protected $description = "Orders.UF_DELIVERY_CITY: string → hlblock-привязка к Cities";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $ordersHlId = $helper->Hlblock()->getHlblockIdIfExists('Orders');
        $citiesHlId = $helper->Hlblock()->getHlblockIdIfExists('Cities');
        if (!$ordersHlId || !$citiesHlId) {
            $this->outError('HL Orders / Cities не найдены');
            return;
        }

        // 0. Display-поле для hlblock-привязки — UF_NAME_RU.
        $displayField = \Bitrix\Main\UserFieldTable::query()
            ->where('ENTITY_ID', 'HLBLOCK_' . $citiesHlId)
            ->where('FIELD_NAME', 'UF_NAME_RU')
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();
        $displayFieldId = (int) ($displayField['ID'] ?? 0);

        // 1. Snapshot orderId → старый slug (потом нужен для backfill).
        $ordersHl = \Bitrix\Highloadblock\HighloadBlockTable::getById($ordersHlId)->fetch();
        $ordersCls = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($ordersHl)->getDataClass();

        $slugByOrder = [];
        $rows = $ordersCls::query()->setSelect(['ID', 'UF_DELIVERY_CITY'])->exec();
        while ($row = $rows->fetch()) {
            $slug = (string) ($row['UF_DELIVERY_CITY'] ?? '');
            if ($slug !== '' && !ctype_digit($slug)) {
                $slugByOrder[(int) $row['ID']] = $slug;
            }
        }
        $this->out('snapshot: %d заказов со старым string-значением', count($slugByOrder));

        // 2. Удалить старое поле (тип менять нельзя) и пересоздать как hlblock.
        if ($helper->Hlblock()->getField('Orders', 'UF_DELIVERY_CITY')) {
            $helper->Hlblock()->deleteField('Orders', 'UF_DELIVERY_CITY');
        }
        $helper->Hlblock()->saveField('Orders', [
            'FIELD_NAME'        => 'UF_DELIVERY_CITY',
            'USER_TYPE_ID'      => 'hlblock',
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Город',    'en' => 'City'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Город',    'en' => 'City'],
            'SETTINGS' => [
                'HLBLOCK_ID'    => $citiesHlId,
                'HLFIELD_ID'    => $displayFieldId,
                'DISPLAY'       => 'LIST',
                'SHOW_NO_VALUE' => 'N',
            ],
        ]);
        $this->outSuccess('UF_DELIVERY_CITY → hlblock-привязка');

        if (!$slugByOrder) {
            $this->outSuccess('Backfill: заказы со старыми slug отсутствуют');
            return;
        }

        // 3. Резолв slug → city.ID.
        $citiesHl = \Bitrix\Highloadblock\HighloadBlockTable::getById($citiesHlId)->fetch();
        $citiesCls = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($citiesHl)->getDataClass();

        $citiesRes = $citiesCls::query()
            ->whereIn('UF_CODE', array_values($slugByOrder))
            ->setSelect(['ID', 'UF_CODE'])
            ->exec();
        $idBySlug = [];
        while ($row = $citiesRes->fetch()) {
            $idBySlug[(string) $row['UF_CODE']] = (int) $row['ID'];
        }

        // Не нашли — выпадаем на Ташкент (был дефолтом исторически).
        $fallback = $idBySlug['tashkent'] ?? 0;
        $touched = 0;
        foreach ($slugByOrder as $orderId => $slug) {
            $cityId = $idBySlug[$slug] ?? $fallback;
            if ($cityId <= 0) {
                continue;
            }
            $ordersCls::update($orderId, ['UF_DELIVERY_CITY' => $cityId]);
            $touched++;
        }
        $this->outSuccess('Backfill: %d заказов', $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — потеря данных');
    }
}
