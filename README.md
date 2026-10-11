# Nexus Operations Suite (Nexus Ops)
### Plataforma Corporativa de Gestión Operativa, Flujo Audiovisual y Acuerdos con Realtors

**Nexus Operations Suite** es un sistema web integral de gestión de operaciones, producción audiovisual y acuerdos de servicio para agencias de medios y marketing inmobiliario en Miami, Florida. Desarrollado bajo arquitectura **MVC estricta (Modelo - Vista - Controlador)**, esquema de base de datos relacional normalizado en **Tercera Forma Normal (3NF)** y control de acceso basado en roles (**RBAC**) con permisos granulares por especialidad.

---

## Filosofía y Enfoque Operativo

> [!IMPORTANT]
> **Enfoque 100% Operativo y de Actividades:**  
> La plataforma mide **trabajo, actividades, entregables audiovisuales y cobertura territorial**. No maneja transacciones monetarias, pasarelas de pago, facturación ni tarifas; toda la gestión se centra en el ciclo de vida de la producción creativa y los compromisos de servicio formalizados.

- **Metodología CRUD por Niveles:**
  - **Dirección General (Jefe):** Control total de las 4 operaciones (Crear, Modificar, Ver, y Suspender / Eliminar) sobre contratos, tareas, equipo de trabajo, especialidades, roles y clientes.
  - **Equipo Operativo (Empleados):** Permisos para Crear, Modificar y Ver sus tareas asignadas y borradores de acuerdos.
- **Diseño Ejecutivo:** Paleta de colores sobria, elegante y corporativa (tonos pizarra, grafito, marino y salvia), diseñada para supervisión gerencial.

---

## Pila Tecnológica y Arquitectura

| Capa | Tecnología | Descripción |
| :--- | :--- | :--- |
| **Arquitectura** | MVC Puro (PHP 8.2+) | Separación estricta de responsabilidades, rutas centralizadas y autoloading PSR-4. |
| **Base de Datos** | MySQL / MariaDB (XAMPP) | Esquema relacional normalizado en 3NF con integridad referencial (`ON DELETE RESTRICT / CASCADE / SET NULL`) e índices de alto rendimiento. |
| **Frontend** | Bootstrap 5.3 + Vanilla JS | Interfaz responsiva, componentes modales interactivos y renderizado dinámico en tiempo real sin recarga de página. |
| **Seguridad** | RBAC + Hashing Bcrypt | Control de sesión seguro (HttpOnly), prevención de inyección SQL mediante sentencias preparadas (PDO) y protección contra suplantación de identidad. |

---

## Módulos del Sistema

### 1. Tablero de Dirección General (Jefe)
- **Pipeline Operativo en Vivo:** Indicadores clave de flujo (Tareas Nuevas, Canalizadas en Producción, Por Aprobar y Culminadas).
- **Mini-Calendario Interactivo del Mes:** Visualización diaria de rodajes, sesiones con dron, entregas de carruseles y fechas límite del equipo en Miami-Dade.
- **Alertas de Aprobación:** Notificaciones de nuevos acuerdos remitidos por el personal.

### 2. Generador y Repositorio de Contratos Formalizados
- **Generador Dinámico:** Redacción parametrizada vinculada a brokerages inmobiliarios (Brickell, South Beach, Coral Gables, Sunny Isles, etc.) con previsualización en tiempo real en hoja legal membretada.
- **Flujo de Aprobación:** Pases de estado estrictos (`BORRADOR` &rarr; `EN_REVISION` &rarr; `APROBADO` o `RECHAZADO`).
- **Repositorio Oficial de Contratos Finales:** Archivo exclusivo de Dirección con auditoría de resolución, búsqueda por zonas y descarga directa en PDF imprimible.

### 3. Flujo de Trabajo y Producción Audiovisual
- **Asignación y Canalización:** Delegación de tareas por formato (Edición de Video, Carrusel Fotográfico, Rodaje en Locación, Community Management).
- **Panel "Mis Tareas Audiovisuales":** Espacio de trabajo para creativos con carga de enlaces a entregables (Frame.io, Google Drive, Dropbox).
- **Control de Calidad y Retroalimentación:** Módulo de inspección para Dirección con opción de validación inmediata (`APROBADA`) o devolución a ajustes (`CORRECCION`) con observaciones puntuales.

### 4. Gestión de Equipo, Roles y Especialidades
- **Gestión Integral de Personal:** Creación, edición y suspensión de usuarios del sistema con asignación múltiple de cargos operativos (Editor de Video, Diseñador de Carruseles, Camarógrafo/Piloto Dron, Community Manager).
- **Control de Roles y Estado:** Habilitación, modificación y suspensión de roles sin alterar los perfiles del sistema.

