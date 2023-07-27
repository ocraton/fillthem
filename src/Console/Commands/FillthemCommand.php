<?php 

declare(strict_types=1);
 
namespace Ocraton\Fillthem\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

 
final class FillthemCommand extends Command
{
    protected $signature = 'make:fillthem {name : The name of the resource (singular form)} {--fillable= : Comma-separated list of fillable fields}';

    protected $description = 'Create a model, migration, and seeder with fillable fields';

    public function handle()
    {

        $name = Str::studly($this->argument('name'));
        $fillable = $this->option('fillable');

        // Crea il modello
        $this->call('make:model', [
            'name' => $name,
        ]);

        // Aggiorna il modello con gli $fillable
        $fillableArray = array_map('trim', explode(',', $fillable));
        $this->updateModelFillable($name, $fillableArray);

        // 3. Crea la migration
        $tableName = Str::plural(Str::snake($name));
        $this->call('make:migration', [
            'name' => "create_{$tableName}_table",
            '--create' => $tableName,
        ]);

        // 4. Aggiorna la migration con i campi fillable
        $migrationFile = database_path('migrations') . '/' . $this->getLastMigrationFile();
        $migrationContents = file_get_contents($migrationFile);
        $fillableMigrationCode = implode('', array_map(fn ($field) => "\$table->string('$field');\n" . str_repeat(' ', 12), $fillableArray));
        $migrationContents = str_replace('$table->id();', "\$table->id();\n" . str_repeat(' ', 12) . $fillableMigrationCode, $migrationContents);
        file_put_contents($migrationFile, $migrationContents);


        $this->info("Model created successfully with fillables!");

    }

    private function updateModelFillable(string $modelName, array $fillableArray): void
    {
        $modelPath = app_path("Models/{$modelName}.php");
        $modelContent = file_get_contents($modelPath);
        $fillableString = implode("',\n\t\t" . "'", $fillableArray);
        $fillableCode = "protected \$fillable = [\n\t\t'{$fillableString}'\n\t];";

        // Inserire l'array $fillable prima della parentesi di chiusura della classe
        $modelContent = preg_replace(
            '/\n\s*}\s*$/',
            "\n\n" . str_repeat(' ', 4) . $fillableCode . "\n" . "}\n",
            $modelContent
        );

        file_put_contents($modelPath, $modelContent);
    }


    private function getLastMigrationFile(): string
    {
        $files = scandir(database_path('migrations'), SCANDIR_SORT_DESCENDING);
        return $files[0];
    }

}

?>