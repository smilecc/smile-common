<?php


namespace App\Graph\Mutation;


use Smile\Common\GraphQL\Definition\ObjectType;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;

class MRoot extends ObjectType
{
    public function init(GraphTypeAttrs &$attrs): void
    {
        $attrs->name = 'MRoot';
        $attrs->desc = '变更根节点';
    }

    public function fields(GraphTypeFactory $types): array
    {
        return [
            'mHelloWorld' => $types->fastString(),
        ];
    }

    public function resolveMHelloWorld()
    {
        return 'Hi, Mutation World';
    }

    /**
     * 注意：请勿移除该方法
     * @return array
     */
    public function resolveField()
    {
        return [];
    }
}
