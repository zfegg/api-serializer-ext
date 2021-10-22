<?php

namespace Zfegg\ApiSerializerExt\Paginator;


trait PaginatorPropertyTrait
{
    use ItemsPerPageTrait;

    private int $currentPage = 1;

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

}
