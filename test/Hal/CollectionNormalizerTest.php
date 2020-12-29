<?php

namespace SimpleSerializerTest\Hal;

use Zfegg\ApiSerializerExt\Hal\CollectionNormalizer;
use PHPUnit\Framework\TestCase;
use Zfegg\ApiSerializerExt\JsonLd\ItemNormalizer;
use Zfegg\ApiSerializerExt\Serializer\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class CollectionNormalizerTest extends TestCase
{

    public function testSerializer()
    {
        $serializer = new Serializer(
            [
                new ItemNormalizer(),
                new CollectionNormalizer('page'),
            ],
            [
                new JsonEncoder('jsonhal'),
            ]
        );

        $data = [
            ['id' => 1, 'name' => 'aaa'],
        ];

        $result = $serializer->serialize($data, 'jsonhal');

        $this->assertJsonStringEqualsJsonString(
            $result,
            $result
        );
    }
}
