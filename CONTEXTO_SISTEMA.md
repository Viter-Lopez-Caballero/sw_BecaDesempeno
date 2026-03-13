# BecasLaravel_AD — Contexto Completo del Sistema para IA

> **Proyecto:** Sistema de Gestión de Becas Académicas — TecNM  
> **Framework:** Laravel 12 (PHP 8.2+) + Vue 3 + Inertia.js  
> **Fecha de documentación:** Marzo 2026  
> Use este archivo como contexto de sistema en tu IA de escritorio para responder preguntas o generar código relacionado con este proyecto.

---

## 1. Descripción General

BecasLaravel_AD es una plataforma web MVC corporativa para el Tecnológico Nacional de México (TecNM) que gestiona de extremo a extremo el proceso de becas académicas. Cubre desde la publicación de convocatorias hasta la entrega de reconocimientos digitales con verificación pública vía código QR.

**Patrón arquitectónico:** MVC + Service Layer. La lógica de negocio está encapsulada en 10 clases de servicio independientes del controlador.

**SPA sin API REST explícita:** Usa Inertia.js como puente entre los controladores Laravel y las páginas Vue 3. No existe una API REST separada; los controladores retornan respuestas Inertia (`Inertia::render(...)`).

---

## 2. Stack Tecnológico

### Backend
| Paquete | Versión | Uso |
|---------|---------|-----|
| laravel/framework | ^12.0 | Framework MVC principal |
| PHP | 8.2+ | Lenguaje del servidor |
| laravel/fortify | latest | Auth completa: login, 2FA, reset, verify email |
| spatie/laravel-permission | ^6 | RBAC: roles y permisos |
| maatwebsite/excel | ^3.1 | Exports/Imports Excel via PhpSpreadsheet |
| setasign/fpdi + setasign/fpdf | latest | Generación de PDFs sobre plantillas |
| endroid/qr-code | latest | Generación de imágenes QR para PDFs |
| tightenco/ziggy | latest | Rutas PHP disponibles en JavaScript via `@routes` |
| laravel/wayfinder | latest | Tipado de rutas blade en TypeScript |
| pragmarx/google2fa | latest | (2FA, aunque implementado via email code) |

### Frontend
| Paquete | Versión | Uso |
|---------|---------|-----|
| @inertiajs/vue3 | latest | Puente SPA entre Laravel y Vue |
| vue | ^3 | Framework UI reactivo (Composition API) |
| tailwindcss | ^4 | Utilidades CSS atómicas |
| reka-ui | latest | Biblioteca de componentes accesibles |
| pinia | latest | Gestión de estado global |
| vue-select | latest | Selects dinámicos con búsqueda |
| chart.js | latest | Gráficas en dashboards |
| pdfjs-dist | latest | Visor PDF integrado en el navegador |
| sweetalert2 | latest | Modales de confirmación |
| @mdi/js | latest | Iconos Material Design |

### Infraestructura
- **Base de datos:** MySQL
- **Session driver:** database (tabla `sessions`)
- **Cache driver:** database (tabla `cache`)
- **Queue driver:** database (tabla `jobs`) — **no Redis**
- **SMTP:** Brevo — `smtp-relay.brevo.com:2525`
- **Bundler:** Vite
- **Tests E2E:** Playwright
- **Entorno local:** Laragon (Windows)

---

## 3. Roles y Permisos (Spatie RBAC)

### Roles del sistema (4)

#### Super Admin
- Bypass automático de TODOS los permisos via `Gate::before()` en `AuthServiceProvider`
- Acceso exclusivo a: panel SuperAdmin, backups, control global de solicitudes
- Único que puede gestionar BackupSchedule y ejecutar backups

#### Admin
- Gestiona el flujo operativo: convocatorias, solicitudes, evaluadores, veredictos
- También gestiona catálogos (instituciones, áreas, documentos, plantillas, rúbricas)
- Puede exportar reportes Excel
- Recibe el resumen semanal de solicitudes (WeeklyApplicationsSummary)
- Permisos relevantes: `applications.*`, `evaluators.*`, `recognitions.*`, `announcements.*`, `institutions.*`, `priority_areas.*`, `sub_areas.*`, `documents.*`, `templates.*`, `rubrics.*`

