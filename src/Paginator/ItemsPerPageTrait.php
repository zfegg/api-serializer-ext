<?php

namespace Zfegg\ApiSerializerExt\Paginator;


trait ItemsPerPageTrait
{
    private int $itemsPerPage = 30;

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = max($itemsPerPage, 1);
    }
}