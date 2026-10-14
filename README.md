# Nexus Operations Suite (Nexus Ops)
### Plataforma Corporativa de Gestión Operativa, Producción Audiovisual y Acuerdos con Realtors

**Nexus Operations Suite** es una plataforma web integral de supervisión de operaciones, flujo audiovisual y gestión de clientes inmobiliarios (Realtors) en Miami, Florida. Desarrollada bajo arquitectura **MVC estricta (Modelo - Vista - Controlador)**, esquema relacional normalizado en **Tercera Forma Normal (3NF)** con soporte nativo para **MySQL (utf8mb4)** y control de acceso granular basado en roles (**RBAC**) con especialidades técnicas de producción.

---

## Filosofía y Enfoque Operativo

> [!IMPORTANT]
> **Regla Estricta CERO DINERO (Zero Finance Directive):**  
> La plataforma mide exclusivamente **productividad, entregables creativos, cobertura territorial y tiempo de ejecución**. No procesa pagos, tarifas, comisiones ni métricas financieras. Todos los modelos, vistas y esquemas de base de datos están concebidos para la gestión operativa y formalización documental.

- **Diseño Ejecutivo Formal:** Paleta corporativa sobria (pizarra, grafito, marino y salvia) en tipografía *Plus Jakarta Sans*.
- **Control de Acceso Granular:** Separación estricta entre Dirección General (Jefe), Asistente, Estratega de Marketing y Social Manager, complementada con 6 especialidades operativas.
- **Seguridad Corporativa:** Protección contra inyecciones SQL (PDO preparado), hashing de credenciales en Bcrypt (costo 12), protección de sesiones (HttpOnly, SameSite, regeneración de ID) y sanitización de errores (CWE-209).

---

## Roles y Especialidades del Sistema

### 1. Cuatro Roles Corporativos (RBAC)
| Rol | Identificador | Alcance Operativo |
| :--- | :--- | :--- |
| **Jefe (Dirección)** | `JEFE` | Supervisión total, aprobación de entregables, auditoría, reportes ejecutivos y administración de equipo. |
| **Asistente Operativo** | `ASISTENTE` | Coordinación de agendas, gestión de Realtors, soporte operativo y seguimiento de tareas. |
| **Estrategia de Marketing** | `ESTRATEGA_MARKETING` | Planificación de campañas de Email Marketing, cronograma de difusión y estructuración de contenidos. |
| **Social Manager** | `SOCIAL_MANAGER` | Producción de entregables audiovisuales, carruseles, publicaciones y carga de enlaces a medios. |

### 2. Seis Especialidades Técnicas (Relación M:N)
Cada usuario puede tener múltiples especialidades asignadas de forma granular:
1. `EDITOR_VIDEO`: **Editor de Videos** (Reels, videos de propiedades y walkthroughs).
2. `EDITOR_CARRUSELES`: **Editor de Carruseles** (Diseño en Canva/Photoshop, 5 tips de inversión, infografías).
3. `EMAIL_MARKETING`: **Email Marketing** (Campañas y newsletters para compradores calificados).
4. `BLOGGERS`: **Bloggers** (Artículos editoriales de blog y notas de prensa).
5. `AGENDADOR_REUNIONES`: **Agendador de Reuniones** (Coordinación de llamadas y citas con prospectos).
6. `AUTOMATIZADOR`: **Automatizador** (Secuencias automatizadas y flujos de trabajo operativos).

---

## Módulos y Funcionalidades Principales

### 1. Dashboard Ejecutivo & Tablero de Producción FIFO
- **Tablero de Producción:** Ordenamiento temporal secuencial FIFO (las tareas más recientes y prioritarias arriba), con buscador en vivo en tiempo real y filtrado dinámico por estado (`NUEVA`, `EN_PROCESO`, `ENTREGADA_REVISION`, `APROBADA`).
- **Slider Bar (Barra Lateral):** Inicia automáticamente **cerrada/colapsada** (`sidebar-collapsed` y `main-expanded`) en cualquier resolución (laptop, desktop, tablet y móvil) para maximizar el área de trabajo útil.
- **Reloj Ejecutivo en Vivo:** Monitoreo horario continuo sincronizado con la Hora Legal de Venezuela (`America/Caracas`, UTC-4).
- **Indicadores Operativos:** Métricas directas de volumen de tareas, entregables en revisión y actividades culminadas.

