<?php

namespace Sprint\Migration;

/**
 * Локализационный rename: все парные iblock-свойства *_EN → *_UZ.
 *
 * Использует `\CIBlockProperty::Update()` (нативный API) — он корректно
 * сбрасывает iblock tag-cache и инвалидирует автогенерируемые ORM-классы
 * `Bitrix\Iblock\Elements\Element*`. Прямой `PropertyTable::update()` этого
 * НЕ делает — поэтому в первой ревизии миграции часть iblock в админке и в
 * compileEntity по-прежнему показывала `NAME_EN`, хотя в b_iblock_property
 * запись уже была обновлена.
 *
 * Полностью идемпотентна. Состояния каждого свойства при re-run:
 *   - есть только *_EN → переименовать в *_UZ.
 *   - есть только *_UZ → пропустить, уже сделано.
 *   - есть оба (странная гонка) → удалить *_EN, *_UZ оставить как есть.
 *   - нет ни одного → пропустить.
 *
 * После всех правок принудительно сбрасываем кэши: iblock tag-cache на
 * каждый трогаемый iblock, плюс managed cache и Entity::cleanCache().
 *
 * Данные в b_iblock_element_property остаются нетронутыми — они привязаны
 * к PROPERTY_ID, не к CODE.
 */
class Version20260519000001 extends Version
{
    protected $description = "Переименование iblock-свойств _EN → _UZ (идемпотентно)";

    /** @var array<string, string[]> iblock CODE → список base-имён свойств без _EN */
    private array $iblocks = [
        'products' => [
            'NAME', 'PREVIEW_TEXT', 'DETAIL_TEXT',
            'COOLING_POWER', 'HEATING_POWER', 'NOISE',
            'INDOOR_DIMENSIONS', 'OUTDOOR_DIMENSIONS',
            'INDOOR_WEIGHT', 'OUTDOOR_WEIGHT',
            'WARRANTY_TEXT', 'KIT_TEXT', 'INSTALLATION_TEXT',
        ],
        'products_offers' => [
            'COOLING_POWER', 'HEATING_POWER', 'NOISE',
            'INDOOR_DIMENSIONS', 'OUTDOOR_DIMENSIONS',
            'INDOOR_WEIGHT', 'OUTDOOR_WEIGHT',
        ],
        'brands'              => ['NAME', 'DETAIL_TEXT'],
        'home_slider'         => ['NAME', 'SUBTITLE', 'BUTTON_TEXT'],
        'home_gree_cards'     => ['NAME', 'PREVIEW_TEXT'],
        'home_gree_stats'     => ['NAME', 'PREVIEW_TEXT', 'NUMBER_PREFIX', 'NUMBER_SUFFIX'],
        'home_app_features'   => ['NAME', 'PREVIEW_TEXT'],
        'home_technologies'   => ['NAME', 'PREVIEW_TEXT'],
        'brand_history'       => ['NAME', 'DETAIL_TEXT'],
        'brand_why_gree'      => ['NAME', 'PREVIEW_TEXT', 'BUTTON_TEXT'],
        'brand_gree_cards'    => ['NAME', 'PREVIEW_TEXT'],
        'brand_gree_stats'    => ['NAME', 'PREVIEW_TEXT', 'NUMBER_PREFIX', 'NUMBER_SUFFIX'],
        'brand_about_cards'   => ['NAME', 'DETAIL_TEXT'],
        'brand_technologies'  => ['NAME', 'DETAIL_TEXT'],
        'blog'                => ['NAME', 'PREVIEW_TEXT', 'DETAIL_TEXT'],
        // Каталожные дубликаты cards/stats — отдельные iblock из 20260516000001
        'catalog_gree_cards'  => ['NAME', 'PREVIEW_TEXT'],
        'catalog_gree_stats'  => ['NAME', 'PREVIEW_TEXT', 'NUMBER_PREFIX', 'NUMBER_SUFFIX'],
    ];

    public function up(): void
    {
        $this->renameAll(reverse: false);
    }

    public function down(): void
    {
        $this->renameAll(reverse: true);
    }

