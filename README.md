# Plataforma Digital - Jaguares E-Sports UAM

Club de Deportes Electrónicos de la Universidad Americana, Managua.

**Actualizado:** 13 de septiembre de 2026

Si llegaste acá porque te invitaron a colaborar, leé las secciones 1, 2 y 9. El resto es referencia.

---

## 1. Qué es esto

El club necesita dos cosas distintas y hoy no tiene ninguna de las dos resueltas del todo:

1. **Una cara pública.** Quiénes somos, qué juegos tenemos, quién juega en cada equipo, qué torneos vienen y cómo se convalidan créditos. Esto ya está en línea.
2. **Un sistema de asistencia.** Pasar lista en cada reunión semanal, calcular el 80% que habilita la convalidación de créditos y generar el reporte para Registro Académico. Esto todavía no existe.

Hoy el segundo punto se lleva a mano en `Asistencia_Anual_ESports_UAM.xlsx`. Los capitanes anotan la asistencia cada semana, alguien transcribe todo al final del semestre, calcula porcentajes y arma el reporte. Ningún estudiante puede saber cómo va su asistencia hasta que el semestre terminó, y de ese cálculo depende que convalide un crédito.

---

## 2. La decisión estructural: son dos sistemas, no uno

Es lo más importante del proyecto y conviene que todo el equipo lo tenga claro antes de proponer cambios.

| | Sitio público | Sistema de asistencia |
|---|---|---|
| **Qué hace** | Quiénes somos, equipos, roster por tier, torneos, reglamento | Perfil del miembro, pasar lista, calcular el 80%, solicitar créditos, exportar reportes |
| **Con qué** | WordPress + Astra sobre Hostinger Premium | Sin definir (pendiente P11) |
| **Dónde vive** | Hostinger | En otro lado. Hostinger Premium no lo soporta |
| **Datos sensibles** | **Ninguno** | CIF, teléfonos, carreras, datos de tutores |
| **Estado** | **En línea** | Especificación cerrada, falta construir |

### Por qué no van juntos

**Técnico.** Hostinger Premium corre PHP. No corre Python, así que Django queda descartado ahí. Node.js solo está en los planes Business y Cloud, no en Premium, así que Next.js tampoco. Ninguno de los dos stacks candidatos funciona en ese plan.

**De fondo.** No existe un plugin de WordPress que haga asistencia ponderada más convalidación de créditos con las restricciones definidas. Habría que programarlo desde cero en PHP, sobre una plataforma cuyo modelo de datos no está pensado para miles de registros relacionales.

**De seguridad.** WordPress es la plataforma más atacada de internet. Un sitio con plugins desactualizados se compromete en meses. Si ese sitio guarda CIF, teléfonos y datos de contacto de tutores de menores de edad, el problema deja de ser técnico y pasa a ser institucional.

> **La regla que resuelve casi todo:** el sitio WordPress no guarda ni un dato sensible. Si algún día lo hackean, no hay nada que robar.

### Consecuencia práctica: el sitio no tiene registro ni inicio de sesión

Es la pregunta que aparece siempre, así que queda escrita. El sitio público **no** tiene "Registrate" ni "Iniciá sesión" ni perfil de usuario, y no es un olvido. Agregarle registro de miembros a WordPress rompe la regla de arriba.

El perfil del miembro y su porcentaje de asistencia van en el otro sistema. Cuando exista, se le agrega al menú del sitio un enlace externo del tipo **Portal de miembros** que apunta hacia allá.

El único inicio de sesión del sitio es `/wp-admin`, y es para las dos o tres personas que editan el contenido.

---

## 3. Estado actual

### Sitio público: en línea

https://slategrey-baboon-425311.hostingersite.com

| | |
|---|---|
| WordPress | 7.1, instalado en Hostinger Premium |
| PHP | 8.3, que es el tope del plan |
| Tema | Astra 4.13.11 con el tema hijo del club encima |
| Plugin | `jaguares-datos`, activo |
| Enlaces permanentes | "Nombre de la entrada" |
| SSL | Activado, HTTPS forzado |

