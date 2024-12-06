<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Zfegg\ApiSerializerExt\Serializer;

use Zfegg\ApiSerializerExt\Paginator\OffsetPaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function count;
use function is_countable;

/**
 * Base collection normalizer.
 *
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 */
abstract class AbstractCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * This constant must be overridden in the child class.
     */
    public const FORMAT = 'to-override';


    public function __construct(protected string $pageParameterName = 'page')
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return static::FORMAT === $format &&
            is_iterable($data) &&
            isset($context['api_resource']) &&
            $context['api_resource'] == 'collection' &&
            ! isset($context['api_sub_level']);
    }


    /**
     * {@inheritdoc}
     *
     * @param iterable $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (isset($context['api_sub_level'])) {
            return $this->normalizeRawCollection($object, $format, $context);
        }

        $context = $this->initContext() + $context;
        $data = [];

        return array_merge_recursive(
            $data,
            $this->getPaginationData($object, $context),
            $this->getItemsData($object, $format, $context)
        );
    }

    /**
     * Normalizes a raw collection (not API resources).
     *
     */
    protected function normalizeRawCollection($object, ?string $format = null, array $context = []): array
    {
        $data = [];
        foreach ($object as $index => $obj) {
            $data[$index] = $this->normalizer->normalize($obj, $format, $context);
        }

        return $data;
    }

    /**
     * Gets the pagination configuration.
     *
     */
    protected function getPaginationConfig(iterable $object, array $context = []): array
    {
        $currentPage = $itemsPerPage = $totalItems = $pageCount = null;

        if (is_countable($object)) {
            $totalItems = count($object);
        }
        if ($object instanceof OffsetPaginatorInterface) {
            $currentPage = $object->getCurrentPage();
            $itemsPerPage = $object->getItemsPerPage();
            $pageCount = ceil($totalItems / $itemsPerPage);
        }

        return [$currentPage, $itemsPerPage, $totalItems, $pageCount];
    }

    /**
     * Gets the pagination data.
     *
     */
    abstract protected function getPaginationData(iterable $object, array $context = []): array;

    /**
     * Gets items data.
     *
     */
    abstract protected function getItemsData(iterable $object, ?string $format = null, array $context = []): array;

    private function initContext(): array
    {
        return [
            'api_sub_level' => true,
        ];
    }
}
