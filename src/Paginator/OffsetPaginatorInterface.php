<?php


namespace Zfegg\ApiSerializerExt\Paginator;


interface OffsetPaginatorInterface extends PaginatorInterface, \Countable
{
    public function getCurrentPage(): int;

    public function getItemsPerPage(): int;
}
