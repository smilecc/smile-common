<?php


namespace App\Graph\Query;


use App\Graph\Type\THello;
use Smile\Common\GraphQL\Definition\ObjectType;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;

class QRoot extends ObjectType
{
    public function init(GraphTypeAttrs &$attrs): void
    {
        $attrs->name = 'QRoot';
        $attrs->desc = '查询根节点';
    }

    public function fields(GraphTypeFactory $types): array
    {
        return [
            'qHelloWorld' => $types->fastString(),
            'qHello' => $types->fast(THello::class),
        ];
    }

    public function resolveQHelloWorld()
    {
        return 'Hi, Query World';
    }

    public function resolveQHello()
    {
        return [
            'world' => 'Hi, THello\'s World',
        ];
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
