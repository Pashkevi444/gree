# Gree — сайт официального дистрибьютора в Узбекистане

PHP 8.4 · Bitrix CMS · Blade · Symfony DI · Composer · PHPUnit 11

---

## Быстрый старт

```bash
composer install
composer test:unit          # юниты на стабах Bitrix — мгновенно, без БД
composer test:integration   # реальные репозитории через bootstrap Bitrix + транзакция-rollback
composer test               # оба прогона подряд

./vendor/bin/phpunit --filter testHappyPathPlacesOrderAndEmptiesCart
```

**Запустить интеграцию напрямую, не через composer** (понадобится подкинуть `-d short_open_tag=On` для Bitrix `tools.php`):

```bash
php -d short_open_tag=On vendor/bin/phpunit --testsuite Integration
```

Два режима ходят через **один** `local/tests/bootstrap.php`. Переключение —
env `GREE_TEST_INTEGRATION=1` ИЛИ argv (`--testsuite Integration`,
`local/tests/Integration/...`).

- **unit (по умолчанию)** — `class_alias` на стабы из `local/tests/Stub/`, без живой БД.
- **integration** — bootstrap'ит настоящий `prolog_before.php` Bitrix, репозитории идут к живой БД, **каждый тест обёрнут в транзакцию** через `TransactionService` → автоматический rollback в tearDown.

**Почему `./vendor/bin/phpunit` без аргументов запускает только Unit:** `class_alias` стабов и боевые `\Bitrix\…` классы нельзя смешивать в одном php-процессе (alias необратим). Поэтому в `phpunit.xml` стоит `defaultTestSuite="Unit"`, а оба сьюта вместе гоняется через `composer test`, который запускает unit и integration в **разных** php-процессах.

Что распознаётся как «integration»:
- composer-скрипт `test:integration` (он же выставляет `GREE_TEST_INTEGRATION=1` и `-d short_open_tag=On`);
- ручной `./vendor/bin/phpunit --testsuite Integration` (bootstrap сам видит argv);
- ручной `./vendor/bin/phpunit local/tests/Integration/SomeTest.php` — тоже.

При ручном запуске нужно вручную подкинуть `-d short_open_tag=On` (или прописать в php.ini), иначе bootstrap сразу остановится с понятным сообщением. Bitrix `tools.php` использует короткие теги `<?`, PHP 8.4 CLI их по умолчанию отключает.

Bootstrap (`local/tests/bootstrap.php`) также:
- авто-детектит MySQL-сокет (MAMP / brew / apt / rpm дефолтные пути; override через `GREE_TEST_MYSQL_SOCKET=...`);
- объявляет `LANGUAGE_ID`/`SITE_ID` константы, которые обычно ставит HTTP-обёртка;
- снимает Bitrix exception handler — он в CLI ломается в собственной обработке (LogFormatter падает на `new DateTime()` в shutdown), маскируя реальные исключения.

тут записать необходимо сервер для интеграционных тестов
```bash
TEST_BASE_URL=https://staging.example.com composer test:integration 
```

---

## Архитектура

### Жизненный цикл запроса

```
HTTP → .htaccess → /bitrix/routing_index.php
  → local/routes/web.php | api.php
  → Closure → App::get(Controller::class)->method(...)
        ├── $this->applySeo($this->seo->forPage('home'))   // SEO (HL «Seo» + IPROPERTY)
        ├── $this->addPageAssets('home')                   // /dist/styles/*.css + /dist/scripts/*.js
        └── return $this->view('home/index', new HomeViewData(...))
                   └── Blade::factory()->make() → render() → HttpResponse
                          + security headers
                          + outgoing cookies (CSRF, cart_token) flushed
```

API-маршруты возвращают `$this->json($data, $status)` — Blade-рендер пропускается.

### Слои

