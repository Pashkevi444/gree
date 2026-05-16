<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\BlogCategory;

final readonly class BlogArticleDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $code,
        public string $title,
        public string $description,
        public string $image,
        public string $url,
        public string $date,
        public int $readingTime,
        public BlogCategory $category,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id:          (int) ($data['id'] ?? 0),
            code:        (string) ($data['code'] ?? ''),
            title:       (string) ($data['title'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            image:       (string) ($data['image'] ?? ''),
            url:         (string) ($data['url'] ?? ''),
            date:        (string) ($data['date'] ?? ''),
            readingTime: (int) ($data['readingTime'] ?? 0),
            category:    BlogCategory::from((string) ($data['category'] ?? 'tips')),
        );
    }

    /**
     * @return array{id:int, code:string, title:string, description:string, image:string, url:string, date:string, readingTime:int, category:string}
     */
    public function toJson(): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'title'       => $this->title,
            'description' => $this->description,
            'image'       => $this->image,
            'url'         => $this->url,
            'date'        => $this->date,
            'readingTime' => $this->readingTime,
            'category'    => $this->category->value,
        ];
    }
}
