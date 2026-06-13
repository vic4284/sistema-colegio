<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Gestión de Notas</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-nota">Nueva Nota</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Materia</th>
                    <th>Paralelo</th>
                    <th>Gestión</th>
                    <th>1er Trimestre</th>
                    <th>2do Trimestre</th>
                    <th>3er Trimestre</th>
                    <th>Promedio</th>
                    <th>Observación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($notas)): ?>
                    <?php foreach ($notas as $nota): ?>
                        <tr>
                            <td><?= esc($nota['id_nota']) ?></td>
                            <td><?= esc($nota['estudiante_nombres'] . ' ' . $nota['estudiante_apellidos']) ?></td>
                            <td><?= esc($nota['nombre_materia']) ?></td>
                            <td><?= esc($nota['nombre_nivel'] . ' ' . $nota['nombre_grado'] . ' ' . $nota['nombre_seccion']) ?></td>
                            <td><?= esc($nota['nombre_gestion']) ?></td>
                            <td><?= esc($nota['primer_trimestre']) ?></td>
                            <td><?= esc($nota['segundo_trimestre']) ?></td>
                            <td><?= esc($nota['tercer_trimestre']) ?></td>
                            <td><?= esc($nota['promedio']) ?></td>
                            <td><?= esc($nota['observacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $nota['id_nota'] ?>">Editar</a>

                                <a class="btn btn-desactivar"
                                   href="<?= base_url('/notas-profesor/eliminar/' . $nota['id_nota']) ?>"
                                   onclick="return confirm('¿Desea eliminar esta nota?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="sin-registros">No existen notas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-nota" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Nota</h2>

        <form action="<?= base_url('/notas-profesor/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label>Materias Asignadas</label>
                <select name="id_asignacion" required>
                    <option value="">Seleccione asignación</option>
                    <?php foreach ($asignaciones_profesor as $asignacion): ?>
                        <option value="<?= esc($asignacion['id_asignacion']) ?>">
                            <?= esc($asignacion['nombre_materia'] . ' - ' . $asignacion['nombre_nivel'] . ' ' . $asignacion['nombre_grado'] . ' ' . $asignacion['nombre_seccion'] . ' - ' . $asignacion['nombre_gestion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label>Estudiante asignado</label>
                <select name="id_asignacion_estudiante" required>
                    <option value="">Seleccione estudiante</option>
                    <?php foreach ($asignaciones_estudiante as $estudiante): ?>
                        <option value="<?= esc($estudiante['id_asignacion_estudiante']) ?>">
                            <?= esc($estudiante['nombres'] . ' ' . $estudiante['apellidos'] . ' - ' . $estudiante['nombre_nivel'] . ' ' . $estudiante['nombre_grado'] . ' ' . $estudiante['nombre_seccion'] . ' - ' . $estudiante['nombre_gestion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label>Primer trimestre</label>
                <input type="number" name="primer_trimestre" step="0.01" min="0" max="100">
            </div>

            <div class="grupo">
                <label>Segundo trimestre</label>
                <input type="number" name="segundo_trimestre" step="0.01" min="0" max="100">
            </div>

            <div class="grupo">
                <label>Tercer trimestre</label>
                <input type="number" name="tercer_trimestre" step="0.01" min="0" max="100">
            </div>

            <div class="grupo">
                <label>Observación</label>
                <textarea name="observacion"></textarea>
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($notas)): ?>
    <?php foreach ($notas as $nota): ?>
        <div id="modal-editar-<?= $nota['id_nota'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Nota</h2>

                <form action="<?= base_url('/notas-profesor/actualizar/' . $nota['id_nota']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label>Asignación del profesor</label>
                        <select name="id_asignacion" required>
                            <?php foreach ($asignaciones_profesor as $asignacion): ?>
                                <option value="<?= esc($asignacion['id_asignacion']) ?>"
                                    <?= ((int)$nota['id_asignacion'] === (int)$asignacion['id_asignacion']) ? 'selected' : '' ?>>
                                    <?= esc($asignacion['nombre_materia'] . ' - ' . $asignacion['nombre_nivel'] . ' ' . $asignacion['nombre_grado'] . ' ' . $asignacion['nombre_seccion'] . ' - ' . $asignacion['nombre_gestion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Estudiante asignado</label>
                        <select name="id_asignacion_estudiante" required>
                            <?php foreach ($asignaciones_estudiante as $estudiante): ?>
                                <option value="<?= esc($estudiante['id_asignacion_estudiante']) ?>"
                                    <?= ((int)$nota['id_asignacion_estudiante'] === (int)$estudiante['id_asignacion_estudiante']) ? 'selected' : '' ?>>
                                    <?= esc($estudiante['nombres'] . ' ' . $estudiante['apellidos'] . ' - ' . $estudiante['nombre_nivel'] . ' ' . $estudiante['nombre_grado'] . ' ' . $estudiante['nombre_seccion'] . ' - ' . $estudiante['nombre_gestion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Primer trimestre</label>
                        <input type="number" name="primer_trimestre" step="0.01" min="0" max="100" value="<?= esc($nota['primer_trimestre']) ?>">
                    </div>

                    <div class="grupo">
                        <label>Segundo trimestre</label>
                        <input type="number" name="segundo_trimestre" step="0.01" min="0" max="100" value="<?= esc($nota['segundo_trimestre']) ?>">
                    </div>

                    <div class="grupo">
                        <label>Tercer trimestre</label>
                        <input type="number" name="tercer_trimestre" step="0.01" min="0" max="100" value="<?= esc($nota['tercer_trimestre']) ?>">
                    </div>

                    <div class="grupo">
                        <label>Promedio actual</label>
                        <input type="text" value="<?= esc($nota['promedio']) ?>" disabled>
                    </div>

                    <div class="grupo">
                        <label>Observación</label>
                        <textarea name="observacion"><?= esc($nota['observacion']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>