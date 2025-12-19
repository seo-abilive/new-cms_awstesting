<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCoreMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:core-migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file in a specific core';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // モジュールリストを取得
        $corePath = app_path('Core');
        $cores = collect(File::directories($corePath))
            ->map(fn($dir) => basename($dir))
            ->values();

        if ($cores->isEmpty()) {
            $this->error('No cores found in the Cores directory.');
            return Command::FAILURE;
        }

        // モジュールを選択
        $selectedCore = $this->choice('Select the Core', $cores->toArray());

        // ファイルパスを決定
        $migrationDir = $corePath . "/$selectedCore/Database/migrations";
        if (!File::isDirectory($migrationDir)) {
            File::makeDirectory($migrationDir, 0755, true);
        }
        $migrationPath = "app/Core/{$selectedCore}/Database/migrations";

        // マイグレーション名からファイル名を生成
        $migrationName = $this->argument('name');
        $table = $this->input->getOption('table');
        $create = $this->input->getOption('create') ?: false;

        // マイグレーションコマンドを実行
        Artisan::call('make:migration', [
            'name' => $migrationName,
            '--table' => $table,
            '--create' => $create,
            '--path' => $migrationPath
        ], $this->getOutput());
        $result = Artisan::output();
        $this->info($result);
    }
}