| Слой | Namespace | Назначение |
|------|-----------|-----------|
| Controller | `Gree\Controller` | Тонкий: парсит вход, дёргает сервис, возвращает `view()`/`json()` |
| Service | `Gree\Service` | Бизнес-логика, оркестрация репозиториев, fail-soft логирование |
| Repository | `Gree\Repository` | D7 iblock / HL-block запросы, возвращают доменные DTO |
| DTO | `Gree\DTO` | `final readonly`, `fromArray()` / `toJson()` named constructors |
| Collection | `Gree\Collection` | Типизированные коллекции (extends `BaseCollection`) |
| Enum | `Gree\Enum` | Backed string enums с `label()` / `slug()` |
| View | `Gree\View` | Readonly ViewData DTO (extends `BaseViewData`), идут в Blade |
| Security | `Gree\Security` | CSRF, ApiGuard, AccessDeniedException |
| Http | `Gree\Http` | `BitrixHttpContext` / `InMemoryHttpContext` — обёртка над cookies/headers/server |
| Logging | `Gree\Logging` | `FileLogger` (singleton, фaйлы в `local/logs/`) |
| Helpers | `Gree\Helpers` | Static facades — `Language::t`, `Route::to` |
| Routing | `Gree\Helpers\Route` | Генерация URL из именованных маршрутов через Bitrix Router |

### Базовые классы (последний принцип SOLID — depend on abstractions)

| Базовый класс | Используется в |
|---------------|----------------|
| `Gree\Controller\BaseController` | Все контроллеры. `view()`, `json()`, `applySeo()`, `addPageAssets()`, `getRequest()` |
| `Gree\Service\BaseService` | Маркер для всех сервисов (включая security) |
| `Gree\Repository\BaseRepository` | Для iblock-репозиториев — `resolveIblockId()`, `localizedSelect()`, `localized()` |
| `Gree\Repository\BaseHlblockRepository` | Для HL-block репозиториев (Cart, CartItem, Translation, Seo). `hlblock(): HlblockCode` указывает enum-кейс; типизированные `query()/addRow()/updateRow()/deleteRow()` — PhpStorm-aware, без скрытого `class-string::method()` chain |
| `Gree\DTO\BaseDto` | Все DTO, требует `fromArray(array): static` |
| `Gree\Collection\BaseCollection` | Все коллекции, требует `itemClass()` |
| `Gree\View\BaseViewData` | Все ViewData, `toArray()` |
| `Gree\Service\Exception\BaseServiceException` | Маркер-база доменных сервисных исключений (CheckoutValidation, EmptyCart, OfferNotFound). Контроллер может ловить базу и единообразно мапить в 422. |
| `Gree\Security\BaseSecurityException` | Маркер-база security-исключений (AccessDenied и др.) → 403 в контроллере. |
| `Gree\Http\BaseHttpContext` | Абстрактный родитель реализаций `HttpContextInterface` (BitrixHttpContext / InMemoryHttpContext). |
| `Gree\Helpers\BaseHelper` | Маркер static-facade хелперов (Language, Route). Конструктор приватный — `new` запрещён. |
| `Gree\DB\BaseDbService` | Маркер сервисов слоя БД (TransactionService и будущие). |

Все базовые классы — `abstract`.

---

## Структура проекта

```
/
├── composer.json           # PSR-4: Gree\ → local/lib/
├── phpunit.xml             # Unit + Integration suites, env TEST_BASE_URL
├── deploy.php              # one-shot pull+composer+phpunit без SSH (через web-вызов)
│
├── bitrix/                 # ядро Bitrix — не трогать
│   └── .settings.php       # routing: ['config' => ['web.php', 'api.php']]
│
└── local/
    ├── php_interface/
    │   ├── init.php        # vendor/autoload.php + OnBeforeProlog → routes
    │   └── migrations/     # sprint.migration — VersionYYYYMMDDXXXXXX.php
    ├── modules/gree.core/  # Bitrix-модуль (settings page)
    ├── templates/gree/     # шаблон сайта (header.php + footer.php)
    ├── routes/             # web.php (HTML) + api.php (JSON)
    ├── lib/                # автозагружаемый код, см. слои выше
    │   ├── Contract/       # интерфейсы (Repository / Service / Http / Security)
    │   └── config/services.php  # Symfony DI ContainerBuilder
    ├── views/              # Blade-шаблоны
    │   ├── layouts/app.blade.php
    │   ├── partials/breadcrumbs.blade.php
    │   ├── home/ catalog/ brand/ blog/ cart/ errors/
    ├── cache/blade/        # компиляция Blade (gitignored)
    ├── logs/               # FileLogger output (gitignored)
    └── tests/
        ├── bootstrap.php
        ├── Stub/           # Bitrix-классы для unit-тестов
        ├── Unit/           # 200+ юнит-тестов
        └── Integration/    # HTTP-тесты против live-сервера
```

