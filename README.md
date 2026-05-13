# Gree — сайт официального дистрибьютора

PHP 8.4 · Bitrix CMS · Blade · Composer · PHPUnit 11

---

## Быстрый старт

### Установить зависимости
```bash
composer install
```

### Запустить тесты
```bash
# Все юнит-тесты
./vendor/bin/phpunit

# Один файл
./vendor/bin/phpunit local/tests/Unit/Controller/HomeControllerTest.php

# Один метод
./vendor/bin/phpunit --filter testIndexReturnsHttp200
```

### Добавить пакет
```bash
composer require vendor/package
```

---

## Архитектура

### Как работает запрос

```
HTTP-запрос
    │
    ▼
.htaccess → /bitrix/routing_index.php   (Bitrix bootstrap)
    │
    ▼
local/routes/web.php  или  local/routes/api.php
    │
    ▼
Controller::action()
    │
    ├── $this->setMeta('Title')               // до view() — попадёт в ShowHead()
    ├── $this->addPageAssets('page')          // регистрирует page.css + page.js
    └── return $this->view('home/index', new HomeViewData(...))
                │
                └── Blade::factory()->make('home.index', $data->toArray())
                        │
                        └── local/views/layouts/app.blade.php
                              ├── require header.php    // <!doctype> + <head> + <header>
                              ├── @yield('content')     // содержимое страницы
                              └── require footer.php    // <footer> + </body></html>
```

API-маршруты возвращают `$this->json($data)` — шаблон не подключается.

---

## Структура проекта

```
/
├── composer.json           # PSR-4: Gree\ → local/lib/
├── phpunit.xml             # конфиг тестов (bootstrap → local/tests/bootstrap.php)
├── vendor/                 # Composer-зависимости (не в git)
│
├── bitrix/                 # ЯДРО БИТРИКС — не трогать
│   ├── .settings.php       # БД + routing: config: ['web.php', 'api.php']
│   └── routing_index.php   # точка входа роутинга
│
├── .htaccess               # перенаправляет все запросы на routing_index.php
│
└── local/                  # весь кастомный код
    ├── php_interface/
    │   └── init.php        # грузит vendor/autoload.php + регистрирует OnBeforeProlog
    │
    ├── modules/
    │   └── gree.core/      # Bitrix-модуль (настройки, события)
    │       ├── install/    # установка/удаление модуля из админки
    │       ├── include.php # пустой (автозагрузка через Composer)
    │       └── options.php # страница настроек в /bitrix/admin/
    │
    ├── templates/
    │   └── gree/           # шаблон сайта
    │       ├── header.php  # <!doctype>…<header>: Asset::addCss/addJs, ShowHead()
    │       ├── footer.php  # <footer>…</html>: читает Options::getInstance()
    │       ├── styles/     # main.css + page-specific CSS
    │       ├── scripts/    # main.js  + page-specific JS
    │       ├── fonts/      # HelveticaNeue
    │       └── images/     # статические изображения (*.png в .gitignore)
    │
    ├── routes/
    │   ├── web.php         # GET-маршруты → HTML-ответы
    │   └── api.php         # /api/v1/… → JSON-ответы
    │
    ├── lib/                # автозагружаемые классы (namespace Gree\)
    │   ├── Core/
    │   │   ├── Options.php          # readonly singleton: phone, email, address, TG, VK
    │   │   ├── Blade.php            # illuminate/view Factory singleton
    │   │   └── Event/Module.php     # onBeforeProlog, clearTaggedCache
    │   ├── Controller/
    │   │   ├── BaseController.php   # view() / json() / setMeta() / addPageAssets()
    │   │   ├── HomeController.php
    │   │   ├── CatalogController.php
    │   │   ├── ProductController.php
    │   │   ├── BrandController.php
    │   │   └── BlogController.php
    │   └── View/
    │       └── ViewData.php         # abstract readonly base для ViewData DTO
    │
    ├── views/              # Blade-шаблоны страниц (.blade.php)
    │   ├── layouts/
    │   │   └── app.blade.php        # базовый layout (header + content + footer)
    │   ├── home/index.blade.php
    │   ├── catalog/index.blade.php
    │   ├── catalog/product.blade.php
    │   ├── brand/show.blade.php
    │   └── blog/
    │       ├── index.blade.php
    │       └── show.blade.php
    │
    ├── cache/blade/        # кэш скомпилированных Blade-шаблонов (не в git)
    │
    └── tests/
        ├── bootstrap.php       # require vendor/autoload.php
        └── Unit/
            └── Controller/
                └── BaseControllerTest.php
```

