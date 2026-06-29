<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

global $APPLICATION, $USER;

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Access denied');
}

\Bitrix\Main\Loader::includeModule('highloadblock');
\Bitrix\Main\Loader::includeModule('iblock');

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Type\DateTime;

$resolveDataClass = static function (string $name): ?string {
    $row = HighloadBlockTable::query()->where('NAME', $name)->setSelect(['*'])->setLimit(1)->exec()->fetch();
    return $row ? HighloadBlockTable::compileEntity($row)->getDataClass() : null;
};

$orderId = (int) ($_GET['ID'] ?? 0);
if ($orderId <= 0) {
    LocalRedirect('/bitrix/admin/gree_orders.php?lang=' . LANG);
}

$ordersCls = $resolveDataClass('Orders');
$itemsCls  = $resolveDataClass('OrderItems');
$citiesCls = $resolveDataClass('Cities');

$order = $ordersCls ? $ordersCls::query()->where('ID', $orderId)->setSelect(['*'])->setLimit(1)->exec()->fetch() : null;
if (!$order) {
    echo '<div class="adm-info-message-wrap adm-info-message-red"><div class="adm-info-message">Заказ #' . $orderId . ' не найден</div></div>';
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
    return;
}

$cityId = (int) ($order['UF_DELIVERY_CITY'] ?? 0);
$cityRow = ($cityId > 0 && $citiesCls)
    ? $citiesCls::query()->where('ID', $cityId)->setSelect(['ID', 'UF_NAME_RU', 'UF_CODE'])->setLimit(1)->exec()->fetch()
    : null;

$itemsHlRow = $resolveDataClass('OrderItems') ? HighloadBlockTable::query()->where('NAME', 'OrderItems')->setSelect(['ID'])->setLimit(1)->exec()->fetch() : null;
$itemsHlId = (int) ($itemsHlRow['ID'] ?? 0);

// Фильтр + поиск + пагинация позиций (10 на страницу). Параметры в URL —
// items_q (LIKE по name/code/color), items_page (страница). Пагинация автономна
// от шапки заказа: смена страницы не дёргает основной orders list.
$itemsQ = trim((string) ($_GET['items_q'] ?? ''));
$itemsPage = max(1, (int) ($_GET['items_page'] ?? 1));
$itemsPerPage = 10;

$itemsFilter = ['UF_ORDER_ID' => $orderId];
if ($itemsQ !== '') {
    $itemsFilter[] = [
        'LOGIC' => 'OR',
        ['%UF_PRODUCT_NAME'  => $itemsQ],
        ['%UF_PRODUCT_CODE'  => $itemsQ],
        ['%UF_OFFER_COLOR'   => $itemsQ],
    ];
}

$items = [];
$itemsTotal = 0;
if ($itemsCls) {
    $itemsTotal = (int) $itemsCls::getCount($itemsFilter);
    $itemsRes = $itemsCls::query()
        ->setFilter($itemsFilter)
        ->setSelect(['*'])
        ->setOrder(['ID' => 'ASC'])
        ->setLimit($itemsPerPage)
        ->setOffset(($itemsPage - 1) * $itemsPerPage)
        ->exec();
    while ($row = $itemsRes->fetch()) {
        $items[] = $row;
    }
}
$itemsPages = (int) max(1, ceil($itemsTotal / $itemsPerPage));

$paymentLabels = ['card' => 'Карта', 'uzum_bank' => 'Рассрочка UZUM', 'anor_bank' => 'Рассрочка Anorbank'];
$statusLabels  = ['new' => 'Новый', 'confirmed' => 'Подтверждён', 'shipped' => 'Отправлен', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'];

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES);
$fmt = static fn(int $v): string => number_format($v, 0, '.', ' ');
$row = static function (string $title, string $body) use ($h): void {
    echo '<tr><td class="adm-detail-content-cell-l" width="30%">' . $h($title) . '</td><td class="adm-detail-content-cell-r">' . $body . '</td></tr>';
};

