<?php 

declare(strict_types=1);
 
namespace Ocraton\Fillthem\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

 
final class FillthemCommand extends Command
{
    protected $signature = 'make:fillthem {name : The name of the resource (singular form)} {--fillable= : Comma-separated list of fillable fields with types es. type:name}';

    protected $description = 'Create a model, migration, and seeder with fillable fields';

    public function handle()
    {

        $name = Str::studly($this->argument('name'));
        $fillable = $this->option('fillable');
        $fillableArray = [];
        $migrationArray = [];

        $fieldsArray = array_map('trim', explode(',', $fillable));
        foreach ($fieldsArray as $pair) {
            list($type, $field) = array_map('trim', explode(':', $pair));
            $migrationArray[] = ['type' => $type, 'name' => $field];
            $fillableArray[] = $field;
        }

        // Crea il modello
        $this->call('make:model', [
            'name' => $name,
        ]);

        // Aggiorna il modello con gli $fillable
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
        $fillableMigrationCode = implode("",array_map(fn ($field) => "\$table->".$field['type']."('".$field['name']."');\n" . str_repeat(' ', 12), $migrationArray));
        $migrationContents = str_replace('$table->id();', "\$table->id();\n" . str_repeat(' ', 12) . $fillableMigrationCode, $migrationContents);
        file_put_contents($migrationFile, $migrationContents);


        // 5. Crea il seeder
        $this->call('make:seeder', [
            'name' => "{$name}Seeder",
        ]);

        // 6. Aggiorna il seeder con il metodo create
        $seederPath = database_path("seeders/{$name}Seeder.php");
        $seederContents = file_get_contents($seederPath);
        $seederCreateCode = $this->generateSeederCreateCode($name, $migrationArray);
          $seederContents = str_replace([
            '//',
            'use Illuminate\Database\Seeder;'
        ], [
            $seederCreateCode,
            "use Illuminate\Database\Seeder;\nuse App\Models\\{$name};"
        ], $seederContents);
        $seederContents = str_replace('//', $seederCreateCode, $seederContents);
        file_put_contents($seederPath, $seederContents);


        $this->info("Model, migration and seeder created successfully with fillables!");

    }

    private function updateModelFillable(string $modelName, array $migrationArray): void
    {
        $modelPath = app_path("Models/{$modelName}.php");
        $modelContent = file_get_contents($modelPath);
        $fillableString = implode("',\n\t\t" . "'", $migrationArray);
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

    function generateSeederCreateCode(string $modelName, array $migrationArray)
    {

        $fieldTemplate = "$modelName::create([\n";
        foreach ($migrationArray as $fieldData) {
            $name = $fieldData['name'];
            $type = $fieldData['type'];

            switch ($type) {
                case 'int':
                case 'integer':
                    $fieldTemplate .= "\t\t\t'$name' => 123,\n"; 
                    break;
                case 'string':
                    $fieldTemplate .= "\t\t\t'$name' => 'example_$name',\n"; 
                    break;
                case 'text':
                    $fieldTemplate .= "\t\t\t'$name' => 'example_text_$name',\n"; 
                    break;
                default:
                    // TODO: unknow cases as a string
                    $fieldTemplate .= "\t\t\t'$name' => 'example_text_$name',\n"; 
                    break;
            }
        }

        $fieldTemplate .= "\t\t]);\n" . str_repeat(' ', 8);

        return $fieldTemplate;
    }

}

?>