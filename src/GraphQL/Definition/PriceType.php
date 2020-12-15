<?php


namespace Smile\Common\GraphQL\Definition;


use Exception;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;

class PriceType extends ScalarType
{
    public $name = 'Price';

    public $description = '标准金额，小数后截断两位';

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     *
     */
    public function serialize($value)
    {
        return $this->parseValue($value);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param mixed $value
     *
     * @return mixed
     *
     */
    public function parseValue($value)
    {
        if (empty($value)) {
            return '0.00';
        }

        return bcadd($value, 0, 2);
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param Node $valueNode
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $valueNode->value;
    }
}