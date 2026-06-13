<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Asignación de Profesores</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-asignacion">Nueva Asignación</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profesor</th>
                    <th>Materia</th>
                    <th>Paralelo</th>
                    <th>Horario</th>
                    <th>Aula</th>
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
                            <td><?= esc($asignacion['id_asignacion']) ?></td>
                            <td><?= esc($asignacion['nombres'] . ' ' . $asignacion['apellidos']) ?></td>
                            <td><?= esc($asignacion['nombre_materia']) ?></td>
                            <td><?= esc($asignacion['nombre_nivel'] . ' ' . $asignacion['nombre_grado'] . ' ' . $asignacion['nombre_seccion']) ?></td>
                            <td><?= esc($asignacion['dia'] . ' ' . $asignacion['hora_inicio'] . ' - ' . $asignacion['hora_fin']) ?></td>
                            <td><?= esc($asignacion['nombre_aula'] . ' - Capacidad: ' . $asignacion['capacidad']) ?></td>
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
                                <a class="btn btn-editar" href="#modal-editar-<?= $asignacion['id_asignacion'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$asignacion['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/asignacion-profesores/desactivar/' . $asignacion['id_asignacion']) ?>"
                                       onclick="return confirm('¿Desea desactivar esta asignación?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/asignacion-profesores/activar/' . $asignacion['id_asignacion']) ?>"
                                       onclick="return confirm('¿Desea activar esta asignación?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="sin-registros">No existen asignaciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr style="margin: 35px 0;">

    <h2>Horario por Paralelo</h2>

    <form method="get" action="<?= base_url('asignacion-profesores') ?>" style="margin-bottom: 20px;">
        <div class="grupo">
            <label>Seleccione paralelo</label>
            <select name="paralelo_horario" required>
                <option value="">Seleccione paralelo</option>
                <?php foreach ($paralelos as $paralelo): ?>
                    <option value="<?= esc($paralelo['id_paralelo']) ?>"
                        <?= (!empty($paralelo_seleccionado) && (int)$paralelo_seleccionado === (int)$paralelo['id_paralelo']) ? 'selected' : '' ?>>
                        <?= esc($paralelo['nombre_nivel'] . ' ' . $paralelo['nombre_grado'] . ' ' . $paralelo['nombre_seccion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-guardar">Ver horario</button>
    </form>

    <?php if (!empty($horario_paralelo)): ?>

        <?php
            $tablaHorario = [];

            foreach ($horario_paralelo as $h) {
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

    <?php elseif (!empty($paralelo_seleccionado)): ?>

        <div class="mensaje-error">
            No existen horarios asignados para este paralelo.
        </div>

    <?php endif; ?>
</div>

<div id="modal-insertar-asignacion" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Asignación</h2>

        <form action="<?= base_url('/asignacion-profesores/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label>Profesor</label>
                <select name="id_profesor" required>
                    <option value="">Seleccione profesor</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?= esc($profesor['id_profesor']) ?>">
                            <?= esc($profesor['nombres'] . ' ' . $profesor['apellidos']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label>Materia</label>
                <select name="id_materia" required>
                    <option value="">Seleccione materia</option>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?= esc($materia['id_materia']) ?>">
                            <?= esc($materia['nombre_materia']) ?>
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
                <label>Horario</label>
                <select name="id_horario" required>
                    <option value="">Seleccione horario</option>
                    <?php foreach ($horarios as $horario): ?>
                        <option value="<?= esc($horario['id_horario']) ?>">
                            <?= esc($horario['dia'] . ' ' . $horario['hora_inicio'] . ' - ' . $horario['hora_fin']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label>Aula</label>
                <select name="id_aula" required>
                    <option value="">Seleccione aula</option>
                    <?php foreach ($aulas as $aula): ?>
                        <option value="<?= esc($aula['id_aula']) ?>">
                            <?= esc($aula['nombre_aula'] . ' - Capacidad: ' . $aula['capacidad']) ?>
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
        <div id="modal-editar-<?= $asignacion['id_asignacion'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Asignación</h2>

                <form action="<?= base_url('/asignacion-profesores/actualizar/' . $asignacion['id_asignacion']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label>Profesor</label>
                        <select name="id_profesor" required>
                            <?php foreach ($profesores as $profesor): ?>
                                <option value="<?= esc($profesor['id_profesor']) ?>"
                                    <?= ((int)$asignacion['id_profesor'] === (int)$profesor['id_profesor']) ? 'selected' : '' ?>>
                                    <?= esc($profesor['nombres'] . ' ' . $profesor['apellidos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Materia</label>
                        <select name="id_materia" required>
                            <?php foreach ($materias as $materia): ?>
                                <option value="<?= esc($materia['id_materia']) ?>"
                                    <?= ((int)$asignacion['id_materia'] === (int)$materia['id_materia']) ? 'selected' : '' ?>>
                                    <?= esc($materia['nombre_materia']) ?>
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
                        <label>Horario</label>
                        <select name="id_horario" required>
                            <?php foreach ($horarios as $horario): ?>
                                <option value="<?= esc($horario['id_horario']) ?>"
                                    <?= ((int)$asignacion['id_horario'] === (int)$horario['id_horario']) ? 'selected' : '' ?>>
                                    <?= esc($horario['dia'] . ' ' . $horario['hora_inicio'] . ' - ' . $horario['hora_fin']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Aula</label>
                        <select name="id_aula" required>
                            <?php foreach ($aulas as $aula): ?>
                                <option value="<?= esc($aula['id_aula']) ?>"
                                    <?= ((int)$asignacion['id_aula'] === (int)$aula['id_aula']) ? 'selected' : '' ?>>
                                    <?= esc($aula['nombre_aula'] . ' - Capacidad: ' . $aula['capacidad']) ?>
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