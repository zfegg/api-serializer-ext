<?php

namespace SimpleSerializerTest;

use PHPUnit\Framework\TestCase;
use Zfegg\ApiSerializerExt\Hal\Serializer\CollectionNormalizer;
use Symfony\Component\Serializer\Serializer;

class HalTest extends TestCase
{

    public function testSerializer()
    {
        $serializer = new Serializer(
            [
                new CollectionNormalizer()
            ]
        );
    }
}
