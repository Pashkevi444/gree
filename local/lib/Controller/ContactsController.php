<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\ContactsServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\View\ContactsViewData;

final class ContactsController extends BaseController
{
    public function __construct(
        private readonly ContactsServiceInterface $contacts,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('contacts'));
        $this->addPageAssets('contacts');

        return $this->view('contacts/index', new ContactsViewData(
            breadcrumbs: $this->breadcrumbs->contacts(),
            channels: $this->contacts->getChannels(),
            addresses: $this->contacts->getAddresses(),
        ));
    }
}
