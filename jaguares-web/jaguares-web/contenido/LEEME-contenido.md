# Contenido de las páginas

Cada archivo de esta carpeta es el contenido exacto de una página publicada, en formato de bloques de WordPress (los comentarios `<!-- wp:... -->` son lo que hace que el editor los reconozca como bloques y no como un pegote de HTML).

No son archivos que WordPress lea solo. Están acá para que el texto viva en el repositorio y se pueda reconstruir el sitio si hace falta.

## Cómo se recrea una página

1. Páginas → Añadir nueva. Poner el título y el slug de la tabla de abajo.
2. En el editor, menú de tres puntos arriba a la derecha → **Editor de código** (`Ctrl` + `Shift` + `Alt` + `M`).
3. Pegar el contenido del archivo completo.
4. Volver al editor visual y publicar.

## Correspondencia

| Archivo | Título | Slug |
|---|---|---|
| `inicio.html` | Inicio | `inicio` |
| `equipos.html` | Equipos | `equipos` |
| `torneos.html` | Torneos | `torneos` |
| `convalidacion-de-creditos.html` | Convalidación de créditos | `convalidacion-de-creditos` |
| `como-unirse.html` | Cómo unirse | `como-unirse` |
| `contacto.html` | Contacto | `contacto` |
| `politica-de-privacidad.html` | Política de privacidad | `politica-de-privacidad` |

Después: Ajustes → Lectura → "Tu página de inicio muestra: una página estática" → Inicio.

## Ojo con esto

- `inicio.html` usa bloques HTML con las clases `jg-hero`, `jg-datos` y `jg-cards`. Esas clases solo tienen estilo si el tema hijo está activo. Si se cambia de tema, la portada se ve como texto suelto.
- Los `[Completar: ...]` son huecos a propósito. No los borren sin reemplazarlos: son las partes que solo conoce la directiva.
- Los enlaces internos van con ruta absoluta (`/equipos/`), así que funcionan igual si el sitio cambia de dominio.
