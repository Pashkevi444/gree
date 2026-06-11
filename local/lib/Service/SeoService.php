<?php

declare(strict_types=1);

namespace Gree\Service;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Gree\Contract\Repository\SeoRepositoryInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\DTO\SeoDto;
use Gree\Enum\IblockCode;
use Gree\Logging\FileLogger;

final class SeoService extends BaseService implements SeoServiceInterface
{
    public function __construct(
        private readonly SeoRepositoryInterface $seo,
        private readonly LanguageServiceInterface $language,
    ) {}

    public function forPage(string $code): SeoDto
    {
        try {
            $dto = $this->seo->findByPageCode($code, $this->language->get());
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'code' => $code,
                'exception' => $e,
            ]);
            $dto = null;
        }
        return $dto ?? new SeoDto(title: '');
    }

    public function forElement(IblockCode $iblock, int $elementId): ?SeoDto
    {
        try {
            $iblockId = $this->resolveIblockId($iblock);
            if ($iblockId <= 0) {
                return null;
            }
            return $this->seo->findByElement($iblockId, $elementId, $this->language->get());
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'iblock' => $iblock->value,
                'elementId' => $elementId,
                'exception' => $e,
            ]);
            return null;
        }
    }

    /**
     * Mirrors BaseRepository::resolveIblockId — duplicated here so SeoService
     * doesn't have to extend a repository base just for one helper.
     */
    private function resolveIblockId(IblockCode $code): int
    {
        Loader::includeModule('iblock');

        $row = IblockTable::query()
            ->where('API_CODE', $code->value)
            ->setSelect(['ID'])
            ->setCacheTtl(2592000) // маппинг API_CODE → ID меняется только при пересоздании iblock
            ->exec()
            ->fetch();

        return (int) ($row['ID'] ?? 0);
    }
}
