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

namespace Zfegg\ApiSerializerExt\Serializer;

use Zfegg\ApiSerializerExt\Paginator\OffsetPaginatorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Base collection normalizer.
 *
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 */
abstract class AbstractCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * This constant must be overridden in the child class.
     */
    public const FORMAT = 'to-override';

    protected $pageParameterName;

    public function __construct(string $pageParameterName = 'page')
    {
        $this->pageParameterName = $pageParameterName;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return static::FORMAT === $format &&
            is_iterable($data) &&
            isset($context['api_resource']) &&
            $context['api_resource'] == 'collection' &&
            !isset($context['api_sub_level']);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param iterable $object
     */
    public function normalize($object, string $format = null, array $context = [])
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
     * @param string|null $format
     */
    protected function normalizeRawCollection($object, $format = null, array $context = []): array
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
     * @param iterable $object
     */
    protected function getPaginationConfig($object, array $context = []): array
    {
        $currentPage = $itemsPerPage = $totalItems = $pageCount = null;

        if (\is_array($object) || $object instanceof \Countable) {
            $totalItems = \count($object);
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
     * @param iterable $object
     */
    abstract protected function getPaginationData($object, array $context = []): array;

    /**
     * Gets items data.
     *
     * @param iterable $object
     */
    abstract protected function getItemsData($object, string $format = null, array $context = []): array;

    private function initContext()
    {
        return [
            'api_sub_level' => true,
        ];
    }
}
