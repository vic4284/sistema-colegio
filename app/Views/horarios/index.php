<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Horarios</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-horario">Nuevo Horario</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Día</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($horarios)): ?>
                    <?php foreach ($horarios as $horario): ?>
                        <tr>
                            <td><?= esc($horario['id_horario']) ?></td>
                            <td><?= esc($horario['dia']) ?></td>
                            <td><?= esc($horario['hora_inicio']) ?></td>
                            <td><?= esc($horario['hora_fin']) ?></td>

                            <td>
                                <?php if ((int)$horario['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($horario['fecha_creacion']) ?></td>

                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $horario['id_horario'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$horario['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/horarios/desactivar/' . $horario['id_horario']) ?>"
                                       onclick="return confirm('¿Desea desactivar este horario?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/horarios/activar/' . $horario['id_horario']) ?>"
                                       onclick="return confirm('¿Desea activar este horario?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="sin-registros">No existen horarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-horario" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Horario</h2>

        <form action="<?= base_url('/horarios/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="dia">Día</label>
                <select name="dia" id="dia" required>
                    <option value="">Seleccione día</option>
                    <option value="Lunes" <?= old('dia') === 'Lunes' ? 'selected' : '' ?>>Lunes</option>
                    <option value="Martes" <?= old('dia') === 'Martes' ? 'selected' : '' ?>>Martes</option>
                    <option value="Miércoles" <?= old('dia') === 'Miércoles' ? 'selected' : '' ?>>Miércoles</option>
                    <option value="Jueves" <?= old('dia') === 'Jueves' ? 'selected' : '' ?>>Jueves</option>
                    <option value="Viernes" <?= old('dia') === 'Viernes' ? 'selected' : '' ?>>Viernes</option>
                    <option value="Sábado" <?= old('dia') === 'Sábado' ? 'selected' : '' ?>>Sábado</option>
                </select>
            </div>

            <div class="grupo">
                <label for="hora_inicio">Hora inicio</label>
                <input type="time"
                       name="hora_inicio"
                       id="hora_inicio"
                       required
                       value="<?= esc(old('hora_inicio')) ?>">
            </div>

            <div class="grupo">
                <label for="hora_fin">Hora fin</label>
                <input type="time"
                       name="hora_fin"
                       id="hora_fin"
                       required
                       value="<?= esc(old('hora_fin')) ?>">
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($horarios)): ?>
    <?php foreach ($horarios as $horario): ?>
        <div id="modal-editar-<?= $horario['id_horario'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Horario</h2>

                <form action="<?= base_url('/horarios/actualizar/' . $horario['id_horario']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="dia_<?= $horario['id_horario'] ?>">Día</label>
                        <select name="dia" id="dia_<?= $horario['id_horario'] ?>" required>
                            <option value="">Seleccione día</option>
                            <option value="Lunes" <?= ($horario['dia'] === 'Lunes') ? 'selected' : '' ?>>Lunes</option>
                            <option value="Martes" <?= ($horario['dia'] === 'Martes') ? 'selected' : '' ?>>Martes</option>
                            <option value="Miércoles" <?= ($horario['dia'] === 'Miércoles') ? 'selected' : '' ?>>Miércoles</option>
                            <option value="Jueves" <?= ($horario['dia'] === 'Jueves') ? 'selected' : '' ?>>Jueves</option>
                            <option value="Viernes" <?= ($horario['dia'] === 'Viernes') ? 'selected' : '' ?>>Viernes</option>
                            <option value="Sábado" <?= ($horario['dia'] === 'Sábado') ? 'selected' : '' ?>>Sábado</option>
                        </select>
                    </div>

                    <div class="grupo">
                        <label for="hora_inicio_<?= $horario['id_horario'] ?>">Hora inicio</label>
                        <input type="time"
                               name="hora_inicio"
                               id="hora_inicio_<?= $horario['id_horario'] ?>"
                               value="<?= esc(substr($horario['hora_inicio'], 0, 5)) ?>"
                               required>
                    </div>

                    <div class="grupo">
                        <label for="hora_fin_<?= $horario['id_horario'] ?>">Hora fin</label>
                        <input type="time"
                               name="hora_fin"
                               id="hora_fin_<?= $horario['id_horario'] ?>"
                               value="<?= esc(substr($horario['hora_fin'], 0, 5)) ?>"
                               required>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$horario['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>