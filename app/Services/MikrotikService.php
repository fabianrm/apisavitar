<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'host' => '10.100.100.2', // IP de MikroTik dentro del túnel
            'user' => 'fabianrm',        // cambia si tienes otro usuario
            'pass' => '*binroot*',  // asegúrate de usar uno seguro
            'port' => 8728,           // API puerto por defecto
        ]);
    }

    public function crearUsuarioPPP($nombre, $password, $perfil = 'default')
    {
        $query = new Query('/ppp/secret/add');
        $query->equal('name', $nombre)
            ->equal('password', $password)
            ->equal('service', 'pppoe')
            ->equal('profile', $perfil);

        return $this->client->query($query)->read();
    }

    public function deshabilitarUsuarioPPP($nombre)
    {
        // Primero obtener el ID del usuario
        $query = (new Query('/ppp/secret/print'))->where('name', $nombre);
        $result = $this->client->query($query)->read();

        if (count($result)) {
            $id = $result[0]['.id'];
            $disable = (new Query('/ppp/secret/disable'))->equal('.id', $id);
            return $this->client->query($disable)->read();
        }

        return false;
    }

    public function obtenerPerfilesPPP()
    {
        $query = new \RouterOS\Query('/ppp/profile/print');
        return $this->client->query($query)->read();
    }
}
