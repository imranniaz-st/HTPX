<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about-project', function () {
    $this->info(config('app.name'));
})->purpose('Display the project name');