Lo que ya funciona: las siete páginas publicadas con su menú, los cinco juegos creados, los tres niveles de roster, el diseño con los colores institucionales de la UAM, y los shortcodes de juegos, roster y torneos.

Lo que falta cargar: los datos. Ningún juego tiene marcada la casilla "Grupo activo este semestre" ni cargado día, hora, lugar y capitán, así que la portada y la página de Equipos muestran "Todavía no hay grupos activos". Tampoco hay roster ni torneos.

Detalle completo en `jaguares-web/README.md`.

### Sistema de asistencia: no empezado

La especificación funcional está cerrada y el esquema de base de datos está escrito. Falta decidir con qué se construye y falta quien lo construya. Ver secciones 5 y 6.

---

## 4. Reglas de negocio confirmadas

Todas aprobadas por la directiva del club. Quien vaya a programar el sistema de asistencia trabaja contra estas reglas, no contra su intuición.

### Identidad

- Verificación por correo institucional **`@uamv.edu.ni`**. Quien lo tenga verificado es **INTERNO** y puede vincular su CIF.
- Sin correo institucional es **EXTERNO**: miembro pleno del club, pero no convalida créditos ni puede ser capitán.
- Se aceptan miembros **desde los 15 años**. Los menores de 18 registran nombre, correo y teléfono de su tutor, con consentimiento fechado. Sus datos nunca se muestran públicamente.

### Asistencia

```
puntos = PRESENTE + (0.5 × TARDE) + MIN(JUSTIFICADA, 5)
%      = puntos / encuentros_realizados × 100
```

| Estado | Cuándo | Valor |
|---|---|---|
| Presente | Llegó dentro de los primeros 15 minutos | 1 |
| Tarde | Llegó pasados los 15 minutos | 0.5 |
| Justificada | Aprobada por el capitán, hasta 5 por semestre | 1 |
| Ausente | Resto | 0 |

- **Umbral: 80%.** Denominador: encuentros **realizados**, no los programados.
- Se justifica con aviso de 24 horas o por emergencia. La aprueba el capitán.
- Sin límite superior codificado para la tardanza: criterio del capitán. El manual recomienda marcar ausente si llega pasada la mitad de la reunión.
- El capitán edita asistencia pasada hasta 2 semanas después. Luego solo un admin, con registro.
- **Mínimo de encuentros** para que un grupo genere convalidaciones: configurable por semestre, referencia 13.
- **Ingreso a mitad de semestre:** el denominador es el total del semestre. Quien entra tarde y ya no puede alcanzar el 80% no convalida. El sistema debe advertírselo al inscribirse, no al final.

### Convalidación

- **Una por persona por semestre.** Es general del club, no por grupo.
- Tipos: crédito cultural, social, deportivo, horas laborales.
- Un semestre cumplido equivale a **25 horas laborales**. Valor fijo, sin prorrateo.
- Un tipo ya aprobado no se vuelve a solicitar nunca.
- Solo internos con CIF verificado.

### Grupos

- **Mínimo 10 personas para abrir** un grupo. Sin cupo máximo. El mínimo **no** se verifica al cerrar: si el grupo cae por debajo a mitad de semestre, sus miembros conservan el derecho a convalidar y la alerta es informativa.
- Se puede estar en cuantos grupos se quiera mientras los horarios no choquen. El sistema lo detecta y lo bloquea.
- Grupos activos: Valorant, **Warzone y Redsec** (uno solo, no dos), Overwatch, Fight Games, Marvel Rivals.
- Tiers: Tier 1 representa a la universidad, Tier 2 es el segundo equipo, y el resto son miembros. Los externos sí pueden integrar Tier 1.
- Salir sin notificar al capitán genera **veto de un semestre, solo de ese grupo**. Lo levanta el capitán o un admin.

### Datos y accesos

