<?php


namespace Smile\Common\GraphQL\Definition;


use GraphQL\Type\Definition\EnumType as GraphQLEnumType;
use ReflectionClass;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;

abstract class EnumType extends GraphQLEnumType
{
    /**
     * @var GraphTypeFactory
     */
    private GraphTypeFactory $typeFactory;

    public function __construct($config, GraphTypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;

        $attrs = new GraphTypeAttrs();
        $this->init($attrs);

        if (array_key_exists('fromConst', $config)) {
            $values = $this->fromConst($config['fromConst']);
        } else {
            $values = $this->values($typeFactory);
        }

        parent::__construct([
            'name' => $attrs->name,
            'description' => $attrs->desc,
            'values' => $values,
        ]);
    }

    public function fromConst(string $classPath) {
        $reflectionClass = new ReflectionClass($classPath);
        $constants = $reflectionClass->getConstants();

        return array_map(function ($value) use ($classPath) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->typeFactory->enumValue($value, $classPath::getText($value));
        }, $constants);
    }

    abstract public function init(GraphTypeAttrs &$attrs): void;

    abstract public function values(GraphTypeFactory $types): array;

}