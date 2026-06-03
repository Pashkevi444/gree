<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Security\ApiGuardInterface;
use Gree\Contract\Service\FeedbackServiceInterface;
use Gree\Security\AccessDeniedException;

/**
 * Единственный feedback-эндпоинт. Канал берётся из URL: /api/v1/feedback/{channel}.
 * Контроллер не знает про конкретные формы — он только дёргает FeedbackService,
 * который резолвит канал в FeedbackChannelRegistry и валидирует/сохраняет данные.
 *
 * Добавить новую модалку = один класс FeedbackChannelInterface + регистрация в DI.
 */
final class FeedbackController extends BaseController
{
    public function __construct(
        private readonly FeedbackServiceInterface $feedback,
        private readonly ApiGuardInterface $guard,
    ) {}

    public function create(string $channel): HttpResponse
    {
        if (($denial = $this->ensureSafe()) !== null) {
            return $denial;
        }

        try {
            $id = $this->feedback->save($channel, $this->payload());
        } catch (\DomainException) {
            return $this->json(['error' => 'unknown_channel'], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => 'invalid', 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return $this->json(['error' => 'storage_failed'], 500);
        }

        return $this->json(['ok' => true, 'id' => $id], 201);
    }

    private function ensureSafe(): ?HttpResponse
    {
        try {
            $this->guard->guardStateChanging($this->getRequest());
            return null;
        } catch (AccessDeniedException $e) {
            return $this->json(['error' => 'forbidden', 'reason' => $e->getMessage()], 403);
        }
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        $request = $this->getRequest();
        if ($request->isJson()) {
            $request->decodeJson();
            return $request->getJsonList()->toArray();
        }
        return $request->getPostList()->toArray();
    }
}