- Visible sin sesión: gamer tags, juegos, roster por tier, capitanes. Nada más.
- Conservación indefinida, con derecho de eliminación por **anonimización**: se borran nombre, correo, teléfono, fecha de nacimiento y datos del tutor; se conservan CIF y convalidación aprobada como respaldo ante Registro Académico.
- Rol SUPERADMIN en **cuentas individuales** con verificación en dos pasos, no una credencial compartida. Toda acción sensible queda en bitácora atribuida a una persona.
- Roles: MIEMBRO, CAPITAN, ADMIN, SUPERADMIN.

---

## 5. Qué existe y dónde

### Código

| Carpeta | Qué es |
|---|---|
| `jaguares-web/astra-child-jaguares/` | Tema hijo de Astra: toda la apariencia del sitio |
| `jaguares-web/jaguares-datos/` | Plugin: juegos, roster, torneos y los shortcodes |
| `jaguares-web/contenido/` | El texto de las siete páginas publicadas, en bloques de WordPress |
| `jaguares-web/instalables/` | Los dos paquetes en .zip, listos para subir |
| `jaguares-web/README.md` | Instalación, shortcodes, diseño y pendientes del sitio |

Los datos van en un plugin y no en el tema a propósito: si algún día cambian de tema, el roster y los torneos siguen existiendo.

### Documentos

| Archivo | Contenido |
|---|---|
| `Plan_Sitio_Web_Jaguares_ESports.md` | Hoja de ruta por fases, checklist, bitácora de decisiones |
| `esquema_bd_jaguares.sql` | Esquema PostgreSQL con tablas, restricciones, vistas y triggers |
| `Guia_Hostinger_WordPress.md` | Paso a paso de hosting, plugins, seguridad y alternativas sin código |
| `Convocatoria_Estudiantes_Sistemas.md` | Brief para reclutar estudiantes de Sistemas |
| `convocatoria_jaguares_esports.png` | Flyer de la convocatoria |
| `Asistencia_Anual_ESports_UAM.xlsx` | El workbook actual. Sigue siendo la fuente oficial de verdad |

### Notion

Workspace **Jaguares E-Sports**, página *Plataforma Digital*: especificación funcional, esquema de base de datos, decisiones y pendientes, y una base de **77 tareas** con módulo, bloque, prioridad y responsable, con dos vistas ("Arrancar ya" y "Por bloque").

La página está en privado. Para que la vea el resto de la directiva hay que moverla a un teamspace.

---

## 6. Qué falta

### Bloquea el arranque del sistema de asistencia

1. **P11: con qué se construye.** Es el único pendiente de especificación que queda. Depende de qué sepan programar los estudiantes que se sumen: JavaScript lleva a Supabase + Next.js, Python lleva a Django. El esquema PostgreSQL sirve igual para ambos. De esto también depende dónde se despliega, porque Hostinger Premium no sirve para ninguno de los dos.

2. **Contar los encuentros del semestre.** Cuántos se realizaron ya y cuántos quedan hasta el 11 de diciembre. Ese número define el mínimo configurable. Si la holgura es menor a 3 encuentros, hay que bajarlo de 13.

3. **Confirmar con Registro Académico** el formato del reporte y que acepten un respaldo identificado solo por CIF, sin nombre, para cuentas anonimizadas. Diez minutos de conversación que evitan rehacer el módulo.

4. **Definir el segundo titular de SUPERADMIN.** Alguien que no se gradúe al mismo tiempo, idealmente un docente o personal de Vida Estudiantil.

### Del sitio público

- Marcar los juegos como activos y cargarles día, hora, lugar y capitán
- Cargar el roster por gamer tag
- Llenar los `[Completar: ...]` de Convalidación, Contacto y Privacidad
- Logo del club en la cabecera y en el flyer
- Revisarlo desde un celular
- Respaldo automático con UpdraftPlus, **probado restaurándolo**
- Verificación en dos pasos en Hostinger y en WordPress
- Borrar la entrada de ejemplo "Hello world!"

### Menor

- Llenar el contacto del flyer donde dice "Escribinos a:"
- Mover la página de Notion a un teamspace

---

## 7. Calendario

