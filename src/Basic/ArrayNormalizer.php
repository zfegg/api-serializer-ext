<?php


namespace Zfegg\ApiSerializerExt\Basic;


use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

final class ArrayNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    private $defaultContext = [
        AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => null,
        AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        AbstractNormalizer::IGNORED_ATTRIBUTES => [],
    ];

    private ?NameConverterInterface $nameConverter;

    public function __construct(NameConverterInterface $nameConverter = null, array $defaultContext = [])
    {
        $this->nameConverter = $nameConverter;
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);

        if (isset($this->defaultContext[AbstractNormalizer::CALLBACKS])) {
            if (!\is_array($this->defaultContext[AbstractNormalizer::CALLBACKS])) {
                throw new InvalidArgumentException(sprintf('The "%s" default context option must be an array of callables.', AbstractNormalizer::CALLBACKS));
            }

            foreach ($this->defaultContext[AbstractNormalizer::CALLBACKS] as $attribute => $callback) {
                if (!\is_callable($callback)) {
                    throw new InvalidArgumentException(sprintf('Invalid callback found for attribute "%s" in the "%s" default context option.', $attribute, AbstractNormalizer::CALLBACKS));
                }
            }
        }

        if (isset($this->defaultContext[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER]) && !\is_callable($this->defaultContext[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER])) {
            throw new InvalidArgumentException(sprintf('Invalid callback found in the "%s" default context option.', AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER));
        }
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return (is_array($data) && array_is_list($data)) || $data instanceof \ArrayObject;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = [];
        foreach ($object as $attribute => $attributeValue) {
            if (! $this->isAllowedAttribute($attribute, $context)) {
                continue;
            }

            if (isset($context[AbstractNormalizer::CALLBACKS][$attribute])) {
                $attributeValue = $context[AbstractNormalizer::CALLBACKS][$attribute](
                    $attributeValue, $object, $attribute, $format, $context
                );
            }

            if ($this->nameConverter) {
                $attribute = $this->nameConverter->normalize($attribute, 'stdClass', $format, $context);
            }

            if (null !== $attributeValue && !is_scalar($attributeValue)) {
                if (!$this->serializer instanceof NormalizerInterface) {
                    throw new LogicException(sprintf('Cannot normalize attribute "%s" because the injected serializer is not a normalizer.', $attribute));
                }

                $data[$attribute] = $this->serializer->normalize($attributeValue, $format, $this->createChildContext($context, $attribute, $format));
            } else {
                $data[$attribute] = $attributeValue;
            }
        }

        return $data;
    }

    private function createChildContext(array $parentContext, string $attribute, ?string $format): array
    {
        if (isset($parentContext[AbstractNormalizer::ATTRIBUTES][$attribute])) {
            $parentContext[AbstractNormalizer::ATTRIBUTES] = $parentContext[AbstractNormalizer::ATTRIBUTES][$attribute];
        } else {
            unset($parentContext[AbstractNormalizer::ATTRIBUTES]);
        }

        return $parentContext;
    }

    private function isAllowedAttribute(string $attribute, array $context = []): bool
    {
        $ignoredAttributes = $context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? $this->defaultContext[AbstractNormalizer::IGNORED_ATTRIBUTES];
        if (\in_array($attribute, $ignoredAttributes)) {
            return false;
        }

        $attributes = $context[AbstractNormalizer::ATTRIBUTES] ?? $this->defaultContext[AbstractNormalizer::ATTRIBUTES] ?? null;
        if (isset($attributes[$attribute])) {
            // Nested attributes
            return true;
        }

        if (\is_array($attributes)) {
            return \in_array($attribute, $attributes, true);
        }

        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \ArrayObject::class => true,
            'native-array' => true,
        ];
    }
}
