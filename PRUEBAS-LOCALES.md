# Pruebas locales (Windows + Laragon)

Guía para levantar el proyecto en tu PC y comprobar que todo funciona, sin tocar Hostinger.
Usa **SQLite**, así que no hace falta instalar ni configurar ninguna base de datos.

## Requisitos

- [Laragon Full](https://laragon.org/download/) con **PHP 8.3 o superior** (`php -v`).
- Composer (ya viene con Laragon).
- Node **no** hace falta: el sitio usa `public/css/*.css`, no Vite.

Usá la **Terminal de Laragon** (menú → Terminal) o PowerShell con PHP en el PATH.

> **Consejo:** guardá el proyecto en `C:\laragon\www\...` y no dentro de OneDrive.
> OneDrive sincroniza `vendor/` (miles de archivos) y vuelve lento `composer install`.

## 1. Activar extensiones de PHP (una sola vez)

Abrí el `php.ini` (Laragon → clic derecho → PHP → php.ini) y quitá el `;` de estas líneas:

```ini
extension=zip
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_sqlite
extension=sqlite3
```

Reiniciá la terminal y verificá:

```powershell
php -m | findstr /i "zip sqlite mbstring fileinfo openssl"
```

## 2. Primera vez

Dentro de la carpeta del proyecto:

```powershell
copy .env.example .env
type nul > database\database.sqlite
```

Editá `.env` y ajustá estas líneas (los valores con espacios **van entre comillas**):

```env
DB_CONNECTION=sqlite
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
ADMIN_EMAIL=tu-correo@uamv.edu.ni
ADMIN_PASSWORD="una-clave-segura"
ADMIN_NAME="Administrador del club"
```

Luego:

```powershell
composer install
php artisan key:generate
php artisan config:clear
php artisan migrate
php artisan db:seed
php artisan serve
```

Abrí `http://127.0.0.1:8000`.

| Qué | Dónde |
|-----|-------|
| Sitio público | `/` |
| Panel de contenido | `/admin` (usuario y clave de `ADMIN_EMAIL` / `ADMIN_PASSWORD`) |
| Portal de miembros | `/portal` |

## 3. Día a día

```powershell
php artisan serve                      # levantar el servidor (Ctrl+C para parar)
php artisan config:clear               # después de cambiar el .env
php artisan migrate                    # si hay migraciones nuevas
php artisan migrate:fresh --seed       # BORRA todo y deja la base como nueva
php artisan db:seed --class=DemoSeeder # datos falsos para ver el diseño (solo local)
```

## 4. Qué probar

1. **Sitio público:** inicio, equipos, torneos, convalidación, cómo unirse, contacto, privacidad.
2. **Admin:** entrá a `/admin/login`; creá o editá un grupo, un integrante y un torneo; revisá semestres, asistencia y usuarios.
3. **Portal:** registrate en `/portal` con un correo `@uamv.edu.ni`. Con `MAIL_MAILER=log` no se envía correo real: el enlace de confirmación queda escrito al final de `storage\logs\laravel.log`. Copialo en el navegador, completá el perfil y probá asistencia y convalidación.

## 5. Pruebas automáticas

```powershell
php artisan test
```

Usan SQLite en memoria; no tocan tu base local.

## Problemas comunes

| Mensaje | Causa y solución |
|---------|------------------|
| `zip extension and unzip/7z commands are both missing` | Activá `extension=zip` en `php.ini` y reabrí la terminal. |
| `The environment file is invalid! ... unexpected whitespace` | Un valor del `.env` con espacios sin comillas. Ponelo entre comillas: `ADMIN_NAME="Administrador del club"`. |
| `could not find driver` / `Connection refused` / error de `pgsql` | El `.env` quedó con `DB_CONNECTION=pgsql`. Cambialo a `sqlite` y corré `php artisan config:clear`. |
| `Database file ... does not exist` | Falta crear `database\database.sqlite` (`type nul > database\database.sqlite`). |
| `No application encryption key has been specified` | Corré `php artisan key:generate`. |
| Cambié el `.env` y no hace efecto | `php artisan config:clear`. |
| Página de error genérica | Mirá el detalle al final de `storage\logs\laravel.log`. |
| `composer install` muy lento o falla con archivos bloqueados | Mové el proyecto fuera de OneDrive. |