| Cuándo | Qué |
|---|---|
| **Septiembre** | WordPress instalado con PHP 8.3, tema y plugin arriba, páginas publicadas. Montar la asistencia provisional en Google Forms + Sheets |
| **Octubre** | Los capitanes pasan lista desde el formulario. Cargar los datos del sitio. Reclutar estudiantes de Sistemas |
| **Noviembre** | Publicar el sitio. Arrancar el desarrollo del sistema de asistencia |
| **Diciembre** | Cierra el semestre el 11. **Las convalidaciones se tramitan con el Excel, como siempre** |
| **Enero en adelante** | Construcción del sistema completo. Piloto en paralelo con el Excel durante todo el semestre |

**El workbook sigue siendo la fuente oficial de verdad** hasta que la plataforma complete un semestre en paralelo y los números coincidan. No se retira antes.

---

## 8. Riesgos vivos

**El calendario es apretado.** Quedan alrededor de 13 semanas de semestre. No alcanza para construir el sistema completo y pilotearlo. Por eso el sitio público va primero y el sistema de asistencia arranca en enero.

**El proyecto puede morir con la directiva.** Es el riesgo más serio y el menos técnico. Si las credenciales, el dominio y el repositorio quedan a nombre de estudiantes que se gradúan, el sitio desaparece en dos años. Todo debe tener un segundo responsable institucional desde el primer día, y el repositorio debe estar bajo una cuenta del club o de la universidad, no bajo una cuenta personal.

**Nadie programa todavía.** El sistema de asistencia necesita a alguien que escriba código. La vía natural es ofrecerlo a Ingeniería en Sistemas como práctica profesional, proyecto de curso o trabajo de graduación. La especificación y el esquema ya están hechos, que es lo que normalmente toma meses definir.

---

## 9. Si venís a colaborar

**Si vas a trabajar en el sitio público:** leé `jaguares-web/README.md`. Necesitás acceso a WordPress, no a Hostinger. Todo cambio de apariencia va al tema hijo; el tema Astra no se toca nunca.

**Si vas a construir el sistema de asistencia:** empezá por la sección 4 de este documento y por `esquema_bd_jaguares.sql`. Antes de escribir una línea hay que cerrar P11 (sección 6). No hace falta que sepas de esports; hace falta que respetes el cálculo del 80% y las reglas de datos, porque de ahí sale un crédito académico real de una persona real.

**Si vas a cargar contenido:** Jaguares → Juegos en el escritorio de WordPress. El título de un integrante es su gamer tag, nunca su nombre real.

---

## 10. Reglas que no se rompen

1. Ningún dato sensible en WordPress. Ni CIF, ni teléfono, ni fecha de nacimiento, ni datos de tutores.
2. El sitio público no lleva registro de miembros. El portal va aparte.
3. Actualizaciones automáticas activadas para WordPress, Astra y plugins.
4. Respaldo semanal automático fuera del servidor, **probado restaurándolo al menos una vez**.
5. Contraseñas únicas en un gestor. Nunca compartidas por WhatsApp.
6. Verificación en dos pasos en Hostinger y en WordPress.
7. Dos personas con acceso, al menos una que no se gradúe este año.
8. Nada de plugins piratas.
9. El usuario administrador no se llama "admin".
10. Nombre real de nadie visible sin autorización expresa, y nunca el de un menor.
11. No editar el tema Astra directamente. Para eso existe el tema hijo.

---

## Glosario

| Término | Qué es |
|---|---|
| **CIF** | Número de carné del estudiante en la UAM |
| **Convalidación** | Reconocimiento de la participación en el club como crédito de vida universitaria |
| **Tier 1 / Tier 2** | Primer y segundo equipo de cada juego. Tier 1 representa a la universidad |
| **Interno / Externo** | Con correo `@uamv.edu.ni` verificado o sin él. Solo los internos convalidan |
| **Gamer tag** | Nombre de juego. Es lo único que se publica de cada integrante |
| **Tema hijo** | Capa de personalización sobre Astra que sobrevive a sus actualizaciones |
