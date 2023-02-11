<?php


namespace Zfegg\ApiSerializerExt\Paginator;


use Exception;
use Traversable;

class Paginator implements OffsetPaginatorInterface
{
    use PaginatorPropertyTrait;

    private iterable $data;
    private int $count;

    public function __construct(iterable $data, int $count, int $currentPage = 1, int $itemsPerPage = 30)
    {
        $this->data = $data;
        $this->count = $count;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return $this->count;
    }
}
