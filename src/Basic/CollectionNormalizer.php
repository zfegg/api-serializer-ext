<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Zfegg\ApiSerializerExt\Basic;

use Zfegg\ApiSerializerExt\Serializer\AbstractCollectionNormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Normalizes collections in the JSON API format.
 *
 * @author Kevin Dunglas <dunglas@gmail.com>
 * @author Hamza Amrouche <hamza@les-tilleuls.coop>
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 */
final class CollectionNormalizer extends AbstractCollectionNormalizer
{
    public const FORMAT = 'json';

    /**
     * {@inheritdoc}
     */
    protected function getPaginationData($object, array $context = []): array
    {
        [$currentPage, $itemsPerPage, $totalItems, $pageCount] = $this->getPaginationConfig($object, $context);

        if ($totalItems) {
            $data['total'] = $totalItems;
        }
        if ($currentPage) {
            $data['page'] = $currentPage;
        }
        if ($pageCount) {
            $data['page_count'] = $pageCount;
        }
        if ($itemsPerPage) {
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
