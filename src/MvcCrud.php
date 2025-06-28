<?php

namespace NaingMinKhant\SimpleCrud;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MvcCrud extends CrudGenerator
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
        $controllerPath = app_path("Http/Controllers/{$class}Controller.php");

        if (File::exists($controllerPath)) {
            $this->error("Controller {$class}Controller already exists.");
        }

        $viewFolder = Str::kebab(Str::pluralStudly($class));
        $routeName = $viewFolder;

        $stub = File::get(__DIR__ . "/stubs/controller.mvc.stub");
        $stub = str_replace(
            ['{{model}}', '{{variable}}', '{{fqcn}}', '{{viewFolder}}', '{{routeName}}'],
            [$class, $variable, $this->fqcn, $viewFolder, $routeName],
            $stub
        );

        File::put($controllerPath, $stub);
        $this->command->info("- Controller created.");
    }

    /**
     * Step
     * Append route
     * @return void
     */
    protected function appendRoute(): void
    {
        $class = class_basename($this->fqcn);
        $routeName = Str::kebab(Str::plural($class));
        $route = "Route::resource('{$routeName}', App\\Http\\Controllers\\{$class}Controller::class);";

        File::append(base_path('routes/web.php'), "\n" . $route);
        $this->command->info("- Route added to routes/web.php");
    }

    /**
     * Create views files
     * @return void
     */
    public function makeViewFiles(): void
    {
        $class = class_basename($this->fqcn);
        $viewFolder = Str::kebab(Str::pluralStudly($class)); // e.g., 'products'
        $viewPath = resource_path("views/{$viewFolder}");

        if (!File::exists($viewPath)) {
            File::makeDirectory($viewPath, 0755, true);
            $this->command->info("🗂️ Created view directory: resources/views/{$viewFolder}");
        }

        $views = ['index', 'create', 'edit', 'show'];

        foreach ($views as $view) {
            $viewFile = "{$viewPath}/{$view}.blade.php";
            if (!File::exists($viewFile)) {
                File::put($viewFile, '');
                $this->command->info("✅ Created: resources/views/{$viewFolder}/{$view}.blade.php");
            } else {
                $this->command->warn("⚠️ View already exists: resources/views/{$viewFolder}/{$view}.blade.php");
            }
        }

        $this->command->info("- View files created.");
    }
}
