<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Modulo;

$modulo = new Modulo();
$modulo->nombre = 'Beca de Investigación 2026';
$modulo->descripcion = 'Convocatoria abierta para profesores investigadores que desarrollen proyectos de innovación tecnológica y desarrollo científico.';
$modulo->key = 'beca-investigacion-2026';
$modulo->save();

echo "Convocatoria creada exitosamente con ID: " . $modulo->id . "\n";
