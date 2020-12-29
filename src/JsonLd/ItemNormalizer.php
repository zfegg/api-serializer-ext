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

namespace Zfegg\ApiSerializerExt\JsonLd;

use Zfegg\ApiSerializerExt\ContextTrait;
use Zfegg\ApiSerializerExt\Serializer\AbstractItemNormalizer;
use Zfegg\ApiSerializerExt\Util\ClassInfoTrait;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Converts between objects and array including JSON-LD and Hydra metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ItemNormalizer extends AbstractItemNormalizer
{
    use ClassInfoTrait;
    use ContextTrait;
    use JsonLdContextTrait;

    public const FORMAT = 'jsonld';

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return self::FORMAT === $format && parent::supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     *
     * @throws LogicException
     */
    public function normalize($object, $format = null, array $context = [])
    {
//        $resourceClass = $this->resourceClassResolver->getResourceClass($object, $context['resource_class'] ?? null);
//        $context = $this->initContext($resourceClass, $context);
//        $iri = $this->iriConverter->getIriFromItem($object);
//        $context['iri'] = $iri;
        $context['api_normalize'] = true;

//        $metadata = $this->addJsonLdContext($this->contextBuilder, $resourceClass, $context);

        $data = parent::normalize($object, $format, $context);
        if (!\is_array($data)) {
            return $data;
        }


//        $metadata['@id'] = $iri;
//        $metadata['@type'] = $resourceMetadata->getIri() ?: $resourceMetadata->getShortName();

        return $data;
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
        if (isset($data['@id']) && !isset($context[self::OBJECT_TO_POPULATE])) {
            if (true !== ($context['api_allow_update'] ?? true)) {
                throw new NotNormalizableValueException('Update is not allowed for this operation.');
            }

            $context[self::OBJECT_TO_POPULATE] = $this->iriConverter->getItemFromIri($data['@id'], $context + ['fetch_data' => true]);
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