### 2. Repositorio de Contratos Finales Concluidos
- **Almacén Exclusivo de PDFs:** Diseñado exclusivamente para almacenar contratos concluidos y firmados legalmente en formato PDF.
- **Fecha de Subida Automatizada:** Registro del día y hora de subida calculado automáticamente por el sistema al momento de la carga.
- **Asociación Directa al Realtor:** Búsqueda, filtrado y visualización protegida del documento PDF en visor integrado sin exponer el sistema de archivos del servidor.

### 3. Directorio de Realtors y Enlaces Operativos
- **Enlaces a Recursos Externos:** Cada Realtor almacena dos URLs fundamentales:
  - **Bóveda de Credenciales (`credentials_url`):** Enlace seguro donde la agencia custodia claves, correos y contraseñas del cliente.
  - **Carpeta de Dropbox (`dropbox_url`):** Enlace directo para que los especialistas creativos descarguen el material audiovisual en bruto.
- **Botón "Ver Proyectos":** Modal interactivo aislado que consulta vía AJAX y despliega únicamente los proyectos y tareas vinculadas al Realtor seleccionado.

### 4. Cronograma de Email Marketing
- **Calendario Extendido:** Vista de pantalla amplia para planificar y calendarizar la subida y programación de emails por cada Realtor.
- **Filtros por Cliente:** Navegación por fechas estipuladas para la base de datos de compradores de cada agente inmobiliario.

### 5. Agenda de Reuniones & Sincronización Google Calendar
- **Google Calendar Sync:** Enlace directo formateado según el estándar oficial de Google para añadir la reunión en un clic y recibir alertas automáticas en el teléfono móvil vía correo electrónico.
- **Descarga de Archivos `.ics`:** Generación nativa de archivos iCalendar estándar RFC 5545 para sincronización con Apple Calendar, Microsoft Outlook y aplicaciones móviles.

### 6. Reportes Ejecutivos (Filtros Estrictos 15 / 30 Días)
- **Periodos Fijos:** Conmutador estricto entre 15 días y 30 días de historial operativo.
- **Métricas Operativas:**
  - Tasa de rendimiento global del equipo (% de cumplimiento sobre tareas asignadas).
  - Productividad individual por colaborador (tareas completadas vs. en curso).
- **Cumplimiento Cero Dinero:** Reporte completamente libre de valores monetarios, enfocado en volumen y calidad.

### 7. Control de Inactividad de Sesión (Idle Session Timeout)
- **Monitoreo en Cliente:** Detección de inactividad basada en eventos de usuario (`mousemove`, `mousedown`, `keydown`, `touchstart`, `scroll`).
- **Modal "¿Sigues ahí?":**
  - Backdrop estático (`data-bs-backdrop="static"` y `data-bs-keyboard="false"`) que exige interacción explícita.
  - Cronómetro regresivo visible en vivo (`60s` &rarr; `0s`) con barra de progreso porcentual.
  - Alerta sonora armónica suave emitida a través de la Web Audio API.
  - Opción **"Continuar en la Sesión"** que emite un heartbeat (`/api/session/keep-alive`), renueva la marca en el servidor y oculta el modal.
  - Cierre automático al llegar a 0s con redirección a `/login?reason=inactivity` y notificación de seguridad.
- **Expiración en Servidor:** `Auth::IDLE_TIMEOUT = 900` segundos (15 minutos). Peticiones AJAX expiradas reciben respuesta HTTP 401 en formato JSON.

### 8. Notificaciones en Tiempo Real & Web Audio API
- **Sondeo en Segundo Plano:** Consulta periódica a `/api/notifications`.
- **Tono Armónico Dual:** Oscilador de audio nativo Web Audio API (Re5 a 587.33 Hz &rarr; La5 a 880.00 Hz) con decaimiento exponencial suave, sin depender de archivos de audio externos.
- **Animación y Marcado:** Animación de campana, badge numérico y marcado individual o global de notificaciones.

