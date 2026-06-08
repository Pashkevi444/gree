<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\Color;
use Gree\Enum\ProductType;
use Gree\Enum\SortField;

final readonly class FilterDto extends BaseDto
{
    private const SESSION_KEY = 'catalog_filter';

    /**
     * @param ProductType[] $types
     * @param int[]         $areas
     * @param Color[]       $colors
     */
    public function __construct(
        public array $types = [],
        public int $priceMin = 0,
        public int $priceMax = PHP_INT_MAX,
        public array $areas = [],
        public ?bool $bestseller = null,
        public ?bool $inverterMotor = null,
        public array $colors = [],
        public SortField $sortField = SortField::Popular,
        public int $page = 1,
        public int $perPage = 9,
    ) {}

    public static function fromRequest(\Bitrix\Main\HttpRequest $request): static
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();

        $post = $request->getPostList()->toArray();
        $get = $request->getQueryList()->toArray();
        $params = $post ?: $get;

        $filterKeys = ['type', 'price', 'area', 'bestseller', 'inverter_motor', 'color', 'sort', 'page'];
        $hasFilterParams = (bool) array_intersect_key($params, array_flip($filterKeys));

        if ($hasFilterParams) {
            $session->set(self::SESSION_KEY, $params);
        } elseif ($session->has(self::SESSION_KEY)) {
            $params = $session->get(self::SESSION_KEY);
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

        $colors = [];
        foreach ((array) ($params['color'] ?? []) as $raw) {
            $color = Color::tryFrom((string) $raw);
            if ($color !== null) {
                $colors[] = $color;
            }
        }

        $prices = (array) ($params['price'] ?? []);

        $bestsellerRaw = ($params['bestseller'][0] ?? null);
        $inverterMotorRaw = ($params['inverter_motor'][0] ?? null);

        $page = max(1, (int) ($params['page'] ?? 1));
        $perPage = max(1, (int) ($params['per_page'] ?? 9));

        return new self(
            types: $types,
            priceMin: isset($prices[0]) ? (int) $prices[0] : 0,
            priceMax: isset($prices[1]) ? (int) $prices[1] : PHP_INT_MAX,
            areas: array_map('intval', (array) ($params['area'] ?? [])),
            bestseller: $bestsellerRaw !== null ? $bestsellerRaw === 'yes' : null,
            inverterMotor: $inverterMotorRaw !== null ? $inverterMotorRaw === 'yes' : null,
            colors: $colors,
            sortField: SortField::tryFrom((string) ($params['sort'] ?? '')) ?? SortField::default(),
            page: $page,
            perPage: $perPage,
        );
    }
}
