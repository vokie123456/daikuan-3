<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\AppRepository;

class ResetAppNumber extends Command
{
    protected $appRepository;

    /**
     * The name and signature of the console command.
     * 命令名
     *
     * @var string
     */
    protected $signature = 'clear:app-number {--min=300} {--max=600}';

    /**
     * The console command description.
     * 指令注释
     * 
     * @var string
     */
    protected $description = '[自定义]重置所有APP的申请人数';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AppRepository $appRepository)
    {
        parent::__construct();
        $this->appRepository = $appRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $min = $this->option('min');
        $max = $this->option('max');
        $ret = $this->appRepository->resetAllApplyNumber(min($min, $max), max($min, $max));
        $date = date('Y-m-d H:i:s');
        $this->info("[{$date}] 共有{$ret}个APP的申请人数被成功重置。");
        $this->info("[{$date}] 重置范围: min= {$min}, max={$max}。");
        $this->info('-----------------------------------------');
    }
}
