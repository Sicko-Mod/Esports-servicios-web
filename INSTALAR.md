# Asistencia Jaguares: proyecto Laravel + sitio público

Este zip es tu proyecto con el sitio público ya integrado (WordPress → Laravel/Blade).
No incluye `vendor/`, `.env` ni la base SQLite vieja: se quedan como los tenés.

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
   ```
3. En la terminal de Laragon, dentro del proyecto:
   ```
   php artisan config:clear
   php artisan migrate
   php artisan db:seed
   ```
   `db:seed` crea los cinco grupos del club (inactivos).
4. Abrí `http://asistencia-jaguares.test`.
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

## Falta

- Panel para cargar juegos, roster y torneos (hoy: seeders o `php artisan tinker`).
- Logo del club.
- Reescribir los textos que dicen que el registro y los datos personales viven «fuera de este sitio»: dejan de ser ciertos cuando la asistencia esté en esta misma app.
- Cruzar estas tablas con `esquema_bd_jaguares.sql` cuando lo consigas.
