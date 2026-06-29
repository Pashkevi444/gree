<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

global $APPLICATION, $USER;

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Access denied');
}

\Bitrix\Main\Loader::includeModule('highloadblock');

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Type\DateTime;

$resolveDataClass = static function (string $name): ?string {
    $row = HighloadBlockTable::query()->where('NAME', $name)->setSelect(['*'])->setLimit(1)->exec()->fetch();
    return $row ? HighloadBlockTable::compileEntity($row)->getDataClass() : null;
};

/** @var class-string<\Bitrix\Main\ORM\Data\DataManager>|null $ordersCls */
$ordersCls = $resolveDataClass('Orders');
$citiesCls = $resolveDataClass('Cities');
if (!$ordersCls) {
    echo '<div class="adm-info-message-wrap adm-info-message-red"><div class="adm-info-message">HL «Orders» не найден</div></div>';
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
    return;
}

$citiesById = [];
if ($citiesCls) {
    $res = $citiesCls::query()->setSelect(['ID', 'UF_NAME_RU'])->exec();
    while ($row = $res->fetch()) {
        $citiesById[(int) $row['ID']] = (string) $row['UF_NAME_RU'];
    }
}

$tableId = 'gree_orders_list';
$oSort = new CAdminSorting($tableId, 'UF_CREATED_AT', 'desc');
$lAdmin = new CAdminList($tableId, $oSort);

// ── Фильтр ────────────────────────────────────────────────────────────────
$filterFields = [
    'find', 'find_status', 'find_public_id', 'find_name', 'find_phone', 'find_telegram',
    'find_street', 'find_house', 'find_payment',
    'find_total_from', 'find_total_to',
    'find_items_count',
    'find_created_from', 'find_created_to',
];
$lAdmin->InitFilter($filterFields);

global $find, $find_status, $find_public_id, $find_name, $find_phone, $find_telegram;
global $find_street, $find_house, $find_payment;
global $find_total_from, $find_total_to, $find_items_count;
global $find_created_from, $find_created_to;

// CAdminFilter показывает/скрывает строку по input.name = ключ из filterTitles.
// Поэтому каждое поле — в своей <tr>, иначе toggle второго поля не работает.
$filterTitles = [
    'find'              => 'Поиск',
    'find_status'       => 'Статус',
    'find_public_id'    => 'Номер',
    'find_name'         => 'Имя',
    'find_phone'        => 'Телефон',
    'find_telegram'     => 'Telegram',
    'find_street'       => 'Улица',
    'find_house'        => 'Дом',
    'find_payment'      => 'Оплата',
    'find_total_from'   => 'Сумма от',
    'find_total_to'     => 'Сумма до',
    'find_items_count'  => 'Позиций',
    'find_created_from' => 'Создан от',
    'find_created_to'   => 'Создан до',
];
$oFilter = new CAdminFilter($tableId . '_filter', $filterTitles);

// Реальные min/max UF_TOTAL по всем заказам — для подсказки в полях «Сумма от/до».
$maxRow = $ordersCls::getList([
    'select' => ['UF_TOTAL'],
    'order'  => ['UF_TOTAL' => 'DESC'],
    'limit'  => 1,
])->fetch();
$minRow = $ordersCls::getList([
    'select' => ['UF_TOTAL'],
    'order'  => ['UF_TOTAL' => 'ASC'],
    'limit'  => 1,
])->fetch();
$totalMax = (int) ($maxRow['UF_TOTAL'] ?? 0);
$totalMin = (int) ($minRow['UF_TOTAL'] ?? 0);

$ormFilter = [];
if ((string) $find_status      !== '') { $ormFilter['=UF_STATUS']            = $find_status; }
if ((string) $find_public_id   !== '') { $ormFilter['%UF_PUBLIC_ID']         = $find_public_id; }
if ((string) $find_name        !== '') { $ormFilter['%UF_CUSTOMER_NAME']     = $find_name; }
if ((string) $find_phone       !== '') { $ormFilter['%UF_CUSTOMER_PHONE']    = $find_phone; }
if ((string) $find_telegram    !== '') { $ormFilter['%UF_CUSTOMER_TELEGRAM'] = $find_telegram; }
if ((string) $find_street      !== '') { $ormFilter['%UF_DELIVERY_STREET']   = $find_street; }
if ((string) $find_house       !== '') { $ormFilter['%UF_DELIVERY_HOUSE']    = $find_house; }
if ((string) $find_payment     !== '') { $ormFilter['=UF_PAYMENT_METHOD']    = $find_payment; }
if ((string) $find_total_from !== '') { $ormFilter['>=UF_TOTAL'] = (int) $find_total_from; }
if ((string) $find_total_to   !== '') { $ormFilter['<=UF_TOTAL'] = (int) $find_total_to; }
if ((string) $find_items_count !== '') { $ormFilter['=UF_ITEMS_COUNT']       = (int) $find_items_count; }
if ((string) $find_created_from !== '') {
    $ts = MakeTimeStamp($find_created_from);
    if ($ts > 0) { $ormFilter['>=UF_CREATED_AT'] = DateTime::createFromTimestamp($ts); }
}
if ((string) $find_created_to !== '') {
    $ts = MakeTimeStamp($find_created_to);
    if ($ts > 0) { $ormFilter['<=UF_CREATED_AT'] = DateTime::createFromTimestamp($ts + 86399); }
}
if ((string) $find !== '') {
    $ormFilter[] = [
        'LOGIC' => 'OR',
        ['%UF_PUBLIC_ID'         => $find],
        ['%UF_CUSTOMER_NAME'     => $find],
        ['%UF_CUSTOMER_PHONE'    => $find],
        ['%UF_CUSTOMER_TELEGRAM' => $find],
        ['%UF_DELIVERY_STREET'   => $find],
        ['%UF_DELIVERY_HOUSE'    => $find],
    ];
}

