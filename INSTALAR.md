# Asistencia Jaguares: proyecto Laravel + sitio público

Este zip es tu proyecto con el sitio público ya integrado (WordPress → Laravel/Blade).
No incluye `vendor/`, `.env` ni la base SQLite vieja: se quedan como los tenés.

## No tengo `php` ni `php artisan` en la PC

Laravel necesita **PHP 8.3+** y **Composer**. En Windows lo más simple para este club es **Laragon** (el proyecto ya asume `asistencia-jaguares.test`):

1. Descargá [Laragon](https://laragon.org/download/) (Full) e instalalo.
2. Copiá o cloná este repo en `C:\laragon\www\asistencia-jaguares` (o el nombre de carpeta que uses).
3. Abrí Laragon → **Start All** → menú → **Terminal** (ahí `php` y `composer` ya funcionan).
4. En esa terminal, dentro de la carpeta del proyecto:
   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   ```
   En `.env` podés usar SQLite sin instalar nada más:
   ```env
   DB_CONNECTION=sqlite
   ```
   y creá el archivo vacío `database\database.sqlite` (o dejá que `php artisan migrate` lo pida).
5. Luego:
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```
   Abrí `http://127.0.0.1:8000` (o el virtual host que configure Laragon).

**Comprobar:** `php -v` y `php artisan --version` deben responder en la terminal de Laragon, no necesariamente en PowerShell suelto hasta que agregues PHP al PATH.

**Alternativa sin Laragon:** instalador oficial de PHP para Windows — en PowerShell (como administrador, una vez):
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```
Cerrá y volvé a abrir la terminal, instalá [Composer](https://getcomposer.org/download/), y repetí los comandos del paso 4 en la carpeta del proyecto.

**Solo Hostinger:** si ya tenés hosting, subí el código y por SSH ejecutá `composer install`, `php artisan migrate --force`, etc. (ver [HOSTINGER.md](HOSTINGER.md)). Podés probar el sitio en el dominio sin tener PHP en tu laptop.

## Cómo aplicarlo

1. Extraelo **encima** de `C:\laragon\www\asistencia-jaguares` y aceptá reemplazar archivos.
2. En tu `.env` (opcional pero recomendado) cambiá:
   ```
   APP_LOCALE=es
   APP_FALLBACK_LOCALE=es
   ```
   y agregá al final, para el contacto del sitio (pueden quedar vacías):
   ```
   CLUB_CORREO=
   CLUB_INSTAGRAM=
   CLUB_DISCORD=
   CLUB_PRESENCIAL=
   ADMIN_EMAIL=tu-correo@uamv.edu.ni
   ADMIN_PASSWORD=una-contraseña-segura
   ADMIN_NAME=Administrador
   ```
3. En la terminal de Laragon, dentro del proyecto:
   ```
   php artisan config:clear
   php artisan migrate
   php artisan db:seed
   ```
   `db:seed` crea los cinco grupos del club (inactivos) y, si configuraste `ADMIN_EMAIL` / `ADMIN_PASSWORD`, la cuenta del panel.
4. Abrí `http://asistencia-jaguares.test`. Panel de contenido: `/admin`. Portal de miembros (asistencia): `/portal`.
5. Solo en local, para ver el diseño con datos falsos:
   ```
   php artisan db:seed --class=DemoSeeder
   ```
6. Pruebas: `php artisan test` (usan SQLite en memoria).

## Qué cambió en tu proyecto

- `routes/web.php`: reemplaza la ruta de bienvenida por las del sitio.
- `config/app.php`: zona horaria `America/Managua` (en Laravel 13 está fija ahí, no sale del `.env`).
- `database/seeders/DatabaseSeeder.php`: ya no crea el «Test User»; carga los cinco juegos.
- `tests/Feature/ExampleTest.php`: usa `RefreshDatabase` porque `/` ahora consulta la base.
- `.env.example`: idioma en español y variables de contacto.
- Se quitó un archivo vacío llamado `prepareBindings($bindings)` que había quedado en la raíz (se coló al copiar el error de la terminal).
- `resources/views/welcome.blade.php` sigue ahí pero ya no se usa.

## Equivalencias con WordPress

| WordPress | Laravel |
|---|---|
| `jg_juego` | tabla `juegos`, modelo `Juego` |
| `jg_miembro` + taxonomía `jg_tier` | tabla `integrantes`, modelo `Integrante`, enum `Nivel` |
| `jg_torneo` | tabla `torneos`, modelo `Torneo` |
| `[jg_juegos]`, `[jg_roster]`, `[jg_torneos]` | `<x-lista-juegos>`, `<x-roster>`, `<x-lista-torneos>` |
| CSS del tema hijo | `public/css/jaguares.css` |
| Las 7 páginas | `resources/views/inicio` y `paginas/*` |
| Huecos de contacto | `config/club.php` + `.env` |

## Panel `/admin`

- Grupos, roster (manual o sincronizado desde el portal), torneos, **semestres académicos**, reporte de **asistencia** (con CSV por sesión) y **usuarios**.
- Entran usuarios con rol `admin` o `superadmin`. Solo un superadmin cambia roles en `/admin/usuarios`; el panel avisa si hay menos de dos.
- Tras `migrate`, corré `db:seed` con `ADMIN_*` en el `.env` o creá un admin en tinker.

## Portal `/portal` (Bloque A)

- Registro solo con correo `@uamv.edu.ni` (`CLUB_CORREO_DOMINIO` en `.env`), con enlace de confirmación. En local, con `MAIL_MAILER=log`, el enlace queda escrito en `storage/logs/laravel.log`.
- Fecha de nacimiento obligatoria: los menores de 18 deben cargar tutor y consentimiento.
- Perfil obligatorio antes de usar el portal (CIF cifrado en base, carrera, teléfono, grupo).
- En `/admin` → editar un grupo → asignar **Capitán en el portal** (miembros con perfil en ese grupo).
- Semestres: en `/admin/semestres` o con `SemestreSeeder`; solo uno activo a la vez.
- Roster público: al guardar perfil en `/portal` se sincroniza tag y grupo; el tier se edita en `/admin/integrantes`. Resync masivo: `php artisan roster:sincronizar`.

## Hostinger

Guía de despliegue: **[HOSTINGER.md](HOSTINGER.md)** (document root `public/`, MySQL, sin Node en el servidor).

## Bloque B — Convalidaciones

- Miembros: `/portal/convalidacion` (requiere al menos 80 % de asistencia y semestre activo; ver `config/club.php`).
- Admin: `/admin/convalidaciones` — resumen de horas en pantalla; **Informe completo (CSV)** (resumen + todas las solicitudes) o **Solo aprobadas** para Registro.
- Estados: enviada, en revisión, aprobada, rechazada.

## Falta

- Logo del club.
- Confirmar columnas del CSV con Registro Académico (ajustar export si piden otro layout).
- Bloque C: piloto en paralelo con el Excel.
- Cruzar tablas con `esquema_bd_jaguares.sql` cuando lo consigas.
