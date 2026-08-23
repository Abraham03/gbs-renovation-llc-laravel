# GBS Renovations LLC — API

API REST que da servicio al sitio de [GBS Renovations LLC](https://gbsrenovationsllc.services).
Gestiona el catálogo de proyectos de remodelación (con imágenes y videos), las categorías,
el formulario de contacto y la autenticación del panel administrativo.

- **Producción:** https://api.gbsrenovationsllc.services
- **Frontend:** Angular, en https://gbsrenovationsllc.services

## Stack

| | |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Autenticación | JWT (`tymon/jwt-auth`) |
| Base de datos | MySQL en producción, SQLite en local |
| Imágenes | `intervention/image` — reescala a 1200px y convierte a WebP |
| Video | `pbmedia/laravel-ffmpeg` — compresión H.264 en cola |
| Assets | Vite 8 + Tailwind 4 |

## Instalación local

```bash
composer setup      # instala deps, copia .env, genera APP_KEY, migra y compila assets
php artisan jwt:secret
composer dev        # levanta servidor + worker de colas + logs + vite
```

`composer dev` deja la API en http://localhost:8000. El frontend de Angular corre en
http://localhost:4200, que ya está permitido en [config/cors.php](config/cors.php).

Alternativa con Docker: `./vendor/bin/sail up` usando [compose.yaml](compose.yaml)
(MySQL, Redis, RabbitMQ y Mailpit).

## Variables de entorno

Además de las de [.env.example](.env.example), en producción hacen falta:

| Variable | Para qué |
|---|---|
| `JWT_SECRET` | Firma de los tokens. Generar con `php artisan jwt:secret` |
| `FRONTEND_URL` | Origen permitido por CORS (el dominio del sitio Angular) |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexión a MySQL |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | SMTP del formulario de contacto |

El `.env` de producción vive **solo en el servidor** y está excluido del despliegue.

## Endpoints

Definidos en [routes/api.php](routes/api.php). Todos bajo el prefijo `/api`.

### Públicos

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/api/auth/login` | Devuelve el token JWT |
| `POST` | `/api/auth/refresh` | Renueva el token |
| `POST` | `/api/contact` | Envía el formulario de contacto por correo |
| `GET` | `/api/projects` | Lista de proyectos |
| `GET` | `/api/projects/{project}` | Detalle de un proyecto |
| `GET` | `/api/categories` | Lista de categorías |

### Protegidos (`Authorization: Bearer <token>`)

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/api/auth/logout` | Invalida el token |
| `POST` | `/api/auth/me` | Datos del usuario autenticado |
| `POST` | `/api/projects` | Crea un proyecto con thumbnail y galería |
| `POST` | `/api/projects/{project}` | Actualiza un proyecto |
| `DELETE` | `/api/projects/{project}` | Elimina un proyecto y sus archivos |
| `DELETE` | `/api/project-media/{id}` | Elimina una foto o video de la galería |
| `POST` | `/api/categories` | Crea una categoría |
| `PUT` | `/api/categories/{category}` | Actualiza una categoría |
| `DELETE` | `/api/categories/{category}` | Elimina una categoría |

> Las actualizaciones de proyecto usan `POST` en vez de `PUT` porque PHP no parsea
> `multipart/form-data` en peticiones `PUT`.

`GET /up` es el healthcheck de Laravel y lo usa el pipeline de despliegue.

## Almacenamiento de archivos

Hay dos conjuntos de media y conviene no confundirlos:

- **`public/images/` y `public/videos/`** — catálogo histórico, versionado en git y
  cargado por `OldProjectsSeeder` desde [database/data/projects.json](database/data/projects.json).
  Es estático; ningún código escribe ahí.
- **`storage/app/public/projects/`** — todo lo que se sube desde el panel
  (`thumbnails/`, `gallery/`, `videos/`). Se sirve por el symlink `public/storage`,
  así que en el navegador aparece como `/storage/projects/...`.
  **No está versionado y el despliegue nunca lo toca.**

Si el symlink falta: `php artisan storage:link`.

## Despliegue

Automático vía [.gitlab-ci.yml](.gitlab-ci.yml). Cada push a `main` dispara:

1. **`build:vendor`** — `composer install --no-dev` en el runner.
2. **`build:assets`** — `npm run build` (Vite).
3. **`deploy:production`** — `rsync` al servidor + limpieza y recreación de cachés,
   terminando con un healthcheck contra `/up`.
4. **`migrate:production`** — **manual**. Aparece con un botón ▶ en el pipeline y solo
   corre si le das clic. Ejecuta `php artisan migrate --force`, que aplica únicamente
   las migraciones pendientes; si no hay ninguna, responde *"Nothing to migrate"*.
   Es manual a propósito, porque una migración destructiva no tiene rollback automático.

[.rsyncignore](.rsyncignore) define qué se excluye del despliegue. Lo importante:
`.env`, `storage/` y el symlink `public/storage` quedan intactos en el servidor,
así que ni la configuración ni los archivos subidos por usuarios se pierden.

Variables requeridas en GitLab (Settings → CI/CD → Variables): `SSH_PRIVATE_KEY`
(clave privada en base64, una sola línea), `SSH_HOST`, `SSH_USER` y `SSH_PORT`.

### Procesamiento de video

La compresión de video se despacha a una cola (`QUEUE_CONNECTION=database`), así que
el servidor necesita un worker. En hosting compartido se resuelve con un cron:

```
cd /ruta/al/proyecto && php artisan queue:work --stop-when-empty --tries=3 --max-time=280
```

Requiere los binarios `ffmpeg` y `ffprobe` instalados en el host.