### 9. Blindaje de Errores y Seguridad (CWE-209)
- **Plantilla de Error 500 Sanitizada:** Eliminación total de stack traces, rutas físicas (`C:\xampp\...`) y líneas de código ante el cliente.
- **Códigos de Auditoría:** Generación de códigos de incidencia interna (`NEX-XXXXX`) registrados exclusivamente en el servidor.
- **Codificación UTF-8mb4:** Configuración estricta en MySQL (`utf8mb4_unicode_ci`) y cabeceras HTTP `Content-Type: text/html; charset=UTF-8` para garantizar la correcta visualización de acentos y caracteres del español.

---

## Pila Tecnológica

| Capa | Tecnología | Especificación |
| :--- | :--- | :--- |
| **Backend** | PHP 8.2+ | Arquitectura MVC pura, Tipado estricto (`declare(strict_types=1)`), Autoloading PSR-4. |
| **Base de Datos** | MySQL / MariaDB | Normalización 3NF, Cotejamiento `utf8mb4_unicode_ci`, Índices B-Tree en llaves foráneas. |
| **Frontend** | Bootstrap 5.3.3 + Bootstrap Icons | Interfaz responsiva, componentes modales estáticos y toasts. |
| **JavaScript** | Vanilla JS (ES6+) | Web Audio API, Fetch API, Cache-busting dinámico por `filemtime`. |
| **Estándares** | RFC 5545 (iCalendar) / CWE-209 | Sincronización de agendas e integridad de seguridad contra fuga de información. |

---

## Estructura de Directorios

```text
miami-agency-ops/
├── app/
│   ├── Controllers/             # Controladores MVC
│   │   ├── ActivityController.php
│   │   ├── AuthController.php
│   │   ├── ContractController.php
│   │   ├── Controller.php       # Controlador Base (cabeceras UTF-8, anti-caché, render)
│   │   ├── DashboardController.php
│   │   ├── EmailMarketingController.php
│   │   ├── MeetingController.php
│   │   ├── ProfileController.php
│   │   ├── RealtorController.php
│   │   ├── ReportController.php
│   │   └── TeamController.php
│   ├── Models/                  # Modelos con transacciones PDO seguras
│   │   ├── ActivityModel.php
│   │   ├── ContractModel.php
│   │   ├── EmailMarketingModel.php
│   │   ├── MeetingModel.php
│   │   ├── Model.php            # Modelo Base
│   │   ├── RealtorModel.php
│   │   ├── ReportModel.php
│   │   └── UserModel.php
│   ├── Services/                # Lógica de soporte y notificaciones
│   │   └── NotificationService.php
│   └── Views/                   # Vistas organizadas por módulos
│       ├── activities/          # Tablero de producción FIFO y Mis Tareas
│       ├── auth/                # Login corporativo con avisos de inactividad
│       ├── contracts/           # Repositorio de contratos finales concluidos (PDF)
│       ├── dashboard/           # Paneles de Jefe y Colaborador con mini-calendario
│       ├── email_marketing/     # Cronograma y programación de emails por Realtor
│       ├── errors/              # Vistas sanitizadas 404 y 500 (CWE-209)
│       ├── layouts/             # Plantilla principal (main.php con modal de inactividad)
│       ├── meetings/            # Agenda sincronizada con Google Calendar
│       ├── profile/             # Edición de perfil de usuario
│       ├── realtors/            # Directorio de Realtors y modal "Ver Proyectos"
│       ├── reports/             # Reportes de productividad (15 / 30 días)
│       └── team/                # Gestión de personal, roles y especialidades
├── config/                      # Configuración global y servicios
│   ├── Auth.php                 # Autenticación, RBAC y control de inactividad
│   ├── config.php               # Rutas, zona horaria y codificación UTF-8
│   └── Database.php             # Singleton PDO MySQL con soporte utf8mb4
├── database/                    # Esquemas y migraciones
│   ├── fix_utf8_charset.php     # Script de migración y saneamiento a utf8mb4
│   ├── migrate_v2.sql           # Migración a 4 roles, 6 especialidades y contratos finales
│   ├── schema_mysql.sql         # Esquema oficial MySQL en Tercera Forma Normal (3NF)
│   └── schema_postgres.sql      # Esquema alternativo PostgreSQL
├── public/                      # Punto de entrada público
│   ├── assets/
│   │   ├── css/style.css        # Hoja de estilos con variables ejecutivas
│   │   └── js/app.js            # JavaScript Vanilla (Audio API, Idle Monitor, Polling)
│   ├── index.php                # Front Controller y enrutador central
│   └── .htaccess                # Reescribidor Apache hacia index.php
├── storage/                     # Almacenamiento privado de la agencia
│   └── contracts/               # Contratos finales concluidos en PDF
├── tests/                       # Suites de pruebas automatizadas
│   ├── BlackBoxTest.php         # Suite de Caja Negra (64 pruebas HTTP / Funcionales)
│   └── WhiteBoxTest.php         # Suite de Caja Blanca (65 pruebas Lógicas / Estructurales)
├── .gitignore
├── composer.json
├── LICENSE                      # Licencia propietaria y confidencial
└── README.md                    # Documentación técnica oficial
```

