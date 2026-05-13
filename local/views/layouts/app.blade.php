@php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }
global $APPLICATION;
require $templatePath . '/header.php';
@endphp
@yield('content')
@php
global $APPLICATION;
require $templatePath . '/footer.php';
@endphp
