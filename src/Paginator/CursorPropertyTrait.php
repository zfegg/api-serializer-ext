<?php


namespace Zfegg\ApiSerializerExt\Paginator;


trait CursorPropertyTrait
{
    use ItemsPerPageTrait;

    private ?int $cursor = null;

    private ?int $prevCursor = null;

    private ?int $nextCursor = null;

    public function getCursor(): ?int
    {
        return $this->cursor;
    }

    public function setCursor(?int $cursor)
    {
        $this->cursor = $cursor;
    }

    public function getPrevCursor(): ?int
    {
        return $this->prevCursor;
    }

    public function getNextCursor(): ?int
    {
        return $this->nextCursor;
    }
}