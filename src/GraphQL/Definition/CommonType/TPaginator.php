<?php


namespace Smile\Common\GraphQL\Definition\CommonType;

use Smile\Common\GraphQL\Definition\ObjectType;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;

class TPaginator extends ObjectType
{
    public function init(GraphTypeAttrs &$attrs): void
    {
        $attrs->name = 'TPagination';
        $attrs->desc = '分页';
    }

    public function fields(GraphTypeFactory $typeFactory): array
    {
        return [
            'total' => $typeFactory->int(),
            'pageSize' => $typeFactory->int(),
            'currentPage' => $typeFactory->int(),
        ];
    }
}