---

## Роутинг

Маршруты объявлены в двух файлах:

| Файл | Назначение | Пример |
|------|-----------|--------|
| `local/routes/web.php` | Публичные страницы | `GET /catalog/` → `CatalogController::index()` |
| `local/routes/api.php` | API-эндпоинты | `POST /api/v1/feedback/` → `FeedbackController::store()` |

Оба файла подключены в `bitrix/.settings.php`:
```php
'routing' => ['value' => ['config' => ['web.php', 'api.php']]]
```

### Текущие маршруты

| Метод | URL | Контроллер | Имя |
|-------|-----|-----------|-----|
| GET | `/` | `HomeController::index` | `home` |
| GET | `/catalog/` | `CatalogController::index` | `catalog.index` |
| GET | `/catalog/{code}/` | `ProductController::show` | `catalog.product` |
| GET | `/brand/{code}/` | `BrandController::show` | `brand` |
| GET | `/blog/` | `BlogController::index` | `blog.index` |
| GET | `/blog/{code}/` | `BlogController::show` | `blog.show` |

---

## Шаблон сайта

Страница собирается через Blade-layout `layouts/app.blade.php`:

```
layouts/app.blade.php
  ├── require header.php    →  <!doctype html> + <head> + ShowHead() + <header>
  ├── @yield('content')     →  содержимое конкретной страницы
  └── require footer.php    →  <footer> + ShowAjaxHead() + </body></html>
```

`main.css` и `main.js` добавляются через `Asset` в `header.php`.  
Страничные `page.css` и `page.js` регистрирует контроллер через `$this->addPageAssets('page')`.

Каждый Blade-шаблон:

```blade
@extends('layouts.app')

@section('content')
    <main class="main">
        {{-- содержимое страницы --}}
    </main>
@endsection
```

## ViewData

Данные из контроллера в шаблон передаются через типизированные `readonly` DTO, расширяющие `Gree\View\ViewData`:

```php
// local/lib/View/HomeViewData.php
final readonly class HomeViewData extends ViewData
{
    public function __construct(
        public string $phone = '',
    ) {}
}
```

`toArray()` преобразует свойства в массив — каждое свойство становится переменной в Blade:

```blade
{{-- в шаблоне: --}}
{{ $phone }}
```

---

## Настройки модуля gree.core

Настройки сайта (телефон, email, адрес, TG, VK) хранятся в Bitrix-опциях модуля `gree.core`.

**Путь в админке:** `/bitrix/admin/settings.php?mid=gree.core`

После первой установки Bitrix нужно установить модуль из админки:  
`Настройки → Управление модулями → Gree Core → Установить`

Читать в коде:
```php
$options = \Gree\Core\Options::getInstance();
echo $options->phone;   // +998 71 500 00 00
echo $options->email;   // info@gree.uz
echo $options->tgLink;  // https://t.me/…
```

---

## Написать новый контроллер

1. Создать `ViewData` DTO в `local/lib/View/` с типизированными свойствами
2. Создать контроллер в `local/lib/Controller/`, namespace `Gree\Controller`, `final`, extends `BaseController`
3. Зарегистрировать маршрут в `local/routes/web.php` (или `api.php`)
4. Создать Blade-шаблон в `local/views/`
5. Написать тест в `local/tests/Unit/Controller/`

```php
// local/lib/View/ContactsViewData.php
final readonly class ContactsViewData extends ViewData
{
    public function __construct(
        public string $phone = '',
    ) {}
}

// local/lib/Controller/ContactsController.php
final class ContactsController extends BaseController
{
    public function index(): HttpResponse
    {
        $this->setMeta('Контакты');
        $this->addPageAssets('contacts');
        return $this->view('contacts/index', new ContactsViewData(
            phone: Options::getInstance()->phone,
        ));
    }
}

// local/routes/web.php
$routes->get('/contacts/', [ContactsController::class, 'index'])->name('contacts');

// local/views/contacts/index.blade.php
@extends('layouts.app')

@section('content')
    <main class="main">
        <p>{{ $phone }}</p>
    </main>
@endsection
```

---

## CI/CD

| Ветка | Среда | Запуск |
|-------|-------|--------|
| `develop` | dev `https://gree.all4it.org` | автоматически при push |
| `master` | prod `https://gree.all4it.org` | **вручную** в GitLab |

Деплой: SSH → `git pull --rebase`.
