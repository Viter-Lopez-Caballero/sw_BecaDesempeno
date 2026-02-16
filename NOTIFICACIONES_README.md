# Sistema de Notificaciones Semanales

## Descripción
Sistema que envía notificaciones semanales a los administradores con el resumen de solicitudes recibidas durante la semana cuando hay una convocatoria activa.

## Características
- ✅ Notificaciones en pantalla (Dashboard)
- ✅ Notificaciones por correo electrónico
- ✅ Resumen de solicitudes por día de la semana (Lunes a Viernes)
- ✅ Envío automático cada viernes
- ✅ Disponible para roles: **Administrador**, **Evaluador**, **Docente**
- ✅ Ícono de campana en la esquina superior derecha
- ✅ Badge con contador de notificaciones no leídas

## Ubicación en el Sistema

El ícono de notificaciones (campana 🔔) aparece en la **esquina superior derecha** de todas las pantallas, al mismo nivel del botón de colapsar el sidebar, para los siguientes roles:
- Administrador
- Evaluador
- Docente

Al hacer clic en el ícono, se accede a la página completa de notificaciones.

## Instalación

### 1. Ejecutar la migración
```bash
php artisan migrate
```

### 2. Configurar el Scheduler de Laravel

#### Opción A: Usando Cron (Producción - Linux)
Agregar esta línea al crontab:
```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Para editar el crontab:
```bash
crontab -e
```

#### Opción B: Usando Task Scheduler (Producción - Windows)
1. Abrir "Programador de tareas" (Task Scheduler)
2. Crear tarea básica
3. Nombre: "Laravel Scheduler"
4. Desencadenador: Al iniciar el sistema, repetir cada 1 minuto
5. Acción: Iniciar programa
   - Programa: `C:\ruta\a\php.exe`
   - Argumentos: `artisan schedule:run`
   - Directorio: `C:\ruta\a\tu\proyecto`

#### Opción C: Para desarrollo local
Ejecutar en una terminal separada:
```bash
php artisan schedule:work
```
Este comando mantendrá el scheduler corriendo y revisando cada minuto.

### 3. Registrar el comando en app/Console/Kernel.php

Agregar en el método `schedule()`:
```php
protected function schedule(Schedule $schedule): void
{
    // Enviar notificaciones semanales cada viernes a las 9:00 AM
    $schedule->command('notifications:send-weekly')
             ->weeklyOn(5, '9:00'); // 5 = Viernes
}
```

## Uso Manual

### Enviar notificación inmediatamente (para pruebas):
```bash
php artisan notifications:send-weekly
```

### Ver notificaciones en el dashboard:
1. Iniciar sesión como Administrador, Evaluador o Docente
2. Buscar el ícono de campana (🔔) en la esquina superior derecha
3. El badge rojo muestra el número de notificaciones no leídas
4. Hacer clic en el ícono para ver el listado completo de notificaciones

## Estructura de Datos

### Notificación en base de datos:
```json
{
  "title": "Resumen Semanal de Solicitudes",
  "data": {
    "Lunes": 10,
    "Martes": 23,
    "Miércoles": 32,
    "Jueves": 23,
    "Viernes": 34
  },
  "type": "weekly_summary",
  "read_at": null,
  "created_at": "2026-02-16 09:00:00"
}
```

## Correo Electrónico

El correo se envía a todos los usuarios con rol "Admin" e incluye:
- Título: "Resumen Semanal de Solicitudes"
- Desglose por día de la semana
- Total semanal
- Diseño consistente con otros correos del sistema

## Archivos Creados

### Backend:
- `app/Models/Notification.php` - Modelo de notificaciones
- `app/Http/Controllers/Admin/NotificationController.php` - Controlador de notificaciones
- `app/Console/Commands/SendWeeklyNotifications.php` - Comando para enviar notificaciones
- `app/Mail/WeeklyApplicationsSummary.php` - Mailable para el correo
- `database/migrations/2026_02_16_000001_create_notifications_table.php` - Migración

### Frontend:
- `resources/js/pages/Admin/Notifications/Index.vue` - Vista de notificaciones
- `resources/views/emails/weekly-applications-summary.blade.php` - Vista del correo

### Configuración:
- `routes/web.php` - Rutas agregadas en el grupo de Admin
- `resources/js/layouts/LayoutAuthenticated.vue` - Ícono de notificaciones en header
- `app/Http/Middleware/HandleInertiaRequests.php` - Compartir contador global de notificaciones

## Notas Importantes

1. **Convocatoria activa**: El sistema solo envía notificaciones si hay una convocatoria con estado "Activa"

2. **Rango de fechas**: Cuenta las solicitudes de los últimos 7 días (de lunes a viernes)

3. **Horario**: Por defecto está configurado para los viernes a las 9:00 AM. Puedes cambiarlo en `routes/console.php`

4. **Emails**: Asegúrate de tener Brevo configurado correctamente en el archivo `.env`

5. **Ícono de notificaciones**: Aparece automáticamente en la esquina superior derecha para usuarios con roles Admin, Evaluador o Docente. El badge rojo muestra el conteo de notificaciones no leídas en tiempo real.

## Personalización

### Cambiar día y hora de envío:
Editar `routes/console.php`:
```php
// Enviar los lunes a las 8:00 AM
Schedule::command('notifications:send-weekly')
         ->weeklyOn(1, '8:00');

// Enviar diariamente a las 10:00 AM
Schedule::command('notifications:send-weekly')
         ->dailyAt('10:00');
```

### Cambiar diseño del correo:
Editar `resources/views/emails/weekly-applications-summary.blade.php`

### Agregar más datos a la notificación:
Modificar el método `handle()` en `app/Console/Commands/SendWeeklyNotifications.php`

### Mostrar notificaciones para otros roles:
1. Editar `resources/js/layouts/LayoutAuthenticated.vue` - actualizar la lista de roles en `showNotifications`
2. Editar `app/Http/Middleware/HandleInertiaRequests.php` - agregar el rol a `hasAnyRole()`
