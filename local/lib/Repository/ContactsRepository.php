<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;
use Gree\Contract\Repository\ContactsRepositoryInterface;
use Gree\DTO\ContactAddressDto;
use Gree\DTO\ContactChannelDto;
use Gree\Enum\IblockCode;

final class ContactsRepository extends BaseRepository implements ContactsRepositoryInterface
{
    public function getChannels(): ContactChannelCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::ContactsChannels);
        if (!$iblockId) {
            return new ContactChannelCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('DESCRIPTION'),
                $this->localizedSelect('BUTTON_LABEL'),
                [
                    'BUTTON_URL_VALUE' => 'BUTTON_URL.VALUE',
                    'ICON_CODE_VALUE'  => 'ICON_CODE.VALUE',
                    'LATITUDE_VALUE'   => 'LATITUDE.VALUE',
                    'LONGITUDE_VALUE'  => 'LONGITUDE.VALUE',
                ],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $rows->fetch()) {
            $items[] = new ContactChannelDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'DESCRIPTION'),
                buttonLabel: $this->localized($row, 'BUTTON_LABEL'),
                buttonUrl: (string) ($row['BUTTON_URL_VALUE'] ?? ''),
                iconCode: (string) ($row['ICON_CODE_VALUE'] ?? ''),
                latitude: (string) ($row['LATITUDE_VALUE'] ?? ''),
                longitude: (string) ($row['LONGITUDE_VALUE'] ?? ''),
            );
        }
        return new ContactChannelCollection(...$items);
    }

    public function getAddresses(): ContactAddressCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::ContactsAddresses);
        if (!$iblockId) {
            return new ContactAddressCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        // PHONES — multi-string, дублирует строки в результате; группируем по ID.
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('SCHEDULE'),
                [
                    'PHONES_VALUE'    => 'PHONES.VALUE',
                    'IMAGE_VALUE'     => 'IMAGE.VALUE',
                    'LATITUDE_VALUE'  => 'LATITUDE.VALUE',
                    'LONGITUDE_VALUE' => 'LONGITUDE.VALUE',
                ],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $byId = [];
        while ($row = $rows->fetch()) {
            $id = (int) $row['ID'];
            if (!isset($byId[$id])) {
                $byId[$id] = [
                    'name'      => $this->localized($row, 'NAME'),
                    'schedule'  => $this->localized($row, 'SCHEDULE'),
                    'image_id'  => (int) ($row['IMAGE_VALUE'] ?? 0),
                    'latitude'  => (string) ($row['LATITUDE_VALUE'] ?? ''),
                    'longitude' => (string) ($row['LONGITUDE_VALUE'] ?? ''),
                    'phones'    => [],
                ];
            }
            $phone = (string) ($row['PHONES_VALUE'] ?? '');
            if ($phone !== '' && !in_array($phone, $byId[$id]['phones'], true)) {
                $byId[$id]['phones'][] = $phone;
            }
        }

        $items = [];
        foreach ($byId as $id => $data) {
            $items[] = new ContactAddressDto(
                id: $id,
                name: $data['name'],
                schedule: $data['schedule'],
                phones: $data['phones'],
                imageUrl: $data['image_id'] > 0 ? (string) \CFile::GetPath($data['image_id']) : '',
                latitude: $data['latitude'],
                longitude: $data['longitude'],
            );
        }
        return new ContactAddressCollection(...$items);
    }
}
