<?php

return [

    'nombre' => 'Jaguares E-Sports',

    'universidad' => 'Universidad Americana',

    'correo_institucional_dominio' => env('CLUB_CORREO_DOMINIO', 'uamv.edu.ni'),

    /*
    | Reglas que se muestran en el sitio. La regla real de asistencia
    | (80 %, 25 horas) se aplicará en el módulo de asistencia; aquí solo
    | se publican los números.
    */
    'umbral_asistencia' => 80,
    'horas_por_semestre' => 25,

    /*
    | Regla de ausencias justificadas. En false (por defecto) un justificado
    | cuenta como ausencia. En true se descuenta del total de sesiones, así
    | no baja el porcentaje. La directiva del club decide cuál aplica.
    */
    'justificados_descuentan_del_total' => (bool) env('CLUB_JUSTIFICADOS_DESCUENTAN', false),

    /*
    | Datos de contacto. Se llenan en el .env (CLUB_CORREO, etc.).
    | Mientras estén vacíos, la página muestra el hueco «[completar ...]».
    */
    'contacto' => [
        'correo' => env('CLUB_CORREO'),
        'instagram' => env('CLUB_INSTAGRAM'),
        'discord' => env('CLUB_DISCORD'),
        'presencial' => env('CLUB_PRESENCIAL'),
    ],

    /*
    | Cuenta del panel /admin (ver AdminUserSeeder).
    */
    'admin_email' => env('ADMIN_EMAIL'),
    'admin_password' => env('ADMIN_PASSWORD'),
    'admin_name' => env('ADMIN_NAME', 'Administrador del club'),

];
