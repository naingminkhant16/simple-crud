<?php

namespace NaingMinKhant\SimpleCrud;

use Exception;
use Illuminate\Console\Command;

class GenerateCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'simple-crud {model} {--api}';

    /**
     * The console command description.
     */
    protected $description = 'Generate CRUD Migration, Model, Controller, Repository, and route for a model';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $model = $this->argument('model');
        $isApi = $this->option('api');

        $generator = ($isApi) ? new ApiCrud($model, $this) : new MvcCrud($model, $this);

        // Calling template method
        if ($generator->generate()) {
            $this->info("✅ CRUD generated successfully for model '{$model}'");

            // Create view files if it is not api
            if (!$isApi) $generator->makeViewFiles();

            return self::SUCCESS;
        }

        return self::FAILURE;
    }
}
