<?php


namespace Smile\Common\Support\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"ALL"})
 */
class Text extends AbstractAnnotation
{
    public function __construct($value = null)
    {
        parent::__construct($value);
    }
}
