<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Smile\Common;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            // 组件默认配置文件，即执行命令后会把 source 的对应的文件复制为 destination 对应的的文件
            'publish' => [
                [
                    'id' => 'GraphConfig',
                    'description' => 'GraphQL的配置文件', // 描述
                    // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
                    'source' => __DIR__ . '/../publish/config/smile.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/smile.php', // 复制为这个路径下的该文件
                ],
                [
                    'id' => 'Graph',
                    'description' => 'GraphQL初始化目录',
                    'source' => __DIR__ . '/../publish/Graph',
                    'destination' => BASE_PATH . '/app/Graph',
                ],
            ],
        ];
    }
}
