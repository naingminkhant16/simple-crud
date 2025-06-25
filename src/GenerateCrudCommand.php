<?php

namespace NaingMinKhant\SimpleCrud;

use Exception;
use Illuminate\Console\Command;

class GenerateCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'simple-crud-api {model}';

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
        $generator = new ApiCrud($model, $this);
        // Calling template method
        if ($generator->generate()) {
            $this->info("✅ CRUD generated successfully for model '{$model}'");
            $this->info("- Migration created.");
            $this->info("- Model created.");
            $this->info("- Controller created.");
            $this->info("- Repository created.");
            $this->info("- Route added to routes/api.php");
            return self::SUCCESS;
        }
        return self::FAILURE;
    }
}