$dateStr = $order['UF_CREATED_AT'] instanceof DateTime ? $order['UF_CREATED_AT']->format('d.m.Y H:i') : (string) $order['UF_CREATED_AT'];

$APPLICATION->SetTitle('Заказ №' . $h((string) $order['UF_PUBLIC_ID']) . ' (#' . $orderId . ')');
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

$context = new \Bitrix\Main\Grid\Panel\Snippet();
?>
<?php $ordersHlId = (int) (HighloadBlockTable::query()->where('NAME', 'Orders')->setSelect(['ID'])->setLimit(1)->exec()->fetch()['ID'] ?? 0); ?>
<div class="adm-detail-toolbar-buttons">
    <a href="/bitrix/admin/gree_orders.php?lang=<?= LANG ?>" class="adm-btn">← К списку</a>
    <a href="/bitrix/admin/highloadblock_row_edit.php?ENTITY_ID=<?= $ordersHlId ?>&ID=<?= $orderId ?>&lang=<?= LANG ?>" class="adm-btn">Редактировать в HL</a>
</div>

<table class="adm-detail-content-table edit-table" style="width:100%">
    <tbody>
        <tr class="heading"><td colspan="2">Заказ</td></tr>
        <?php
        $row('Номер заказа', '<b>' . $h((string) $order['UF_PUBLIC_ID']) . '</b>');
        $row('ID', (string) $orderId);
        $row('Статус', $h($statusLabels[$order['UF_STATUS']] ?? (string) $order['UF_STATUS']));
        $row('Создан', $h($dateStr));
        $row('Локаль', $h((string) $order['UF_LOCALE']));
        $row('IP', $h((string) $order['UF_IP']));
        $row('User-Agent', '<small>' . $h((string) $order['UF_USER_AGENT']) . '</small>');
        ?>

        <tr class="heading"><td colspan="2">Покупатель</td></tr>
        <?php
        $row('Имя', $h((string) $order['UF_CUSTOMER_NAME']));
        $phone = (string) $order['UF_CUSTOMER_PHONE'];
        $row('Телефон', '<a href="tel:' . $h(preg_replace('/\s+/', '', $phone)) . '">' . $h($phone) . '</a>');
        $tg = (string) ($order['UF_CUSTOMER_TELEGRAM'] ?? '');
        $row('Telegram', $tg !== '' ? '<a href="https://t.me/' . $h(ltrim($tg, '@')) . '" target="_blank">' . $h($tg) . '</a>' : '—');
        ?>

        <tr class="heading"><td colspan="2">Доставка</td></tr>
        <?php
        $row('Город', $cityRow ? $h((string) $cityRow['UF_NAME_RU']) . ' (' . $h((string) $cityRow['UF_CODE']) . ')' : '#' . $cityId);
        $row('Улица', $h((string) $order['UF_DELIVERY_STREET']));
        $row('Дом', $h((string) $order['UF_DELIVERY_HOUSE']));
        $row('Квартира', $h((string) ($order['UF_DELIVERY_APARTMENT'] ?? '')) ?: '—');
        $row('Комментарий', $h((string) ($order['UF_DELIVERY_COMMENT'] ?? '')) ?: '—');
        ?>

        <tr class="heading"><td colspan="2">Оплата</td></tr>
        <?php
        $row('Способ', $h($paymentLabels[$order['UF_PAYMENT_METHOD']] ?? (string) $order['UF_PAYMENT_METHOD']));
        $row('Сумма', '<b>' . $fmt((int) $order['UF_TOTAL']) . ' UZS</b>');
        $row('Позиций (шт)', (string) (int) $order['UF_ITEMS_COUNT']);
        ?>
    </tbody>
</table>

<h3 style="margin-top:30px">Позиции заказа <span style="font-weight:normal;font-size:13px;color:#888">— всего <?= $itemsTotal ?></span></h3>

