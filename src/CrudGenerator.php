<?php

namespace NaingMinKhant\SimpleCrud;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class CrudGenerator
{
    protected string $model;
    protected string $modelPath;
    protected string $fqcn;
    protected Command $command;

    /**
     * @param string $model
     * @param Command $command
     */
    public function __construct(string $model, Command $command)
    {
        $this->model = $model;
        $this->command = $command;
        $this->modelPath = app_path('Models/' . $model . '.php');
    }

    /**
     * Template Method for generating
     * @return bool
     */
    public function generate(): bool
    {
        // Check if provided name is valid
        if (!$this->isValidModelName()) {
            $this->error("❌ Invalid model name. Use StudlyCase (e.g., User, ProductItem).");
            return false;
        }

        // Create migration
        $this->makeMigration();
        // Create model
        $this->makeModel();
        // Update model unguarded
        $this->modifyModel();

        $this->fqcn = $this->findModelClass();

        if (!$this->fqcn) {
            $this->error("Model not found after generation.");
            return false;
        }

        // Create repository which extend my repository package
        $this->makeRepository();
        // Crete controller
        $this->makeController();
        // Append route
        $this->appendRoute();

        return true;
    }

    /**
     * Check model name is valid
     * @return bool
     */
    protected function isValidModelName(): bool
    {
        return preg_match('/^[A-Z][A-Za-z0-9]+$/', $this->model);
    }

    /**
     * Step
     * Create migration file for model
     * @return void
     */
    protected function makeMigration(): void
    {
        $migrationName = 'create_' . Str::snake(Str::pluralStudly($this->model)) . '_table';
        $this->command->info("- Migration created.");
        Artisan::call('make:migration', ['name' => $migrationName]);
    }

    /**
     * Step
     * Create model file
     * @return void
     */
    protected function makeModel(): void
    {
        Artisan::call('make:model', ['name' => $this->model]);
        $this->command->info("- Model created.");
    }

    /**
     * Step
     * Make model unguarded
     * @return void
     */
    protected function modifyModel(): void
    {
        if (File::exists($this->modelPath)) {
            $contents = File::get($this->modelPath);
            $contents = preg_replace(
                '/(class\s+' . $this->model . '\s+extends\s+Model\s*\{)/',
                "$1\n    protected \$guarded = [];",
                $contents
            );
            File::put($this->modelPath, $contents);
        }
    }

    /**
     * Step
     * Find model class in Models folder
     * @return string|null
     */
    protected function findModelClass(): ?string
    {
        $modelFile = $this->model . '.php';
        $directory = app_path('Models');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $modelFile) {
                $relativePath = Str::after($file->getPathname(), app_path() . DIRECTORY_SEPARATOR);
                $classPath = str_replace(['/', '\\'], '\\', $relativePath);
                $classPath = str_replace('.php', '', $classPath);
                return 'App\\' . $classPath;
            }
        }

        return null;
    }

    /**
     * Step
     * Make repository file
     * @return void
     */
    protected function makeRepository(): void
    {
        $class = class_basename($this->fqcn);
        $repositoryPath = app_path("Repositories/{$class}Repository.php");

        if (!File::exists(app_path("Repositories"))) {
            File::makeDirectory(app_path("Repositories"), 0777, true);
        }

        if (File::exists($repositoryPath)) {
            $this->error("Repository {$class}Repository already exists.");
        }

        $stub = File::get(__DIR__ . "/stubs/repository.stub");
        $stub = str_replace(
            ['{{model}}', '{{fqcn}}'],
            [$class, $this->fqcn],
            $stub
        );

        File::put($repositoryPath, $stub);
        $this->command->info("- Repository created.");
    }

    /**
     * Step implementation for making controller file
     * @return void
     */
    abstract protected function makeController(): void;

    /**
     * Step
     * Append route
     * @return void
     */
    abstract protected function appendRoute(): void;

    /**
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        $this->command->error($message);
        exit($this->command::FAILURE);
    }
}

