<?php

declare(strict_types=1);

namespace Gree\Contract\DB;

/**
 * Контракт для оборачивания операций с БД в транзакции. Поддерживает вложенные
 * вызовы (через SAVEPOINT), безопасен к неявному rollback MySQL (deadlock,
 * timeout, constraint violation сносят все savepoint'ы — сервис ловит это и
 * восстанавливает свой стейт).
 *
 * Идиоматичное использование — `run()` с замыканием: автоматический commit
 * на успехе и rollback на исключении.
 */
interface TransactionServiceInterface
{
    public function startTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;

    /**
     * Выполняет callable внутри транзакции. Возвращает то же, что вернул callable.
     * Любое исключение → rollback + повторный throw.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function run(callable $callback): mixed;
}
