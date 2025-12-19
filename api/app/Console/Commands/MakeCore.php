<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:core';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $coreName;
    protected $coreNameLower;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // mod name
        $coreName = $this->ask('input core name.');
        $this->coreName = ucfirst(Str::camel($coreName));
        $this->coreNameLower = lcfirst(Str::snake($coreName));

        // exists mod
        $corePath = app_path('Core');
        $cores = collect(File::directories($corePath))
            ->map(fn($dir) => basename($dir))
            ->values()->toArray();

        // file creating
        $this->info('creating files ...');
        $this->createFiles();

        $this->info("{$coreName} is created.");
    }

    protected function createFiles(string $path = DIRECTORY_SEPARATOR): void
    {
        $dirPath = resource_path("stubs/core{$path}");
        if (File::isDirectory($dirPath)) {
            // ファイル一覧
            foreach (File::files($dirPath) as $file) {
                $this->createFromTemplate($path, $file->getFilenameWithoutExtension());
            }

            // ディレクトリ一覧
            foreach (File::directories($dirPath) as $directory) {
                $directory = str_replace(resource_path("stubs/core{$path}"), "", $directory);
                $this->createFiles($path . $directory . DIRECTORY_SEPARATOR);
            }
        }
    }


    protected function createFromTemplate(string $path, string $fileName): void
    {
        // load stub file
        $template = file_get_contents(resource_path("stubs/core{$path}{$fileName}.stub"));

        // replace
        $template = $this->replace($template);
        $fileName = $this->replace($fileName) . '.php';

        // put file
        $direPath = app_path("Core/{$this->coreName}{$path}");

        if (!File::isDirectory($direPath)) {
            File::makeDirectory($direPath, 0755, true);
        }

        $fileFullPath = $direPath . $fileName;
        if (!File::isFile($fileFullPath)) {
            File::put($direPath . $fileName, $template);
            $this->info('file ' . $direPath . $fileName . ' created');
        }
    }

    protected function replace(string $str): string
    {
        $str = str_replace('{{name}}', $this->coreName, $str);
        $str = str_replace('{{name_lower}}', $this->coreNameLower, $str);
        $str = str_replace('{{datetime}}', (new \DateTime())->format('Y_m_d_His'), $str);

        return $str;
    }
}
