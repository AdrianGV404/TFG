# ProMaTi — Plataforma de Gestión de Proyectos y Tareas

> Trabajo de Fin de Grado (TFG) · Adrián García  
> Aplicación web multi-tenant para la gestión de proyectos, tareas y registro de tiempo, desarrollada con **Laravel 10**, **Livewire 3** y **Bootstrap 5**.

---

## Índice

1. [Descripción general](#descripción-general)
2. [Tecnologías y dependencias](#tecnologías-y-dependencias)
3. [Arquitectura](#arquitectura)
4. [Funcionalidades principales](#funcionalidades-principales)
5. [Modelo de datos](#modelo-de-datos)
6. [Sistema de permisos](#sistema-de-permisos)
7. [Infraestructura con Docker](#infraestructura-con-docker)
8. [Instalación y puesta en marcha](#instalación-y-puesta-en-marcha)
9. [Variables de entorno](#variables-de-entorno)
10. [Base de datos y migraciones](#base-de-datos-y-migraciones)
11. [API REST](#api-rest)
12. [Tests](#tests)
13. [Estructura del proyecto](#estructura-del-proyecto)

---

## Descripción general

**ProMaTi** es una plataforma SaaS de gestión de proyectos y tareas con aislamiento por tenant. Permite a empresas y usuarios individuales organizar su trabajo mediante proyectos, tareas con prioridad configurable, etiquetas, registro de tiempo y notificaciones en tiempo real.

Cada tenant (empresa o usuario personal) tiene su propio espacio de datos completamente aislado: proyectos, tareas, etiquetas y usuarios no se comparten entre tenants.

---

## Tecnologías y dependencias

### Backend
| Tecnología | Versión |
|---|---|
| PHP | ^8.1 |
| Laravel Framework | ^10.10 |
| Livewire | ^3.7 |
| Laravel Sanctum | ^3.3 |
| Doctrine DBAL | ^3.10 |
| Guzzle HTTP | ^7.2 |

### Frontend
| Tecnología | Versión |
|---|---|
| Vite | ^5.0.0 |
| Axios | ^1.6.4 |
| Bootstrap | 5.3.3 (CDN) |
| Font Awesome | 6.4.2 (CDN) |
| Chart.js | 4.4.0 (CDN) |
| Alpine.js | integrado con Livewire 3 |

### Testing
| Herramienta | Versión |
|---|---|
| Pest PHP | ^2.36 |
| pest-plugin-laravel | ^2.4 |
| Mockery | ^1.4.4 |

### Base de datos
- **MySQL** (producción) — configurado con zona horaria `+01:00` (Madrid)
- **SQLite** (tests) — base de datos en memoria con `RefreshDatabase`

---

## Arquitectura

El proyecto sigue una arquitectura **MVC extendida** con los siguientes patrones:

```
┌─────────────────────────────────────────────────────┐
│                    FRONTEND (Blade + Livewire)        │
│  Componentes Livewire ←→ Vistas Blade ←→ Alpine.js  │
└───────────────────────────┬─────────────────────────┘
                            │ HTTP / Wire
┌───────────────────────────▼─────────────────────────┐
│                CAPA DE APLICACIÓN                    │
│  Controllers  │  Livewire Components  │  Policies   │
│  Form Requests│  Events & Listeners   │  Observers  │
└───────────────────────────┬─────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────┐
│                   CAPA DE SERVICIO                   │
│          TaskService  │  NotificationService         │
│          JsonPlaceholderService                      │
└───────────────────────────┬─────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────┐
│                CAPA DE DATOS (Eloquent)              │
│  Models: User, Tenant, Project, Task, Label,         │
│  TaskTimeEntry, HistoricoHorasDia, Notification,     │
│  UserPermission, UserSettings                        │
└─────────────────────────────────────────────────────┘
```

### Patrones utilizados

- **Multi-tenancy** — aislamiento de datos por `tenant_id` en proyectos, tareas y etiquetas
- **Service Layer** — `TaskService` y `NotificationService` desacoplan la lógica de negocio de los controladores
- **Observer** — `TaskObserver` reacciona a cambios de estado de tarea (dispara eventos, notificaciones)
- **Events & Listeners** — `TaskCompleted` → `SendTaskCompletedEmail` + `LogTaskCompleted` + `DispatchProcessTaskJob`
- **Queue Jobs** — `ProcessTaskJob` ejecuta tareas pesadas de forma asíncrona
- **Policy** — `ProjectPolicy` y `TaskPolicy` centralizan las reglas de autorización
- **Traits Livewire** — `WithSearchAndPagination`, `HasInlineEditing`, `ScopedByProject`, `RegistraHistoricoHoras`, `Notifies`, `FormValidationRules`
- **Soft Deletes + Prunable** — borrado lógico con limpieza automática configurable (30 días para tareas, 90 para proyectos)

---

## Funcionalidades principales

### Gestión de proyectos
- Crear, editar y eliminar proyectos (soft delete con restauración)
- Estados: `active` / `archived` / `deleted`
- Asignación de usuarios al equipo del proyecto
- Progreso visual mediante barra de tareas (hechas / en curso / pendientes)
- Filtrado por nombre, ID y etiquetas

### Gestión de tareas
- Crear, editar y eliminar tareas con soft delete
- **Prioridad numérica** (0–10) con etiquetas: Muy Alta, Alta, Media, Baja, Muy Baja
- **Estados**: `pending` | `in_progress` | `on_hold` | `testing` | `done`
- Fecha límite (`due_date`) con aviso visual de vencimiento
- Asignación de etiquetas (muchos a muchos)
- Vista de detalle individual con toda la información consolidada

### Registro de tiempo
- Cronómetro integrado: iniciar / detener grabación por tarea y usuario
- Entrada de tiempo manual (horas + minutos)
- Historial de entradas por tarea con desglose por usuario
- Acumulación automática en `historico_horas_dia` para el dashboard
- Cambio automático de estado: `in_progress` al iniciar, `on_hold` al detener

### Etiquetas
- Gestión de etiquetas por tenant (crear, editar, eliminar)
- Asignación múltiple a tareas
- Filtrado de proyectos y tareas por etiqueta

### Dashboard y analítica
- Gráfico circular (Chart.js) de distribución de estados de tareas
- **Heatmap anual** de actividad (horas imputadas por día)
- Lista de tareas con tiempo registrado ordenada por horas
- Estadísticas por etiqueta: horas totales, número de tareas y desglose de estados
- Últimas 15 tareas modificadas
- Filtros por usuario y por proyecto (con roles: admin ve todos, empleado solo ve los suyos)

### Calendario
- Vista mensual de tareas por fecha límite
- Navegación por meses
- Chips de tarea con color por estado y barra lateral por prioridad
- Máximo 3 tareas visibles por día con indicador de desbordamiento

### Notificaciones
- Centro de notificaciones en tiempo real (polling cada 10 s)
- Tipos: asignación a proyecto/tarea, cambio de estado, vencimiento próximo
- Marcar como leída individualmente o todas a la vez
- Respeta las preferencias de notificación del usuario

### Gestión de usuarios (Admin)
- Crear usuarios corporativos con contraseña
- Sistema de **permisos** por usuario
- Edición inline: nombre, email, contraseña, permisos
- Eliminar usuarios del tenant

### Perfil de usuario
- Actualizar nombre, email y foto de perfil
- Cambio de contraseña con validación de contraseña actual
- Tema claro / oscuro (persistido en base de datos)
- Preferencias de notificaciones por tipo y canal
- Configuración de retención de datos y etiquetas de empleados

### Autenticación y registro
- Login con Livewire (email + contraseña)
- Registro en modo **Personal** (1 usuario, tenant propio)
- Registro en modo **Empresa** (admin + N usuarios, tenant compartido)
- Logout con invalidación de sesión

---

## Modelo de datos

```
tenants
  └── users (tenant_id)
        └── user_permissions (user_id, 1:1)
        └── user_settings (user_id, 1:1)

tenants
  └── projects (tenant_id, created_by → users)
        ├── project_user (project_id, user_id) [pivote]
        └── tasks (project_id, created_by → users, tenant_id)
              ├── task_time_entries (task_id, user_id)
              └── label_task (task_id, label_id) [pivote]

tenants
  └── labels (tenant_id)
        └── label_task (label_id, task_id) [pivote]

tenants + projects
  └── historico_horas_dia (tenant_id, project_id, dia) [UNIQUE]

users
  └── notifications (user_id)
```

### Prioridades de tarea

| Valor | Etiqueta | Clase CSS |
|---|---|---|
| 0 | Muy Alta | `very_high` |
| 1–3 | Alta | `high` |
| 4–6 | Media | `mid` |
| 7–8 | Baja | `low` |
| 9–10 | Muy Baja | `very_low` |

---

## Sistema de permisos

El sistema implementa un **control de acceso basado en roles y permisos granulares**.

### Roles
- `admin` — acceso total, bypasa todas las políticas
- `user` — acceso según permisos asignados en `user_permissions`

### Permisos granulares (`user_permissions`)

| Permiso | Descripción |
|---|---|
| `can_create_projects` | Crear nuevos proyectos |
| `can_create_project_tasks_by_others` | Crear tareas en proyectos de su tenant |
| `can_create_any_task` | Crear cualquier tarea |
| `can_edit_project_tasks_by_others` | Editar tareas ajenas en su proyecto |
| `can_edit_any_task` | Editar cualquier tarea del tenant |
| `can_delete_project_tasks_by_others` | Eliminar tareas ajenas en su proyecto |
| `can_delete_any_task` | Eliminar cualquier tarea del tenant |
| `can_reassign_users` | Reasignar usuarios |

### Regla de persistencia jerárquica
Cuando `can_any` y `can_project_by_others` conviven, prevalece `can_any` si está activo y el otro no. En el resto de casos se evalúa el de menor alcance con contexto de tenant y creador.

---

## Infraestructura con Docker

El proyecto incluye un `docker-compose.yml` que levanta todos los servicios de infraestructura necesarios para el entorno de desarrollo: base de datos MySQL, interfaz web phpMyAdmin, caché/cola con Redis e interfaz Redis Insight.

### Servicios

| Servicio | Imagen | Puerto local | Descripción |
|---|---|---|---|
| `mysql` | `mysql:8.1` | `3306` | Base de datos principal |
| `phpmyadmin` | `phpmyadmin/phpmyadmin` | `8080` | Interfaz web de administración de MySQL |
| `redis` | `redis:7.2-alpine` | `6379` | Caché y sistema de colas |
| `redisinsight` | `redis/redisinsight:latest` | `5540` | Interfaz web de administración de Redis |

### Levantar los servicios

```bash
docker compose up -d
```

Para detenerlos:

```bash
docker compose down
```

### Credenciales por defecto

**MySQL**

| Campo | Valor |
|---|---|
| Host | `127.0.0.1` |
| Puerto | `3306` |
| Base de datos | `mydatabase` |
| Usuario | `root` |
| Contraseña | `rootpassword` |

**phpMyAdmin** — accesible en [http://localhost:8080](http://localhost:8080)

| Campo | Valor |
|---|---|
| Usuario | `root` |
| Contraseña | `rootpassword` |

**RedisInsight** — accesible en [http://localhost:5540](http://localhost:5540)

> RedisInsight se conecta automáticamente al contenedor `redis` a través de la red interna `redisnet` (host `redis`, puerto `6379`).

### Volúmenes y persistencia

| Servicio | Volumen local | Destino en contenedor |
|---|---|---|
| `mysql` | `./mysql_data` | `/var/lib/mysql` |
| `redis` | `./data` | `/data` |
| `redis` | `./redis.conf` | `/usr/local/etc/redis/redis.conf` |

> El archivo `redis.conf` debe existir en la raíz del proyecto antes de levantar los servicios. Redis arranca con esa configuración personalizada mediante el comando `redis-server /usr/local/etc/redis/redis.conf`.

### Redes

Los servicios `redis` y `redisinsight` comparten la red bridge `redisnet`, de forma que RedisInsight puede conectar con Redis usando el nombre de servicio como hostname (`redis:6379`). El resto de servicios (MySQL, phpMyAdmin) utilizan la red por defecto de Docker Compose.

### Variables de entorno para usar con Docker

Una vez los contenedores estén en marcha, configura el `.env` de Laravel con los siguientes valores:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mydatabase
DB_USERNAME=root
DB_PASSWORD=rootpassword

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Usar Redis como driver de cola y caché
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

> Si prefieres seguir usando colas síncronas en local, mantén `QUEUE_CONNECTION=sync` y usa Redis únicamente para caché y sesiones.

---

## Instalación y puesta en marcha

### Requisitos previos
- PHP 8.1+
- Composer
- Node.js 18+ y npm
- Docker y Docker Compose (recomendado para la infraestructura)
- MySQL 8+ (o SQLite para desarrollo sin Docker)

### Pasos

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd promati

# 2. Levantar la infraestructura con Docker (MySQL, Redis, phpMyAdmin, RedisInsight)
docker compose up -d

# 3. Instalar dependencias PHP
composer install

# 4. Instalar dependencias JS
npm install

# 5. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 6. Ajustar las variables de base de datos y Redis en .env
#    (ver sección "Infraestructura con Docker" → "Variables de entorno para usar con Docker")

# 7. Ejecutar migraciones y seeders
php artisan migrate --seed

# 8. Enlace simbólico para almacenamiento público
php artisan storage:link

# 9. Compilar assets
npm run build
# o en desarrollo:
npm run dev

# 10. Lanzar el servidor de desarrollo (incluye cola y Vite en paralelo)
composer run dev
```

### Credenciales por defecto (seeder)

| Campo | Valor |
|---|---|
| Email | `admin@admin.admin` |
| Contraseña | `admin` |
| Rol | `admin` |
| Tenant | Empresa 1 |

Los usuarios normales creados por el seeder tienen contraseña `1234`.

---

## Variables de entorno

```env
APP_NAME=ProMaTi
APP_ENV=local
APP_KEY=           # generada con php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mydatabase
DB_USERNAME=root
DB_PASSWORD=rootpassword

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Cola (usar 'redis' en producción o con Docker, 'sync' en desarrollo sin Docker)
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file

# Correo
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

### Limpieza automática (Prunable)

Configurable en `config/prune.php`:

```php
'days_to_keep_deleted' => [
    \App\Models\Task::class    => 30,  // tareas borradas → 30 días
    \App\Models\Project::class => 90,  // proyectos borrados → 90 días
],
```

Activar con el comando programado diariamente:
```bash
php artisan model:prune
```

---

## API REST

La API está disponible bajo el prefijo `/api` sin autenticación por defecto (configurable con Sanctum).

### Endpoints

| Método | Ruta | Descripción |
|---|---|---|
| `GET` | `/api/tasks` | Listar todas las tareas (filtrable con `?project_id=`) |
| `POST` | `/api/tasks` | Crear una tarea |
| `GET` | `/api/tasks/{id}` | Obtener detalle de una tarea |
| `PUT/PATCH` | `/api/tasks/{id}` | Actualizar una tarea |
| `DELETE` | `/api/tasks/{id}` | Eliminar una tarea (soft delete) |

### Cuerpo de creación (`POST /api/tasks`)

```json
{
  "project_id": 1,
  "title": "Nombre de la tarea",
  "description": "Descripción opcional",
  "status": "pending",
  "priority": "mid"
}
```

**Valores válidos:**
- `status`: `pending` | `in_progress` | `on_hold` | `testing` | `done`
- `priority`: `very_high` | `high` | `mid` | `low` | `very_low`

### Respuesta (estructura `data`)

```json
{
  "data": {
    "id": 1,
    "project_id": 1,
    "title": "Nombre de la tarea",
    "description": null,
    "status": "pending",
    "priority": 5,
    "due_date": null,
    "created_at": "2026-05-31T10:00:00.000000Z",
    "updated_at": "2026-05-31T10:00:00.000000Z"
  }
}
```

**Límite de peticiones:** 60 req/min por usuario o IP (configurable en `RouteServiceProvider`).

---

## Tests

El proyecto incluye una suite completa de tests con **Pest PHP**.

### Ejecutar tests

```bash
# Todos los tests
php artisan test

# Con cobertura
php artisan test --coverage

# Un fichero concreto
php artisan test tests/Feature/ProjectAndTaskTest.php
```

### Suites de tests

| Fichero | Tipo | Qué prueba |
|---|---|---|
| `TenantAndUserTest` | Feature | Creación de tenants, usuarios, autenticación, roles |
| `ProjectAndTaskTest` | Feature | CRUD de proyectos y tareas, policies, soft delete |
| `LabelAndTimeTrackingTest` | Feature | Etiquetas, relaciones muchos-a-muchos, registro de tiempo |
| `ApiTaskTest` | Feature | Endpoints REST: listado, filtrado, validación, CRUD |
| `LivewireTest` | Feature | Componentes Livewire: login, proyectos, tareas, permisos |
| `NotificationAndObserverTest` | Feature | Observer de tareas, NotificationService, rendimiento N+1 |
| `SecurityTest` | Feature | Aislamiento por tenant, rutas protegidas, permisos |
| `ModelAndServiceTest` | Unit | Prioridades, `isAdmin`, `TaskService`, `UserPermission` |
| `TaskModelTest` | Unit | Método `isDone()` del modelo Task |
| `ProcessTaskJobTest` | Unit | Job de procesamiento asíncrono de tareas |
| `JsonPlaceholderServiceTest` | Unit | Servicio de API externa con HTTP fake |

### Configuración de tests

Los tests de Feature usan `RefreshDatabase` con SQLite en memoria. La base de datos de test se configura en `phpunit.xml`:

```xml
<env name="APP_ENV" value="testing"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
<env name="MAIL_MAILER" value="array"/>
```

---

## Estructura del proyecto

```
promati/
├── app/
│   ├── Console/            # Kernel de comandos Artisan
│   ├── Events/             # TaskCompleted, TaskCreated
│   ├── Exceptions/         # Handler de excepciones
│   ├── Http/
│   │   ├── Controllers/    # Controladores web y API
│   │   ├── Middleware/     # Auth, IsAdmin, CORS, CSRF…
│   │   ├── Requests/       # Form Requests con validación
│   │   └── Resources/      # API Resources (TaskResource)
│   ├── Jobs/               # ProcessTaskJob
│   ├── Listeners/          # SendTaskCompletedEmail, LogTaskCompleted, DispatchProcessTaskJob
│   ├── Livewire/           # Componentes Livewire
│   │   ├── Tasks/          # TaskDetail
│   │   └── Traits/         # WithSearchAndPagination, HasInlineEditing, RegistraHistoricoHoras…
│   ├── Mail/               # TaskCompletedMail
│   ├── Models/             # User, Tenant, Project, Task, Label, TaskTimeEntry…
│   ├── Observers/          # TaskObserver
│   ├── Policies/           # ProjectPolicy, TaskPolicy
│   ├── Providers/          # AppServiceProvider, EventServiceProvider, AuthServiceProvider…
│   └── Services/           # TaskService, NotificationService, JsonPlaceholderService
├── config/
│   └── prune.php           # Días de retención por modelo
├── database/
│   ├── factories/          # ProjectFactory, TaskFactory, UserFactory
│   ├── migrations/         # Todas las migraciones ordenadas por fecha
│   └── seeders/            # DatabaseSeeder, UserSeeder, ProjectSeeder, TaskSeeder
├── docker-compose.yml      # Infraestructura de desarrollo (MySQL, Redis, phpMyAdmin, RedisInsight)
├── redis.conf              # Configuración personalizada de Redis
├── public/
│   └── css/app.css         # Estilos principales
├── resources/
│   ├── js/                 # app.js + bootstrap.js (Axios)
│   └── views/
│       ├── emails/         # Plantillas de correo
│       ├── layouts/        # app.blade.php (layout principal con sidebar)
│       └── livewire/       # Vistas de todos los componentes Livewire
├── routes/
│   ├── web.php             # Rutas web (públicas + autenticadas + admin)
│   └── api.php             # Rutas API REST
└── tests/
    ├── Feature/            # Tests de integración y funcionales
    └── Unit/               # Tests unitarios
```