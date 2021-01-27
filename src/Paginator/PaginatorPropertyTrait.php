<?php


namespace Zfegg\ApiSerializerExt\Paginator;


trait PaginatorPropertyTrait
{
    private int $currentPage = 1;

    private int $itemsPerPage = 30;


    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }
}