    private function renameAll(bool $reverse): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $oldSuffix = $reverse ? '_UZ' : '_EN';
        $newSuffix = $reverse ? '_EN' : '_UZ';
        $oldLabel  = $reverse ? '(UZ)' : '(EN)';
        $newLabel  = $reverse ? '(EN)' : '(UZ)';

        $renamed   = 0;
        $dropped   = 0;
        $skipped   = 0;
        $touched   = [];

        foreach ($this->iblocks as $iblockCode => $bases) {
            $iblockId = $this->iblockIdByCode($iblockCode);
            if (!$iblockId) {
                $this->out('  %s: инфоблок не найден, пропуск', $iblockCode);
                continue;
            }

            foreach ($bases as $base) {
                $oldCode = $base . $oldSuffix;
                $newCode = $base . $newSuffix;

                $oldRow = $this->propertyRow($iblockId, $oldCode);
                $newRow = $this->propertyRow($iblockId, $newCode);

                // Уже переименовано — ничего не делаем.
                if (!$oldRow && $newRow) {
                    $skipped++;
                    continue;
                }
                // Свойства нет вовсе — ничего не делаем.
                if (!$oldRow && !$newRow) {
                    continue;
                }
                // Дубликат: новое уже есть + старое осталось → удаляем старое.
                if ($oldRow && $newRow) {
                    if ($this->deleteProperty((int) $oldRow['ID'])) {
                        $dropped++;
                        $touched[$iblockId] = true;
                    }
                    continue;
                }
                // Обычный rename: только старое — переименовываем в новое.
                $newName = (string) preg_replace('/' . preg_quote($oldLabel, '/') . '/u', $newLabel, (string) $oldRow['NAME']);
                if ($this->updateProperty((int) $oldRow['ID'], $newCode, $newName)) {
                    $renamed++;
                    $touched[$iblockId] = true;
                }
            }
            $this->out('  %s обработан', $iblockCode);
        }

        $this->flushCaches(array_keys($touched));

        $this->outSuccess(
            'Готово. Переименовано: %d, удалено дублей: %d, пропущено уже-готовых: %d',
            $renamed, $dropped, $skipped,
        );
    }

    /**
     * @return array{ID: int, NAME: string}|null
     */
    private function propertyRow(int $iblockId, string $code): ?array
    {
        $row = \Bitrix\Iblock\PropertyTable::query()
            ->where('IBLOCK_ID', $iblockId)
            ->where('CODE', $code)
            ->setSelect(['ID', 'NAME'])
            ->exec()
            ->fetch();
        return $row ?: null;
    }

    private function updateProperty(int $propertyId, string $newCode, string $newName): bool
    {
        $prop = new \CIBlockProperty();
        $ok = (bool) $prop->Update($propertyId, ['CODE' => $newCode, 'NAME' => $newName]);
        if (!$ok) {
            $this->out('    FAIL rename id=%d: %s', $propertyId, $prop->LAST_ERROR ?: 'unknown');
        }
        return $ok;
    }

    private function deleteProperty(int $propertyId): bool
    {
        $ok = (bool) \CIBlockProperty::Delete($propertyId);
        if (!$ok) {
            $this->out('    FAIL delete id=%d', $propertyId);
        }
        return $ok;
    }

    /**
     * Сбрасываем все слои кеша, иначе Bitrix Iblock\Iblock::wakeUp() будет
     * отдавать compiled ORM-класс со старыми CODE до перезапуска процесса.
     *
     * @param int[] $iblockIds
     */
    private function flushCaches(array $iblockIds): void
    {
        foreach ($iblockIds as $iblockId) {
            \CIBlock::clearIblockTagCache($iblockId);
        }

        \Bitrix\Iblock\IblockTable::cleanCache();
        \Bitrix\Iblock\PropertyTable::cleanCache();

        $mc = \Bitrix\Main\Application::getInstance()->getManagedCache();
        $mc->cleanDir('b_iblock');
        $mc->cleanDir('b_iblock_property');
    }

    private function iblockIdByCode(string $code): int
    {
        $row = \Bitrix\Iblock\IblockTable::query()
            ->where('CODE', $code)
            ->setSelect(['ID'])
            ->exec()
            ->fetch();
        return (int) ($row['ID'] ?? 0);
    }
}