`/dist/` (на корне) — фронтенд-билд: `styles/*.css`, `scripts/*.js`, `images/*`. Контроллер регистрирует через `addPageAssets('page')`, которое отдаёт `/dist/styles/page.css` + `/dist/scripts/page.js`.

---

## Роутинг

Группировка через `prefix(...)->name(...)->group(closure)`. Подводные камни:

- `prefix()` **не** должен начинаться с `/` — иначе компилируется `//api/v1//cart`.
- URI внутри группы тоже без `/` — конкатенация `prefix + '/' + uri`.
- Пустой URI (`get('', …)`) даёт trailing slash (`/api/v1/cart/`).
- После правок роутов **обязательно сноси кэш**: `rm -rf bitrix/cache/routing bitrix/managed_cache`.

### Web (`local/routes/web.php`)

| Метод | URL | Контроллер | Имя |
|-------|-----|-----------|-----|
| GET | `/` | `HomeController::index` | `home` |
| GET | `/catalog/` | `CatalogController::index` | `catalog.index` |
| GET | `/catalog/{section}/` | `CatalogController::section` | `catalog.section` |
| GET | `/catalog/{section}/{code}/` | `ProductController::show` | `catalog.product` |
| GET | `/brand/{code}/` | `BrandController::show` | `brand.show` |
| GET | `/blog/` | `BlogController::index` | `blog.index` |
| GET | `/blog/{code}/` | `BlogController::show` | `blog.show` |
| GET | `/cart/` | `CartController::index` | `cart.index` |
| GET | `/order/` | `OrderController::checkout` | `order.checkout` |
| GET | `/order/success/{publicId}/` | `OrderController::success` | `order.success` |
| GET | `/lang/{locale}/` | `LanguageController::switch` | `lang.switch` |

`{section}` ограничен `nastennie|kolonnye|promyshlennye`, `{locale}` — `ru|en`.

### API (`local/routes/api.php`)

| Метод | URL | Контроллер | Имя |
|-------|-----|-----------|-----|
| GET | `/api/catalog` | `CatalogController::filter` | `api.catalog.filter` |
| GET | `/api/v1/blog` | `BlogController::paginate` | `api.v1.blog.paginate` |
| GET | `/api/v1/cart/` | `CartController::get` | `api.v1.cart.get` |
| POST | `/api/v1/cart/items` | `CartController::add` | `api.v1.cart.items.add` |
| PATCH | `/api/v1/cart/items/{id}` | `CartController::update` | `api.v1.cart.items.update` |
| DELETE | `/api/v1/cart/items/{id}` | `CartController::remove` | `api.v1.cart.items.remove` |
| POST | `/api/v1/order` | `OrderController::place` | `api.v1.order.place` |

State-changing методы (POST/PATCH/DELETE) защищены `ApiGuard` (см. ниже).

### Генерация URL — `Gree\Helpers\Route`

Все внутренние ссылки строятся через `Route::to($name, $params)` — никаких хардкод-строк
`href="/cart/"` в шаблонах/контроллерах/сервисах. Источник правды — имена в
`local/routes/{web,api}.php`. Поменялся URL — все ссылки автоматом подхватятся.

```php
Route::to('home')
  → /

Route::to('catalog.section', ['section' => 'nastennie'])
  → /catalog/nastennie/

Route::to('catalog.product', ['section' => 'nastennie', 'code' => 'gree-bora-x-07'])
  → /catalog/nastennie/gree-bora-x-07/

Route::to('api.v1.cart.items.update', ['id' => 42])
  → /api/v1/cart/items/42
```

В Blade:
```blade
<a href="{{ Route::to('cart.index') }}">{{ Language::t('header.cart') }}</a>
```

