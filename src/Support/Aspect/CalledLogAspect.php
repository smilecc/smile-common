<?php


namespace Smile\Common\Support\Aspect;


use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Smile\Common\Support\Annotation\CalledLog;
use Smile\Common\Support\Util\Logger;

/**
 * Class CalledLogAspect
 * @package Smile\Common\Support\Aspect
 * @Aspect()
 */
class CalledLogAspect extends AbstractAspect
{
    public $annotations = [
        CalledLog::class
    ];

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function process(ProceedingJoinPoint $point)
    {
        /** @var CalledLog $annotation */
        $annotation = $point->getAnnotationMetadata()->method[CalledLog::class];
        $result = null;
        try {
            // 为了日志顺序 若需要打印result 则先执行再打印日志
            if ($annotation->result) {
                $result = $point->process();
            }
        } finally {
            $logData = [
                'method' => $point->getReflectMethod()->name,
                'class' => $point->getReflectMethod()->class,
                'arguments' => $point->getArguments(),
            ];

            if ($annotation->result) {
                $logData['result'] = $result;
            }

            Logger::get('CalledLog')->info('进入方法调用', $logData);
            // 若不需要result 日志打印后执行
            if (!$annotation->result) {
                $result = $point->process();
            }
            return $result;
        }
    }
}