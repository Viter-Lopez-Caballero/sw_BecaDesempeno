# Guía de Pruebas - Sistema de Notificaciones

## 1. Preparación Inicial

### Ejecutar la migración (IMPORTANTE - SOLO UNA VEZ)
```powershell
php artisan migrate
```

## 2. Pruebas de Notificaciones

### 📧 Notificación 1: Resumen Semanal para Administradores
**Cuándo se envía:** Automáticamente cada viernes a las 9:00 AM
**A quién:** Todos los usuarios con rol "Admin"
**Contenido Email:** Desglose por día (Lunes a Viernes) + Total
**Contenido Notificación:** Solo el total

#### Probar manualmente:
```powershell
php artisan notifications:send-weekly
```

#### Verificar:
- [ ] Se creó una notificación por cada Admin en la base de datos
- [ ] Cada Admin recibió un correo con el desglose diario
- [ ] La notificación solo muestra el total
- [ ] El email muestra: Lunes, Martes, Miércoles, Jueves, Viernes + Total

---

### 📧 Notificación 2: Asignación de Evaluaciones (Evaluadores)
**Cuándo se envía:** Cuando un Admin asigna evaluaciones a un evaluador
**A quién:** El evaluador específico asignado
**Contenido:** Número de evaluaciones asignadas + advertencia de 5 días

#### Probar con Tinker:
```powershell
php artisan tinker
```

```php
// Obtener un evaluador de prueba
$evaluador = App\Models\User::role('Evaluador')->first();

// Probar el servicio de notificaciones
$service = app(App\Services\NotificationService::class);
$service->notifyEvaluatorAssignment($evaluador->id, 3);

exit
```

#### Verificar:
- [ ] Se creó una notificación para el evaluador
- [ ] El evaluador recibió un correo
- [ ] El email menciona el número de evaluaciones (3 en el ejemplo)
- [ ] El email menciona el límite de 5 días
- [ ] Solo el evaluador asignado puede ver esta notificación

---

### 📧 Notificación 3: Resultado de Solicitud (Docentes)
**Cuándo se envía:** Cuando un Admin aprueba o rechaza una solicitud
**A quién:** El docente que hizo la solicitud
**Contenido:** Estado (aprobada/no aceptada) + nombre de convocatoria

#### Probar con Tinker:
```powershell
php artisan tinker
```

```php
// Obtener un docente de prueba
$docente = App\Models\User::role('Docente')->first();

// Probar notificación de APROBACIÓN
$service = app(App\Services\NotificationService::class);
$service->notifyApplicationVerdict(1, $docente->id, 'approved', 'Becas 2026');

// Probar notificación de RECHAZO (usa "no aceptada" en vez de "rechazada")
$service->notifyApplicationVerdict(1, $docente->id, 'rejected', 'Becas 2026');

exit
```

#### Verificar:
- [ ] Se creó una notificación para el docente
- [ ] El docente recibió un correo
- [ ] El email para aprobación dice "aprobada"
- [ ] El email para rechazo dice "no aceptada" (NO "rechazada")
- [ ] Solo el docente específico puede ver esta notificación

---

### 📧 Notificación 4: Cambio de Etapa de Convocatoria (TODOS)
**Cuándo se envía:** Cuando cambia la etapa de una convocatoria activa
**A quién:** Admin, Evaluadores y Docentes (TODOS)
**Contenido:** Nueva etapa + fecha (si aplica)

#### Probar con Tinker:
```powershell
php artisan tinker
```

```php
$service = app(App\Services\NotificationService::class);
$service->notifyAnnouncementStageChange(
    1,                              // ID de convocatoria
    'Becas 2026',                   // Título de convocatoria
    'Evaluación',                   // Nueva etapa
    now()->addDays(7)->toDateString() // Fecha de la etapa
);

exit
```

#### Verificar:
- [ ] Se creó una notificación para cada usuario (Admin, Evaluador, Docente)
- [ ] Todos recibieron un correo
- [ ] El email muestra la nueva etapa
- [ ] El email muestra la fecha de la etapa
- [ ] Cada usuario ve su propia notificación (no la de otros)

---

### 📧 Notificación 5: Cambio de Fechas de Convocatoria (TODOS)
**Cuándo se envía:** Cuando se modifican las fechas de una convocatoria
**A quién:** Admin, Evaluadores y Docentes (TODOS)
**Contenido:** Lista de fechas que cambiaron (viejo → nuevo)

#### Probar con Tinker:
```powershell
php artisan tinker
```