Если имя маршрута неизвестно или нет нужного параметра — `Route::to()` пишет
critical в лог и возвращает `#`. Лучше битый якорь, чем 500 на рендере.

---

## SEO

Двухуровневая модель:

| Что | Где хранится | Чем читается |
|-----|--------------|--------------|
| Статические страницы (home, catalog, blog, cart, brand) | HL-блок `Seo`, поля `UF_TITLE_RU/UZ`, `UF_DESCRIPTION_RU/UZ`, `UF_KEYWORDS_RU/UZ`, `UF_OG_TITLE_RU/UZ`, `UF_OG_DESCRIPTION_RU/UZ`, `UF_OG_IMAGE`, ключ `UF_PAGE_CODE` | `SeoService::forPage('home')` |
| Карточка товара / статья блога | Bitrix IPROPERTY-шаблоны на iblock-уровне (Products, Blog) — `ELEMENT_META_TITLE`, `ELEMENT_META_KEYWORDS`, `ELEMENT_META_DESCRIPTION`, `ELEMENT_PAGE_TITLE`. Плейсхолдеры `{=this.Name}` / `{=this.PreviewText}`. Поверх шаблона работают per-element overrides в админке Bitrix. | `SeoService::forElement(IblockCode::Products, $id)` через `Bitrix\Iblock\InheritedProperty\ElementValues::getValues()` |

Применение в контроллере:

```php
$this->applySeo($this->seo->forPage('catalog-nastennie'));
// или для деталки:
$seo = $this->seo->forElement(IblockCode::Products, $product->id) ?? new SeoDto(title: $product->name);
$this->applySeo($seo);
```

`BaseController::applySeo()` ставит `title`, `meta name="description|keywords"` через `SetPageProperty` (рендерятся `ShowHead()`) и **OG-теги** через `Asset::addString('<meta property="og:*">')` (page property для них не работает).

Коды статических SEO-записей: `home`, `catalog`, `catalog-nastennie`, `catalog-kolonnye`, `catalog-promyshlennye`, `brand`, `blog`, `cart`. Управляются в админке `/bitrix/admin/highloadblock_rows_list.php` для HL `Seo`. IPROPERTY-шаблоны лежат в инфоблоках Products/Blog → таб «Шаблоны полей».

---

## Корзина (анонимная)

Идентификация без личного кабинета:

- HttpOnly-кука **`cart_token`** = UUID v4 (122 бита энтропии), SameSite=Lax, Secure под HTTPS, lifetime 1 год.
- Сервер при первом обращении выпускает токен → создаёт строку в HL-блоке `Carts` → отдаёт куку.
- Позиции лежат в HL-блоке `CartItems` (UF_CART_ID, UF_OFFER_ID, UF_QUANTITY).
- Пара (cart_id, offer_id) уникальна на уровне репозитория: при `add` сначала find, инкремент qty; иначе insert.

DTO: `CartItemDto` (storage-уровень), `CartLineDto` (enriched — product/offer данные для шаблона/API).

Bitrix-нативная работа с куками — через `Gree\Http\HttpContextInterface` (см. ниже), не `$_COOKIE`/`setcookie`.

---

## Оформление заказа

Анонимный чекаут — без личного кабинета. Из `/cart/` пользователь идёт на `/order/`, заполняет 3 секции формы (контакты, доставка, оплата), `POST /api/v1/order` создаёт заказ и редиректит на `/order/success/{publicId}/`.

### Хранение — нормализованно, 2 HL-блока

**`Orders`** — шапка:

