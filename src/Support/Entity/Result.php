<?php


namespace Smile\Common\Support\Entity;


use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Smile\Common\Support\Parent\BaseEntity;

/**
 * @property int $code
 * @property string $message
 * @property object $data
 * @property string $timestamp
 */
class Result extends BaseEntity
{
    public function __construct(int $code, string $message, $payload = null)
    {
        parent::__construct([]);
        $this->code = $code;
        $this->message = $message;
        $this->timestamp = time();
        $this->data = $payload;
    }

    public static function respond(int $code, $payload, string $message = null)
    {
        if (empty($message)) {
            /** @var ConfigInterface $config */
            $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
            $errorCodeClass = $config->get('smile.error_code_class');

            $message = $errorCodeClass::getMessage($code) ?? '服务器走丢啦，请稍后再试';
        }

        return new Result($code, $message, $payload);
    }

    public static function success($payload, string $message = null)
    {
        /** @var ConfigInterface $config */
        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        return self::respond($config->get('smile.success_code', 0), $payload, $message);
    }

    public static function error(int $code, string $message = null, $payload = null)
    {
        return self::respond($code, $payload, $message);
    }
}
