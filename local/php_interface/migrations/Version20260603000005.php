<?php

namespace Sprint\Migration;

/**
 * Переводит UF_URL пунктов меню (iblock `menu`) на «чистые» URL без .html.
 * Подменю «Помощь» получает якоря на конкретные секции страницы /help/.
 *
 * Идемпотентно: проверяем по section CODE, обновляем через CIBlockSection::Update
 * (она корректно сбрасывает iblock-кеш). Поиск секций — через CIBlockSection::GetList:
 * Bitrix\Iblock\SectionTable не знает про UF-поля конкретного iblock'а
 * (нужен compileEntityBySection), а Cwrappers это умеют из коробки.
 */
class Version20260603000005 extends Version
{
    protected $description = "Меню: чистые URL + якоря для подменю /help/";

    /** @var array<string, string> section CODE → новый UF_URL */
    private array $urls = [
        // верхние пункты
        'help'         => '/help/',
        'buy'          => '/where-to-buy/',
        'partners'     => '/partners/',
        'contacts'     => '/contacts/',

        // подпункты помощи → якоря на блоки /help/
        'help-payment'  => '/help/#payment',
        'help-delivery' => '/help/#delivery',
        'help-exchange' => '/help/#exchange',
        'help-return'   => '/help/#refund',
        'help-service'  => '/help/#service',
    ];

    /** @var array<string, string> Откат — исходные значения из Version20260516000016 */
    private array $previous = [
        'help'         => '/help.html',
        'buy'          => '/buy.html',
        'partners'     => '/partners.html',
        'contacts'     => '/contacts.html',
        'help-payment'  => '',
        'help-delivery' => '',
        'help-exchange' => '',
        'help-return'   => '',
        'help-service'  => '',
    ];

    public function up(): void
    {
        $this->apply($this->urls);
    }

    public function down(): void
    {
        $this->apply($this->previous);
    }

    /**
     * @param array<string, string> $map
     */
    private function apply(array $map): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->getHelperManager()->Iblock()->getIblockIdIfExists('menu');
        if (!$iblockId) {
            $this->outError('Iblock menu не найден');
            return;
        }

        $sec = new \CIBlockSection();
        $updated = 0;
        $missing = 0;
        foreach ($map as $code => $url) {
            $rs = \CIBlockSection::GetList(
                [],
                ['IBLOCK_ID' => $iblockId, 'CODE' => $code],
                false,
                ['ID', 'UF_URL'],
            );
            $row = $rs->Fetch();
            if (!$row) {
                $missing++;
                $this->out('  %s: секция не найдена', $code);
                continue;
            }
            if ((string) ($row['UF_URL'] ?? '') === $url) {
                continue;
            }
            $sec->Update((int) $row['ID'], ['UF_URL' => $url]);
            $updated++;
        }

        // MenuRepository кеширует дерево через ORM ::setCacheTtl(3600) —
        // это файловый кэш в bitrix/cache/, а не managed cache. clearIblockTagCache
        // его НЕ сбросит, поэтому чистим оба слоя руками:
        \CIBlock::clearIblockTagCache($iblockId);
        \Bitrix\Iblock\IblockTable::cleanCache();
        \Bitrix\Iblock\SectionTable::cleanCache();
        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/iblock/');

        $this->outSuccess('Меню: обновлено URL %d, пропущено отсутствующих %d', $updated, $missing);
    }
}
