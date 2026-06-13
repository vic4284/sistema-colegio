<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ComunicadosModelo;
class ControladorComunicados extends BaseController
{
    public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'ADMINISTRATIVO' && session()->get('rol') !== 'PROFESOR' && session()->get('rol') !== 'PSICOLOGIA') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        $modelo = new ComunicadosModelo();

 $buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $comunicados = $modelo->buscarComunicados(session()->get('id_usuario'), $buscar);
} else {
    $comunicados = $modelo->listarComunicados(session()->get('id_usuario'));
}

        foreach ($comunicados as &$comunicado) {
            $comunicado['roles_destino'] = $modelo->obtenerDestinosPorComunicado($comunicado['id_comunicado']);
        }

        $data = [
            'comunicados' => $comunicados,
            'roles'       => $modelo->obtenerRoles()
        ];

        return view('Comunicados/index', $data);
    }

    public function guardar()
    {
        if (!session()->get('logueado') || session()->get('rol') !== 'ADMINISTRATIVO' && session()->get('rol') !== 'PROFESOR' && session()->get('rol') !== 'PSICOLOGIA') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $titulo       = trim($this->request->getPost('titulo'));
        $mensaje      = trim($this->request->getPost('mensaje'));
        $rolesDestino = $this->request->getPost('roles_destino');
        $imagen       = $this->request->getFile('imagen');

        if ($titulo === '' || $mensaje === '') {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Debe completar título y mensaje.');
        }

        if (empty($rolesDestino)) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Debe seleccionar al menos un rol destino.');
        }

        $nombreImagen = null;

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $extension = strtolower($imagen->getExtension());

            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return redirect()->to(base_url('/comunicados'))
                                 ->with('error', 'Solo se permiten imágenes JPG, JPEG o PNG.');
            }

            $nombreImagen = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/assets/img/comunicados', $nombreImagen);
        }

        $modelo = new ComunicadosModelo();

        $modelo->insert([
            'id_usuario'         => session()->get('id_usuario'),
            'titulo'             => $titulo,
            'mensaje'            => $mensaje,
            'imagen'             => $nombreImagen,
            'fecha_actualizacion'=> null,
            'estado'             => 1
        ]);

        $idComunicado = $modelo->insertID();
        $modelo->guardarDestinos($idComunicado, $rolesDestino);

        return redirect()->to(base_url('/comunicados'))
                         ->with('success', 'Comunicado registrado correctamente.');
    }

    public function actualizar($idComunicado = null)
    {
        if (!session()->get('logueado') || session()->get('rol') !== 'ADMINISTRATIVO' && session()->get('rol') !== 'PROFESOR' && session()->get('rol') !== 'PSICOLOGIA') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idComunicado === null) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'ID de comunicado no válido.');
        }

        $modelo = new ComunicadosModelo();
        $comunicado = $modelo->obtenerComunicadoPorId($idComunicado);

        if ((int)$comunicado['id_usuario'] !== (int)session()->get('id_usuario')) {
    return redirect()->to(base_url('/comunicados'))
                     ->with('error', 'No tiene permisos para modificar este comunicado.');
}

        if (!$comunicado) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Comunicado no encontrado.');
        }

        $titulo       = trim($this->request->getPost('titulo'));
        $mensaje      = trim($this->request->getPost('mensaje'));
        $rolesDestino = $this->request->getPost('roles_destino');
        $imagen       = $this->request->getFile('imagen');

        if ($titulo === '' || $mensaje === '') {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Debe completar título y mensaje.');
        }

        if (empty($rolesDestino)) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Debe seleccionar al menos un rol destino.');
        }

        $datosActualizar = [
            'titulo'              => $titulo,
            'mensaje'             => $mensaje,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $extension = strtolower($imagen->getExtension());

            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return redirect()->to(base_url('/comunicados'))
                                 ->with('error', 'Solo se permiten imágenes JPG, JPEG o PNG.');
            }

            $nuevoNombre = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/assets/img/comunicados', $nuevoNombre);

            if (!empty($comunicado['imagen']) && file_exists(ROOTPATH . 'public/assets/img/comunicados/' . $comunicado['imagen'])) {
                unlink(ROOTPATH . 'public/assets/img/comunicados/' . $comunicado['imagen']);
            }

            $datosActualizar['imagen'] = $nuevoNombre;
        }

        $modelo->update($idComunicado, $datosActualizar);
        $modelo->guardarDestinos($idComunicado, $rolesDestino);

        return redirect()->to(base_url('/comunicados'))
                         ->with('success', 'Comunicado actualizado correctamente.');
    }

    public function activar($idComunicado = null)
    {
        if (!session()->get('logueado') || (session()->get('rol') !== 'ADMINISTRATIVO' && session()->get('rol') !== 'PROFESOR' && session()->get('rol') !== 'PSICOLOGIA')) {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idComunicado === null) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'ID de comunicado no válido.');
        }

        $modelo = new ComunicadosModelo();
        $comunicado = $modelo->obtenerComunicadoPorId($idComunicado);


        if ((int)$comunicado['id_usuario'] !== (int)session()->get('id_usuario')) {
    return redirect()->to(base_url('/comunicados'))
                     ->with('error', 'No tiene permisos para activar este comunicado.');
}
        if (!$comunicado) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Comunicado no encontrado.');
        }

        $modelo->cambiarEstado($idComunicado, 1);

        return redirect()->to(base_url('/comunicados'))
                         ->with('success', 'Comunicado activado correctamente.');
    }

    public function desactivar($idComunicado = null)
    {
        if (!session()->get('logueado') || (session()->get('rol') !== 'ADMINISTRATIVO' && session()->get('rol') !== 'PROFESOR' && session()->get('rol') !== 'PSICOLOGIA')) {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idComunicado === null) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'ID de comunicado no válido.');
        }

        $modelo = new ComunicadosModelo();
        $comunicado = $modelo->obtenerComunicadoPorId($idComunicado);

        if ((int)$comunicado['id_usuario'] !== (int)session()->get('id_usuario')) {
    return redirect()->to(base_url('/comunicados'))
                     ->with('error', 'No tiene permisos para desactivar este comunicado.');
}
        if (!$comunicado) {
            return redirect()->to(base_url('/comunicados'))
                             ->with('error', 'Comunicado no encontrado.');
        }

        $modelo->cambiarEstado($idComunicado, 0);

        return redirect()->to(base_url('/comunicados'))
                         ->with('success', 'Comunicado desactivado correctamente.');
    }

    public function misComunicados()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        $modelo = new ComunicadosModelo();

       $buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $comunicados = $modelo->buscarComunicadosPorRol(session()->get('id_rol'), $buscar);
} else {
    $comunicados = $modelo->listarComunicadosPorRol(session()->get('id_rol'));
}

$data = [
    'comunicados' => $comunicados
];

        return view('Comunicados/mis_comunicados', $data);
    }


    public function ver($idComunicado = null)
{
    if (!session()->get('logueado')) {
        return redirect()->to(base_url('/login'));
    }

    if ($idComunicado === null) {
        return redirect()->to(base_url('/mis-comunicados'))
                         ->with('error', 'Comunicado no válido.');
    }

    $modelo = new \App\Models\ComunicadosModelo();

    $comunicado = $modelo->obtenerComunicadoDetalle(
        $idComunicado,
        session()->get('id_rol')
    );

    if (!$comunicado) {
        return redirect()->to(base_url('/mis-comunicados'))
                         ->with('error', 'No tiene acceso a este comunicado.');
    }

    return view('Comunicados/ver', [
        'comunicado' => $comunicado
    ]);
}
}
