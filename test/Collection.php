<?php


namespace SimpleSerializerTest;


use Exception;
use Zfegg\ApiSerializerExt\Paginator\OffsetPaginatorInterface;
use Traversable;

class Collection implements OffsetPaginatorInterface
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getCurrentPage(): int
    {
        return 1;
    }

    public function getItemsPerPage(): int
    {
        return 10;
    }

    public function count(): int
    {
        return 100;
    }

    public function getIterator()
    {
        return new \ArrayObject($this->data);
    }
}
