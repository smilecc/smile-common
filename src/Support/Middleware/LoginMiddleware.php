<?php


namespace Smile\Common\Support\Middleware;


use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Smile\Common\Support\Entity\SessionPayloadEntity;
use Smile\Common\Support\Util\SessionUtil;

class LoginMiddleware implements MiddlewareInterface
{
    const PAYLOAD_KEY = '';

    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $userId = $request->getHeader('X-User-Id')[0] ?? '';
        $params = $request->getQueryParams();

        if (array_key_exists('debugUser', $params) && env('APP_ENV') != 'production') {
            $userId = $params['debugUser'];
        }

        if (empty($userId)) {
            $userId = SessionUtil::VISITOR_ID;
        }

        $sessionPayload = new SessionPayloadEntity();
        $sessionPayload->userId = $userId;

        $request = Context::override(ServerRequestInterface::class, fn() => $request->withAttribute(self::PAYLOAD_KEY, $sessionPayload));

        return $handler->handle($request);
    }
}