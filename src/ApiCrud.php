<?php

namespace NaingMinKhant\SimpleCrud;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;

class ApiCrud extends CrudGenerator
{
    /**
     * @param string $model
     * @param Command $command
     */
    public function __construct(string $model, private readonly Command $command)
    {
        parent::__construct($model);
    }

    /**
     * Step implementation for making repository file
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
    }

    /**
     * Step implementation for making controller file
     * @return void
     */
    protected function makeController(): void
    {
        $class = class_basename($this->fqcn);
        $variable = Str::camel($class);
        $controllerPath = app_path("Http/Controllers/{$class}Controller.php");

        if (File::exists($controllerPath)) {
            $this->error("Controller {$class}Controller already exists.");
        }

        $stub = File::get(__DIR__ . "/stubs/controller.stub");
        $stub = str_replace(
            ['{{model}}', '{{variable}}', '{{fqcn}}'],
            [$class, $variable, $this->fqcn],
            $stub
        );

        File::put($controllerPath, $stub);
    }

    /**
     * Step implementation for appending route
     * @return void
     */
    protected function appendRoute(): void
    {
        $class = class_basename($this->fqcn);
        $routeName = Str::kebab(Str::plural($class));
        $route = "Route::apiResource('{$routeName}', App\\Http\\Controllers\\{$class}Controller::class);";

        if (!File::exists(base_path("routes/api.php"))) {
            $this->error("api.php file doesn't exist!");
        }

        File::append(base_path('routes/api.php'), "\n" . $route);
    }

    /**
     * @param string $message
     * @return void
     */
    #[NoReturn] protected function error(string $message): void
    {
        $this->command->error($message);
        exit($this->command::FAILURE);
    }
}