```php
$service = app(App\Services\NotificationService::class);

$changes = [
    [
        'label' => 'Fecha de inicio',
        'old' => '2026-03-01',
        'new' => '2026-03-15'
    ],
    [
        'label' => 'Fecha de cierre',
        'old' => '2026-04-01',
        'new' => '2026-04-20'
    ]
];

$service->notifyAnnouncementDateChange(1, 'Becas 2026', $changes);

exit
```

#### Verificar:
- [ ] Se creó una notificación para cada usuario (Admin, Evaluador, Docente)
- [ ] Todos recibieron un correo
- [ ] El email muestra la tabla de cambios (fecha vieja → fecha nueva)
- [ ] Cada usuario ve su propia notificación (no la de otros)

---

### 🗑️ Comando 6: Eliminar Evaluaciones Vencidas
**Cuándo se ejecuta:** Automáticamente cada día
**Qué hace:** Elimina evaluaciones pendientes con más de 5 días

#### Probar manualmente:
```powershell
php artisan evaluations:remove-expired
```

#### Verificar:
- [ ] El comando muestra cuántas evaluaciones eliminó
- [ ] Solo elimina evaluaciones con status 'pending'
- [ ] Solo elimina las que tienen más de 5 días desde created_at

---

## 3. Verificar en la Base de Datos

### Ver todas las notificaciones:
```powershell
php artisan tinker
```

```php
App\Models\Notification::with('user')->latest()->get();
exit
```

### Ver notificaciones de un usuario específico:
```php
$user = App\Models\User::find(1); // Cambia el ID
$user->notifications; // Si tienes relación en el modelo User
// O directamente:
App\Models\Notification::where('user_id', 1)->get();
exit
```

### Limpiar todas las notificaciones (para volver a probar):
```php
App\Models\Notification::truncate();
exit
```

---

## 4. Configuración de Email

Asegúrate de que tu archivo `.env` tenga la configuración de correo correcta:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Probar configuración de email:
```powershell
php artisan tinker
```

```php
Mail::raw('Prueba de correo', function ($message) {
    $message->to('tu-email@ejemplo.com')
            ->subject('Test Email');
});
exit
```

---

## 5. Comandos Programados (Scheduler)

Para que los comandos automáticos funcionen, debes configurar el cron:

### En Producción (Linux):
```bash
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### En Desarrollo (Windows/Laragon):
Ejecuta manualmente cuando necesites probar:
```powershell
php artisan schedule:work
```

O ejecuta los comandos individuales:
```powershell
# Resumen semanal (normalmente viernes 9:00 AM)
php artisan notifications:send-weekly

# Actualizar estado de convocatorias (diario)
php artisan convocatoria:update-status

# Eliminar evaluaciones vencidas (diario)
php artisan evaluations:remove-expired
```

---

## 6. Checklist de Pruebas Completas

### Preparación:
- [ ] Migración ejecutada (`php artisan migrate`)
- [ ] Configuración de email en `.env`
- [ ] Al menos un usuario de cada rol (Admin, Evaluador, Docente)

### Notificaciones:
- [ ] Resumen semanal para Admin
- [ ] Asignación de evaluaciones para Evaluador
- [ ] Aprobación de solicitud para Docente
- [ ] Rechazo de solicitud para Docente ("no aceptada")
- [ ] Cambio de etapa para todos
- [ ] Cambio de fechas para todos

### Verificaciones:
- [ ] Cada notificación aparece en el dropdown del usuario correcto
- [ ] Cada notificación solo la ve el usuario asignado
- [ ] El contador de notificaciones no leídas funciona
- [ ] Marcar como leída funciona
- [ ] Eliminar notificación funciona
- [ ] Los correos se envían correctamente
- [ ] Los correos tienen el formato correcto

---

## 7. Solución de Problemas

### No se envían correos:
1. Verifica la configuración en `.env`
2. Ejecuta: `php artisan config:clear`
3. Revisa los logs: `storage/logs/laravel.log`

### No aparecen notificaciones:
1. Verifica que la migración se ejecutó
2. Verifica en la base de datos: tabla `notifications`
3. Verifica que `user_id` coincide con tu usuario actual

### Error "Column user_id doesn't exist":
1. Ejecuta la migración: `php artisan migrate`
2. Si ya la ejecutaste, verifica la tabla: `SHOW COLUMNS FROM notifications;`

### Aparecen notificaciones de otros usuarios:
1. Verifica que los cambios en NotificationController estén aplicados
2. Limpia cache: `php artisan cache:clear`
3. Recarga la página con Ctrl+F5
