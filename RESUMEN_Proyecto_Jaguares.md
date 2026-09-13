# Plataforma Digital — Jaguares E-Sports UAM
## Resumen del proyecto

**Actualizado:** 13 de septiembre de 2026

---

## 1. Qué es

Plataforma web del Club de Deportes Electrónicos de la Universidad Americana. Centraliza el registro de miembros, el control de asistencia a las reuniones semanales, la solicitud de convalidación de créditos estudiantiles y la información pública del equipo.

Hoy todo se lleva a mano en `Asistencia_Anual_ESports_UAM.xlsx`. Los capitanes anotan la asistencia cada semana, alguien transcribe todo al final del semestre, calcula porcentajes y arma el reporte para Registro Académico. Ningún estudiante puede saber cómo va su asistencia hasta que el semestre terminó, y de ese cálculo depende que convalide un crédito.

---

## 2. La decisión estructural: son dos sistemas, no uno

Esto es lo más importante del proyecto y conviene que todo el equipo lo tenga claro.

| | Sitio público | Sistema de asistencia |
|---|---|---|
| **Qué hace** | Quiénes somos, equipos, roster por tier, torneos, reglamento | Pasar lista, calcular el 80%, solicitar créditos, exportar reportes |
| **Con qué** | WordPress + Astra sobre Hostinger Premium | Sin definir (ver pendiente P11) |
| **Dónde vive** | Hostinger | En otro lado. Hostinger Premium no lo soporta |
| **Datos sensibles** | **Ninguno** | CIF, teléfonos, carreras, datos de tutores |
| **Estado** | Código entregado, listo para instalar | Especificación cerrada, falta construir |

### Por qué no van juntos

**Técnico:** Hostinger Premium corre PHP. No corre Python, así que Django queda descartado ahí. Node.js solo está disponible en los planes Business y Cloud, no en Premium, así que Next.js tampoco. Ninguno de los dos stacks candidatos funciona en ese plan.

**De fondo:** no existe un plugin de WordPress que haga asistencia ponderada más convalidación de créditos con las restricciones definidas. Habría que programarlo desde cero en PHP, sobre una plataforma cuyo modelo de datos no está pensado para miles de registros relacionales.

**De seguridad:** WordPress es la plataforma más atacada de internet. Un sitio con plugins desactualizados se compromete en meses. Si ese sitio guarda CIF, teléfonos y datos de contacto de tutores de menores de edad, el problema deja de ser técnico y pasa a ser institucional.

> **La regla que resuelve casi todo:** el sitio WordPress no guarda ni un dato sensible. Si algún día lo hackean, no hay nada que robar.

---

## 3. Reglas de negocio confirmadas

Todas aprobadas por la directiva del club.

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

## 4. Lo que ya existe

### Documentos

| Archivo | Contenido |
|---|---|
| `Plan_Sitio_Web_Jaguares_ESports.md` | Hoja de ruta por fases, checklist completo, bitácora de decisiones |
| `esquema_bd_jaguares.sql` | Esquema PostgreSQL con tablas, restricciones, vistas y triggers |
| `Guia_Hostinger_WordPress.md` | Paso a paso de hosting, plugins, seguridad y alternativas sin código |
| `Convocatoria_Estudiantes_Sistemas.md` | Brief para reclutar estudiantes de Sistemas |
| `convocatoria_jaguares_esports.png` | Flyer de la convocatoria, listo para imprimir o redes |

### Código del sitio público

| Archivo | Qué es |
|---|---|
| `astra-child-jaguares.zip` | Tema hijo de Astra. Se sube desde Apariencia → Temas |
| `jaguares-datos.zip` | Plugin con juegos, roster y torneos. Se sube desde Plugins |
| `jaguares-web/README.md` | Instalación en Hostinger, uso, shortcodes |

Los datos van en un plugin y no en el tema a propósito: si algún día cambian de tema, el roster y los torneos siguen existiendo.

**Shortcodes disponibles:** `[jg_juegos]`, `[jg_roster juego="valorant"]`, `[jg_torneos]`.

### Notion

Workspace **Jaguares E-Sports**, página *Plataforma Digital*, con:

- Especificación funcional
- Esquema de base de datos
- Decisiones y pendientes
- Base de datos de **77 tareas** con módulo, bloque, prioridad y responsable asignable, más dos vistas: "Arrancar ya (Previo)" y "Por bloque"

La página está en privado. Para que la vea el resto de la directiva hay que moverla a un teamspace.

---

