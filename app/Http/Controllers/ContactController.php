<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        // 1. Obtenemos estrictamente los datos validados
        $data = $request->validated();

        // 2. Despachamos el correo electrónico a la cuenta oficial de GBS
        Mail::to('gbsrenovationllc@gmail.com')->send(new ContactSubmitted($data));

        // 3. Retornamos la respuesta unificada que Angular leerá directamente
        return response()->json([
            'message' => 'Thank you! Your message has been sent successfully.'
        ], 200);
    }
}