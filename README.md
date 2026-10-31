# Jaguares E-Sports · Plataforma web UAM

Plataforma oficial del **Club de Deportes Electrónicos** de la **Universidad Americana (UAM)**, Managua. Centraliza la presencia pública del club y, en fases posteriores, la operación interna (asistencia, convalidación de créditos y reportes).

Desarrollada por integrantes del club. Cuenta con aval institucional de la UAM.

---

## ¿Para qué sirve?

| Ámbito | Qué hace hoy | Qué viene después |
|--------|----------------|-------------------|
| **Sitio público** | Información del club, grupos activos, rosters por tier, torneos, guías de convalidación y contacto | Textos institucionales finales, logo, reglamento enlazado |
| **Panel `/admin`** | CRUD de grupos, integrantes del roster y torneos (sin tocar la base a mano) | Más roles y permisos cuando exista el módulo de miembros |
| **Portal `/portal`** | Registro, perfil, asistencia, **solicitud de convalidación** | Verificación de correo institucional (futuro) |
| **Admin convalidaciones** | Revisión de solicitudes y **export CSV** (CIF, sin nombres) | Ajuste de formato con Registro Académico |

**Transición importante:** el libro de Excel *Asistencia_Anual_ESports_UAM.xlsx* sigue siendo la fuente oficial hasta que la plataforma complete **un semestre entero en paralelo** con resultados concordantes.

---

## Qué ve un visitante

- **Inicio** — Resumen del club y grupos activos del semestre.
- **Equipos** — Horario, lugar, capitán y roster público (solo *gamer tags* y niveles Tier 1 / Tier 2 / Miembros).
- **Torneos** — Próximos eventos e historial reciente.
- **Convalidación de créditos** — Reglas en lenguaje claro (80 % de asistencia ≈ 25 h laborales por semestre, según `config/club.php`).
- **Cómo unirse**, **Contacto** y **Política de privacidad**.

El sitio público **no** pide login ni guarda datos sensibles (CIF, teléfonos, tutores, etc.). Eso quedará en el módulo de miembros cuando esté construido.

---

## Arquitectura en una línea

```
Visitante  →  Sitio público (Blade)     →  juegos, integrantes, torneos
Editor     →  /admin (rol admin)        →  mismo contenido, vía panel
Futuro     →  Portal de miembros         →  asistencia y convalidaciones
```

Stack: **PHP 8.3+**, **Laravel 13**, **Blade**, CSS propio (`public/css/jaguares.css`). Pruebas con SQLite en memoria; en **Hostinger** usá **MySQL** (ver [HOSTINGER.md](HOSTINGER.md)).

---

## Inicio rápido

> **¿Probando en Windows con Laragon?** Seguí la guía paso a paso: **[PRUEBAS-LOCALES.md](PRUEBAS-LOCALES.md)**.

Requisitos: PHP, Composer, Node (solo si compilás assets con Vite), base de datos configurada en `.env`.

```bash
composer install
cp .env.example .env   # en Windows: copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

1. Copiá `.env.example` a `.env` y configurá la base de datos.
2. Definí `ADMIN_EMAIL` y `ADMIN_PASSWORD` para crear la cuenta del panel al hacer `db:seed`.
3. Abrí el sitio en la URL de tu entorno (por ejemplo `http://asistencia-jaguares.test` en Laragon).
4. **Sitio público** en `/` · **Panel de contenido** en `/admin` · **Portal de miembros** en `/portal`.

Instrucciones detalladas, variables del club y datos de demo: **[INSTALAR.md](INSTALAR.md)**. Despliegue en producción (Hostinger): **[HOSTINGER.md](HOSTINGER.md)** (incluye flujo **local + Hostinger**: Laragon para cambios y pruebas, hosting para el sitio en vivo).

```bash
php artisan test
```

---

## Estructura del código (lo esencial)

| Pieza | Ubicación |
|-------|-----------|
| Rutas públicas | `routes/web.php` → `SitioController` |
| Rutas del panel | `routes/admin.php` → prefijo `/admin` |
| Modelos del sitio | `Juego`, `Integrante`, `Torneo`, enum `Nivel` |
| Configuración del club | `config/club.php` |
| Vistas públicas | `resources/views/inicio`, `equipos`, `torneos`, `paginas/*` |
| Vistas del panel | `resources/views/admin/*` |
| Estilos | `public/css/jaguares.css`, `public/css/admin.css` |

Migración desde WordPress: el sitio que antes vivía en **WordPress + Astra** pasó a esta app Laravel; las equivalencias de tipos de contenido están en `INSTALAR.md`.

---

## Roadmap (resumen)

1. **Bloque A** — Asistencia: registro institucional, perfiles, grupos con capitanes, toma de lista móvil.
2. **Bloque B** — Convalidaciones y exportes (**implementado**; validar CSV con Registro).
3. **Bloque C** — Piloto en paralelo con el Excel; retiro formal del workbook si los números coinciden al cierre del semestre.

No invertir el orden: consolidar asistencia en producción antes de convalidaciones y reportes.

---

## Seguridad y datos

- Identidad de miembros (futuro): correo **`@uamv.edu.ni`**.
- Roster público: solo información que el club ya mostraba en la web (tags y tiers).
- Panel: usuarios con rol `admin` o `superadmin`. Solo un superadmin asigna roles en `/admin/usuarios`, y el panel avisa mientras no haya dos (con años de graduación distintos).

Si encontrás un problema de seguridad en este repositorio, reportalo al equipo del club por los canales institucionales acordados (no uses el correo genérico de Laravel).

---

## Licencia

Código de aplicación bajo licencia MIT salvo que el club defina otra política para el repositorio. Laravel es software open source con licencia MIT.
