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

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/**
 * Converts between objects and array.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Amrouche Hamza <hamza.simperfit@gmail.com>
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 */
final class ItemNormalizer extends AbstractObjectNormalizer
{
    protected function extractAttributes(object $object, string $format = null, array $context = [])
    {
        // TODO: Implement extractAttributes() method.
    }

    protected function getAttributeValue(object $object, string $attribute, string $format = null, array $context = [])
    {
        // TODO: Implement getAttributeValue() method.
    }

    protected function setAttributeValue(object $object, string $attribute, $value, string $format = null, array $context = [])
    {
        // TODO: Implement setAttributeValue() method.
    }
}

