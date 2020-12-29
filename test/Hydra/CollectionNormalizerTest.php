<?php

namespace SimpleSerializerTest\Hydra;

use Zfegg\ApiSerializerExt\Hydra\CollectionNormalizer;
use PHPUnit\Framework\TestCase;
use Zfegg\ApiSerializerExt\JsonLd\ItemNormalizer;
use Zfegg\ApiSerializerExt\Serializer\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CollectionNormalizerTest extends TestCase
{

    public function testNormalize()
    {

        $serializer = new Serializer(
            [
//                new ObjectNormalizer(),
                new ItemNormalizer(),
                new \Zfegg\ApiSerializerExt\Hydra\CollectionNormalizer(),
            ],
            [
                new JsonEncoder('jsonld'),
            ]
        );

        $data = [
            (object)['id' => 1, 'name' => 'aaa'],
        ];

        $result = $serializer->serialize($data, 'jsonld');

        $this->assertJsonStringEqualsJsonString(
            $result,
            $result
        );
    }
}
