<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App;

class ProjectResetCommand extends Command
{
    protected $signature = 'project:reset {--force : enforce}';

    protected $description = "Reset database";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 防呆操作
        $this->productionCheckHint();

        // fixing db:seed class not found
        $this->execShellWithPrettyPrint('composer dump');

        $this->info("Will delete all tables, and run the 'migrate' and 'db:seed' commands");

        $this->nukeDatabase();

        // 生成数据库迁移，同时数据填充
        $this->call('migrate', [
            '--seed'  => 'yes',
            '--force' => 'yes'
        ]);
    }

    // 清洗数据库
    public function nukeDatabase()
    {
        $colname = 'Tables_in_' . env('DB_DATABASE');

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $droplist[] = $table->$colname;
            $this->info('Will delete table - ' . $table->$colname);
        }
        if (!isset($droplist)) {
            $this->error('No table');
            return;
        }
        $droplist = implode(',', $droplist);

        DB::beginTransaction();
        //turn off referential integrity
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement("DROP TABLE $droplist");
        //turn referential integrity back on
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        DB::commit();

        $this->comment("All the tables have been deleted".PHP_EOL);
    }

    // 执行命令行并区块化打印结果
    public function execShellWithPrettyPrint($command)
    {
        $this->info('---');
        $this->info($command);
        $output = shell_exec($command);
        $this->info($output);
        $this->info('---');
    }

    // 高危动作，生产环境下的防呆保护
    public function productionCheckHint($message = '')
    {
        $message = $message ?: 'This is a "very dangerous" operation';
        if (App::environment('production')
            && !$this->option('force')
            && !$this->confirm('Your are in「Production」environment, '.$message.'! Are you sure you want to do this? [y|N]')
        ) {
            exit('Command termination');
        }
    }
}