#### Evaluador
- Recibe asignaciones de evaluación con plazo de 7 días hábiles
- Califica usando la rúbrica asignada a la convocatoria
- Descarga su reconocimiento en PDF (con QR verificable)
- Permisos relevantes: `evaluations.index`, `evaluations.show`, `evaluations.edit`, `recognitions.index`, `recognitions.download`

#### Docente (Teacher)
- Usuario final del sistema. Aplica a la convocatoria vigente
- Sube documentos requeridos (definidos en CatalogDocument)
- Descarga carta de aceptación y reconocimientos
- Permisos relevantes: `applications.create`, `applications.index`, `applications.show`, `recognitions.download`

### Patrón de permisos
```
{módulo}.{acción}
Ejemplo: applications.index  |  applications.create  |  applications.edit  |  applications.delete
```

Módulos con permisos: `modules`, `permissions`, `roles`, `users`, `institutions`, `priority_areas`, `sub_areas`, `announcements`, `rubrics`, `templates`, `documents`, `requests`, `backup`

---

## 4. Modelos de Base de Datos (24 Modelos · 25 Migraciones)

### Modelos principales con campos clave

#### User
```
id, name, email, password
curp (validado via RENAPO)
employee_number
institution_id (FK → institutions)
priority_area_id (FK → priority_areas)
sub_area_id (FK → sub_areas)
position_type_id (FK → position_types)
email_verified_at
verification_code (6 dígitos)
verification_code_expires_at (24h)
remember_token, timestamps
```
Relaciones: `belongsTo Institution`, `belongsTo PriorityArea`, `belongsTo SubArea`, `hasMany Applications`, `hasMany Evaluations`

#### Application
```
id
user_id (FK → users) — el docente solicitante
announcement_id (FK → announcements)
status: pending | in_review | approved | rejected
identifier (ej: ACE-2026-ACE-13-5WCV35) — para QR
digital_seal (hash SHA-256 del PDF)
snapshot_data (JSON — datos completos al momento de firmar)
timestamps
```
Scopes: `scopeForCurrentUser()`, `scopeApproved()`, `scopeBuscarGlobal()`, `scopeValidByIdentifier()`
Relaciones: `belongsTo User`, `belongsTo Announcement`, `hasMany Documents`

#### Announcement
```
id, title, description
status: activa | cerrada | pendiente
institution_id, priority_area_id
rubric_id (FK → rubrics — rúbrica para calificar)
active (bool — solo 1 activa a la vez)
timestamps
```
Relaciones: `hasOne Calendar`, `hasMany Applications`, `belongsTo Rubric`, `hasMany CatalogDocuments`

#### Calendar
```
id
announcement_id (FK — unique)
publication_start  | publication_end
registration_start | registration_end
evaluation_start   | evaluation_end
results_end
timestamps
```
**CRÍTICO:** El cron `convocatoria:update-status` compara `Carbon::today()` con estos campos para transicionar etapas automáticamente.

#### Evaluation
```
id
application_id (FK → applications)
evaluator_id (FK → users)
status: pending | approved | rejected | expired
score (float — calculado automáticamente)
answers (JSON — { question_id: option_id, ... })
deadline_at (Carbon — +7 días hábiles desde asignación)
submitted_at
comments
timestamps
```
Relaciones: `belongsTo Application`, `belongsTo User (evaluator)`

#### Recognition
```
id
user_id (FK → users)
announcement_id (FK → announcements)
template_id (FK → templates)
type: DOC | EVAL
active (bool — activación manual por admin)
sent_at
identifier (ej: ACE-2025-DOC-5-AB3X9K)
digital_seal (hash SHA-256)
snapshot_data (JSON)
timestamps
```
Scopes: `scopeValidByIdentifier()`

