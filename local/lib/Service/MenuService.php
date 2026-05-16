<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\MenuItemCollection;
use Gree\Contract\Repository\MenuRepositoryInterface;
use Gree\Contract\Service\MenuServiceInterface;
use Gree\Logging\FileLogger;

final class MenuService extends BaseService implements MenuServiceInterface
{
    public function __construct(
        private readonly MenuRepositoryInterface $menuRepository,
    ) {}

    public function getHeaderMenu(): MenuItemCollection
    {
        try {
            return $this->menuRepository->getTree();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            return new MenuItemCollection();
        }
    }
}
