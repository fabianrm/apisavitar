<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Str;

class UtilService
{
    public function generateUniqueCode()
    {
        $code = Str::upper(Str::random(8));

        while (Customer::where('client_code', $code)->exists()) {
            $code = Str::upper(Str::random(8));
        }

        return $code;
    }


    public function generateUniqueCodeSavitar(String $prefix )
    {
        // Obtiene el último código generado
        $lastClient = Service::latest()->first();

        if ($lastClient) {
            // Extrae el número del código existente y lo incrementa
            $lastNumber = intval(substr($lastClient->code, 2)) + 1;
        } else {
            // Si no hay clientes previos, empieza desde 1
            $lastNumber = 1;
        }

        // Formatea el nuevo código con ceros a la izquierda
        $newCode = $prefix . str_pad($lastNumber, 5, '0', STR_PAD_LEFT);

        // Verifica si el nuevo código ya existe en la base de datos
        while (Service::where('service_code', $newCode)->exists()) {
            // Si existe, incrementa el número y vuelve a intentarlo
            $lastNumber++;
            $newCode = $prefix . str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
        }

        return $newCode;
    }
}