| Поле | Назначение |
|------|-----------|
| `UF_PUBLIC_ID` | 12-char hex (48 бит). Внешний идентификатор для URL `/order/success/{publicId}/`. Числовой ID наружу не светим — анти-IDOR, анти-перебор. |
| `UF_CART_TOKEN` | Снапшот куки корзины на момент оформления (аудит/анализ). |
| `UF_STATUS` | `new → confirmed → shipped → delivered` / `cancelled`. Старт — `new`. |
| `UF_CUSTOMER_NAME/PHONE/TELEGRAM` | Имя/телефон обязательны. Телефон нормализуется в коде (`+998901234567` без пробелов и скобок). |
| `UF_DELIVERY_CITY/STREET/HOUSE/APARTMENT/COMMENT` | Город (slug), улица, дом обязательны. |
| `UF_PAYMENT_METHOD` | `card` / `uzum_bank` / `anor_bank` — enum `PaymentMethod`. |
| `UF_TOTAL`, `UF_ITEMS_COUNT` | Snapshot на шапке. Даже если строки `OrderItems` потеряются — итог сохраняется. |
| `UF_LOCALE` | Снимок локали пользователя — менеджеру/email на том же языке. |
| `UF_IP`, `UF_USER_AGENT` | Аудит/анти-фрод, UA обрезается до 500 символов в коде. |

**`OrderItems`** — позиции. Все поля — **snapshot**: `UF_PRODUCT_NAME`, `UF_PRODUCT_CODE`, `UF_OFFER_AREA`, `UF_OFFER_COLOR`, `UF_UNIT_PRICE`. Переименование товара, удаление, изменение цены в каталоге **не** рерайтят историю.

### Валидация

`Gree\Service\OrderService` ловит проблемные сабмиты до записи:

| Что | Реакция |
|------|---------|
| Имя пустое или > 100 символов | `CheckoutValidationException` → 422 с `fields: {name: 'invalid'}` |
| Телефон не пройдёт regex `^\+?[0-9\s\-()]{9,32}$` | то же, `fields: {phone}` |
| Город/улица/дом пустые | то же |
| Способ оплаты вне enum | 422 с `fields: {payment}` |
| Корзина пустая или потеряны все офферы | `EmptyCartException` → 422 `error: 'empty_cart'` |
| Origin/Referer чужой | 403 (ApiGuard) |
| CSRF не совпал | 403 |

### Жизненный цикл place()

1. Валидация инпутов
2. `cartToken->read()` → `carts->findIdByToken()` → `cartItems->listByCart()`
3. **Snapshot** — для каждой строки корзины подтягиваем свежий offer + product, фиксируем цену+имя+цвет+площадь
4. Генерируем уникальный `publicId` (12-hex, ретрай при коллизии)
5. Транзакционно: `orders->insert` → `orderItems->insert` × N → `cartItems->delete` × N (очищаем корзину чтоб второй раз не сабмитнул)
6. Возвращаем `OrderDto` с `publicId`

### DTO

- `OrderCustomerDto` — name/phone/telegram
- `OrderDeliveryDto` — city/street/house/apartment/comment
- `OrderItemDto` — snapshot позиции
- `OrderDto` — агрегат-корень, объединяет всё + items collection

---

## Безопасность

| Угроза | Защита |
|--------|--------|
| CSRF (форсированный POST/PATCH/DELETE) | `Gree\Security\ApiGuard`: Origin/Referer-проверка + double-submit cookie pattern |
| IDOR (`/cart/items/{X}` чужого id) | `CartService::assertItemBelongsToCurrentCart()` — `findById` + проверка `UF_CART_ID` совпадает с резолвом по cookie |
| XSS-кража токена корзины | `cart_token` помечен `HttpOnly` |
| Подделка offer_id с фронта | `CartService::add()` сначала зовёт `OfferRepository::existsActive()`; при false → `OfferNotFoundException` → 422 |
| Sniffing / mime-confusion / clickjacking | Security headers в `BaseController::applySecurityHeaders()`: `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-XSS-Protection: 0`, `Permissions-Policy` |
| Cache отравление JSON | `Cache-Control: no-store` на каждом `$this->json()` |
| Mass spam корзины | Hard cap `1 ≤ quantity ≤ 999` в `CartController::add` |

CSRF-токен (`Gree\Security\CsrfService`): 64 hex (256 бит), SameSite=Strict, **не** HttpOnly (JS должен прочитать); в `<meta name="csrf-token">` в `<head>`; фронт мирорит в `X-CSRF-Token`. Сравнение `hash_equals`.

`ApiGuard::guardStateChanging($request)` бросает `AccessDeniedException` → контроллер мапит на 403 JSON. GET/HEAD/OPTIONS никогда не валидируются.

