<?php

namespace Smile\Common\GraphQL\Definition;


use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use \GraphQL\Type\Definition\ResolveInfo;
use \GraphQL\Type\Definition\ObjectType as GraphQLObjectType;
use Smile\Common\GraphQL\Entity\GraphTypeAttrs;
use Smile\Common\GraphQL\Factory\GraphTypeFactory;
use Smile\Common\Support\Parent\BaseEntity;

abstract class ObjectType extends GraphQLObjectType
{
    public string $typeName = '';

    protected array $inputIgnoreFields = [];

    private $args;

    /**
     * @var GraphTypeFactory
     */
    private GraphTypeFactory $typeFactory;

    public function __construct($args, GraphTypeFactory $typeFactory)
    {
        $this->args = $args;
        $this->typeFactory = $typeFactory;

        // 获取属性
        $attrs = new GraphTypeAttrs();
        $this->init($attrs);
        $this->typeName = array_key_exists('name', $args) ? $args['name'] : $attrs->name;

        $config = [
            'name' => $this->typeName,
            'description' => array_key_exists('desc', $args) ? $args['desc'] : $attrs->desc,
            'fields' => $this->buildFields(),
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                // 处理fieldsMap
                $fieldName = $info->fieldName;
                $fieldsMap = $this->fieldsMap();
                if (count($fieldsMap) > 0 && array_key_exists($fieldName, $fieldsMap)) {
                    $fieldName = $fieldsMap[$fieldName];
                }

                // 替换fieldName中的_下划线
                $methodName = "resolve" . str_replace('_', '', $fieldName);

                if (method_exists($this, $methodName)) {
                    // 反射当前类
                    $reflectionClass = new \ReflectionClass($this);
                    $reflectionMethod = $reflectionClass->getMethod($methodName);
                    // 获取当前方法的参数
                    $params = $reflectionMethod->getParameters();
                    $invokeArgs = [];
                    $defaultArgs = [$val, $args, $context, $info];

                    // 通过参数名、类型来依赖翻转
                    for ($i = 0; $i < count($params); $i++) {
                        $param = $params[$i];
                        $argName = $param->getName();

                        switch ($argName) {
                            case 'val':
                            case 'value':
                            case 'values':
                                $invokeArgs[$i] = $val;
                                continue;
                            case 'arg':
                            case 'args':
                            case 'arguments':
                            case 'argument':
                                if ($param->hasType() && !$param->isArray()) {
                                    $argClass = $param->getClass();
                                    if ($argClass->getParentClass()->getName() == BaseEntity::class) {
                                        $argClassName = $argClass->getName();
                                        $invokeArgs[$i] = new $argClassName($args);
                                        continue;
                                    }
                                }
                                $invokeArgs[$i] = $args;
                                continue;
                            case 'context':
                                $invokeArgs[$i] = $context;
                                continue;
                            case 'resolveInfo':
                            case 'info':
                                $invokeArgs[$i] = $info;
                                continue;
                        }

                        $invokeArgs[$i] = $defaultArgs[$i];
                    }

                    return $reflectionMethod->invokeArgs($this, $invokeArgs);
                } else {
                    // 如果定义了resolveField则使用它
                    if (method_exists($this, 'resolveField')) {
                        return $this->resolveField($val, $args, $context, $info);
                    } elseif (is_object($val)) {
                        return isset($val->{$fieldName}) ? $val->{$fieldName} : null;
                    } else if (is_array($val)) {
                        return array_key_exists($fieldName, $val) ? $val[$fieldName] : null;
                    } else {
                        return null;
                    }
                }
            }
        ];

        parent::__construct($config);
    }

    public function buildFields()
    {
        $args = $this->args;
        // 判断是否从args传入
        if (array_key_exists('fields', $args)) {
            $fields = array_merge($this->fields($this->typeFactory), $args['fields']);
        } else {
            $fields = $this->fields($this->typeFactory);
        }

        foreach ($fields as $key => &$field) {
            if (is_array($field)) {
                // 过滤fields简写
                if (array_key_exists('desc', $field)) {
                    $field['description'] = $field['desc'];
                }
                // 过滤args简写
                if (array_key_exists('args', $field) && is_array($field['args'])) {
                    foreach ($field['args'] as &$arg) {
                        if (is_array($arg) && array_key_exists('desc', $arg)) {
                            $arg['description'] = $arg['desc'];
                        }
                    }
                }
            }
        }

        return $fields;
    }

    abstract public function init(GraphTypeAttrs &$attrs): void;

    abstract public function fields(GraphTypeFactory $types): array;

    public function inputFields(GraphTypeFactory $types, string $name = ''): array
    {
        return [];
    }

    public function fieldsMap()
    {
        return [];
    }

    /**
     * 构建Input类型
     * @param string $name
     * @return InputObjectType
     */
    public function buildInput(string $name = ''): InputObjectType
    {
        $fields = $this->buildFields();
        $inputName = $this->typeName . 'Input';

        $newFields = [];

        foreach ($fields as $key => &$value) {
            if (gettype($value) == 'array') {
                $type = $value['type'];
            } else {
                $type = $value;
            }

            if (in_array($key, $this->inputIgnoreFields)) {
                continue;
            }

            $subType = $type;
            $typeWrappers = [];

            // 解出被包装的类型
            for ($i = 0; $i < 3; $i++) {
                if ($subType instanceof NonNull || $subType instanceof ListOfType) {
                    $typeWrappers[] = get_class($subType);
                    $subType = $subType->getWrappedType();
                    continue;
                }
                break;
            }

            // 如果子类型可以被重新打包 则重新打包
            if (method_exists($subType, 'buildInput')) {
                // 重新包装成Input
                $type = $this->typeFactory->input(get_class($subType));

                foreach ($typeWrappers as $wrapperClass) {
                    if ($wrapperClass == NonNull::class) {
                        $type = $this->typeFactory->nonNull($type);
                    }
                    if ($wrapperClass == ListOfType::class) {
                        $type = $this->typeFactory->listOf($type);
                    }
                }
            }

            $fields[$key]['type'] = $type;
            $newFields[$key] = $fields[$key];
        }

        $newFields = array_merge($newFields, $this->inputFields($this->typeFactory, $name));

        return new InputObjectType([
            'name' => $inputName,
            'fields' => $newFields,
        ]);
    }

    /**
     * @return ObjectType
     */
    public function addCommonInputIgnore()
    {
        return $this
            ->addInputIgnore('createdTime')
            ->addInputIgnore('updatedTime');
    }

    /**
     * @param string $field
     * @return ObjectType
     */
    public function addInputIgnore(string $field)
    {
        $this->inputIgnoreFields[] = $field;
        return $this;
    }
}
