<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Asignación de Estudiantes</h1>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="mensaje-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="mensaje-ok">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px;">
        <a class="btn btn-guardar" href="#modal-insertar-asignacion-estudiante">Nueva Asignación</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Correo</th>
                    <th>Paralelo</th>
                    <th>Gestión</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($asignaciones)): ?>
                    <?php foreach ($asignaciones as $asignacion): ?>
                        <tr>
                            <td><?= esc($asignacion['id_asignacion_estudiante']) ?></td>
                            <td><?= esc($asignacion['nombres'] . ' ' . $asignacion['apellidos']) ?></td>
                            <td><?= esc($asignacion['correo']) ?></td>
                            <td><?= esc($asignacion['nombre_nivel'] . ' ' . $asignacion['nombre_grado'] . ' ' . $asignacion['nombre_seccion']) ?></td>
                            <td><?= esc($asignacion['nombre_gestion']) ?></td>

                            <td>
                                <?php if ((int)$asignacion['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($asignacion['fecha_creacion']) ?></td>

                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $asignacion['id_asignacion_estudiante'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$asignacion['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/asignacion-estudiantes/desactivar/' . $asignacion['id_asignacion_estudiante']) ?>"
                                       onclick="return confirm('¿Desea desactivar esta asignación?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/asignacion-estudiantes/activar/' . $asignacion['id_asignacion_estudiante']) ?>"
                                       onclick="return confirm('¿Desea activar esta asignación?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="sin-registros">No existen asignaciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr style="margin: 35px 0;">

    <h2>Horario del Estudiante</h2>

    <form method="get" action="<?= base_url('asignacion-estudiantes') ?>" style="margin-bottom: 20px;">
        <div class="grupo">
            <label>Seleccione estudiante asignado</label>
            <select name="asignacion_horario" required>
                <option value="">Seleccione estudiante</option>

                <?php foreach ($asignaciones as $asignacion): ?>
                    <option value="<?= esc($asignacion['id_asignacion_estudiante']) ?>"
                        <?= (!empty($asignacion_seleccionada) && (int)$asignacion_seleccionada === (int)$asignacion['id_asignacion_estudiante']) ? 'selected' : '' ?>>
                        <?= esc($asignacion['nombres'] . ' ' . $asignacion['apellidos'] . ' - ' . $asignacion['nombre_nivel'] . ' ' . $asignacion['nombre_grado'] . ' ' . $asignacion['nombre_seccion'] . ' - ' . $asignacion['nombre_gestion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-guardar">Ver horario</button>
    </form>

    <?php if (!empty($horario_estudiante)): ?>

        <?php
            $tablaHorario = [];

            foreach ($horario_estudiante as $h) {
                $hora = $h['hora_inicio'] . ' - ' . $h['hora_fin'];

                $tablaHorario[$hora][$h['dia']] =
                    '<strong>' . esc($h['nombre_materia']) . '</strong><br>' .
                    esc($h['nombres'] . ' ' . $h['apellidos']) . '<br>' .
                    esc($h['nombre_aula']);
            }
        ?>

        <div class="tabla-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                        <th>Sábado</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tablaHorario as $hora => $dias): ?>
                        <tr>
                            <td><?= esc($hora) ?></td>
                            <td><?= $dias['Lunes'] ?? '' ?></td>
                            <td><?= $dias['Martes'] ?? '' ?></td>
                            <td><?= $dias['Miércoles'] ?? '' ?></td>
                            <td><?= $dias['Jueves'] ?? '' ?></td>
                            <td><?= $dias['Viernes'] ?? '' ?></td>
                            <td><?= $dias['Sábado'] ?? '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif (!empty($asignacion_seleccionada)): ?>

        <div class="mensaje-error">
            No existen horarios asignados para este estudiante.
        </div>

    <?php endif; ?>
</div>

<div id="modal-insertar-asignacion-estudiante" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Asignación</h2>

        <form action="<?= base_url('/asignacion-estudiantes/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label>Estudiante</label>
                <select name="id_estudiante" required>
                    <option value="">Seleccione estudiante</option>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <option value="<?= esc($estudiante['id_estudiante']) ?>">
                            <?= esc($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label>Paralelo</label>
                <select name="id_paralelo" required>
                    <option value="">Seleccione paralelo</option>
                    <?php foreach ($paralelos as $paralelo): ?>
                        <option value="<?= esc($paralelo['id_paralelo']) ?>">
                            <?= esc($paralelo['nombre_nivel'] . ' ' . $paralelo['nombre_grado'] . ' ' . $paralelo['nombre_seccion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label>Gestión</label>
                <select name="id_gestion" required>
                    <option value="">Seleccione gestión</option>
                    <?php foreach ($gestiones as $gestion): ?>
                        <option value="<?= esc($gestion['id_gestion']) ?>">
                            <?= esc($gestion['nombre_gestion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($asignaciones)): ?>
    <?php foreach ($asignaciones as $asignacion): ?>
        <div id="modal-editar-<?= $asignacion['id_asignacion_estudiante'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Asignación</h2>

                <form action="<?= base_url('/asignacion-estudiantes/actualizar/' . $asignacion['id_asignacion_estudiante']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label>Estudiante</label>
                        <select name="id_estudiante" required>
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <option value="<?= esc($estudiante['id_estudiante']) ?>"
                                    <?= ((int)$asignacion['id_estudiante'] === (int)$estudiante['id_estudiante']) ? 'selected' : '' ?>>
                                    <?= esc($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Paralelo</label>
                        <select name="id_paralelo" required>
                            <?php foreach ($paralelos as $paralelo): ?>
                                <option value="<?= esc($paralelo['id_paralelo']) ?>"
                                    <?= ((int)$asignacion['id_paralelo'] === (int)$paralelo['id_paralelo']) ? 'selected' : '' ?>>
                                    <?= esc($paralelo['nombre_nivel'] . ' ' . $paralelo['nombre_grado'] . ' ' . $paralelo['nombre_seccion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Gestión</label>
                        <select name="id_gestion" required>
                            <?php foreach ($gestiones as $gestion): ?>
                                <option value="<?= esc($gestion['id_gestion']) ?>"
                                    <?= ((int)$asignacion['id_gestion'] === (int)$gestion['id_gestion']) ? 'selected' : '' ?>>
                                    <?= esc($gestion['nombre_gestion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$asignacion['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>