#### Rubric → RubricQuestion → RubricQuestionOption
```
Rubric: id, title, description, announcement_id
RubricQuestion: id, rubric_id, question_text, order, weight
RubricQuestionOption: id, rubric_question_id, option_text, score, order
```
El evaluador selecciona una opción por pregunta. El puntaje final = suma ponderada de `score` de las opciones elegidas. Las respuestas se guardan como JSON en `evaluations.answers`.

#### Template
```
id, name, type (acceptance_letter | teacher_recognition | evaluator_recognition)
path (ruta en storage/ al archivo PDF plantilla)
timestamps
```

#### Institution, PriorityArea, SubArea
```
Institution: id, name, key, state_id, active
PriorityArea: id, name, code, active
SubArea: id, priority_area_id, name, code, active
```

#### Backup / BackupSchedule
```
Backup: id, type (full|incremental), trigger (manual|scheduled), size, path, completed_at
BackupSchedule: id, type, frequency, active, last_run_at, next_run_at
```

#### Notification (DB)
```
id, user_id, title, message, type, read_at, data (JSON), timestamps
```

---

## 5. Flujos de Negocio Completos

### Flujo 1: Registro de un Docente nuevo
1. Usuario accede a `/register` — formulario de registro
2. Sistema llama `CurpService::buscarPorCurp()` → API RENAPO valida CURP
3. Si CURP válido: se crea User con status no verificado
4. Mail: `VerificationCode` enviado con código de 6 dígitos (expira en 24h)
5. Usuario ingresa código en `/email/verify`
6. Email marcado como verificado. Usuario puede hacer login

### Flujo 2: Ciclo completo de una Solicitud de Beca

**Paso 1 — Docente aplica:**
- Requisito: convocatoria en etapa `registro`, sin solicitud previa del docente
- `ApplicationService::checkApplicationEligibility(User, Announcement)` valida ambas condiciones
- Docente llena formulario y sube archivos por tipo de `CatalogDocument` requerido
- Se crea `Application { status = 'pending' }`
- Los documentos se guardan en `storage/app/documents/`
- No se envía email en este paso

**Paso 2 — Admin asigna evaluadores:**
- Admin ve solicitudes en estado `pending` en su panel
- Selecciona 1 ó más evaluadores disponibles
- `AssignmentService::assignEvaluators(Application, array $evaluatorIds)`:
  - Por cada evaluador: crea `Evaluation { status = 'pending', deadline_at = +7 días hábiles }`
  - Actualiza `Application.status = 'in_review'`
- `NotificationService::notifyEvaluatorAssignment(Evaluation)`:
  - Crea notificación DB + envía `EvaluatorAssigned` vía queue a cada evaluador

**Paso 3 — Evaluador califica:**
- Evaluador accede a su panel, ve sus evaluaciones asignadas
- Descarga documentos del docente vía `FileService::download()`
- Responde cada `RubricQuestion` seleccionando una `RubricQuestionOption`
- Al enviar: score calculado automáticamente, `answers` guardado como JSON
- `Evaluation.status = 'approved' | 'rejected'`

**Paso 4 — Admin emite veredicto:**
- Admin revisa evaluaciones y puntajes
- Clic en "Aprobar" o "Rechazar"
- Si **aprueba**: `PdfGenerationService::generateAcceptanceLetter(Application)` → genera PDF → identifier + digital_seal guardados en `applications` → `Application.status = 'approved'`
- Si **rechaza**: `Application.status = 'rejected'`
- `NotificationService::notifyApplicationVerdict(Application)`:
  - Mail `ApplicationVerdict` (con resultado) al docente

### Flujo 3: Ciclo de vida de una Convocatoria

Etapas y transiciones automáticas (cron `convocatoria:update-status` — diario):

| Etapa | Condición (Calendar) | Email disparado |
|-------|---------------------|-----------------|
| `publicacion` | `today >= publication_start` | Ninguno |
| `registro` | `today >= registration_start` | `NewAnnouncement` → todos los docentes |
| `evaluacion` | `today >= evaluation_start` | `AnnouncementStageChange` → admin+evaluadores |
| `resultados` | `today >= evaluation_end` | `AnnouncementStageChange` → todos |
| `terminada` | `today > results_end` | `AnnouncementClosed` → todos los usuarios |

