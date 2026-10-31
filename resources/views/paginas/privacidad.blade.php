@extends('layouts.app')

@section('titulo', 'Política de privacidad')

@section('contenido')
    <p>Esta política describe qué datos recopila el sitio web del Club de Deportes Electrónicos de la UAM y cómo se usan.</p>

    <h2>Sitio público</h2>
    <p>Las páginas abiertas (inicio, equipos, torneos, convalidación informativa) muestran solo información del club: nombres de grupos, horarios, gamer tags del roster y torneos. No hay formularios que recojan datos personales en estas páginas.</p>

    <h2>Portal de miembros</h2>
    <p>En <a href="{{ route('portal.login') }}">/portal</a> los integrantes del club crean cuenta con correo institucional y guardan datos necesarios para asistencia y convalidación (CIF, carrera, teléfono, y datos de tutor si son menores de edad). Esos datos <strong>no</strong> se publican en el sitio abierto.</p>

    <h2>Cookies y sesión</h2>
    <p>El portal y el panel de administración usan cookies de sesión técnicas para mantener tu inicio de sesión. No usamos cookies de publicidad ni rastreadores de terceros en este sitio.</p>

    <h2>Panel de administración</h2>
    <p>Voluntarios autorizados del club acceden a <code>/admin</code> para publicar contenido y gestionar asistencia y convalidaciones. El acceso está restringido por cuenta y contraseña.</p>

    <h2>Contacto</h2>
    <p>Para ejercer derechos sobre tus datos o consultas de privacidad, usá los canales del club en la página de <a href="{{ route('contacto') }}">contacto</a>.</p>
@endsection
