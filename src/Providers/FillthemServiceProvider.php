<?php

declare(strict_types=1);

namespace Ocraton\Fillthem\Providers;

use Illuminate\Support\ServiceProvider;
use Ocraton\Fillthem\Console\Commands\FillthemCommand;

 
final class FillthemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FillthemCommand::class
            ]);
        }

    }
}

?>