**Regla crítica:** Solo puede existir UNA convocatoria con `active = true` simultáneamente. Validado en `AnnouncementService::createAnnouncement()`.

### Flujo 4: Generación de PDF con QR y Sello Digital

1. Se carga el archivo PDF plantilla (`Template.path`) con `FPDI::setSourceFile()`
2. Se importan todas las páginas de la plantilla con `addPage() + useTemplate()`
3. Se sobreimprimen datos dinámicos: nombre, institución, área, fecha, convocatoria
4. `SignatureService::generateDigitalSeal($content)` calcula SHA-256 del contenido
5. Se genera `identifier = "{TIPO}-{AÑO}-{PREFIX}-{ID}-{6chars_random}"`
6. `endroid/qr-code` genera PNG del QR con URL: `/verify-recognition/{identifier}`
7. Se agrega página extra con QR + datos de verificación + leyenda legal
8. `identifier` y `digital_seal` se guardan en BD (`applications` o `recognitions`)
9. PDF entregado al usuario como stream descargable

### Flujo 5: Verificación pública del documento (POST-QR)

```
GET /verify-recognition/{identifier}
  ↓
VerificationController::show($identifier)
  ↓
Recognition::validByIdentifier($identifier) → si encuentra: render Public/VerifyRecognition
  ↓ (si no encontró)
Application::validByIdentifier($identifier) → si encuentra: render Public/VerifyRecognition
  ↓ (si tampoco)
abort(404)
```
No requiere autenticación. Retorna página Vue pública con nombre, fecha, institución, convocatoria y estado del documento.

---

## 6. Servicios de Negocio (app/Services/)

| Servicio | Métodos principales | Responsabilidad |
|---------|---------------------|-----------------|
| `ApplicationService` | `checkApplicationEligibility()`, `createApplication()`, `updateApplicationStatus()` | Validación y mutación del ciclo de solicitudes |
| `AnnouncementService` | `createAnnouncement()`, `updateAnnouncement()`, `syncDocuments()`, `getActiveAnnouncement()` | CRUD de convocatorias y documentos requeridos |
| `AssignmentService` | `assignEvaluators()`, `removeEvaluator()`, `calculateDeadline()` | Asignación de evaluadores con plazo automático |
| `NotificationService` | `notifyEvaluatorAssignment()`, `notifyApplicationVerdict()`, `notifyAnnouncementStageChange()`, `notifyRecognitionAvailable()`, `notifyEvaluatorReminder()` | Notificaciones DB + email (todas queued) |
| `PdfGenerationService` | `generateAcceptanceLetter()`, `generateTeacherRecognitionPdf()`, `generateEvaluatorRecognitionPdf()`, `addLegalSignaturePage()` | Generación de PDFs con FPDI, QR y sellos |
| `SignatureService` | `generateDigitalSeal($content)`, `verifyDocument($identifier)` | Hash SHA-256 de documentos PDF |
| `FileService` | `upload()`, `download()`, `stream()`, `delete()` | Gestión de archivos en storage |
| `CurpService` | `buscarPorCurp($curp)` | Consulta API RENAPO para validar CURP |
| `TemplateService` | `getTemplate($id)`, `renderTemplate()` | Gestión de plantillas PDF |
| `CatalogDocumentService` | `syncRequiredDocs()`, `validateDocumentSet()` | Documentos requeridos por convocatoria |

---

## 7. Comandos Artisan Programados (app/Console/Commands/)

### `convocatoria:update-status` — Diario
Lógica: Para cada `Announcement` activa, carga su `Calendar`. Compara cada fecha con `Carbon::today()`. Si la fecha de inicio de la siguiente etapa ha llegado, actualiza `announcement.status`. Si el status cambió, llama `NotificationService::notifyAnnouncementStageChange(Announcement)`.

