<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/', 'ControladorLogin::index');
$routes->get('/login', 'ControladorLogin::index');
$routes->post('/login/autenticar', 'ControladorLogin::autenticar');
$routes->get('/logout', 'ControladorLogin::logout');


$routes->get('/activar-cuenta', 'ControladorLogin::activarCuenta');
$routes->post('/activar-cuenta/enviar-codigo', 'ControladorLogin::enviarCodigoActivacion');
$routes->post('/activar-cuenta/verificar-codigo', 'ControladorLogin::verificarCodigoActivacion');
$routes->post('/activar-cuenta/registrar', 'ControladorLogin::registrarCuenta');

$routes->get('/testemail', 'TestEmail::index');



$routes->get('/olvide-password', 'ControladorLogin::olvidePassword');
$routes->post('/olvide-password/enviar-codigo', 'ControladorLogin::enviarCodigoRecuperacion');
$routes->post('/olvide-password/verificar-codigo', 'ControladorLogin::verificarCodigoRecuperacion');
$routes->post('/olvide-password/actualizar', 'ControladorLogin::actualizarPassword');



$routes->get('/dashboard', 'Home::index');

$routes->get('/usuarios', 'ControladorUsuarios::index');
$routes->post('/usuarios/actualizar/(:num)', 'ControladorUsuarios::actualizar/$1');
$routes->get('/usuarios/activar/(:num)', 'ControladorUsuarios::activar/$1');
$routes->get('/usuarios/desactivar/(:num)', 'ControladorUsuarios::desactivar/$1');




$routes->get('/profesores', 'ControladorProfesores::index');
$routes->post('/profesores/insertar', 'ControladorProfesores::insertar');
$routes->post('/profesores/actualizar/(:num)', 'ControladorProfesores::actualizar/$1');
$routes->get('/profesores/activar/(:num)', 'ControladorProfesores::activar/$1');
$routes->get('/profesores/desactivar/(:num)', 'ControladorProfesores::desactivar/$1');



$routes->get('/estudiantes', 'ControladorEstudiantes::index');
$routes->post('/estudiantes/insertar', 'ControladorEstudiantes::insertar');
$routes->post('/estudiantes/actualizar/(:num)', 'ControladorEstudiantes::actualizar/$1');
$routes->get('/estudiantes/activar/(:num)', 'ControladorEstudiantes::activar/$1');
$routes->get('/estudiantes/desactivar/(:num)', 'ControladorEstudiantes::desactivar/$1');



$routes->get('/psicologos', 'ControladorPsicologos::index');
$routes->post('/psicologos/insertar', 'ControladorPsicologos::insertar');
$routes->post('/psicologos/actualizar/(:num)', 'ControladorPsicologos::actualizar/$1');
$routes->get('/psicologos/activar/(:num)', 'ControladorPsicologos::activar/$1');
$routes->get('/psicologos/desactivar/(:num)', 'ControladorPsicologos::desactivar/$1');



$routes->get('/administrativos', 'ControladorAdministrativos::index');
$routes->post('/administrativos/insertar', 'ControladorAdministrativos::insertar');
$routes->post('/administrativos/actualizar/(:num)', 'ControladorAdministrativos::actualizar/$1');
$routes->get('/administrativos/activar/(:num)', 'ControladorAdministrativos::activar/$1');
$routes->get('/administrativos/desactivar/(:num)', 'ControladorAdministrativos::desactivar/$1');



$routes->get('/comunicados', 'ControladorComunicados::index');
$routes->post('/comunicados/guardar', 'ControladorComunicados::guardar');
$routes->post('/comunicados/actualizar/(:num)', 'ControladorComunicados::actualizar/$1');
$routes->get('/comunicados/activar/(:num)', 'ControladorComunicados::activar/$1');
$routes->get('/comunicados/desactivar/(:num)', 'ControladorComunicados::desactivar/$1');
$routes->get('/mis-comunicados', 'ControladorComunicados::misComunicados');
$routes->get('/comunicados/ver/(:num)', 'ControladorComunicados::ver/$1');







