<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\BlogArticleCollection;
use Gree\Contract\Repository\BlogRepositoryInterface;
use Gree\DTO\BlogArticleDto;
use Gree\Enum\BlogCategory;
use Gree\Enum\IblockCode;

final class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function paginate(?BlogCategory $category, int $offset, int $limit): BlogArticleCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Blog);
        if (!$iblockId) {
            return new BlogArticleCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $query = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID', 'CODE', 'ACTIVE_FROM', 'PREVIEW_PICTURE'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                [
                    'CATEGORY_XML_ID'     => 'CATEGORY.ITEM.XML_ID',
                    'READING_TIME_VALUE'  => 'READING_TIME.VALUE',
                ],
            ))
            ->setOrder(['ACTIVE_FROM' => 'DESC', 'SORT' => 'ASC', 'ID' => 'DESC'])
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->setOffset($offset)
            ->setLimit($limit);

        if ($category !== null) {
            $query->where('CATEGORY.ITEM.XML_ID', $category->value);
        }

        $result = $query->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = $this->hydrate($row);
        }

        return new BlogArticleCollection(...$items);
    }

    public function count(?BlogCategory $category): int
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Blog);
        if (!$iblockId) {
            return 0;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $query = $entity::query()
            ->where('ACTIVE', 'Y')
            ->registerRuntimeField('CNT', [
                'data_type' => 'integer',
                'expression' => ['COUNT(DISTINCT %s)', 'ID'],
            ])
            ->setSelect(['CNT']);

        if ($category !== null) {
            $query->where('CATEGORY.ITEM.XML_ID', $category->value);
        }

        $row = $query->exec()->fetch();
        return (int) ($row['CNT'] ?? 0);
    }

    public function recent(BlogCategory $category, int $excludeId, int $limit): BlogArticleCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Blog);
        if (!$iblockId) {
            return new BlogArticleCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $query = $entity::query()
            ->where('ACTIVE', 'Y')
            ->where('CATEGORY.ITEM.XML_ID', $category->value)
            ->setSelect(array_merge(
                ['ID', 'CODE', 'ACTIVE_FROM', 'PREVIEW_PICTURE'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                [
                    'CATEGORY_XML_ID'    => 'CATEGORY.ITEM.XML_ID',
                    'READING_TIME_VALUE' => 'READING_TIME.VALUE',
                ],
            ))
            ->setOrder(['ACTIVE_FROM' => 'DESC', 'SORT' => 'ASC', 'ID' => 'DESC'])
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->setLimit($limit);

        if ($excludeId > 0) {
            $query->whereNot('ID', $excludeId);
        }

        $result = $query->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = $this->hydrate($row);
        }

        return new BlogArticleCollection(...$items);
    }

    public function findByCode(string $code): ?BlogArticleDto
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Blog);
        if (!$iblockId) {
            return null;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $row = $entity::query()
            ->where('ACTIVE', 'Y')
            ->where('CODE', $code)
            ->setSelect(array_merge(
                ['ID', 'CODE', 'ACTIVE_FROM', 'PREVIEW_PICTURE'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                $this->localizedSelect('DETAIL_TEXT'),
                [
                    'CATEGORY_XML_ID'     => 'CATEGORY.ITEM.XML_ID',
                    'READING_TIME_VALUE'  => 'READING_TIME.VALUE',
                ],
            ))
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->setLimit(1)
            ->exec()
            ->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row, includeBody: true);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row, bool $includeBody = false): BlogArticleDto
    {
        $category = BlogCategory::tryFrom((string) ($row['CATEGORY_XML_ID'] ?? '')) ?? BlogCategory::Tips;

        $description = $this->localized($row, 'PREVIEW_TEXT');
        if ($includeBody) {
            $description = $this->localized($row, 'DETAIL_TEXT') ?: $description;
        }

        $code = (string) ($row['CODE'] ?? '');

        return new BlogArticleDto(
            id:          (int) $row['ID'],
            code:        $code,
            title:       $this->localized($row, 'NAME'),
            description: $description,
            image:       !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
            url:         $code !== '' ? '/blog/' . $code . '/' : '',
            date:        $this->formatDate($row['ACTIVE_FROM'] ?? null),
            readingTime: (int) ($row['READING_TIME_VALUE'] ?? 0),
            category:    $category,
        );
    }

    private function formatDate(mixed $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        if ($raw instanceof \Bitrix\Main\Type\Date) {
            return $raw->format('Y-m-d');
        }
        $ts = strtotime((string) $raw);
        return $ts ? date('Y-m-d', $ts) : '';
    }
}