<form method="get" action="/bitrix/admin/gree_orders.php" style="margin-bottom:10px">
    <input type="hidden" name="ID" value="<?= $orderId ?>">
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="text" name="items_q" value="<?= $h($itemsQ) ?>" placeholder="Поиск: товар, код, цвет" style="width:300px;padding:4px 8px">
    <button type="submit" class="adm-btn-save">Найти</button>
    <?php if ($itemsQ !== ''): ?>
        <a href="/bitrix/admin/gree_orders_view.php?ID=<?= $orderId ?>&lang=<?= LANG ?>" class="adm-btn">Сбросить</a>
    <?php endif; ?>
</form>
<script>
    // form.action указан на gree_orders.php — переопределяем на view
    document.currentScript.previousElementSibling.action = '/bitrix/admin/gree_orders_view.php';
</script>

<?php if (!$items): ?>
    <p>Нет позиций<?= $itemsQ !== '' ? ' по запросу «' . $h($itemsQ) . '»' : '' ?>.
        <a href="/bitrix/admin/highloadblock_rows_list.php?ENTITY_ID=<?= $itemsHlId ?>&set_filter=Y&find_UF_ORDER_ID=<?= $orderId ?>&lang=<?= LANG ?>">Открыть HL «OrderItems»</a>
    </p>