$routes->get('/materias', 'ControladorMaterias::index');
$routes->post('/materias/insertar', 'ControladorMaterias::insertar');
$routes->post('/materias/actualizar/(:num)', 'ControladorMaterias::actualizar/$1');
$routes->get('/materias/activar/(:num)', 'ControladorMaterias::activar/$1');
$routes->get('/materias/desactivar/(:num)', 'ControladorMaterias::desactivar/$1');



$routes->get('/paralelos', 'ControladorParalelos::index');
$routes->post('/paralelos/insertar', 'ControladorParalelos::insertar');
$routes->post('/paralelos/actualizar/(:num)', 'ControladorParalelos::actualizar/$1');
$routes->get('/paralelos/activar/(:num)', 'ControladorParalelos::activar/$1');
$routes->get('/paralelos/desactivar/(:num)', 'ControladorParalelos::desactivar/$1');


$routes->get('/secciones', 'ControladorSecciones::index');
$routes->post('/secciones/insertar', 'ControladorSecciones::insertar');
$routes->post('/secciones/actualizar/(:num)', 'ControladorSecciones::actualizar/$1');
$routes->get('/secciones/activar/(:num)', 'ControladorSecciones::activar/$1');
$routes->get('/secciones/desactivar/(:num)', 'ControladorSecciones::desactivar/$1');



$routes->get('/aulas', 'ControladorAulas::index');
$routes->post('/aulas/insertar', 'ControladorAulas::insertar');
$routes->post('/aulas/actualizar/(:num)', 'ControladorAulas::actualizar/$1');
$routes->get('/aulas/activar/(:num)', 'ControladorAulas::activar/$1');
$routes->get('/aulas/desactivar/(:num)', 'ControladorAulas::desactivar/$1');




$routes->get('/horarios', 'ControladorHorarios::index');
$routes->post('/horarios/insertar', 'ControladorHorarios::insertar');
$routes->post('/horarios/actualizar/(:num)', 'ControladorHorarios::actualizar/$1');
$routes->get('/horarios/activar/(:num)', 'ControladorHorarios::activar/$1');
$routes->get('/horarios/desactivar/(:num)', 'ControladorHorarios::desactivar/$1');






$routes->get('/asignacion-profesores', 'ControladorAsignacionProfesores::index');
$routes->post('/asignacion-profesores/insertar', 'ControladorAsignacionProfesores::insertar');
$routes->post('/asignacion-profesores/actualizar/(:num)', 'ControladorAsignacionProfesores::actualizar/$1');
$routes->get('/asignacion-profesores/activar/(:num)', 'ControladorAsignacionProfesores::activar/$1');
$routes->get('/asignacion-profesores/desactivar/(:num)', 'ControladorAsignacionProfesores::desactivar/$1');




$routes->get('/asignacion-estudiantes', 'ControladorAsignacionEstudiantes::index');
$routes->post('/asignacion-estudiantes/insertar', 'ControladorAsignacionEstudiantes::insertar');
$routes->post('/asignacion-estudiantes/actualizar/(:num)', 'ControladorAsignacionEstudiantes::actualizar/$1');
$routes->get('/asignacion-estudiantes/activar/(:num)', 'ControladorAsignacionEstudiantes::activar/$1');
$routes->get('/asignacion-estudiantes/desactivar/(:num)', 'ControladorAsignacionEstudiantes::desactivar/$1');

//PROFESOR
$routes->get('/notas-profesor', 'ControladorNotasProfesor::index');
$routes->post('/notas-profesor/insertar', 'ControladorNotasProfesor::insertar');
$routes->post('/notas-profesor/actualizar/(:num)', 'ControladorNotasProfesor::actualizar/$1');
$routes->get('/notas-profesor/eliminar/(:num)', 'ControladorNotasProfesor::eliminar/$1');

$routes->get('/horarios-profesor', 'ControladorHorariosProfesor::index');


//PSICOLOGO
$routes->get('/alertas', 'ControladorAlertas::index');



