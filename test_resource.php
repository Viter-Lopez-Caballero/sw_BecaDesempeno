<?php
$solicitudes = App\Models\Solicitud::with(['convocatoria' => function($q) { $q->withTrashed(); }])
    ->take(5)
    ->get();

$resource = App\Http\Resources\SolicitudResource::collection($solicitudes);
echo json_encode($resource->resolve(), JSON_PRETTY_PRINT);
