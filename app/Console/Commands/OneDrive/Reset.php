<?php

namespace App\Console\Commands\OneDrive;

use Illuminate\Console\Command;

class Reset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset App';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('重置将会抹去全部数据，继续吗？')) {
            $this->warn('开始重置...');
            $this->call('cache:clear');
            copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            $this->warn('重置完成！');
        } else
            return false;
    }
}
