<?php


namespace Smile\Common\Support\Command;


use Hyperf\Command\Command;
use Hyperf\Database\Commands\ModelOption;
use Hyperf\Utils\Str;
use Smile\Common\Support\Parent\BaseModel;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class MakeModelCommand
 * @package Smile\Common\Support\Command
 * @\Hyperf\Command\Annotation\Command()
 */
class MakeModelCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'make:model';

    public function configure()
    {
        parent::configure();
        $this->setHelp('由璨简化后的gen:model命令');
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        $table = $this->input->getArgument('table');
        $module = $this->input->getArgument('module');
        $studlyModule = Str::studly($module);

        $tableSplits = explode('_', $table);
        $realTable = Str::after($table, $tableSplits[0] . '_');
        $studlyTable = Str::studly($table);
        $studlyRealTable = Str::studly($realTable);

        $this->call('gen:model', [
            'table' => $table,
            '--path' => "app/Storage/{$studlyModule}/Model",
            '--property-case' => ModelOption::PROPERTY_CAMEL_CASE,
            '--force-casts' => true,
            '--with-comments' => true,
            '--inheritance' => 'BaseModel',
            '--uses' => BaseModel::class,
        ]);

        // 生成repo文件
        $repoDir = BASE_PATH . "/app/Storage/{$studlyModule}/Repo";
        if (!is_dir($repoDir)) {
            mkdir($repoDir);
        }

        $repoStub = file_get_contents(__DIR__ . '/../Stubs/repo.stub');
        $repoStub = str_replace('%NAMESPACE%', "App\Storage\{$studlyModule}\Repo", $repoStub);
        $repoStub = str_replace('%CLASS%', "{$studlyRealTable}Repo", $repoStub);
        $repoFilePath = "{$repoDir}/{$studlyRealTable}Repo.php";


        file_put_contents($repoFilePath, $repoStub);
        $this->line('Repo 生成完毕 ' . $repoFilePath);
        $this->line("请手动修改 {$studlyTable}.php 为 {$studlyRealTable}Model.php");
    }

    protected function getArguments()
    {
        return [
            ['table', InputArgument::REQUIRED, '表名称'],
            ['module', InputArgument::REQUIRED, '模块名称，决定在Storage目录中的位置，如填user则最终生成至Storage/User/Model'],
        ];
    }
}