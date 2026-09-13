# Sitio público - Jaguares E-Sports UAM

Código del sitio público del Club de Deportes Electrónicos de la Universidad Americana, sobre WordPress + Astra en Hostinger Premium.

**Actualizado:** 13 de septiembre de 2026
**En línea:** https://slategrey-baboon-425311.hostingersite.com

---

## La regla que no se rompe

> Todo lo que se guarda en este sitio es visible para cualquiera en internet.
> Nunca se ingresa acá: CIF, teléfono, correo, fecha de nacimiento, carrera ni datos de tutores.

El plugin muestra ese recordatorio en cada pantalla de edición. No es decoración: si algún día hackean el sitio, no debe haber nada que robar.

### Por eso este sitio no tiene registro ni inicio de sesión

Es la decisión estructural del proyecto: son **dos sistemas, no uno**.

| | Este sitio | Sistema de asistencia |
|---|---|---|
| Qué hace | Quiénes somos, equipos, roster por tier, torneos, reglamento | Perfil del miembro, pasar lista, calcular el 80%, solicitar créditos |
| Con qué | WordPress + Astra sobre Hostinger Premium | Sin definir todavía (pendiente P11) |
| Datos sensibles | Ninguno | CIF, teléfonos, carreras, datos de tutores |
| Estado | En línea | Especificación cerrada, se construye desde enero |

Agregarle registro de usuarios a WordPress rompería la regla de arriba: terminaría guardando CIF y datos de tutores de menores de edad en la plataforma más atacada de internet.

Cuando el sistema de asistencia exista, se le agrega al menú un enlace externo del tipo **Portal de miembros** que apunta hacia allá. Son cinco minutos de trabajo el día que haya URL.

El único inicio de sesión del sitio es `/wp-admin`, y es para las dos o tres personas que editan el contenido.

---

## Qué contiene este repositorio

```
jaguares-web/
├── astra-child-jaguares/        ← tema hijo: la apariencia
│   ├── style.css
│   ├── functions.php
│   └── assets/jaguares.css
├── jaguares-datos/              ← plugin: los datos
│   └── jaguares-datos.php
├── contenido/                   ← el texto de las páginas publicadas
├── instalables/                 ← los mismos dos paquetes, en .zip listos para subir
└── README.md
```

**¿Por qué dos piezas y no una?** Los datos van en un plugin y no en el tema a propósito. Si algún día cambian de tema, el roster y los torneos siguen existiendo. Si estuvieran en el tema, desaparecerían todos de golpe.

---

## Requisitos

| | |
|---|---|
| Plan | Hostinger Premium o superior |
| PHP | **8.3** (es el tope del plan y sirve perfectamente; recibe parches hasta diciembre de 2027) |
| WordPress | 6.4 o superior. En producción corre 7.1 |
| Tema padre | Astra (versión gratuita). En producción corre 4.13.11 |

---

## Instalación desde cero

1. **Instalar WordPress** desde hPanel, con un usuario administrador que **no** se llame `admin`.
2. **SSL activado y HTTPS forzado.**
3. **Instalar Astra** en Apariencia → Temas → Añadir nuevo. **No activarlo.**
4. **Subir el tema hijo:** `instalables/astra-child-jaguares.zip` en Apariencia → Temas → Subir tema. Activarlo. Al activarse queda Astra como tema padre.
5. **Subir el plugin:** `instalables/jaguares-datos.zip` en Plugins → Subir plugin. Activarlo. Al activarse crea solos los tres niveles (Tier 1, Tier 2, Miembro) y los cinco juegos.
6. **Enlaces permanentes:** Ajustes → Enlaces permanentes → "Nombre de la entrada" y guardar. Sin esto, las páginas de juegos y torneos dan 404.
7. **Crear las páginas** con el contenido de `contenido/` (ver el LEEME de esa carpeta), armar el menú y poner Inicio como página de portada en Ajustes → Lectura.

Para actualizar una versión ya instalada: se sube el .zip igual que la primera vez y WordPress ofrece **"Reemplazar el instalado con el subido"**.

---

## Correcciones aplicadas al paquete original

Dos errores del paquete entregado en septiembre, los dos en el tema hijo. Si alguien vuelve a subir una versión vieja, reaparecen.

**1. `/torneos/` daba 404.** El plugin registra el archivo automático del tipo `jg_torneo` con el slug `torneos`, que es el mismo de la página "Torneos". El archivo le ganaba a la página y, sin torneos cargados, devolvía 404. Se agregó en `functions.php`:

```php
add_filter( 'register_post_type_args', 'jaguares_sin_archivo_torneos', 10, 2 );
```

que desactiva ese archivo para que gane la página, que es la que tiene el shortcode y el historial.

**2. Error crítico en cada página de juego y de torneo.** `jaguares_limpiar_meta()` estaba declarada como `( array $partes ): array`, pero Astra le pasa una **cadena** al filtro `astra_single_post_meta`. Eso produce un TypeError y una pantalla de error fatal en `/juegos/valorant/` y las otras cuatro. Ahora acepta ambos casos.

**3. Texto del pie.** El filtro `astra_footer_copyright` quedó viejo. La versión actual de Astra usa el constructor de pie de página; se agregó `astra_get_option_footer-copyright-editor`.

---

## Diseño

Base oscura con los colores institucionales de la UAM, tomados de uam.edu.ni. Todo sale de las variables al inicio de `astra-child-jaguares/assets/jaguares.css`:

