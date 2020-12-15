<?php

namespace App\Graph\Type;

use Smile\Common\GraphQL\Definition\ObjectType;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;

class THello extends ObjectType
{

    public function init(GraphTypeAttrs &$attrs): void
    {
        $attrs->name = 'THello';
        $attrs->desc = '示例类型';
    }

    public function fields(GraphTypeFactory $types): array
    {
        return [
            'world' => $types->fastString('示例类型中的字段'),
        ];
    }
}