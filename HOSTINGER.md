# Despliegue en Hostinger

Este proyecto está pensado para **Hostinger Premium** (u otro plan con **PHP** y **MySQL**). No requiere Node.js en el servidor: el sitio usa Blade y CSS estático; Vite solo hace falta en tu PC si compilás assets (`npm run build`).

## Requisitos del plan

| Requisito | Detalle |
|-----------|---------|
| PHP | **8.3** o superior (selector en hPanel → PHP Configuration) |
| Base de datos | **MySQL** (recomendado en shared hosting). PostgreSQL del `.env.example` es para desarrollo local si lo usás en Laragon |
| Document root | Debe apuntar a la carpeta **`public`** del proyecto |
| Extensiones PHP | `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo` |

## Estructura en el servidor

Opción habitual en Hostinger:

1. Subí el repositorio (Git en hPanel o FTP) a una carpeta fuera de `public_html`, por ejemplo `~/domains/tudominio.com/laravel/`.
2. Configurá el dominio para que el **document root** sea `.../laravel/public`.
3. Si el panel solo permite `public_html`, mové el contenido de `public/` a `public_html/` y ajustá `index.php` para cargar el autoload desde la ruta correcta (Hostinger suele ofrecer guía “Laravel en Hostinger”).

## Variables de entorno (`.env` en producción)

```env
APP_NAME="Jaguares E-Sports"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_clave

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

CLUB_CORREO_DOMINIO=uamv.edu.ni
# CLUB_CORREO, CLUB_INSTAGRAM, etc.
CLUB_JUSTIFICADOS_DESCUENTAN=false

ADMIN_EMAIL=...
ADMIN_PASSWORD=...

# Correo de confirmación de cuentas (cuenta creada en hPanel → Correos)
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=no-responder@tudominio.com
MAIL_PASSWORD=clave_del_buzon
MAIL_FROM_ADDRESS=no-responder@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

Sin SMTP configurado nadie puede confirmar su correo y, por lo tanto, nadie entra al portal. Probá el envío creando una cuenta de prueba justo después del despliegue. Si el puerto 465 falla, usá `MAIL_PORT=587` con `MAIL_SCHEME=smtp`.

Generá la clave en el servidor:

```bash
php artisan key:generate
```

## Comandos tras cada despliegue

Desde SSH (si está habilitado) o la terminal de Hostinger:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force   # solo la primera vez o si necesitás semestre/juegos base
php artisan roster:sincronizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Permisos de escritura en `storage/` y `bootstrap/cache/` (755 o 775 según indique Hostinger).

## Cron (opcional)

Si más adelante usás tareas programadas:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Hoy el club no depende del scheduler para asistencia.

## Seguridad en producción

- `APP_DEBUG=false` siempre.
- No subas `.env` al repositorio.
- Usá HTTPS (certificado gratuito en Hostinger).
- Cuentas `ADMIN_*` y segundo SUPERADMIN con graduaciones distintas (gobernanza del club).
- El portal guarda CIF cifrado; la base en Hostinger debe tener backup activo en hPanel.

## Qué va en cada URL

| Ruta | Función |
|------|---------|
| `/` | Sitio público |
| `/admin` | Contenido (grupos, roster manual, torneos, **semestres**) |
| `/portal` | Miembros: perfil, asistencia, capitanes, convalidación |
| `/admin/convalidaciones` | Revisión y export CSV para Registro |

## Problemas frecuentes

**Error 500 tras subir:** revisá `storage/logs/laravel.log`, permisos de `storage`, y que `APP_KEY` exista.

**CSS no carga:** `APP_URL` debe coincidir con el dominio; los assets están en `public/css/` (no hace falta `npm` en el servidor si ya están en el repo).

**Sesión expira rápido:** `SESSION_LIFETIME` en minutos; con `SESSION_DRIVER=database` ejecutá migraciones.

**Roster público desactualizado:** pedí a los miembros que guarden perfil en `/portal` o ejecutá `php artisan roster:sincronizar`.

## Flujo de trabajo: local + Hostinger

Es el esquema recomendado del club: **desarrollás y probás en tu PC**, **publicás en Hostinger** cuando el cambio esté listo.

| Dónde | Para qué | Base de datos | Guía |
|-------|-----------|---------------|------|
| **Local (Laragon)** | Código nuevo, `php artisan test`, probar `/portal` y `/admin` sin tocar datos reales | SQLite o MySQL local | [INSTALAR.md](INSTALAR.md) |
| **Hostinger** | Sitio que ven miembros y capitanes | MySQL del hosting | Esta guía |

### En tu PC (cada vez que trabajes)

1. Terminal de **Laragon** en la carpeta del proyecto.
2. Después de cambios: `php artisan test` (si falla, no subas a producción todavía).
3. Para ver la UI: `php artisan serve` → `http://127.0.0.1:8000`.

No uses el mismo `.env` que producción: en local `APP_DEBUG=true` y `APP_URL=http://127.0.0.1:8000` están bien. El **CIF cifrado** usa `APP_KEY`: si copiás una base de prod a local, necesitás la misma clave o los CIF no se leen.

### Subir cambios a Hostinger

1. Subí el código (Git en hPanel, FTP o `git pull` por SSH). **No subas** `.env`, `vendor/` ni `node_modules/` si no usás CI.
2. En SSH, dentro del proyecto:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. Si tocaste roster o migraste datos: `php artisan roster:sincronizar` (solo si aplica).

### Buenas prácticas

- **Producción:** `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL=https://tu-dominio`.
- **Primera vez en Hostinger:** `php artisan key:generate` en el servidor (clave distinta a la local).
- Hacé backup de la base en hPanel antes de migraciones grandes.
- Los miembros del club prueban en el dominio real; vos validás lógica y regresiones con `php artisan test` en local.
