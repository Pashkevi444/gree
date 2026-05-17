<?php

declare(strict_types=1);

namespace Gree\Service\Exception;

/**
 * Маркер-база для всех доменных исключений сервисного слоя
 * (CheckoutValidation, EmptyCart, OfferNotFound и т. д.).
 *
 * Контроллер может ловить **этот** базовый тип, чтобы единообразно мапить
 * сервис-уровневые отказы в 4xx-ответы (валидация → 422, отсутствие → 404 и
 * т. п.), не перечисляя каждое конкретное исключение в catch-цепочке.
 *
 * Тело пустое — это именно базовый класс «на вырост». Если в будущем
 * появятся общие поля (например, error_code для i18n или http_status для
 * автоматического маппинга в HTTP-ответ), они переедут сюда.
 */
abstract class BaseServiceException extends \RuntimeException {}
