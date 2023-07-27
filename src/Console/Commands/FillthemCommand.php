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

        // 1. Crea il modello
        $this->call('make:model', [
            'name' => $name,
        ]);

        // 2. Aggiorna il modello con gli $fillable
        $fillableArray = array_map('trim', explode(',', $fillable));
        $this->updateModelFillable($name, $fillableArray);


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

}

?>