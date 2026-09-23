<?php

return [

    'nombre' => 'Jaguares E-Sports',

    'universidad' => 'Universidad Americana',

    /*
    | Reglas que se muestran en el sitio. La regla real de asistencia
    | (80 %, 25 horas) se aplicará en el módulo de asistencia; aquí solo
    | se publican los números.
    */
    'umbral_asistencia' => 80,
    'horas_por_semestre' => 25,

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

];
