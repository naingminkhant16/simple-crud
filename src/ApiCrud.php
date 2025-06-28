<?php

namespace NaingMinKhant\SimpleCrud;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiCrud extends CrudGenerator
{
    /**
     * @param string $model
     * @param Command $command
     */
    public function __construct(string $model, Command $command)
    {
        parent::__construct($model, $command);
    }

    /**
     * Step implementation for making controller file
     * @return void
     */
    protected function makeController(): void
    {
        $class = class_basename($this->fqcn);
        $variable = Str::camel($class);
        $controllerPath = app_path("Http/Controllers/Api/{$class}Controller.php");

        // Create Api dir in controllers
        if (!File::exists(app_path("Http/Controllers/Api"))) {
            File::makeDirectory(app_path("Http/Controllers/Api"), 0777, true);
        }

        if (File::exists($controllerPath)) {
            $this->error("Controller {$class}Controller already exists.");
        }

        $stub = File::get(__DIR__ . "/stubs/controller.api.stub");
        $stub = str_replace(
            ['{{model}}', '{{variable}}', '{{fqcn}}'],
            [$class, $variable, $this->fqcn],
            $stub
        );

        File::put($controllerPath, $stub);
        $this->command->info("- Controller created.");
    }

    /**
     * Step implementation for appending route
     * @return void
     */
    protected function appendRoute(): void
    {
        $class = class_basename($this->fqcn);
        $routeName = Str::kebab(Str::plural($class));
        $route = "Route::apiResource('{$routeName}', App\\Http\\Controllers\\Api\\{$class}Controller::class);";

        if (!File::exists(base_path("routes/api.php"))) {
            $this->error("api.php file doesn't exist!");
        }

        File::append(base_path('routes/api.php'), "\n" . $route);
        $this->command->info("- Route added to routes/api.php");
    }
}
