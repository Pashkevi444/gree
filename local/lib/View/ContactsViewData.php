<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;

final readonly class ContactsViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public ContactChannelCollection $channels,
        public ContactAddressCollection $addresses,
    ) {}
}
