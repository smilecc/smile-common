<?php


namespace Smile\Common\GraphQL\Definition\CommonType;

use Smile\Common\GraphQL\Definition\EnumType;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;
use Smile\Common\Support\Annotation\Text;

/**
 * Class EEditStatus
 * @package Smile\Common\GraphQL\Definition\CommonType
 */
class EEditStatus extends EnumType
{
    /**
     * @Text("无更改")
     */
    const NO_CHANGE = 0;

    /**
     * @Text("新创建")
     */
    const CREATED = 3;

    /**
     * @Text("已被更改")
     */
    const MODIFIED = 1;

    /**
     * @Text("已删除")
     */
    const REMOVED = 2;

    public function init(GraphTypeAttrs &$attrs): void
    {
        $attrs->name = 'EEditType';
        $attrs->desc = '用于变更时标记字段状态';
    }

    public function values(GraphTypeFactory $types): array
    {
        return $this->fromConst(EEditStatus::class);
    }
}
