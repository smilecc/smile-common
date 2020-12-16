<?php


namespace Smile\Common\GraphQL\Definition;


use GraphQL\Type\Definition\EnumType as GraphQLEnumType;
use Hyperf\Constants\ConstantsCollector;
use Hyperf\Constants\Exception\ConstantsException;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Str;
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

    public function fromConst(string $classPath)
    {
        $reflectionClass = new ReflectionClass($classPath);
        $constants = $reflectionClass->getConstants();

        return array_map(function ($value) use ($classPath) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->typeFactory->enumValue($value, $classPath::getText($value));
        }, $constants);
    }

    public function fromCurrent()
    {
        return $this->fromConst(self::class);
    }

    /**
     * @param $name
     * @param $arguments
     * @return string|null
     * @throws ConstantsException
     */
    public static function __callStatic($name, $arguments)
    {
        if (!Str::startsWith($name, 'get')) {
            throw new ConstantsException('The function is not defined!');
        }

        if (!isset($arguments) || count($arguments) === 0) {
            throw new ConstantsException('The Code is required');
        }

        $code = $arguments[0];
        $name = strtolower(substr($name, 3));
        $class = get_called_class();

        var_dump($name);
        $message = ConstantsCollector::getValue($class, $code, $name);
        var_dump($message);

        array_shift($arguments);

        $result = self::translate($message, $arguments);
        // If the result of translate doesn't exist, the result is equal with message, so we will skip it.
        if ($result && $result !== $message) {
            return $result;
        }

        $count = count($arguments);
        if ($count > 0) {
            return sprintf($message, ...(array)$arguments[0]);
        }

        return $message;
    }

    protected static function translate($key, $arguments): ?string
    {
        if (!ApplicationContext::hasContainer() || !ApplicationContext::getContainer()->has(TranslatorInterface::class)) {
            return null;
        }

        $replace = $arguments[0] ?? [];
        if (!is_array($replace)) {
            return null;
        }

        $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);

        return $translator->trans($key, $replace);
    }

    abstract public function init(GraphTypeAttrs &$attrs): void;

    abstract public function values(GraphTypeFactory $types): array;

}