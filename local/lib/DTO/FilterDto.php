<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\ProductType;
use Gree\Enum\SortField;

final readonly class FilterDto extends BaseDto
{
    /**
     * @param ProductType[] $types
     * @param int[]         $areas
     */
    public function __construct(
        public array $types = [],
        public int $priceMin = 0,
        public int $priceMax = PHP_INT_MAX,
        public array $areas = [],
        public ?bool $bestseller = null,
        public ?bool $inverterMotor = null,
        public SortField $sortField = SortField::Popular,
        public int $page = 1,
        public int $perPage = 3,
    ) {}

    private const SESSION_KEY = 'catalog_filter';

    public static function fromRequest(\Bitrix\Main\HttpRequest $request): static
    {
        $params = $request->getQueryList()->toArray();

        $session = \Bitrix\Main\Application::getInstance()->getSession();

        $filterKeys = ['type', 'price', 'area', 'bestseller', 'inverter_motor', 'sort'];
        $hasFilterParams = (bool) array_intersect_key($params, array_flip($filterKeys));

        if ($hasFilterParams) {
            $session->set(self::SESSION_KEY, $params);
        } elseif ($session->has(self::SESSION_KEY)) {
            $saved = $session->get(self::SESSION_KEY);
            $params = array_merge($saved, array_filter($params, fn($v) => $v !== '' && $v !== null));
        }

        return static::fromArray($params);
    }

    public static function fromArray(array $params): static
    {
        $types = [];
        foreach ((array) ($params['type'] ?? []) as $raw) {
            $type = ProductType::tryFrom((string) $raw);
            if ($type !== null) {
                $types[] = $type;
            }
        }

        $prices = (array) ($params['price'] ?? []);

        $bestsellerRaw = ($params['bestseller'][0] ?? null);
        $inverterMotorRaw = ($params['inverter_motor'][0] ?? null);

        $page = max(1, (int) ($params['page'] ?? 1));
        $perPage = max(1, (int) ($params['per_page'] ?? 12));

        return new self(
            types: $types,
            priceMin: isset($prices[0]) ? (int) $prices[0] : 0,
            priceMax: isset($prices[1]) ? (int) $prices[1] : PHP_INT_MAX,
            areas: array_map('intval', (array) ($params['area'] ?? [])),
            bestseller: $bestsellerRaw !== null ? $bestsellerRaw === 'yes' : null,
            inverterMotor: $inverterMotorRaw !== null ? $inverterMotorRaw === 'yes' : null,
            sortField: SortField::tryFrom((string) ($params['sort'] ?? '')) ?? SortField::default(),
            page: $page,
            perPage: $perPage,
        );
    }
}
