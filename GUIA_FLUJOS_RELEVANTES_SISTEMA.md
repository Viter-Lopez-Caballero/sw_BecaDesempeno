# Guia narrativa de flujos del sistema con ubicaciones (BecasLaravel_AD)

Este documento describe el funcionamiento del sistema de forma humana y, al mismo tiempo, deja claro en donde se implementa cada parte.

## 1. El sistema empieza por las personas

El sistema esta organizado por perfiles para que cada quien vea solo lo necesario: docente, evaluador y administracion.
Esta decision evita que una persona se pierda entre opciones que no le corresponden y reduce errores operativos desde el primer contacto con la plataforma.
En la practica, esto hace que el flujo se sienta ordenado: cada perfil entra, entiende su espacio y sabe con claridad cual es su siguiente accion.

### Backend
- Modelo de usuarios, roles y permisos: [app/Models/User.php](app/Models/User.php)
- Rutas protegidas por rol: [routes/web.php](routes/web.php)
- Datos de sesion/roles compartidos a toda la app: [app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php)

### Frontend
- Layout que consume rol y arma menu: [resources/js/layouts/TeacherLayout.vue](resources/js/layouts/TeacherLayout.vue)
- Configuracion de menus por perfil: [resources/js/config/menu/menuConfig.js](resources/js/config/menu/menuConfig.js)

## 2. Inicio de sesion y redireccion por rol

Cuando alguien inicia sesion, el sistema valida credenciales, verifica estado de correo y lo dirige al panel correcto.
Este paso funciona como un filtro de seguridad y tambien como una guia de experiencia: no solo protege acceso, tambien ubica a la persona en su contexto de trabajo.
Con esto se evita que usuarios entren a zonas equivocadas o intenten acciones fuera de su rol.

