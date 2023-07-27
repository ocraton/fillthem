<?php 

declare(strict_types=1);
 
namespace Ocraton\Fillthem\Console\Commands;

use Illuminate\Console\Command;

 
final class FillthemCommand extends Command
{
    protected $signature = 'make:fillthem {name : The name of the resource (singular form)} {--fillable= : Comma-separated list of fillable fields}';

    protected $description = 'Create a new model, migration, controller resource, and seeder for the specified resource.';

    public function handle()
    {

        $name = ucfirst($this->argument('name'));
        $fillable = $this->option('fillable');

        // Generate the model
        $this->call('make:model', [
            'name' => $name,
            '--migration' => true,
        ]);

        // Generate the controller resource
        $this->call('make:controller', [
            'name' => "{$name}Controller",
            '--resource' => true,
        ]);

        // Generate the seeder
        $this->call('make:seeder', [
            'name' => "{$name}Seeder",
        ]);

        // Create the migration file with specified fillable fields
        $migrationFileName = "create_{$name}_table";
        $migrationFile = database_path("migrations/" . date('Y_m_d_His') . "_{$migrationFileName}.php");
        $fillableArray = $fillable ? explode(',', $fillable) : [];
        $fillableColumns = '';

        if (!empty($fillableArray)) {
            $fillableColumns = "'" . implode("', '", $fillableArray) . "'";
        }

        $stub = __DIR__ . '/stubs/migration.stub';
        $migrationStub = file_get_contents($stub);
        $migrationStub = str_replace(['{{ table }}', '{{ columns }}'], [$name, $fillableColumns], $migrationStub);
        file_put_contents($migrationFile, $migrationStub);

        $this->info('Resource created successfully!');
    }

}

?>