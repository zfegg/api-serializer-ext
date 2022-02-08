<?php

declare(strict_types=1);

namespace Zfegg\ApiSerializerExt\Basic;

use Zfegg\ApiSerializerExt\Serializer\AbstractCollectionNormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

final class CollectionNormalizer extends AbstractCollectionNormalizer
{
    public const FORMAT = 'json';

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPaginationData($object, array $context = []): array
    {
        [$currentPage, $itemsPerPage, $totalItems, $pageCount] = $this->getPaginationConfig($object, $context);

        $data = [];
        if ($totalItems !== null) {
            $data['total'] = $totalItems;
        }
        if ($currentPage !== null) {
            $data['page'] = $currentPage;
        }
        if ($pageCount !== null) {
            $data['page_count'] = $pageCount;
        }
        if ($itemsPerPage !== null) {
            $data['page_size'] = $itemsPerPage;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    protected function getItemsData($object, string $format = null, array $context = []): array
    {
        $data = [
            'data' => [],
        ];

        foreach ($object as $obj) {
            $item = $this->normalizer->normalize($obj, $format, $context);
            if (!\is_array($item)) {
                throw new UnexpectedValueException('Expected item to be an array');
            }

            $data['data'][] = $item;
        }

        return $data;
    }
}