// ── Колонки ───────────────────────────────────────────────────────────────
$lAdmin->AddHeaders([
    ['id' => 'ID',                   'content' => 'ID',       'sort' => 'ID',                  'default' => true],
    ['id' => 'UF_PUBLIC_ID',         'content' => 'Номер',    'sort' => 'UF_PUBLIC_ID',        'default' => true],
    ['id' => 'UF_STATUS',            'content' => 'Статус',   'sort' => 'UF_STATUS',           'default' => true],
    ['id' => 'UF_CUSTOMER_NAME',     'content' => 'Имя',      'sort' => 'UF_CUSTOMER_NAME',    'default' => true],
    ['id' => 'UF_CUSTOMER_PHONE',    'content' => 'Телефон',  'sort' => 'UF_CUSTOMER_PHONE',   'default' => true],
    ['id' => 'UF_CUSTOMER_TELEGRAM', 'content' => 'Telegram', 'sort' => 'UF_CUSTOMER_TELEGRAM','default' => false],
    ['id' => 'UF_DELIVERY_CITY',     'content' => 'Город',    'sort' => '',                    'default' => true],
    ['id' => 'UF_DELIVERY_STREET',   'content' => 'Улица',    'sort' => 'UF_DELIVERY_STREET',  'default' => true],
    ['id' => 'UF_DELIVERY_HOUSE',    'content' => 'Дом',      'sort' => 'UF_DELIVERY_HOUSE',   'default' => false],
    ['id' => 'UF_PAYMENT_METHOD',    'content' => 'Оплата',   'sort' => 'UF_PAYMENT_METHOD',   'default' => true],
    ['id' => 'UF_TOTAL',             'content' => 'Сумма',    'sort' => 'UF_TOTAL',            'default' => true],
    ['id' => 'UF_ITEMS_COUNT',       'content' => 'Позиций',  'sort' => 'UF_ITEMS_COUNT',      'default' => true],
    ['id' => 'UF_CREATED_AT',        'content' => 'Создан',   'sort' => 'UF_CREATED_AT',       'default' => true],
]);

// ── Данные + пагинация ────────────────────────────────────────────────────
$pageSize = 10;
$total = (int) $ordersCls::getCount($ormFilter);

$rs = new CDBResult();
$rs->NavStart($pageSize);
$page = max(1, (int) $rs->NavPageNomer);

$query = $ordersCls::query()
    ->setFilter($ormFilter)
    ->setSelect(['*'])
    ->setOrder([$oSort->getField() => $oSort->getOrder()])
    ->setLimit($pageSize)
    ->setOffset(($page - 1) * $pageSize);

$rows = [];
foreach ($query->exec() as $row) {
    $rows[] = $row;
}

// Финальный CDBResult для NavPrint (берёт total из NavRecordCount).
$rsList = new CDBResult();
$rsList->InitFromArray($rows);
$rsList->NavStart($pageSize);
$rsList->NavRecordCount = $total;
$rsList->NavPageCount = (int) max(1, ceil($total / $pageSize));
$rsList->NavPageNomer = $page;
$lAdmin->NavText($rsList->GetNavPrint('Заказы'));

