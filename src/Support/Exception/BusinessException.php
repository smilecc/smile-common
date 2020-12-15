<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Smile\Common\Support\Exception;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Server\Exception\ServerException;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Throwable;

class BusinessException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            /** @var ConfigInterface $config */
            $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
            $errorCodeClass = $config->get('smile.error_code_class');
            $message = $errorCodeClass::getMessage($code);
        }

        parent::__construct($message, $code, $previous);
    }
}