### Backend
- Logica de autenticacion: [app/Providers/FortifyServiceProvider.php](app/Providers/FortifyServiceProvider.php#L52)
- Vista de login que se entrega por Inertia: [app/Providers/FortifyServiceProvider.php](app/Providers/FortifyServiceProvider.php#L68)
- Redireccion segun estado/rol: [app/Http/Responses/LoginResponse.php](app/Http/Responses/LoginResponse.php)

### Frontend
- Formulario de acceso: [resources/js/pages/Auth/Login.vue](resources/js/pages/Auth/Login.vue)

## 3. Convocatorias activas para docentes

El docente visualiza solamente convocatorias vigentes y el sistema considera su contexto para evitar acciones duplicadas.
La idea es que la persona docente no tenga que interpretar estados complejos: la plataforma presenta solo lo util y disponible en ese momento.
Esto reduce confusiones, mejora la toma de decisiones y evita intentos de registro fuera de tiempo o repetidos.

### Backend
- Ruta de convocatorias docente: [routes/web.php](routes/web.php#L187)
- Consulta principal: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php#L100)
- Scope de activas: [app/Models/Announcement.php](app/Models/Announcement.php#L168)

### Frontend
- Pantalla de convocatorias: [resources/js/pages/Teacher/Announcements/Index.vue](resources/js/pages/Teacher/Announcements/Index.vue)

## 4. Flujo de postulacion con documentos

El docente llena su solicitud y adjunta documentos. El sistema valida y guarda de forma segura para no dejar expedientes incompletos.
Este es uno de los momentos mas sensibles del proceso porque concentra informacion personal, academica y documental.
Por eso, la plataforma combina validaciones previas con guardado transaccional, de modo que una falla tecnica no deje la solicitud a medias.

### Backend
- Endpoint de postulacion: [routes/web.php](routes/web.php#L189)
- Entrada del proceso: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php#L163)
- Reglas de elegibilidad y transaccion: [app/Services/ApplicationService.php](app/Services/ApplicationService.php)

### Frontend
- Formulario de postulacion: [resources/js/pages/Teacher/Announcements/Apply.vue](resources/js/pages/Teacher/Announcements/Apply.vue)

## 5. Carta de aceptacion y control por etapa

La carta solo se descarga cuando corresponde: solicitud aprobada, propietario correcto y etapa de resultados.
Con esta regla se protege la equidad del proceso, ya que nadie puede adelantarse a resultados ni descargar documentos fuera del calendario oficial.
Ademas, se asegura que cada carta este asociada a quien realmente le pertenece.

### Backend
- Ruta de descarga: [routes/web.php](routes/web.php#L182)
- Validaciones de propiedad/estatus/etapa: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php#L181)
- Generacion del PDF: [app/Services/PdfGenerationService.php](app/Services/PdfGenerationService.php)

### Frontend
- Vista de detalle de solicitud docente: [resources/js/pages/Teacher/Applications/Show.vue](resources/js/pages/Teacher/Applications/Show.vue)

## 6. Evaluadores: solo expedientes asignados

Cada evaluador trabaja sobre expedientes que le fueron asignados, lo que protege la informacion y da trazabilidad.
Esta separacion fortalece la confidencialidad y la gobernanza del proceso: se sabe quien revisa que expediente y en que momento.
Tambien facilita la gestion de cargas de trabajo, porque cada evaluador ve un conjunto realista y controlado de tareas.

### Backend
- Grupo de rutas evaluador: [routes/web.php](routes/web.php#L155)
- Dashboard y control de acceso: [app/Http/Controllers/Evaluator/EvaluatorController.php](app/Http/Controllers/Evaluator/EvaluatorController.php)
- Asignacion de evaluadores desde admin: [app/Http/Controllers/Admin/ApplicationController.php](app/Http/Controllers/Admin/ApplicationController.php#L68)
- Servicio de asignacion: [app/Services/AssignmentService.php](app/Services/AssignmentService.php)

### Frontend
- Bandeja de trabajo del evaluador: [resources/js/pages/Evaluator/Dashboard.vue](resources/js/pages/Evaluator/Dashboard.vue)

## 7. Rubrica y calificacion en tiempo real

La evaluacion usa una rubrica dinamica y el puntaje se actualiza mientras la persona evaluadora responde.
Este enfoque mejora la transparencia de la evaluacion, porque los criterios son visibles y el resultado se construye de forma trazable.
Desde la experiencia humana, disminuye la incertidumbre: el evaluador entiende como impacta cada respuesta en la calificacion final.

### Backend
- Carga de rubrica y evaluacion: [app/Http/Controllers/Evaluator/EvaluatorController.php](app/Http/Controllers/Evaluator/EvaluatorController.php#L93)
- Persistencia del resultado: [app/Http/Controllers/Evaluator/EvaluatorController.php](app/Http/Controllers/Evaluator/EvaluatorController.php#L112)
- Estructura de evaluaciones: [app/Models/Evaluation.php](app/Models/Evaluation.php)

### Frontend
- Pantalla de evaluacion: [resources/js/pages/Evaluator/Evaluation/Show.vue](resources/js/pages/Evaluator/Evaluation/Show.vue)

## 8. Notificaciones y comunicacion continua

Durante todo el proceso, el sistema informa cambios importantes mediante notificaciones internas y correos en cola.
La comunicacion no se deja al azar. Cada evento relevante se convierte en un aviso oportuno para que los actores sepan que paso y que sigue.
Esto reduce tiempos muertos, evita dependencia de mensajes informales y mantiene a la comunidad alineada con el avance del proceso.

### Backend
- Servicio de notificaciones: [app/Services/NotificationService.php](app/Services/NotificationService.php)
- Conteo global de no leidas: [app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php#L41)

### Frontend
- Componente de campana y acciones: [resources/js/components/NotificationsDropdown.vue](resources/js/components/NotificationsDropdown.vue)

## 9. Flujo completo contado como historia

1. Una persona inicia sesion.
2. El sistema valida su acceso y la ubica en su panel.
3. Si es docente, revisa convocatorias activas y postula.
4. Administracion asigna evaluadores.
5. Evaluadores revisan y califican con rubrica.
6. Se emite resultado.
7. En etapa de resultados, el docente puede descargar su carta de aceptacion.

Visto como recorrido, el sistema funciona como una cadena coordinada entre personas y reglas: registro, evaluacion, decision y cierre.
Cada etapa prepara la siguiente y todas se apoyan en controles de acceso, validaciones y evidencia documental.

### Backend
- Mapa de rutas por rol: [routes/web.php](routes/web.php)
- Controlador docente: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php)
- Controlador evaluador: [app/Http/Controllers/Evaluator/EvaluatorController.php](app/Http/Controllers/Evaluator/EvaluatorController.php)
- Controlador admin: [app/Http/Controllers/Admin/ApplicationController.php](app/Http/Controllers/Admin/ApplicationController.php)

### Frontend
- Vistas principales: [resources/js/pages](resources/js/pages)
- Layouts por perfil: [resources/js/layouts](resources/js/layouts)

## 10. Texto sugerido para reporte

El sistema organiza el proceso de convocatorias como una experiencia guiada por roles, donde cada participante accede solo a las funciones que le corresponden. Desde el inicio de sesion hasta la descarga final de la carta de aceptacion, la plataforma aplica validaciones, controles de acceso y notificaciones que permiten mantener orden, trazabilidad y transparencia.
En consecuencia, el proceso no depende de interpretaciones aisladas, sino de un flujo institucional estandarizado que mejora la calidad operativa y la confianza de quienes participan.

## 11. Documento complementario

Para el lado de interfaz y experiencia de uso:
- [GUIA_FRONTEND_VUE_FLUJOS.md](GUIA_FRONTEND_VUE_FLUJOS.md)
