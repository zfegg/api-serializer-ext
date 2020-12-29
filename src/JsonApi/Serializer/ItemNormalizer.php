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

namespace Zfegg\ApiSerializerExt\JsonApi\Serializer;

use Zfegg\ApiSerializerExt\Api\IriConverterInterface;
use Zfegg\ApiSerializerExt\Api\ResourceClassResolverInterface;
use Zfegg\ApiSerializerExt\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use Zfegg\ApiSerializerExt\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use Zfegg\ApiSerializerExt\Metadata\Property\PropertyMetadata;
use Zfegg\ApiSerializerExt\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Zfegg\ApiSerializerExt\Serializer\AbstractItemNormalizer;
use Zfegg\ApiSerializerExt\Serializer\CacheKeyTrait;
use Zfegg\ApiSerializerExt\Serializer\ContextTrait;
use Zfegg\ApiSerializerExt\Util\ClassInfoTrait;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Converts between objects and array.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Amrouche Hamza <hamza.simperfit@gmail.com>
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 */
final class ItemNormalizer extends AbstractItemNormalizer
{
    use ClassInfoTrait;

    public const FORMAT = 'jsonapi';

    private $componentsCache = [];

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return self::FORMAT === $format && parent::supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return self::FORMAT === $format && parent::supportsDenormalization($data, $type, $format);
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // Avoid issues with proxies if we populated the object
        if (!isset($context[self::OBJECT_TO_POPULATE]) && isset($data['data']['id'])) {
            if (true !== ($context['api_allow_update'] ?? true)) {
                throw new NotNormalizableValueException('Update is not allowed for this operation.');
            }

            $context[self::OBJECT_TO_POPULATE] = $this->iriConverter->getItemFromIri(
                $data['data']['id'],
                $context + ['fetch_data' => false]
            );
        }

        // Merge attributes and relationships, into format expected by the parent normalizer
        $dataToDenormalize = array_merge(
            $data['data']['attributes'] ?? [],
            $data['data']['relationships'] ?? []
        );

        return parent::denormalize(
            $dataToDenormalize,
            $class,
            $format,
            $context
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = []): void
    {
        parent::setAttributeValue($object, $attribute, \is_array($value) && \array_key_exists('data', $value) ? $value['data'] : $value, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowedAttribute($classOrObject, $attribute, $format = null, array $context = []): bool
    {
        return preg_match('/^\\w[-\\w_]*$/', $attribute) && parent::isAllowedAttribute($classOrObject, $attribute, $format, $context);
    }

}