### `evaluations:remind` — Diario (08:00)
Lógica: Busca `Evaluation { status = 'pending', deadline_at - today() <= 2 días hábiles }`. Por cada una llama `NotificationService::notifyEvaluatorReminder(Evaluation)` → envía `EvaluatorReminder` mail.

### `evaluations:expire` — Diario (01:00)
Lógica: Busca `Evaluation { status = 'pending', deadline_at < Carbon::now() }`. Actualiza `status = 'expired'`. Sin notificación al evaluador (el admin lo ve en su panel).

### `notifications:send-weekly` — Viernes 09:00
Lógica: Genera estadísticas de la semana (solicitudes nuevas, en revisión, aprobadas, rechazadas). Construye `WeeklyApplicationsSummary` y lo envía a todos los usuarios con rol `Admin`.

### `backup:run-scheduled` — Variable
Lógica: Revisa `BackupSchedule` records activos. Ejecuta `mysqldump` según configuración (full/incremental). Comprime, almacena en `storage/`, crea registro `Backup`. Envía `BackupCompleted` al super admin.

### `fix-permissions` — Manual
Lógica: Recorre la lista maestra de permisos del seeder. Verifica que cada permiso exista y esté asignado al rol correcto. Útil post-deploy.

---

## 8. Sistema de Notificaciones por Email (16 Mailable)

**Método de envío:** Todas usan `Mail::to($user)->queue()` — procesadas por worker asíncrono.  
**SMTP:** Brevo — `smtp-relay.brevo.com:2525`  
**Queue driver:** database (tabla `jobs` en MySQL)

| Clase Mailable | Disparador |
|---------------|------------|
| `VerificationCode` | Al registrar usuario o solicitar reenvío |
| `PasswordResetMail` | Al hacer clic en "Olvidé mi contraseña" |
| `NewAnnouncement` | Transición convocatoria → `registro` |
| `AnnouncementStageChange` | Cambio de etapa (`evaluacion`, `resultados`) |
| `AnnouncementDateChange` | Modificación de fechas del calendario |
| `AnnouncementClosed` | Transición → `terminada` |
| `EvaluatorAssigned` | Al crear `Evaluation` record (asignación) |
| `EvaluatorReminder` | Cron `evaluations:remind` (≤2 días) |
| `RecognitionAvailable` | Admin activa reconocimiento del evaluador |
| `ApplicationVerdict` | Admin emite veredicto (aprobado/rechazado) |
| `WeeklyApplicationsSummary` | Cron `notifications:send-weekly` (viernes) |
| `UserStore` | Al crear usuario desde panel de seguridad |
| `UserUpdate` | Al editar usuario existente |
| `UserDelete` | Al eliminar usuario |
| `BackupCompleted` | Al completar backup BD |
| `ContactFormMail` | Formulario de contacto en Home → admin |
| `ContactConfirmationMail` | Formulario contacto → confirmación al remitente |

---

## 9. Rutas y Controladores

### Estructura de controladores (18 total)

```
app/Http/Controllers/
  HomeController.php           — Página pública home
  AdminController.php          — Dashboard admin
  TeacherController.php        — Dashboard docente
  NotificationController.php   — Notificaciones (todos los roles)
  CurpController.php           — Endpoint AJAX validación CURP
  ContactController.php        — Formulario de contacto

  Admin/
    ApplicationController.php  — Gestión de solicitudes
    EvaluatorController.php    — Asignación de evaluadores
    RecognitionController.php  — Gestión reconocimientos

  Teacher/
    ApplicationController.php  — Solicitudes del docente
    AnnouncementController.php — Ver convocatorias
    RecognitionController.php  — Mis reconocimientos

  Evaluator/
    EvaluationController.php   — Calificar evaluaciones
    RecognitionController.php  — Mis reconocimientos

  SuperAdmin/
    DashboardController.php    — Estadísticas globales
    BackupController.php       — Gestión backups

  Public/
    RecognitionSearchController.php  — Búsqueda pública
    VerificationController.php       — Verificación QR

  Catalog/
    AnnouncementController.php       — CRUD convocatorias
    InstitutionController.php        — CRUD instituciones
    DocumentController.php           — CRUD tipos de documento
    RubricController.php             — CRUD rúbricas
    TemplateController.php           — CRUD plantillas PDF

  Security/
    UserController.php         — CRUD usuarios
    RoleController.php         — CRUD roles
    PermissionController.php   — CRUD permisos
    ModuleController.php       — CRUD módulos
```

