<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('awicode:about', function () {
    $this->info('Awi Code Invoice & Receipt Manager v1.0.0');
})->purpose('Show application baseline version');
