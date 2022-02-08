<?php

declare(strict_types=1);

namespace ZfeggTest\ApiSerializerExt\Basic;


use Zfegg\ApiSerializerExt\Basic\ArrayNormalizer;
use Zfegg\ApiSerializerExt\Basic\CollectionNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Zfegg\ApiSerializerExt\Paginator\Paginator;

class CollectionNormalizerTest extends TestCase
{

    public function testNormalize()
    {
        $data = new Paginator(
            [
                ['id' => 1, 'name' => 'aaa'],
            ],
            100, 1, 10
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

        $context['api_resource'] = 'collection';
        $result = $serializer->serialize($data, 'json', $context);

        $this->assertJsonStringEqualsJsonString(
            '{"total":100,"page":1,"page_count":10,"page_size":10,"data":[{"id":1,"name":"aaa"}]}',
            $result
        );
    }
}