$paymentLabels = ['card' => 'Карта', 'uzum_bank' => 'Рассрочка UZUM', 'anor_bank' => 'Рассрочка Anorbank'];
$statusLabels  = ['new' => 'Новый', 'confirmed' => 'Подтверждён', 'shipped' => 'Отправлен', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'];

foreach ($rows as $row) {
    $id = (int) $row['ID'];
    $viewUrl = '/bitrix/admin/gree_orders_view.php?ID=' . $id . '&lang=' . LANG;
    $rowObj = $lAdmin->AddRow($id, $row, $viewUrl);

    $rowObj->AddViewField('ID', '<a href="' . $viewUrl . '"><b>' . $id . '</b></a>');
    $rowObj->AddViewField('UF_PUBLIC_ID', '<a href="' . $viewUrl . '">' . htmlspecialchars((string) $row['UF_PUBLIC_ID'], ENT_QUOTES) . '</a>');
    $rowObj->AddViewField('UF_STATUS', $statusLabels[$row['UF_STATUS']] ?? $row['UF_STATUS']);
    $rowObj->AddViewField('UF_DELIVERY_CITY', $citiesById[(int) $row['UF_DELIVERY_CITY']] ?? ('#' . (int) $row['UF_DELIVERY_CITY']));
    $rowObj->AddViewField('UF_PAYMENT_METHOD', $paymentLabels[$row['UF_PAYMENT_METHOD']] ?? $row['UF_PAYMENT_METHOD']);
    $rowObj->AddViewField('UF_TOTAL', number_format((int) $row['UF_TOTAL'], 0, '.', ' ') . ' UZS');
    $rowObj->AddViewField('UF_CREATED_AT', $row['UF_CREATED_AT'] instanceof DateTime ? $row['UF_CREATED_AT']->format('d.m.Y H:i') : (string) $row['UF_CREATED_AT']);
    $rowObj->AddActions([
        ['ICON' => 'view', 'TEXT' => 'Открыть', 'ACTION' => $lAdmin->ActionRedirect($viewUrl)],
    ]);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle('Заказы');
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
?>

<form name="find_form" method="get" action="<?= htmlspecialchars($APPLICATION->GetCurPage(), ENT_QUOTES) ?>">
    <?php $oFilter->Begin(); ?>

    <tr>
        <td><b>Поиск:</b></td>
        <td><input type="text" name="find" size="40" value="<?= htmlspecialchars((string) $find, ENT_QUOTES) ?>"> <span style="color:#888">— номер, имя, телефон, телеграм, улица, дом</span></td>
    </tr>
    <tr>
        <td>Статус:</td>
        <td>
            <select name="find_status">
                <?php foreach (['' => '(все)'] + $statusLabels as $v => $label): ?>
                    <option value="<?= htmlspecialchars((string) $v, ENT_QUOTES) ?>" <?= ((string) $find_status === (string) $v) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label, ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Номер:</td>
        <td><input type="text" name="find_public_id" size="20" value="<?= htmlspecialchars((string) $find_public_id, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Имя:</td>
        <td><input type="text" name="find_name" size="30" value="<?= htmlspecialchars((string) $find_name, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Телефон:</td>
        <td><input type="text" name="find_phone" size="20" value="<?= htmlspecialchars((string) $find_phone, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Telegram:</td>
        <td><input type="text" name="find_telegram" size="20" value="<?= htmlspecialchars((string) $find_telegram, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Улица:</td>
        <td><input type="text" name="find_street" size="30" value="<?= htmlspecialchars((string) $find_street, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Дом:</td>
        <td><input type="text" name="find_house" size="10" value="<?= htmlspecialchars((string) $find_house, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Оплата:</td>
        <td>
            <select name="find_payment">
                <?php foreach (['' => '(все)'] + $paymentLabels as $v => $label): ?>
                    <option value="<?= htmlspecialchars((string) $v, ENT_QUOTES) ?>" <?= ((string) $find_payment === (string) $v) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label, ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Сумма от:</td>
        <td>
            <input type="text" name="find_total_from" size="12" inputmode="numeric"
                   value="<?= htmlspecialchars((string) $find_total_from, ENT_QUOTES) ?>">
            <span style="color:#888; margin-left:8px;">диапазон по заказам: <?= number_format($totalMin, 0, '.', ' ') ?> – <?= number_format($totalMax, 0, '.', ' ') ?> UZS</span>
        </td>
    </tr>
    <tr>
        <td>Сумма до:</td>
        <td>
            <input type="text" name="find_total_to" size="12" inputmode="numeric"
                   value="<?= htmlspecialchars((string) $find_total_to, ENT_QUOTES) ?>">
            <span style="color:#888; margin-left:8px;">диапазон по заказам: <?= number_format($totalMin, 0, '.', ' ') ?> – <?= number_format($totalMax, 0, '.', ' ') ?> UZS</span>
        </td>
    </tr>
    <tr>
        <td>Позиций:</td>
        <td><input type="text" name="find_items_count" size="5" inputmode="numeric" value="<?= htmlspecialchars((string) $find_items_count, ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td>Создан от:</td>
        <td><?= CAdminCalendar::CalendarDate('find_created_from', $find_created_from, 10, true) ?></td>
    </tr>
    <tr>
        <td>Создан до:</td>
        <td><?= CAdminCalendar::CalendarDate('find_created_to', $find_created_to, 10, true) ?></td>
    </tr>

    <?php
    $oFilter->Buttons([
        'table_id' => $tableId,
        'url'      => $APPLICATION->GetCurPage(),
        'form'     => 'find_form',
    ]);
    $oFilter->End();
    ?>
</form>

<?php
$lAdmin->DisplayList();
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
