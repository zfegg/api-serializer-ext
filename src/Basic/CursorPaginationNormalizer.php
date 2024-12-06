<?php


namespace Zfegg\ApiSerializerExt\Basic;


use UnexpectedValueException;
use Zfegg\ApiSerializerExt\Paginator\CursorPaginatorInterface;
use Zfegg\ApiSerializerExt\Serializer\AbstractCollectionNormalizer;

class CursorPaginationNormalizer extends AbstractCollectionNormalizer
{
    public const FORMAT = 'json';

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CursorPaginatorInterface && parent::supportsNormalization($data, $format, $context);
    }

    protected function getPaginationData($object, array $context = []): array
    {
        if (!$object instanceof CursorPaginatorInterface) {
            return [];
        }

        $data = [];
        $data['page_size'] = $object->getItemsPerPage();
        $data['cursor'] = $object->getCursor();
        $data['prev_cursor'] = $object->getPrevCursor();
        $data['next_cursor'] = $object->getNextCursor();

        if ($object instanceof \Countable) {
            $data['total'] = \count($object);
        }

        return $data;
    }

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

    public function getSupportedTypes(?string $format): array
    {
        return [
            \Traversable::class => true,
            'native-array' => true,
        ];
    }
}