---

## Локализация

Текущие языки: `ru`, `uz` (latin O'zbek). Локаль хранится в сессии (Bitrix), переключение через `GET /lang/{locale}/` (роут принимает только `ru|uz`).

UI-строки — в HL-блоке `Translations` (`UF_CODE`, `UF_VALUE_RU`, `UF_VALUE_UZ`). Чтение:

```php
use Gree\Helpers\Language;
Language::t('header.catalog');                       // строка по текущей локали
Language::t('blog.reading_minutes', ['minutes' => 5]);   // :minutes плейсхолдер
```

Текстовые поля iblock-элементов — пара `_RU` / `_UZ`, читаются через `BaseRepository::localizedSelect('NAME')` + `localized($row, 'NAME')`. Fallback на противоположный язык, если пусто.

> Историческая справка: пара была `_RU/_EN`. Миграции `Version20260519000001/000002/000003` переименовали все `_EN` поля (iblock-свойства, HL UF-поля, UF секций menu) в `_UZ` и залили узбекский контент. Английский как локаль больше не поддерживается — `Locale::En` удалён, в `Accept-Language` английские теги фолбэчатся на `ru`.

---

## Слой HTTP-контекста (без $_COOKIE / $_SERVER)

Все сервисы работают с куками/заголовками/server vars через **`Gree\Contract\Http\HttpContextInterface`**:

```php
$http->getCookie('cart_token');
$http->setCookie('csrf_token', $token, new CookieOptions(lifetimeSeconds: 31_536_000, httpOnly: false, sameSite: 'Strict'));
$http->getHeader('X-CSRF-Token');
$http->getRequestMethod();
$http->isHttps();
$http->flushCookiesInto($response);     // вызывается в BaseController перед return
```

Прод: `Gree\Http\BitrixHttpContext` — обёртка над `Application::getInstance()->getContext()`. Кука пишется через `\Bitrix\Main\Web\Cookie(addPrefix=false)` без BITRIX_SM_ префикса, читается через `getCookieRaw()`.

Тесты: `Gree\Http\InMemoryHttpContext` — массивы в памяти, без Bitrix.

**Не использовать в `local/lib/`**: `$_COOKIE`, `$_SERVER`, `$_POST`, `$_GET`, `setcookie()`, `headers_sent()`, `file_get_contents('php://input')`. Всё это уже выпилено и должно оставаться выпиленным.

---

## DTO и Collection

```php
// Gree\DTO\BlogArticleDto
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

    public static function fromArray(array $data): static { /* ... */ }
    public function toJson(): array { /* ... */ }
}

// Gree\Collection\BlogArticleCollection
final class BlogArticleCollection extends BaseCollection
{
    public function __construct(BlogArticleDto ...$items) { parent::__construct(array_values($items)); }
    protected function itemClass(): string { return BlogArticleDto::class; }
}
```

`BaseCollection::add()` проверяет тип. `createFrom()` сохраняет тип. Не дублировать в наследниках.

---

## ViewData

Типизированные `readonly` DTO, расширяющие `Gree\View\BaseViewData`. `toArray()` через `(array) $this` → каждое public property становится переменной в Blade.

```php
final readonly class BlogViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public BlogArticleCollection $tips,
        public bool $hasMoreTips,
        public BlogArticleCollection $news,
        public bool $hasMoreNews,
        public int $pageSize,
    ) {}
}

// контроллер:
return $this->view('blog/index', new BlogViewData(
    breadcrumbs: $this->breadcrumbs->blog(),
    tips:        $tips['items'],
    ...
));
```

В Blade: `{{ $hasMoreTips }}`, `@foreach ($tips as $card)`.

---

## Dependency Injection

Symfony DI Container, конфиг — `local/lib/config/services.php`. Контракты в `local/lib/Contract/`.

Типизированный resolve в шаблонах/роутах:

```php
App::get(HomeController::class);                    // → HomeController (PhpStorm-aware)
App::get(CartServiceInterface::class);              // → CartService
App::get(HttpContextInterface::class);              // → BitrixHttpContext
```

В контроллерах/сервисах/репозиториях — **constructor injection** через интерфейсы. Никаких `new` руками, никаких `App::get` внутри domain-кода. Service-locator (`$this->resolve(...)`) допустим только в контроллерах для случаев, когда Bitrix-роутинг создаёт инстанс без DI.

---

## Имена инфоблоков и HL-блоков — только через enum

Никаких `'products'` / `'Translations'` строк в репозиториях. Источник правды:

- `Gree\Enum\IblockCode` — кейсы iblock'ов по их `API_CODE` (Products, ProductsOffers, Brands, Blog, Menu, …).
- `Gree\Enum\HlblockCode` — кейсы HL-блоков по их `NAME` (Translations, Seo, Carts, CartItems).

Iblock-репозиторий:
```php
$iblockId = $this->resolveIblockId(IblockCode::Products);
```

HL-block репозиторий — объявляет, какой блок он обслуживает:
```php
final class CartRepository extends BaseHlblockRepository implements CartRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::Carts;
    }

    public function findIdByToken(string $token): ?int
    {
        $row = $this->query()                       // ← Query, типизированный
            ->where('UF_TOKEN', $token)
            ->setSelect(['ID'])->setLimit(1)
            ->exec()->fetch();
        return $row ? (int) $row['ID'] : null;
    }
}
```

`query() / addRow() / updateRow() / deleteRow()` живут в `BaseHlblockRepository` и возвращают `Bitrix\Main\ORM\Query\Query` / `AddResult` / `UpdateResult` / `DeleteResult` — PhpStorm видит весь chain. Никакого `$this->dataClass()::method()` в наследниках.

---

## Миграции

`sprint.migration`. Файлы — `local/php_interface/migrations/VersionYYYYMMDDXXXXXX.php`.

```bash
php bitrix/modules/sprint.migration/tools/migrate.php list
php bitrix/modules/sprint.migration/tools/migrate.php up
php bitrix/modules/sprint.migration/tools/migrate.php up=Version20260517000005
php bitrix/modules/sprint.migration/tools/migrate.php down=Version20260517000005
```

### Правила для миграций

- При `saveIblock()` — обязательно `saveIblockFields()` с авто-CODE, отключённые `ACTIVE_FROM/TO/XML_ID/TAGS` (см. CLAUDE.md).
- Для текстовых полей создавать пары `<CODE>_RU` + `<CODE>_UZ`.
- IPROPERTY-шаблоны для SEO деталок — через `new Bitrix\Iblock\InheritedProperty\IblockTemplates($iblockId)->set([...])`.
- UF-поля на секции — `IBLOCK_<id>_SECTION` entity, `addUserTypeEntitiesIfNotExists()` (множественное число, плоский массив).

---

## Переменные окружения

Источник правды — `.env.example` в корне репозитория, скопируй в `.env` локально и поправь под себя.

| Переменная | Где читается | Зачем |
|------------|--------------|-------|
| `LOG_DIR` | `Gree\Logging\FileLogger` | Папка для логов; абсолютная или относительно корня проекта. |
| `LOG_DEBUG` | `Gree\Logging\FileLogger` | `true` включает debug-уровень. Warning/Error/Critical пишутся всегда. |
| `TEST_BASE_URL` | `IntegrationTestCase::setUp` | Хост для cURL-интеграций (SEO read-only тесты). Дефолт `https://gree:8890`. |
| `GREE_TEST_INTEGRATION` | `local/tests/bootstrap.php` | `1` → boot Bitrix-пролог вместо стабов. Выставляется composer-скриптом `test:integration`, в `.env` обычно не нужна. |
| `GREE_TEST_MYSQL_SOCKET` | `local/tests/bootstrap.php` | Явный путь к unix-socket MySQL для CLI. Bootstrap пробует MAMP/brew/apt/rpm дефолты сам — задавай только если у тебя сокет в нестандартном месте. |
| `GREE_ALLOWED_HOSTS` | `services.php` → `ApiGuard` | Список хостов через запятую (`gree.all4it.org,www.gree.uz`), которым ApiGuard верит как Origin/Referer для state-changing API. За reverse proxy/CDN `HTTP_HOST` приходит внутренним, а браузер шлёт публичное имя → без этой ENV получаешь 403 «foreign origin». На localhost ENV не нужна — фоллбек на `HTTP_HOST` текущего запроса + `X-Forwarded-Host` если proxy его ставит. |

Любая новая `getenv()` в коде → строка в `.env.example` (это требование закреплено в CLAUDE.md).

---

## Логирование

`Gree\Logging\FileLogger::getInstance()` — singleton. Конфиг в `.env`:

```
LOG_DIR=local/logs
LOG_DEBUG=true
```

Файлы:
- `local/logs/YYYY-MM-DD.log` — общая лента
- `local/logs/YYYY-MM-DD-errors.log` — только warning/error/critical

В сервисах: каждый метод с IO оборачивается в try/catch + `FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e])` + rethrow. Fail-soft (UI не падает) — критикал залогирован, в catch возвращён безопасный fallback (см. `TranslatorService::translate`, `MenuService::getHeaderMenu`).

---

## Тесты

```bash
composer test:unit          # юниты — стабы, без живой БД
composer test:integration   # bootstrap Bitrix + транзакция-rollback
composer test               # оба прогона
./vendor/bin/phpunit local/tests/Unit/Service/CartServiceTest.php   # один файл
```

| Suite | Где | Bootstrap | Что валидируется |
|-------|-----|-----------|------------------|
| Unit | `local/tests/Unit/` | class_alias-стабы Bitrix | DTO/Collection/Service логика на моках, Controller-конструкторы |
| Integration | `local/tests/Integration/` | настоящий `bitrix/modules/main/start.php` | Репозитории к живой БД, сервисный чекаут-флоу с rollback'ом, HTTP read-only проверки (SEO) |

Все интеграционные тесты наследуют `Gree\Tests\Integration\IntegrationTestCase`. База даёт:

- **Транзакцию вокруг каждого теста.** setUp дёргает `TransactionService::startTransaction()`, tearDown катит её назад. Любая запись через наши репозитории (`OrderRepository`, `CartItemRepository`, …) откатывается автоматом — БД остаётся чистой. **Никакого ручного DELETE, никакого mysqli.**
- Доступ к настоящему DI-контейнеру через `App::get(SomeService::class)`.

Бутстрап Bitrix из CLI имеет квирки:

- Bitrix `tools.php` использует короткие теги `<?`  composer-скрипт `test:integration` запускается с `php -d short_open_tag=On`.
- `Application::getInstance()->getContext()` в CLI не существует — `services.php` падает обратно на `localhost` для `allowedHost` (см. секцию ApiGuard ниже).
- MySQL-сокет CLI ≠ FPM — если падает `(2002) No such file or directory`, поменяй в `bitrix/.settings.php` `'host' => '127.0.0.1'`. База ловит ConnectionException и грейс-скипает тесты с инструкцией.

Стабы Bitrix для юнит-тестов — `local/tests/Stub/`.

---



Деплой: SSH → `git pull --rebase` и потом  `composer install`.

---

## Где править что (быстрый индекс)

| Изменение | Файл/директория |
|-----------|-----------------|
| Добавить страницу | `local/routes/web.php` + контроллер + ViewData + Blade |
| Добавить API-эндпоинт | `local/routes/api.php` + метод контроллера + JSON-сериализация |
| Поправить SEO статической страницы | админка `/bitrix/admin/highloadblock_rows_list.php`, HL «Seo» |
| Поправить SEO товара/статьи | админка iblock-элемента, таб «SEO» (per-element override) |
| Поправить шаблоны SEO для всех товаров/статей | iblock → таб «Шаблоны полей» (IPROPERTY_TEMPLATES) |
| UI-строка перевода | HL «Translations» в админке (UF_CODE + UF_VALUE_RU/UZ) |
| Новая зависимость DI | `local/lib/config/services.php` |
| Новый Bitrix-event handler | `local/lib/Core/Event/*.php` + регистрация в `local/php_interface/init.php` |
| Новая миграция | `local/php_interface/migrations/VersionYYYYMMDDXXXXXX.php` |
