<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LoginModelo;

class ControladorLogin extends BaseController
{
      public function index()
    {
        return view('Login/index');
    }

    public function autenticar()
    {
        $login = trim($this->request->getPost('login'));
        $password = $this->request->getPost('password');

        if (empty($login) || empty($password)) {
            return redirect()->to(base_url('/login'))
                             ->with('error', 'Debe completar todos los campos.');
        }

        $modelo = new LoginModelo();
        $usuario = $modelo->obtenerUsuarioPorLogin($login);
        if (!$usuario) {
            return redirect()->to(base_url('/login'))
                             ->with('error', 'El usuario o correo no existe.');
        }
        if ((int)$usuario['estado'] !== 1) {
            return redirect()->to(base_url('/login'))
                             ->with('error', 'La cuenta se encuentra inactiva.');
        }

        if (!password_verify($password, $usuario['contrasena_hash'])) {
            return redirect()->to(base_url('/login'))
                             ->with('error', 'Contraseña incorrecta.');
        }
/* no permite a estudiante */
if (strtoupper($usuario['nombre_rol']) === 'ESTUDIANTE') {
    return redirect()->to(base_url('/login'))
                     ->with('error', 'Los estudiantes solo pueden ingresar desde la aplicación móvil.');
}

$modelo->actualizarUltimoInicioSesion($usuario['id_usuario']);
        $modelo->actualizarUltimoInicioSesion($usuario['id_usuario']);

        $datosSesion = [
            'id_usuario'     => $usuario['id_usuario'],
            'nombre_usuario' => $usuario['nombre_usuario'],
            'correo'         => $usuario['correo_electronico'],
            'id_rol'         => $usuario['id_rol'],
            'rol'            => $usuario['nombre_rol'],
            'imagen'         => $usuario['imagen'],
            'logueado'       => true
        ];

        session()->set($datosSesion);

        return redirect()->to(base_url('/mis-comunicados'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }


    // Nuevas funciones para activación de cuenta y recuperación de contraseña
    public function activarCuenta()
    {
        return view('Login/index');
    }

    public function enviarCodigoActivacion()
    {
        $correo = trim($this->request->getPost('correo'));

        if (empty($correo)) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'Debe ingresar su correo.');
        }

        $modelo = new LoginModelo();

        $usuarioExistente = $modelo->obtenerUsuarioPorCorreo($correo);
        if ($usuarioExistente) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'Este correo ya tiene una cuenta creada.');
        }

        $persona = $modelo->buscarPersonaPendientePorCorreo($correo);

        if (!$persona) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'No existe un registro previo para este correo.');
        }

        if (!empty($persona['id_usuario'])) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'Esta persona ya tiene una cuenta activada.');
        }
        if ((int)$persona['bloqueado_activacion'] === 1) {
            return redirect()->to(base_url('/activar-cuenta'))
                     ->with('error', 'La activación de esta cuenta está bloqueada. Solicite desbloqueo al administrador.');
        }

        $codigo = strval(random_int(100000, 999999));

        $modelo->guardarCodigoVerificacion($correo, $codigo);

        $email = \Config\Services::email();
        $email->setTo($correo);
        $email->setSubject('Código de verificación - Sistema Colegio San Francisco');
        $email->setMessage("
            <h3>Verificación de activación de cuenta</h3>
            <p>Su código de verificación es:</p>
            <h2 style='color:#7a1f1f;'>$codigo</h2>
            <p>Este código expira en 10 minutos.</p>
        ");

        if (!$email->send()) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'No se pudo enviar el código al correo.');
        }

        return redirect()->to(base_url('/activar-cuenta?paso=codigo&correo=' . urlencode($correo)))
                         ->with('success', 'Se envió un código de verificación a su correo.');
    }

    public function verificarCodigoActivacion()
    {
        $correo = trim($this->request->getPost('correo'));
        $codigo = trim($this->request->getPost('codigo'));

        if (empty($correo) || empty($codigo)) {
            return redirect()->to(base_url('/activar-cuenta?paso=codigo&correo=' . urlencode($correo)))
                             ->with('error', 'Debe completar el código de verificación.');
        }

        $modelo = new LoginModelo();
        $verificacion = $modelo->obtenerCodigoValido($correo, $codigo);

        if (!$verificacion) {

    $intento = $modelo->incrementarIntentosVerificacion($correo);

    if ($intento && (int)$intento['intentos'] >= 3) {
        $modelo->bloquearPersonaPorCorreo($correo);

        return redirect()->to(base_url('/activar-cuenta'))
                         ->with('error', 'Cuenta bloqueada por superar los 3 intentos de verificación. Solicite desbloqueo al administrador.');
    }

    $intentosRestantes = $intento ? 3 - (int)$intento['intentos'] : 2;

    return redirect()->to(base_url('/activar-cuenta?paso=codigo&correo=' . urlencode($correo)))
                     ->with('error', 'Código incorrecto. Intentos restantes: ' . $intentosRestantes);
}

        $modelo->marcarCodigoVerificado($verificacion['id_verificacion']);

        return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                         ->with('success', 'Código verificado correctamente. Ahora complete su registro.');
    }

    public function registrarCuenta()
    {
        $correo              = trim($this->request->getPost('correo'));
        $nombreUsuario       = trim($this->request->getPost('nombre_usuario'));
        $contrasena          = $this->request->getPost('contrasena');
        $confirmarContrasena = $this->request->getPost('confirmar_contrasena');
        $imagen = $this->request->getFile('imagen');

        if (empty($correo) || empty($nombreUsuario) || empty($contrasena) || empty($confirmarContrasena)) {
            return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                             ->with('error', 'Debe completar todos los campos.');
        }

        if ($contrasena !== $confirmarContrasena) {
            return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                             ->with('error', 'Las contraseñas no coinciden.');
        }

        $modelo = new LoginModelo();

        $verificacionExitosa = $modelo->obtenerVerificacionExitosa($correo);
        if (!$verificacionExitosa) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'Primero debe verificar su correo con el código enviado.');
        }

        $usuarioExistente = $modelo->obtenerUsuarioPorCorreo($correo);
        if ($usuarioExistente) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'Este correo ya tiene una cuenta creada.');
        }

        $usuarioNombreExistente = $modelo->obtenerUsuarioPorNombreUsuario($nombreUsuario);
        if ($usuarioNombreExistente) {
            return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                             ->with('error', 'El nombre de usuario ya está en uso.');
        }

        $persona = $modelo->buscarPersonaPendientePorCorreo($correo);

        if (!$persona) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'No existe un registro previo para este correo.');
        }

        if (!empty($persona['id_usuario'])) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'Esta persona ya tiene una cuenta activada.');
        }

        $idRol = $modelo->obtenerIdRolPorNombre($persona['rol_sistema']);

        if (!$idRol) {
            return redirect()->to(base_url('/activar-cuenta'))
                             ->with('error', 'No se encontró el rol correspondiente en la tabla roles.');
        }
        
        $nombreImagen = null;

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {

        $extension = strtolower($imagen->getExtension());

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
        return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                         ->with('error', 'Solo se permiten imágenes JPG, JPEG o PNG.');
        }

        $nombreImagen = $imagen->getRandomName();
        $imagen->move(ROOTPATH . 'public/assets/img/usuarios', $nombreImagen);
    }
        $hash = password_hash($contrasena, PASSWORD_ARGON2ID);

        $idUsuarioNuevo = $modelo->crearUsuario([
            'nombre_usuario'     => $nombreUsuario,
            'correo_electronico' => $correo,
            'contrasena_hash'    => $hash,
            'id_rol'             => $idRol,
            'estado'             => 1,
            'imagen'             => $nombreImagen
        ]);

        if (!$idUsuarioNuevo) {
            return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                             ->with('error', 'No se pudo crear la cuenta.');
        }

        $actualizado = $modelo->vincularUsuarioAPersona(
            $persona['tabla'],
            $persona['campo_id'],
            $persona['valor_id'],
            $idUsuarioNuevo
        );

        if (!$actualizado) {
            return redirect()->to(base_url('/activar-cuenta?paso=registro&correo=' . urlencode($correo)))
                             ->with('error', 'La cuenta se creó, pero no se pudo vincular correctamente.');
        }

        $modelo->marcarCodigoUsado($verificacionExitosa['id_verificacion']);

        return redirect()->to(base_url('/login'))
                         ->with('success', 'Cuenta activada correctamente. Ahora ya puede iniciar sesión.');
    }
    public function olvidePassword()
    {
        return view('Login/index');
    }

    public function enviarCodigoRecuperacion()
    {
        $correo = trim($this->request->getPost('correo'));

        if (empty($correo)) {
        return redirect()->to(base_url('/olvide-password'))
                         ->with('error', 'Debe ingresar su correo.');
    }

    $modelo = new LoginModelo();
    $usuario = $modelo->obtenerUsuarioPorCorreo($correo);

    if (!$usuario) {
        return redirect()->to(base_url('/olvide-password'))
                         ->with('error', 'No existe una cuenta registrada con ese correo.');
    }

    if ((int)$usuario['estado'] !== 1) {
        return redirect()->to(base_url('/olvide-password'))
                         ->with('error', 'La cuenta se encuentra inactiva.');
    }

    $codigo = strval(random_int(100000, 999999));

    $modelo->guardarCodigoRecuperacion($correo, $codigo);

    $email = \Config\Services::email();
    $email->setTo($correo);
    $email->setSubject('Código de recuperación - Sistema Colegio San Francisco');
    $email->setMessage("
        <h3>Recuperación de contraseña</h3>
        <p>Su código de verificación es:</p>
        <h2 style='color:#7a1f1f;'>$codigo</h2>
        <p>Este código expira en 10 minutos.</p>
    ");

    if (!$email->send()) {
        return redirect()->to(base_url('/olvide-password'))
                         ->with('error', 'No se pudo enviar el código al correo.');
    }

    return redirect()->to(base_url('/olvide-password?paso=codigo&correo=' . urlencode($correo)))
                     ->with('success', 'Se envió un código de recuperación a su correo.');
}

    public function verificarCodigoRecuperacion()
    {
        $correo = trim($this->request->getPost('correo'));
        $codigo = trim($this->request->getPost('codigo'));

        if (empty($correo) || empty($codigo)) {
        return redirect()->to(base_url('/olvide-password?paso=codigo&correo=' . urlencode($correo)))
                         ->with('error', 'Debe completar el código de verificación.');
    }

    $modelo = new LoginModelo();
    $recuperacion = $modelo->obtenerCodigoRecuperacionValido($correo, $codigo);

    if (!$recuperacion) {
        return redirect()->to(base_url('/olvide-password?paso=codigo&correo=' . urlencode($correo)))
                         ->with('error', 'El código es incorrecto o ya expiró.');
    }

    $modelo->marcarCodigoRecuperacionVerificado($recuperacion['id_recuperacion']);

    return redirect()->to(base_url('/olvide-password?paso=nueva&correo=' . urlencode($correo)))
                     ->with('success', 'Código verificado correctamente. Ahora cree su nueva contraseña.');
    }

    public function actualizarPassword()
    {
        $correo = trim($this->request->getPost('correo'));
        $contrasena = $this->request->getPost('contrasena');
        $confirmarContrasena = $this->request->getPost('confirmar_contrasena');

        if (empty($correo) || empty($contrasena) || empty($confirmarContrasena)) {
        return redirect()->to(base_url('/olvide-password?paso=nueva&correo=' . urlencode($correo)))
                         ->with('error', 'Debe completar todos los campos.');
    }

        if ($contrasena !== $confirmarContrasena) {
        return redirect()->to(base_url('/olvide-password?paso=nueva&correo=' . urlencode($correo)))
                         ->with('error', 'Las contraseñas no coinciden.');
    }

        $modelo = new LoginModelo();
        $recuperacionVerificada = $modelo->obtenerRecuperacionVerificada($correo);

        if (!$recuperacionVerificada) {
        return redirect()->to(base_url('/olvide-password'))
                         ->with('error', 'Primero debe verificar el código enviado a su correo.');
    }

        $hash = password_hash($contrasena, PASSWORD_ARGON2ID);

        $actualizado = $modelo->actualizarPasswordPorCorreo($correo, $hash);

        if (!$actualizado) {
        return redirect()->to(base_url('/olvide-password?paso=nueva&correo=' . urlencode($correo)))
                         ->with('error', 'No se pudo actualizar la contraseña.');
    }

        $modelo->marcarCodigoRecuperacionUsado($recuperacionVerificada['id_recuperacion']);
        return redirect()->to(base_url('/login'))
                     ->with('success', 'Contraseña actualizada correctamente. Ahora ya puede iniciar sesión.');
}

}
