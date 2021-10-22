<?php


namespace Zfegg\ApiSerializerExt\Paginator;


interface CursorPaginatorInterface extends PaginatorInterface
{
    public function getCursor(): ?int;

    public function getItemsPerPage(): int;

    public function getPrevCursor(): ?int;

    public function getNextCursor(): ?int;
}
