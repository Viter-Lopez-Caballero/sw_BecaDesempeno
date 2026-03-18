# Guia narrativa del frontend con ubicaciones (Vue + Inertia)

Este documento explica la experiencia de uso en frontend con lenguaje de reporte y, en cada punto, indica donde esta implementado.

## 1. La experiencia empieza en una app fluida

El frontend busca que la persona usuaria sienta continuidad: navegar, capturar, enviar y revisar sin recargas pesadas ni cambios bruscos de contexto.
Este comportamiento es clave en procesos administrativos largos, porque evita que el usuario pierda informacion o tenga que repetir pasos.
La interfaz se comporta como una conversacion continua con el sistema, no como una serie de pantallas desconectadas.

### Backend
- Entrega de paginas por Inertia desde controladores: [app/Http/Controllers](app/Http/Controllers)

### Frontend
- Arranque de Inertia y Vue: [resources/js/app.js](resources/js/app.js)
- Resolucion de paginas: [resources/js/app.js](resources/js/app.js#L16)
- Integracion de rutas de Laravel en frontend: [resources/js/app.js](resources/js/app.js#L22)

## 2. Login claro y con retroalimentacion inmediata

La persona usuaria completa su acceso y recibe mensajes claros cuando hay error o cuando el ingreso fue correcto.
El objetivo aqui es reducir friccion desde el primer minuto: si algo esta mal, se explica; si todo sale bien, se confirma.
Esto transmite confianza y evita que la persona usuaria piense que el sistema "no respondio".

### Backend
- Render desde backend: [app/Providers/FortifyServiceProvider.php](app/Providers/FortifyServiceProvider.php#L68)

### Frontend
- Vista login: [resources/js/pages/Auth/Login.vue](resources/js/pages/Auth/Login.vue)
- Envio de formulario al login: [resources/js/pages/Auth/Login.vue](resources/js/pages/Auth/Login.vue#L59)

## 3. Layout por rol: cada quien en su contexto

Despues del acceso, la interfaz se acomoda segun el perfil para mostrar menu y acciones relevantes.
En terminos de experiencia, esto disminuye carga cognitiva: menos opciones irrelevantes, mas foco en tareas reales.
Tambien reduce errores por navegacion accidental a modulos que no corresponden.

### Backend
- Props globales de auth/layout: [app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php#L53)

### Frontend
- Layout docente: [resources/js/layouts/TeacherLayout.vue](resources/js/layouts/TeacherLayout.vue)
- Configuracion de menus por rol: [resources/js/config/menu/menuConfig.js](resources/js/config/menu/menuConfig.js)

## 4. Dashboard docente: seguimiento y control

La persona docente ve sus solicitudes, filtra, ordena y consulta su avance sin perder estado de la pantalla.
El dashboard funciona como un punto de control personal donde se concentra el historial y estado del tramite.
La persona usuaria puede buscar rapidamente sin tener que volver a cargar toda la pagina o perder contexto.

### Backend
- Render backend: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php#L55)

### Frontend
- Vista docente: [resources/js/pages/Teacher/Dashboard.vue](resources/js/pages/Teacher/Dashboard.vue)
- Busqueda y filtros con router.get: [resources/js/pages/Teacher/Dashboard.vue](resources/js/pages/Teacher/Dashboard.vue#L30)

## 5. Convocatorias en frontend: estado visible y decisiones claras

El frontend muestra si una convocatoria esta activa, fuera de periodo o si la persona ya solicito, para evitar confusion antes de dar clic.
Esta visualizacion evita que el usuario invierta tiempo en intentos que no proceden y deja claras las reglas de elegibilidad en pantalla.
Como resultado, la toma de decisiones se vuelve mas rapida y menos dependiente de soporte externo.

### Backend
- Datos enviados desde backend: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php#L114)

### Frontend
- Vista de convocatorias: [resources/js/pages/Teacher/Announcements/Index.vue](resources/js/pages/Teacher/Announcements/Index.vue)
- Logica de fase visual: [resources/js/pages/Teacher/Announcements/Index.vue](resources/js/pages/Teacher/Announcements/Index.vue#L32)
- Ruta para ir a postular: [resources/js/pages/Teacher/Announcements/Index.vue](resources/js/pages/Teacher/Announcements/Index.vue#L220)

## 6. Formulario de postulacion: guiado y preventivo

La interfaz ayuda a preparar expediente: documentos nuevos, documentos reutilizados, validaciones por via y mensajes antes del envio.
El formulario no solo captura informacion; tambien previene errores frecuentes de formato, peso o campos faltantes.
Esto eleva la calidad de las solicitudes desde origen y disminuye rechazos por detalles administrativos.

### Backend
- Endpoint backend que recibe: [app/Http/Controllers/TeacherController.php](app/Http/Controllers/TeacherController.php#L163)

### Frontend
- Vista de postulacion: [resources/js/pages/Teacher/Announcements/Apply.vue](resources/js/pages/Teacher/Announcements/Apply.vue)
- Estado reactivo del formulario: [resources/js/pages/Teacher/Announcements/Apply.vue](resources/js/pages/Teacher/Announcements/Apply.vue#L24)
- Validacion de archivos y envio: [resources/js/pages/Teacher/Announcements/Apply.vue](resources/js/pages/Teacher/Announcements/Apply.vue#L127)

## 7. Dashboard evaluador: bandeja de trabajo real

La persona evaluadora ve su carga pendiente y puede ir directo a evaluar cada expediente asignado.
La vista se comporta como un tablero operativo: prioriza pendientes y facilita avanzar caso por caso.
De esta forma, la evaluacion se vuelve mas ordenada y medible.

### Backend
- Render backend: [app/Http/Controllers/Evaluator/EvaluatorController.php](app/Http/Controllers/Evaluator/EvaluatorController.php#L63)

### Frontend
- Vista evaluador: [resources/js/pages/Evaluator/Dashboard.vue](resources/js/pages/Evaluator/Dashboard.vue)
- Link hacia evaluacion individual: [resources/js/pages/Evaluator/Dashboard.vue](resources/js/pages/Evaluator/Dashboard.vue#L216)

## 8. Pantalla de evaluacion: rubrica dinamica y puntaje vivo

El evaluador responde criterios y observa su puntaje total en tiempo real. Si la evaluacion no esta editable, la vista cambia a lectura.
Esta retroalimentacion inmediata mejora la consistencia de la calificacion, porque permite revisar el impacto de cada respuesta al momento.
Ademas, el modo de solo lectura protege la integridad del resultado cuando la etapa de evaluacion ya concluyo.

### Backend
- Persistencia de resultado de evaluacion: [app/Http/Controllers/Evaluator/EvaluatorController.php](app/Http/Controllers/Evaluator/EvaluatorController.php#L112)

### Frontend
- Vista de evaluacion: [resources/js/pages/Evaluator/Evaluation/Show.vue](resources/js/pages/Evaluator/Evaluation/Show.vue)
- Calculo del score: [resources/js/pages/Evaluator/Evaluation/Show.vue](resources/js/pages/Evaluator/Evaluation/Show.vue#L88)
- Seleccion de respuestas: [resources/js/pages/Evaluator/Evaluation/Show.vue](resources/js/pages/Evaluator/Evaluation/Show.vue#L103)
- Guardado final al backend: [resources/js/pages/Evaluator/Evaluation/Show.vue](resources/js/pages/Evaluator/Evaluation/Show.vue#L195)

## 9. Notificaciones en interfaz: ver, marcar y limpiar

La campana permite mantener el contexto diario sin depender solo del correo.
Con esto, la persona usuaria tiene un centro de avisos dentro de la misma plataforma para actuar sin salir del flujo.
La gestion de leidas y pendientes ayuda a mantener orden operativo en tareas y recordatorios.

### Backend
- Conteo global no leidas: [app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php#L41)

### Frontend
- Componente de notificaciones: [resources/js/components/NotificationsDropdown.vue](resources/js/components/NotificationsDropdown.vue)
- Carga de notificaciones por rol: [resources/js/components/NotificationsDropdown.vue](resources/js/components/NotificationsDropdown.vue#L149)
- Marcar leidas y eliminar: [resources/js/components/NotificationsDropdown.vue](resources/js/components/NotificationsDropdown.vue#L183)

## 10. Recorrido frontend completo

1. Inicia sesion en Login.
2. El sistema carga layout y menu segun rol.
3. Si es docente, consulta convocatorias y postula.
4. Si es evaluador, entra a su bandeja y califica con rubrica.
5. En todo momento, el sistema acompana con notificaciones y mensajes de estado.

Este recorrido esta pensado para sentirse natural: entrar, entender, ejecutar y confirmar.
La consistencia visual y funcional entre modulos hace que el aprendizaje del sistema sea progresivo y estable.

### Backend
- Rutas del sistema: [routes/web.php](routes/web.php)

### Frontend
- Vistas principales: [resources/js/pages](resources/js/pages)
- Layouts: [resources/js/layouts](resources/js/layouts)

## 11. Texto sugerido para reporte

En el frontend, la plataforma fue construida para guiar a cada perfil durante su recorrido natural dentro de la convocatoria. La experiencia combina navegacion fluida, formularios asistidos, validaciones claras y notificaciones visibles. Esto permite que las personas usuarias comprendan en todo momento donde estan, que accion pueden realizar y que resultado esperar, logrando una operacion mas clara, cercana y confiable.
En suma, el frontend no solo presenta informacion: acompana decisiones, reduce errores y transforma procesos tecnicos en una experiencia util para personas reales.