### Grupos de rutas principales

```
GET /                          — Home pública
GET /verify-recognition/{id}   — Verificación pública QR (sin auth)
POST /contact                  — Formulario de contacto

Grupo auth:
  GET /teacher/applications/*    — Solicitudes del docente
  GET /evaluator/evaluations/*   — Evaluaciones asignadas
  GET /admin/applications/*      — Gestión solicitudes
  GET /superadmin/dashboard      — Panel super admin
  GET /security/users/*          — CRUD usuarios
  GET /catalog/announcements/*   — CRUD convocatorias

Formato de rutas nombradas:
  teacher.applications.index
  admin.applications.show
  evaluator.evaluations.edit
  catalog.announcements.store
  security.users.destroy
  etc.
```

---

## 10. Estructura de Directorios del Proyecto

```
BecasLaravel_AD/
  app/
    Http/Controllers/   — 18 controladores en 7 subdirectorios
    Models/             — 24 modelos Eloquent
    Services/           — 10 clases de servicio (lógica de negocio)
    Mail/               — 16 clases Mailable
    Console/Commands/   — 6 comandos Artisan
    Exports/            — 8 clases Export (Excel)
    Imports/            — 3 clases Import (Excel)
    Providers/          — AuthServiceProvider (Gate::before para Super Admin)
  database/
    migrations/         — 25 archivos de migración
    seeders/            — Seeders de roles, permisos, datos iniciales
  resources/js/
    pages/              — 40+ páginas Vue 3
      Auth/             — Login, Register, RegisterEvaluator, TwoFactor, EmailVerify
      Admin/            — Applications, Recognitions, RequestControl
      Teacher/          — Applications, Announcements, Recognitions
      Evaluator/        — Evaluations, Recognitions
      SuperAdmin/       — Dashboard, Backup, Announcements
      Catalog/          — Institutions, PriorityAreas, SubAreas, Documents, Templates, Rubrics
      Security/         — Users, Roles, Permissions, Modules
      Settings/         — Profile, Password, Appearance, TwoFactor
      Public/           — Home, RecognitionSearch, VerifyRecognition
  routes/
    web.php             — Todas las rutas web (60+ rutas nombradas)
    api.php             — Rutas API (CURP AJAX, etc.)
    settings.php        — Rutas de configuración de perfil
  storage/app/
    documents/          — Documentos subidos por docentes
    pdfs/               — PDFs generados
    templates/          — Plantillas PDF para FPDI
    backups/            — Backups de base de datos
```

---

## 11. Reglas de Negocio Importantes

1. **Una sola convocatoria activa:** `Announcement.active = true` solo puede haber uno. Validado en `AnnouncementService`.
2. **Sin duplicados de solicitud:** Un docente no puede tener >1 `Application` por `Announcement`. Validado en `ApplicationService::checkApplicationEligibility()`.
3. **Plazo de evaluación:** 7 días hábiles calculados con `AssignmentService::calculateDeadline()`. La función excluye sábado y domingo.
4. **Activación manual de reconocimientos:** `Recognition.active = false` por defecto. El admin lo activa manualmente. Hasta entonces no es descargable.
5. **Código de verificación:** 6 dígitos numéricos, expira en 24 horas. Si expira, el usuario puede solicitar reenvío.
6. **PDFs inmutables:** Una vez generado y firmado (`identifier` + `digital_seal`), el PDF no se regenera. El `snapshot_data` captura los datos al momento de firma.
7. **Evaluaciones expiradas:** Si el plazo vence, el evaluador no puede calificar. El admin puede manualmente reasignar o descartar.
8. **Formato de identificador:** `{TIPO}-{AÑO}-{PREFIX}-{ID}-{6CHARS_RANDOM}`
   - Carta de Aceptación: `ACE-2026-ACE-13-5WCV35`
   - Reconocimiento Docente: `ACE-2026-DOC-5-AB3X9K`
   - Reconocimiento Evaluador: `ACE-2026-EVAL-8-7ZNPQR`

