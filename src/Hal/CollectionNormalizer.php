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

namespace Zfegg\ApiSerializerExt\Hal;

use Zfegg\ApiSerializerExt\Serializer\AbstractCollectionNormalizer;
use Zfegg\ApiSerializerExt\Util\IriHelper;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Normalizes collections in the HAL format.
 *
 * @author Kevin Dunglas <dunglas@gmail.com>
 * @author Hamza Amrouche <hamza@les-tilleuls.coop>
 */
final class CollectionNormalizer extends AbstractCollectionNormalizer
{
    public const FORMAT = 'jsonhal';

    /**
     * {@inheritdoc}
     */
    protected function getPaginationData($object, array $context = []): array
    {
        [$currentPage, $itemsPerPage, $totalItems, $pageCount] = $this->getPaginationConfig($object, $context);
        $parsed = IriHelper::parseIri($context['request_uri'] ?? '/', $this->pageParameterName);

        $data = [
            '_links' => [
                'self' => ['href' => IriHelper::createIri($parsed['parts'], $parsed['parameters'], $this->pageParameterName, $currentPage)],
            ],
        ];

        if (null !== $pageCount) {
            $data['_links']['first']['href'] = IriHelper::createIri($parsed['parts'], $parsed['parameters'], $this->pageParameterName, 1.);
            $data['_links']['last']['href'] = IriHelper::createIri($parsed['parts'], $parsed['parameters'], $this->pageParameterName, $pageCount);
        }

        if ($currentPage) {
            if (1. !== $currentPage) {
                $data['_links']['prev']['href'] = IriHelper::createIri($parsed['parts'], $parsed['parameters'], $this->pageParameterName, $currentPage - 1.);
            }

            if ($currentPage < $pageCount) {
                $data['_links']['next']['href'] = IriHelper::createIri($parsed['parts'], $parsed['parameters'], $this->pageParameterName, $currentPage + 1.);
            }
        }

        if (null !== $totalItems) {
            $data['totalItems'] = (int) $totalItems;
        }

        if (null !== $itemsPerPage) {
            $data['itemsPerPage'] = (int) $itemsPerPage;
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
        $data = [];

        foreach ($object as $obj) {
            $item = $this->normalizer->normalize($obj, $format, $context);
            if (!\is_array($item)) {
                throw new UnexpectedValueException('Expected item to be an array');
            }
            $data['_embedded']['item'][] = $item;
            $data['_links']['item'][] = $item['_links']['self'];
        }

        return $data;
    }
}