### 5. Cartera de Clientes Realtors
- Directorio de inmobiliarias y agentes inmobiliarios de Miami con datos de contacto, zonas de cobertura y seguimiento de acuerdos vigentes.

### 6. Reportes Ejecutivos de Operaciones
- Filtros por períodos: **Semanal (7 días)**, **Quincenal (15 días)** y **Mensual (30 días)**.
- Métricas consolidadas: Contratos formalizados, volumen de tareas por formato, cobertura territorial activa por zona de Miami y efectividad porcentual por especialista audiovisual.
- Exportación directa a formato **Excel (CSV)** e impresión formal en **PDF**.

---

## Instalación y Puesta en Marcha

### Prerrequisitos
- **XAMPP** con Apache y MySQL activos (PHP 8.2 o superior).
- **Composer** instalado globalmente.

### Pasos de Configuración

1. **Ubicación del Proyecto:**
   Ubicar la carpeta en el directorio de publicación de XAMPP:
   ```text
   C:\xampp\htdocs\miami-agency-ops
   ```

2. **Base de Datos (MySQL):**
   - Iniciar el módulo MySQL en el panel de control de XAMPP.
   - El sistema se conecta automáticamente a la base de datos `miami_agency_db` con el usuario `root` sin contraseña.
   - Si se desea importar manualmente el esquema DDL normalizado:
     ```bash
     mysql -u root < database/schema_mysql.sql
     ```

3. **Instalación de Dependencias:**
   Desde la consola en la raíz del proyecto:
   ```bash
   composer dump-autoload
   ```

4. **Ejecución del Servidor:**
   - **Opción A (Recomendada - Servidor Integrado PHP):**
     ```bash
     php -S localhost:8000 -t public
     ```
     Acceder en el navegador a: `http://localhost:8000`
   - **Opción B (Apache de XAMPP):**
     Acceder en el navegador a: `http://localhost/miami-agency-ops/public`

---

## Credenciales de Acceso

| Perfil | Rol | Especialidad / Cargo | Correo Electrónico | Contraseña |
| :--- | :--- | :--- | :--- | :--- |
| **Carlos Mendoza** | `JEFE_ADMIN` | Dirección General & Supervisión | `jefe@miamiagency.com` | `password123` |
| **Valentina Rossi** | `EMPLEADO` | Gestión de Acuerdos & Cuentas | `contratos@miamiagency.com` | `password123` |
| **Mateo Silva** | `EMPLEADO` | Editor de Video | `editor@miamiagency.com` | `password123` |
| **Camila Duarte** | `EMPLEADO` | Diseñador de Carruseles & Camarógrafo | `creativo@miamiagency.com` | `password123` |

> *Nota: La pantalla de login incluye botones de acceso rápido que completan automáticamente las credenciales correspondientes a cada perfil.*

---

## Estructura de Directorios

```text
Nexus-Operations-Suite/
├── app/
│   ├── Controllers/       # Lógica de aplicación y despacho de vistas
│   │   ├── ActivityController.php
│   │   ├── AuthController.php
│   │   ├── ContractController.php
│   │   ├── DashboardController.php
│   │   ├── RealtorController.php
│   │   ├── ReportController.php
│   │   └── TeamController.php
│   ├── Models/            # Modelos con transacciones PDO y reglas 3NF
│   │   ├── ActivityModel.php
│   │   ├── ContractModel.php
│   │   ├── RealtorModel.php
│   │   ├── ReportModel.php
│   │   └── UserModel.php
│   └── Views/             # Plantillas organizadas por módulos y roles
│       ├── activities/
│       ├── auth/
│       ├── contracts/
│       ├── dashboard/
│       ├── realtors/
│       ├── reports/
│       └── team/
├── config/                # Parámetros globales y singleton PDO MySQL
│   ├── Auth.php
│   ├── config.php
│   └── Database.php
├── database/              # DDL relacional idempotente 3NF
│   ├── schema_mysql.sql
│   └── schema_postgres.sql
└── public/                # Punto de entrada público (index.php) y assets
    ├── assets/
    │   ├── css/style.css
    │   └── js/app.js
    └── index.php
```

---

## Licencia

Software propietario y confidencial. Copyright © 2026 Gabriel Sánchez. Todos los derechos reservados.
Desarrollado exclusivamente para operaciones designadas de clientes privados. Queda estrictamente prohibida su copia, redistribución, modificación o comercialización no autorizada. Consulta el archivo [LICENSE](LICENSE) para más detalles.