---

## 12. Bugs Corregidos (Sesión de Debugging)

### Bug #1 — CRÍTICO: PHP Unicode Escape Inválido
**Archivo:** `app/Services/PdfGenerationService.php` (línea 197)  
**Problema:** `"Carta de Aceptaci\u00f3n"` — en PHP `\u00f3` NO es una secuencia de escape Unicode (eso es JavaScript). El string resultante contenía literales `\u00f3`, nunca coincidía con `'Carta de Aceptación'`. Por tanto `$isAcceptanceLetter` siempre era `false` → `Application::find()` nunca se ejecutaba → `identifier` nunca se guardaba en BD → el QR del PDF siempre devolvía 404.  
**Fix:** Cambiado a `'Carta de Aceptación'` con carácter `ó` real en UTF-8.

### Bug #2 — ALTO: VerificationController solo buscaba en `recognitions`
**Archivo:** `app/Http/Controllers/Public/VerificationController.php`  
**Problema:** Las Cartas de Aceptación tienen prefijo `ACE-` y se almacenan en la tabla `applications`, no en `recognitions`. El controlador solo ejecutaba `Recognition::validByIdentifier($id)` → siempre 404 para cartas de docentes. El bug era intermitente: en usuarios con reconocimientos sí funcionaba, en otros no.  
**Fix:** Reescrito con fallback secuencial: primero busca en `recognitions`, si no encuentra busca en `applications`.

### Bug #3 — MEDIO: `$fillable` incompleto en Application
**Archivo:** `app/Models/Application.php`  
**Problema:** `identifier`, `digital_seal` y `snapshot_data` no estaban en el array `$fillable`. El Mass Assignment Guard de Eloquent descartaba silenciosamente estos valores al llamar `create()` o `update()`. Sin excepción, sin error visible — los campos quedaban `NULL` en BD.  
**Fix:** Añadidos los 3 campos a `$fillable`. Añadida scope `scopeValidByIdentifier($query, $identifier)`.

---

## 13. Configuración de Queue y Performance

```env
QUEUE_CONNECTION=database
```

**Implicación:** El worker (php artisan queue:work) hace polling cada ~3 segundos a la tabla `jobs`. Esto significa que los correos pueden tardar hasta 3 segundos en iniciar su envío después de ser despachados.

**Para mejorar performance:**
```bash
# Reducir sleep entre ciclos
php artisan queue:work --sleep=1

# O con supervisor en producción (ver deploy/supervisor-worker.conf)
```

**Envío SMTP:** Brevo en puerto 2525. Agrega ~1-3 segundos por conexión TCP. Dado que `notifyAnnouncementStageChange()` puede enviar a cientos de usuarios en loop, se recomienda usar Redis + Horizon en producción.

---

## 14. Frontend — Páginas Vue 3 (Inertia)

Todas las páginas están en `resources/js/pages/`. Usan Composition API con `<script setup>`. Las props de Inertia se reciben via `defineProps`.

Las rutas se usan vía `route('nombre.ruta')` (Ziggy). Para que funcionen, se debe:
1. Ejecutar `php artisan ziggy:generate` al cambiar rutas en PHP
2. Reconstruir assets con `npm run build` o `npm run dev`

Estado global: Pinia stores en `resources/js/stores/`.

---

## 15. Testing

**E2E:** Playwright — tests en `tests/playwright/`  
Tests existentes: `login-success`, `register-success`, `register-evaluator-success`

**Unit/Feature:** PHPUnit — tests en `tests/Unit/` y `tests/Feature/`

---

*Fin del documento de contexto — BecasLaravel_AD · TecNM · 2026*
