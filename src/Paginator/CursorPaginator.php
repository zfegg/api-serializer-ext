<?php


namespace Zfegg\ApiSerializerExt\Paginator;

class CursorPaginator implements CursorPaginatorInterface
{
    use CursorPropertyTrait;

    private iterable $data;

    public function __construct(
        iterable $data,
        ?int $cursor = null,
        ?int $prevCursor = null,
        ?int $nextCursor = null,
        int $itemsPerPage = 30
    ) {
        $this->data = $data;
        $this->cursor = $cursor;
        $this->prevCursor = $prevCursor;
        $this->nextCursor = $nextCursor;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getIterator()
    {
        foreach ($this->data as $item) {
            yield $item;
        }
    }
}