```css
:root {
	--jg-teal:     #0099A8;  /* turquesa institucional UAM */
	--jg-cian:     #00BCD4;  /* acento, botones, Tier 1 */
	--jg-profundo: #0B545B;  /* apoyo */
	--jg-negro:    #090C0F;  /* fondo */
	--jg-panel:    #151E24;  /* tarjetas */
	--jg-texto:    #E9F1F3;
}
```

Tipografía: **Barlow Condensed** en mayúsculas para títulos y navegación, **Inter** para el texto corrido. Se cargan desde Google Fonts en `functions.php`.

### Decisiones que conviene no deshacer sin pensarlo

**Fondo oscuro, no claro.** La primera versión era clara a propósito, porque esta página también la lee alguien de Registro Académico o la mamá de un miembro de 15 años. La versión oscura mantiene el contraste de texto por encima del mínimo accesible y no baja de 16px en el cuerpo. Si Registro Académico se queja, se invierten las variables de arriba y el sitio entero vuelve a claro sin tocar nada más.

**Los tres niveles del roster no son tres tarjetas iguales.** Tier 1 va primero, en grande y con el acento encendido, porque representa a la universidad. Tier 2 va intermedio. Los miembros van en fichas compactas porque son muchos. La jerarquía visual dice algo real sobre el club.

---

## Cómo se usa

En el escritorio aparece un menú **Jaguares** con tres secciones.

### Juegos

Un juego por grupo del club. Los cinco ya vienen creados. En cada uno se llena día y hora de reunión, lugar, gamer tag del capitán, y se marca **"Grupo activo este semestre"**.

> Mientras ningún juego tenga esa casilla marcada, la portada y la página de Equipos muestran "Todavía no hay grupos activos". Es lo primero que hay que llenar.

El roster del juego aparece solo al final de su página. No hay que pegar nada.

### Roster

Un registro por integrante. El título es **el gamer tag**, no el nombre real.

- Asignale el **nivel** desde la caja lateral: Tier 1, Tier 2 o Miembro
- Elegí a qué **juego** pertenece
- El campo "Nombre real" es opcional, requiere permiso expreso de la persona, y **se deja vacío siempre que sea menor de edad**

### Torneos

Nombre, juego, fechas, modalidad, si está abierto a externos y el enlace de inscripción.

---

## Shortcodes

| Shortcode | Qué muestra |
|---|---|
| `[jg_juegos]` | Grupos activos con horario y capitán |
| `[jg_juegos todos="si"]` | Todos, incluidos los inactivos |
| `[jg_roster juego="valorant"]` | Roster de ese juego, ordenado por nivel |
| `[jg_torneos]` | Torneos próximos |
| `[jg_torneos estado="pasados"]` | Historial |
| `[jg_torneos estado="todos" cantidad="20"]` | Todos, hasta 20 |

---

## Páginas publicadas

| Página | URL | Qué tiene |
|---|---|---|
| Inicio | `/` | Hero, franja de datos, `[jg_juegos]`, tarjetas |
| Equipos | `/equipos/` | `[jg_juegos]` |
| Torneos | `/torneos/` | `[jg_torneos]` y el historial |
| Convalidación de créditos | `/convalidacion-de-creditos/` | Reglamento en lenguaje claro |
| Cómo unirse | `/como-unirse/` | Pasos y aviso de datos |
| Contacto | `/contacto/` | Correo, redes, horario |
| Política de privacidad | `/politica-de-privacidad/` | Qué se publica y qué no |

Menú principal: Inicio, Equipos, Torneos, Convalidación, Cómo unirse, Contacto.
Menú del pie: Política de privacidad, Contacto.

Los textos marcados con `[Completar: ...]` están esperando información que solo tiene la directiva.

---

## Qué falta

- [ ] Marcar los juegos como activos y cargarles día, hora, lugar y capitán
- [ ] Cargar el roster por gamer tag
- [ ] Llenar los `[Completar: ...]` de Convalidación, Contacto y Privacidad
- [ ] Logo del club en la cabecera (hoy va el nombre en texto)
- [ ] Revisar el sitio desde un celular
- [ ] Respaldo automático con UpdraftPlus, **probado restaurándolo**
- [ ] Verificación en dos pasos en Hostinger y en WordPress
- [ ] Segunda persona con acceso, que no se gradúe este año
- [ ] Borrar la entrada de ejemplo "Hello world!" y su comentario

---

## Qué NO hacer

1. **No editar el tema Astra directamente.** Se pierde todo en la siguiente actualización. Para eso existe el tema hijo.
2. **No subir WordPress entero a GitHub.** Al repositorio va solo el código propio: estas dos carpetas.
3. **No instalar plugins "nulled" o piratas.**
4. **No guardar datos sensibles**, por más que un formulario lo facilite.
5. **No agregarle registro de miembros a este WordPress.** Ver la sección de arriba.

---

## Para el repositorio

Sugerencia de `.gitignore` si van a versionar el sitio completo:

```
wp-admin/
wp-includes/
wp-content/themes/astra/
wp-content/plugins/*
!wp-content/plugins/jaguares-datos/
wp-content/uploads/
wp-config.php
.htaccess
```

Lo más simple, sin embargo, es versionar solo estas dos carpetas y nada más.

El repositorio debe estar bajo una cuenta del club u organización, no bajo la cuenta personal de un estudiante que se gradúa.