## 5. Lo que falta

### Bloquea el arranque

1. **P11: con qué se construye el sistema de asistencia.** Es el único pendiente de especificación que queda. Depende de qué sepan programar los estudiantes: JavaScript lleva a Supabase + Next.js, Python lleva a Django. El esquema PostgreSQL sirve igual para ambos. De esto también depende dónde se despliega, porque Hostinger Premium no sirve para ninguno de los dos.

2. **Contar los encuentros del semestre.** Cuántos se realizaron ya y cuántos quedan hasta el 11 de diciembre. Ese número define el mínimo configurable. Si la holgura es menor a 3 encuentros, hay que bajarlo de 13.

3. **Confirmar con Registro Académico** el formato del reporte y que acepten un respaldo identificado solo por CIF, sin nombre, para cuentas anonimizadas. Diez minutos de conversación que evitan rehacer el módulo.

4. **Definir el segundo titular de SUPERADMIN.** Alguien que no se gradúe al mismo tiempo, idealmente un docente o personal de Vida Estudiantil.

### Menor

- Llenar el contacto del flyer donde dice "Escribinos a:"
- Agregar el logo del club al flyer y al sitio
- Mover la página de Notion a un teamspace

---

## 6. Calendario

| Cuándo | Qué |
|---|---|
| **Septiembre** | Instalar WordPress en Hostinger con PHP 8.3, subir tema y plugin. Montar la asistencia provisional en Google Forms + Sheets |
| **Octubre** | Los capitanes pasan lista desde el formulario. Construir las páginas del sitio. Reclutar estudiantes de Sistemas |
| **Noviembre** | Publicar el sitio. Arrancar el desarrollo del sistema de asistencia |
| **Diciembre** | Cierra el semestre el 11. **Las convalidaciones se tramitan con el Excel, como siempre** |
| **Enero en adelante** | Construcción del sistema completo. Piloto en paralelo con el Excel durante todo el semestre |

**El workbook sigue siendo la fuente oficial de verdad** hasta que la plataforma complete un semestre en paralelo y los números coincidan. No se retira antes.

---

## 7. Configuración técnica

**Hostinger Premium**
- PHP **8.3.99**, que significa "siempre el último parche de la serie 8.3". Es la más alta que ofrece el plan y sirve perfectamente. Recibe parches de seguridad hasta diciembre de 2027.
- Todos los estudiantes deben usar la misma versión en sus computadoras.
- SSL activado y HTTPS forzado.
- Enlaces permanentes en "Nombre de la entrada", si no las páginas de juegos y torneos dan error 404.

**GitHub**
- Al repositorio va **solo el código propio**: las carpetas `jaguares-datos` y `astra-child-jaguares`. No WordPress entero, no Astra, no plugins de terceros.
- El repositorio debe estar bajo una cuenta del club u organización, no bajo la cuenta personal de un estudiante que se gradúa.

---

## 8. Riesgos vivos

**El calendario es apretado.** Quedan 13 semanas al semestre. No alcanza para construir el sistema completo y pilotearlo. Por eso el sitio público va primero y el sistema de asistencia arranca en enero.

**El proyecto puede morir con la directiva.** Es el riesgo más serio y el menos técnico. Si las credenciales, el dominio y el repositorio quedan a nombre de estudiantes que se gradúan, el sitio desaparece en dos años. Todo debe tener un segundo responsable institucional desde el primer día.

**Nadie programa todavía.** El sistema de asistencia necesita a alguien que escriba código. La vía natural es ofrecerlo a Ingeniería en Sistemas como práctica profesional, proyecto de curso o trabajo de graduación. La especificación y el esquema ya están hechos, que es lo que normalmente toma meses definir.

---

## 9. Reglas que no se rompen

1. Ningún dato sensible en WordPress. Ni CIF, ni teléfono, ni fecha de nacimiento, ni datos de tutores.
2. Actualizaciones automáticas activadas para WordPress, Astra y plugins.
3. Respaldo semanal automático fuera del servidor, **probado restaurándolo al menos una vez**.
4. Contraseñas únicas en un gestor. Nunca compartidas por WhatsApp.
5. Verificación en dos pasos en Hostinger y en WordPress.
6. Dos personas con acceso, al menos una que no se gradúe este año.
7. Nada de plugins piratas.
8. El usuario administrador no se llama "admin".
9. Nombre real de nadie visible sin autorización expresa, y nunca el de un menor.
10. No editar el tema Astra directamente. Para eso existe el tema hijo.
