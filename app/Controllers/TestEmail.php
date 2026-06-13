<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TestEmail extends BaseController
{
     public function index()
    {
        $email = \Config\Services::email();

        $email->setFrom('sistema@vicent42.com', 'Sistema Colegio San Francisco');
        $email->setTo('aalana843@gmail.com');
        $email->setSubject('Prueba de correo SMTP');
        $email->setMessage('Este es un correo de prueba enviado desde CodeIgniter con Titan.');

        if ($email->send()) {
            echo 'Correo enviado correctamente.';
        } else {
            echo $email->printDebugger(['headers']);
        }
    }
}
