<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Paralelos</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-paralelo">Nuevo Paralelo</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Paralelo</th>
                    <th>Nivel</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($paralelos)): ?>
                    <?php foreach ($paralelos as $paralelo): ?>
                        <tr>
                            <td><?= esc($paralelo['id_paralelo']) ?></td>
                            <td><?= esc($paralelo['nombre_nivel'] . ' ' . $paralelo['nombre_grado'] . ' ' . $paralelo['nombre_seccion']) ?></td>
                            <td><?= esc($paralelo['nombre_nivel']) ?></td>
                            <td><?= esc($paralelo['nombre_grado']) ?></td>
                            <td><?= esc($paralelo['nombre_seccion']) ?></td>

                            <td>
                                <?php if ((int)$paralelo['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($paralelo['fecha_creacion']) ?></td>

                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $paralelo['id_paralelo'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$paralelo['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/paralelos/desactivar/' . $paralelo['id_paralelo']) ?>"
                                       onclick="return confirm('¿Desea desactivar este paralelo?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/paralelos/activar/' . $paralelo['id_paralelo']) ?>"
                                       onclick="return confirm('¿Desea activar este paralelo?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="sin-registros">No existen paralelos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-paralelo" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Paralelo</h2>

        <form action="<?= base_url('/paralelos/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="id_grado">Nivel y grado</label>
                <select name="id_grado" id="id_grado" required>
                    <option value="">Seleccione nivel y grado</option>
                    <?php foreach ($grados as $grado): ?>
                        <option value="<?= esc($grado['id_grado']) ?>">
                            <?= esc($grado['nombre_nivel'] . ' - ' . $grado['nombre_grado']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo">
                <label for="id_seccion">Sección</label>
                <select name="id_seccion" id="id_seccion" required>
                    <option value="">Seleccione sección</option>
                    <?php foreach ($secciones as $seccion): ?>
                        <option value="<?= esc($seccion['id_seccion']) ?>">
                            <?= esc($seccion['nombre_seccion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($paralelos)): ?>
    <?php foreach ($paralelos as $paralelo): ?>
        <div id="modal-editar-<?= $paralelo['id_paralelo'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Paralelo</h2>

                <form action="<?= base_url('/paralelos/actualizar/' . $paralelo['id_paralelo']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="id_grado_<?= $paralelo['id_paralelo'] ?>">Nivel y grado</label>
                        <select name="id_grado" id="id_grado_<?= $paralelo['id_paralelo'] ?>" required>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?= esc($grado['id_grado']) ?>"
                                    <?= ((int)$paralelo['id_grado'] === (int)$grado['id_grado']) ? 'selected' : '' ?>>
                                    <?= esc($grado['nombre_nivel'] . ' - ' . $grado['nombre_grado']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label for="id_seccion_<?= $paralelo['id_paralelo'] ?>">Sección</label>
                        <select name="id_seccion" id="id_seccion_<?= $paralelo['id_paralelo'] ?>" required>
                            <?php foreach ($secciones as $seccion): ?>
                                <option value="<?= esc($seccion['id_seccion']) ?>"
                                    <?= ((int)$paralelo['id_seccion'] === (int)$seccion['id_seccion']) ? 'selected' : '' ?>>
                                    <?= esc($seccion['nombre_seccion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$paralelo['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>