<?php else: ?>
    <?php
    // Подтянем CODE товаров для ссылок (через ID iblock-элемента по offer_id → product_id).
    // Делаем 1 запрос: offers по ID → CML2_LINK.VALUE → products.CODE.
    $offerIds = array_filter(array_map(static fn($r) => (int) $r['UF_OFFER_ID'], $items));
    $productInfoByOffer = [];
    if ($offerIds) {
        $offersIblockId = \Bitrix\Iblock\IblockTable::query()->where('API_CODE', 'ProductsOffers')->setSelect(['ID'])->exec()->fetch()['ID'] ?? null;
        if ($offersIblockId) {
            $offersEntity = \Bitrix\Iblock\Iblock::wakeUp((int) $offersIblockId)->getEntityDataClass();
            $offersRes = $offersEntity::query()
                ->whereIn('ID', $offerIds)
                ->setSelect(['ID', 'CODE', 'IBLOCK_ID', 'PRODUCT_ID' => 'CML2_LINK.VALUE'])
                ->exec();
            $offerToProduct = [];
            while ($r = $offersRes->fetch()) {
                $offerToProduct[(int) $r['ID']] = [
                    'offer_code' => (string) $r['CODE'],
                    'offer_iblock_id' => (int) $r['IBLOCK_ID'],
                    'product_id' => (int) $r['PRODUCT_ID'],
                ];
            }
            $productIds = array_filter(array_column($offerToProduct, 'product_id'));
            $productsById = [];
            if ($productIds) {
                $productsIblockId = \Bitrix\Iblock\IblockTable::query()->where('API_CODE', 'Products')->setSelect(['ID'])->exec()->fetch()['ID'] ?? null;
                if ($productsIblockId) {
                    $productsEntity = \Bitrix\Iblock\Iblock::wakeUp((int) $productsIblockId)->getEntityDataClass();
                    $pRes = $productsEntity::query()->whereIn('ID', $productIds)->setSelect(['ID', 'CODE', 'IBLOCK_ID'])->exec();
                    while ($r = $pRes->fetch()) {
                        $productsById[(int) $r['ID']] = ['code' => (string) $r['CODE'], 'iblock_id' => (int) $r['IBLOCK_ID']];
                    }
                }
            }
            foreach ($offerToProduct as $offerId => $info) {
                $product = $productsById[$info['product_id']] ?? null;
                $productInfoByOffer[$offerId] = [
                    'offer_code' => $info['offer_code'],
                    'offer_edit_url' => '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $info['offer_iblock_id'] . '&type=' . urlencode((string) (\Bitrix\Iblock\IblockTable::query()->where('ID', $info['offer_iblock_id'])->setSelect(['IBLOCK_TYPE_ID'])->exec()->fetch()['IBLOCK_TYPE_ID'] ?? '')) . '&ID=' . $offerId . '&lang=' . LANG,
                    'product_edit_url' => $product ? ('/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $product['iblock_id'] . '&type=' . urlencode((string) (\Bitrix\Iblock\IblockTable::query()->where('ID', $product['iblock_id'])->setSelect(['IBLOCK_TYPE_ID'])->exec()->fetch()['IBLOCK_TYPE_ID'] ?? '')) . '&ID=' . $info['product_id'] . '&lang=' . LANG) : null,
                    'product_code' => $product['code'] ?? null,
                ];
            }
        }
    }
    ?>
    <table class="adm-list-table" cellspacing="0" cellpadding="0" border="0" style="width:100%">
        <thead>
        <tr class="adm-list-table-header">
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">#</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Товар</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">ТП</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Цвет</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Площадь</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Кол-во</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Цена</div></td>
            <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Итого</div></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $i => $it):
            $info = $productInfoByOffer[(int) $it['UF_OFFER_ID']] ?? null;
            $productLink = $info && $info['product_edit_url']
                ? '<a href="' . $h($info['product_edit_url']) . '"><b>' . $h((string) $it['UF_PRODUCT_NAME']) . '</b></a>'
                : '<b>' . $h((string) $it['UF_PRODUCT_NAME']) . '</b>';
            $productCode = $info['product_code'] ?? (string) $it['UF_PRODUCT_CODE'];
            $offerCode = $info['offer_code'] ?? '';
            $offerLink = $info
                ? '<a href="' . $h($info['offer_edit_url']) . '">' . $h($offerCode !== '' ? $offerCode : ('#' . (int) $it['UF_OFFER_ID'])) . '</a>'
                : '#' . (int) $it['UF_OFFER_ID'];
            ?>
            <tr class="adm-list-table-row">
                <td class="adm-list-table-cell"><?= $i + 1 ?></td>
                <td class="adm-list-table-cell">
                    <?= $productLink ?><?php if ($productCode !== ''): ?><br><small><?= $h($productCode) ?></small><?php endif; ?>
                </td>
                <td class="adm-list-table-cell"><?= $offerLink ?></td>
                <td class="adm-list-table-cell"><?= $h((string) ($it['UF_OFFER_COLOR'] ?? '')) ?></td>
                <td class="adm-list-table-cell"><?= $h((string) ($it['UF_OFFER_AREA'] ?? '')) ?> м²</td>
                <td class="adm-list-table-cell"><b><?= (int) $it['UF_QUANTITY'] ?></b></td>
                <td class="adm-list-table-cell"><?= $fmt((int) $it['UF_UNIT_PRICE']) ?> UZS</td>
                <td class="adm-list-table-cell"><b><?= $fmt((int) $it['UF_TOTAL']) ?> UZS</b></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($itemsPages > 1): ?>
        <?php
        $buildUrl = static fn(int $p): string => '/bitrix/admin/gree_orders_view.php?ID=' . $orderId
            . '&lang=' . LANG
            . ($itemsQ !== '' ? '&items_q=' . urlencode($itemsQ) : '')
            . '&items_page=' . $p;
        ?>
        <div style="margin-top:12px; display:flex; gap:6px; align-items:center; font-size:13px">
            <span>Страница <?= $itemsPage ?> из <?= $itemsPages ?></span>
            <?php if ($itemsPage > 1): ?>
                <a class="adm-btn" href="<?= $h($buildUrl($itemsPage - 1)) ?>">← Назад</a>
            <?php endif; ?>
            <?php for ($p = 1; $p <= $itemsPages; $p++): ?>
                <?php if ($p === $itemsPage): ?>
                    <span class="adm-btn adm-btn-active"><b><?= $p ?></b></span>
                <?php else: ?>
                    <a class="adm-btn" href="<?= $h($buildUrl($p)) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($itemsPage < $itemsPages): ?>
                <a class="adm-btn" href="<?= $h($buildUrl($itemsPage + 1)) ?>">Вперёд →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
