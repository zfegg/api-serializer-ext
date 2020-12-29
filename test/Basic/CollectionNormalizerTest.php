<?php

namespace SimpleSerializerTest\Basic;

use Zfegg\ApiSerializerExt\Basic\ArrayNormalizer;
use Zfegg\ApiSerializerExt\Basic\CollectionNormalizer;
use PHPUnit\Framework\TestCase;
use SimpleSerializerTest\Collection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class CollectionNormalizerTest extends TestCase
{

    public function testNormalize()
    {
        $data = new Collection(
            [
                ['id' => 1, 'name' => 'aaa'],
            ]
        );

        $serializer = new Serializer(
            [
                new ArrayNormalizer(),
                new CollectionNormalizer(),
            ],
            [
                new JsonEncoder(),
            ]
        );

        $result = $serializer->serialize($data, 'json');

        $this->assertJsonStringEqualsJsonString(
            '{"total":100,"page":1,"page_size":10,"data":[{"id":1,"name":"aaa"}]}',
            $result
        );
    }
}
