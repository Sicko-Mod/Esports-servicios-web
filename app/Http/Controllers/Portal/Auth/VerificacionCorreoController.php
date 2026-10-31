<?php

namespace App\Http\Controllers\Portal\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificacionCorreoController extends Controller
{
    public function aviso(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('portal.perfil.edit');
        }

        return view('portal.auth.verificar-correo');
    }

    public function verificar(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()
            ->route('portal.perfil.edit')
            ->with('estado', 'Correo confirmado. Ahora completá tu perfil de miembro.');
    }

    public function reenviar(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('portal.perfil.edit');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('estado', 'Te enviamos un nuevo enlace de confirmación.');
    }
}