---

## Credenciales de Acceso de Prueba

La pantalla de inicio de sesión dispone de botones de acceso rápido configurados con los roles oficiales:

| Nombre | Rol | Especialidad Asignada | Correo Corporativo | Contraseña |
| :--- | :--- | :--- | :--- | :--- |
| **Carlos Mendoza** | `JEFE` | Dirección General (Acceso Total) | `jefe@miamiagency.com` | `password123` |
| **Valentina Rossi** | `ASISTENTE` | Agendador de Reuniones | `asistente@miamiagency.com` | `password123` |
| **Sofía Herrera** | `ESTRATEGA_MARKETING` | Email Marketing, Bloggers, Automatizador | `estrategia@miamiagency.com` | `password123` |
| **Mateo Silva** | `SOCIAL_MANAGER` | Editor de Videos, Editor de Carruseles | `social@miamiagency.com` | `password123` |

---

## Suites de Pruebas Automatizadas

La plataforma cuenta con **129 pruebas automatizadas** que cubren el 100% de los requerimientos funcionales, de seguridad y estructurales:

### 1. Pruebas de Caja Blanca (`White-Box Testing`)
Evalúa la lógica interna, algoritmos, modelos, restricciones 3NF, criptografía y control de inactividad:
```bash
php tests/WhiteBoxTest.php
```
*Cobertura: 65 pruebas aprobadas (100% OK).*

### 2. Pruebas de Caja Negra (`Black-Box Testing`)
Evalúa el comportamiento externo, puntos de entrada HTTP, seguridad de cabeceras, sesiones, prevención de escalada RBAC, APIs AJAX, codificación UTF-8 y sanitización de errores:
```bash
php tests/BlackBoxTest.php
```
*Cobertura: 64 pruebas aprobadas (100% OK).*

---

## Instalación y Puesta en Marcha

### Prerrequisitos
- **XAMPP** con módulos de Apache y MySQL activos (PHP 8.2 o superior).
- **Composer** para el autoloading PSR-4.

### Pasos de Configuración

1. **Ubicación del Proyecto en XAMPP:**
   ```text
   C:\xampp\htdocs\miami-agency-ops
   ```

2. **Base de Datos (MySQL):**
   - Iniciar el servicio de MySQL en XAMPP.
   - El sistema inicializa y puebla automáticamente la base de datos `miami_agency_db` al iniciar la aplicación.
   - Para reimportar el esquema manualmente:
     ```bash
     mysql -u root < database/schema_mysql.sql
     ```
   - Para verificar o reparar la codificación UTF-8 en cualquier momento:
     ```bash
     php database/fix_utf8_charset.php
     ```

3. **Autoloading de Clases:**
   ```bash
   composer dump-autoload
   ```

4. **Ejecución de la Aplicación:**
   - **Vía Servidor Integrado PHP (Recomendado):**
     ```bash
     php -S 127.0.0.1:8000 -t public
     ```
     Abrir en el navegador: [http://127.0.0.1:8000](http://127.0.0.1:8000)
   - **Vía Apache de XAMPP:**
     Abrir en el navegador: `http://localhost/miami-agency-ops/public`

---

## Licencia

Software propietario y confidencial. Copyright © 2026 Gabriel Sánchez. Todos los derechos reservados.  
Desarrollado exclusivamente para la gestión operativa designada. Queda prohibida su reproducción, distribución o modificación no autorizada. Consulta el archivo [LICENSE](LICENSE) para más detalles.
