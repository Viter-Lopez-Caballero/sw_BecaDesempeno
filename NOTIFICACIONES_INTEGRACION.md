# Sistema de Notificaciones Completo - Guía de Integración

## 📋 Resumen de Cambios

Se ha ampliado el sistema de notificaciones para incluir múltiples tipos de notificaciones y correos electrónicos para diferentes eventos del sistema.

---

## ✅ Funcionalidades Implementadas

### 1. **Notificaciones para Administradores**
- ✅ Resumen semanal de solicitudes (solo muestra el total en notificación)
- ✅ El correo electrónico sigue mostrando el desglose por día
- ✅ Notificaciones individuales por usuario

### 2. **Notificaciones para Evaluadores**
- ✅ Notificación cuando se le asigna una evaluación
- ✅ Correo indicando el número de evaluaciones asignadas
- ✅ Información de que tiene 5 días para completarlas
- ✅ Comando automático que elimina asignaciones vencidas

### 3. **Notificaciones para Docentes**
- ✅ Notificación cuando su solicitud es aprobada
- ✅ Notificación cuando su solicitud es "No Aceptada" (en lugar de "rechazada")
- ✅ Correos con diseño amigable y profesional

### 4. **Notificaciones Globales (Admin, Evaluador, Docente)**
- ✅ Notificación cuando cambia la etapa de una convocatoria activa
- ✅ Notificación cuando cambian las fechas de una convocatoria
- ✅ Correos electrónicos para todos los eventos

---

## 📁 Archivos Creados

### Mailables
- `app/Mail/EvaluatorAssigned.php` - Correo para evaluadores asignados
- `app/Mail/ApplicationVerdict.php` - Correo para veredicto de solicitudes
- `app/Mail/AnnouncementStageChange.php` - Correo para cambio de etapa
- `app/Mail/AnnouncementDateChange.php` - Correo para cambio de fechas

### Vistas de Correo
- `resources/views/emails/evaluator-assigned.blade.php`
- `resources/views/emails/application-verdict.blade.php`
- `resources/views/emails/announcement-stage-change.blade.php`
- `resources/views/emails/announcement-date-change.blade.php`

### Servicios
- `app/Services/NotificationService.php` - Servicio centralizado para notificaciones

### Comandos
- `app/Console/Commands/RemoveExpiredEvaluations.php` - Elimina evaluaciones vencidas

### Migraciones
- `database/migrations/2026_02_17_000001_add_user_id_to_notifications_table.php`

---

## 🔧 Integración en Controladores

### 1. Asignación de Evaluadores

Cuando asignas un evaluador a una solicitud, usa el servicio de notificaciones:

```php
use App\Services\NotificationService;

// En ApplicationController o donde manejes la asignación
public function assignEvaluator(Request $request)
{
    $validatedData = $request->validate([
        'application_id' => 'required|exists:applications,id',
        'evaluator_ids' => 'required|array',
        'evaluator_ids.*' => 'exists:users,id',
    ]);

    $application = Application::findOrFail($validatedData['application_id']);
    
    foreach ($validatedData['evaluator_ids'] as $evaluatorId) {
        // Crear la evaluación
        Evaluation::create([
            'application_id' => $application->id,
            'evaluator_id' => $evaluatorId,
            'status' => 'pending',
        ]);
    }
    
    // Contar cuántas evaluaciones tiene el evaluador
    $evaluationsCount = Evaluation::where('evaluator_id', $evaluatorId)
        ->where('status', 'pending')
        ->count();
    
    // Enviar notificación y correo
    $notificationService = app(NotificationService::class);
    $notificationService->notifyEvaluatorAssignment($evaluatorId, $evaluationsCount);
    
    return redirect()->back()->with('success', 'Evaluadores asignados correctamente');
}
```

### 2. Veredicto de Solicitud (Aprobar/No Aceptar)

Cuando cambias el estado de una solicitud:

```php
use App\Services\NotificationService;

public function verdict(Request $request, $id)
{
    $validatedData = $request->validate([
        'status' => 'required|in:approved,rejected',
        'comentario' => 'required_if:status,rejected|string|max:500',
    ]);

    $application = Application::findOrFail($id);
    $application->status = $validatedData['status'];
    $application->admin_comment = $validatedData['comentario'] ?? null;
    $application->save();

    // Enviar notificación al docente
    $notificationService = app(NotificationService::class);
    $notificationService->notifyApplicationVerdict(
        $application->id,
        $application->user_id,
        $validatedData['status'],
        $application->announcement->title
    );

    return redirect()->back()->with('success', 'Veredicto registrado correctamente');
}
```

### 3. Cambio de Etapa de Convocatoria

Cuando actualizas el comando `UpdateAnnouncementStatus`:

```php
use App\Services\NotificationService;

// En UpdateAnnouncementStatus.php o donde cambies el estado
public function handle()
{
    // ... tu lógica existente ...
    
    // Cuando cambies el estado a "activa"
    $announcement->status = 'activa';
    $announcement->save();
    
    // Notificar el cambio
    $notificationService = app(NotificationService::class);
    $notificationService->notifyAnnouncementStageChange(
        $announcement->id,
        $announcement->title,
        'Registro de Solicitudes', // Nombre de la etapa
        $announcement->calendar->registration_start ?? null
    );
    
    // Cuando cambies a "cerrada"
    $announcement->status = 'cerrada';
    $announcement->save();
    
    $notificationService->notifyAnnouncementStageChange(
        $announcement->id,
        $announcement->title,
        'Publicación de Resultados',
        $announcement->calendar->results_start ?? null
    );
}
```

### 4. Cambio de Fechas de Convocatoria

Cuando actualizas el calendario de una convocatoria:

```php
use App\Services\NotificationService;

public function updateCalendar(Request $request, $id)
{
    $announcement = Announcement::findOrFail($id);
    $calendar = $announcement->calendar;
    
    $changes = [];
    
    // Detectar cambios
    if ($request->registration_start != $calendar->registration_start) {
        $changes[] = [
            'label' => 'Inicio de Registro',
            'old' => $calendar->registration_start,
            'new' => $request->registration_start
        ];
    }
    
    if ($request->registration_end != $calendar->registration_end) {
        $changes[] = [
            'label' => 'Fin de Registro',
            'old' => $calendar->registration_end,
            'new' => $request->registration_end
        ];
    }
    
    // ... más validaciones de cambios ...
    
    // Actualizar el calendario
    $calendar->update($request->all());
    
    // Si hubo cambios, notificar
    if (!empty($changes)) {
        $notificationService = app(NotificationService::class);
        $notificationService->notifyAnnouncementDateChange(
            $announcement->id,
            $announcement->title,
            $changes
        );
    }
    
    return redirect()->back()->with('success', 'Calendario actualizado');
}
```

---

## ⚙️ Configuración del Scheduler

El archivo `routes/console.php` ya está configurado con los comandos:

```php
Schedule::command('convocatoria:update-status')->daily();
Schedule::command('notifications:send-weekly')->weeklyOn(5, '9:00'); // Viernes 9:00 AM
Schedule::command('evaluations:remove-expired')->daily(); // Eliminar evaluaciones vencidas
```

Para desarrollo local, ejecuta:
```bash
php artisan schedule:work
```

Para producción, configura el crontab o Task Scheduler como se indica en el README principal.

---

## 🗄️ Migración

Ejecuta la migración para agregar el campo `user_id` a la tabla `notifications`:

```bash
php artisan migrate
```

---

## 🎨 Tipos de Notificaciones

El componente `NotificationsDropdown.vue` ahora soporta los siguientes tipos:

- `weekly_summary` - Resumen semanal (solo Admin)
- `evaluator_assignment` - Asignación de evaluaciones (Evaluador)
- `application_verdict` - Resultado de solicitud (Docente)
- `announcement_stage_change` - Cambio de etapa (Todos)
- `announcement_date_change` - Cambio de fechas (Todos)

---

## 📧 Correos Electrónicos

Todos los correos siguen el mismo diseño visual de la aplicación con:
- Degradado púrpura en el fondo
- Header con color específico por tipo de notificación
- Contenido claro y profesional
- Footer con información de contacto

---

## 🧪 Pruebas

### Probar notificación de evaluador asignado:
```bash
php artisan tinker
```
```php
$service = app(\App\Services\NotificationService::class);
$service->notifyEvaluatorAssignment(1, 5); // userId, count
```

### Probar veredicto de solicitud:
```php
$service->notifyApplicationVerdict(1, 2, 'approved', 'Convocatoria 2026');
$service->notifyApplicationVerdict(2, 3, 'rejected', 'Convocatoria 2026');
```

### Probar cambio de etapa:
```php
$service->notifyAnnouncementStageChange(1, 'Convocatoria 2026', 'Evaluación de Solicitudes', '2026-03-01');
```

### Probar cambio de fechas:
```php
$changes = [
    ['label' => 'Inicio de Registro', 'old' => '2026-02-01', 'new' => '2026-02-05'],
    ['label' => 'Fin de Registro', 'old' => '2026-02-28', 'new' => '2026-03-05']
];
$service->notifyAnnouncementDateChange(1, 'Convocatoria 2026', $changes);
```

---

## ⚠️ Notas Importantes

1. **Las notificaciones son por usuario**: Cada usuario ve solo sus notificaciones
2. **Retrocompatibilidad**: Las notificaciones antiguas sin `user_id` seguirán visibles para todos
3. **Eliminación automática**: Las evaluaciones pendientes se eliminan a los 5 días
4. **Correos asíncronos**: Considera usar colas (queues) en producción para no bloquear las respuestas

---

## 🚀 Próximos Pasos

1. Integra las llamadas al `NotificationService` en tus controladores existentes
2. Prueba cada tipo de notificación
3. Verifica que los correos se envíen correctamente
4. Configura el scheduler en producción
5. (Opcional) Implementa colas para el envío de correos

---

¿Necesitas ayuda con la integración? Revisa los ejemplos de código arriba o contacta al equipo de desarrollo.
