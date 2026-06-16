<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Comunicados</h1>

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
        <a href="#modal-registrar-comunicado" class="btn btn-guardar">Nuevo comunicado</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Publicado por</th>
                    <th>Destinatarios</th>
                    <th>Fecha creación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($comunicados)): ?>
                    <?php foreach ($comunicados as $comunicado): ?>
                        <tr>
                            <td><?= esc($comunicado['id_comunicado']) ?></td>
                            <td>
                                <?php if (!empty($comunicado['imagen'])): ?>
                                    <img src="<?= base_url('assets/img/comunicados/' . $comunicado['imagen']) ?>" class="imagen-comunicado-tabla">
                                <?php else: ?>
                                    <span class="sin-imagen">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($comunicado['titulo']) ?></td>
                            <td><?= esc(mb_strimwidth($comunicado['mensaje'], 0, 80, '...')) ?></td>
                            <td><?= esc($comunicado['nombre_usuario']) ?></td>
                            <td><?= esc($comunicado['destinos'] ?? 'Sin destino') ?></td>
                            <td><?= esc($comunicado['fecha_creacion']) ?></td>
                            <td>
                                <?php if ((int)$comunicado['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $comunicado['id_comunicado'] ?>">Editar</a>

                                <?php if ((int)$comunicado['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/comunicados/desactivar/' . $comunicado['id_comunicado']) ?>"
                                       onclick="return confirm('¿Desea desactivar este comunicado?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/comunicados/activar/' . $comunicado['id_comunicado']) ?>"
                                       onclick="return confirm('¿Desea activar este comunicado?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="sin-registros">No existen comunicados registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-registrar-comunicado" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>
        <h2>Registrar Comunicado</h2>

        <form action="<?= base_url('/comunicados/guardar') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="titulo">Título</label>
                <input type="text"
                       name="titulo"
                       id="titulo"
                       placeholder="Ingrese el título"
                       required
                       minlength="3"
                       maxlength="100"
                       value="<?= esc(old('titulo')) ?>">
            </div>

            <div class="grupo">
                <label for="mensaje">Mensaje</label>
                <textarea name="mensaje"
                          id="mensaje"
                          rows="5"
                          placeholder="Ingrese el mensaje del comunicado"
                          required
                          minlength="5"
                          maxlength="500"><?= esc(old('mensaje')) ?></textarea>
            </div>

            <div class="grupo">
                <label for="imagen">Imagen (opcional)</label>
                <input type="file"
                       name="imagen"
                       id="imagen"
                       accept="image/png, image/jpeg, image/jpg, image/webp">
            </div>

            <div class="grupo">
                <label>Destinatarios</label>

                <div class="checkbox-destino">
                    <label for="rol_todos"><strong>TODOS</strong></label>
                    <input type="checkbox"
                           name="roles_destino[]"
                           value="TODOS"
                           id="rol_todos">
                </div>

                <?php foreach ($roles as $rol): ?>
                    <div class="checkbox-destino">
                        <label for="rol_<?= $rol['id_rol'] ?>">
                            <?= esc($rol['nombre_rol']) ?>
                        </label>
                        <input type="checkbox"
                               name="roles_destino[]"
                               value="<?= $rol['id_rol'] ?>"
                               id="rol_<?= $rol['id_rol'] ?>">
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-guardar">Guardar comunicado</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($comunicados)): ?>
    <?php foreach ($comunicados as $comunicado): ?>
        <?php
            $totalRoles = count($roles);
            $totalSeleccionados = count($comunicado['roles_destino']);
            $todosSeleccionados = ($totalRoles > 0 && $totalRoles === $totalSeleccionados);
        ?>

        <div id="modal-editar-<?= $comunicado['id_comunicado'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>
                <h2>Editar Comunicado</h2>

                <form action="<?= base_url('/comunicados/actualizar/' . $comunicado['id_comunicado']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="titulo_<?= $comunicado['id_comunicado'] ?>">Título</label>
                        <input type="text"
                               name="titulo"
                               id="titulo_<?= $comunicado['id_comunicado'] ?>"
                               value="<?= esc($comunicado['titulo']) ?>"
                               required
                               minlength="3"
                               maxlength="100">
                    </div>

                    <div class="grupo">
                        <label for="mensaje_<?= $comunicado['id_comunicado'] ?>">Mensaje</label>
                        <textarea name="mensaje"
                                  id="mensaje_<?= $comunicado['id_comunicado'] ?>"
                                  rows="5"
                                  required
                                  minlength="5"
                                  maxlength="500"><?= esc($comunicado['mensaje']) ?></textarea>
                    </div>

                    <div class="grupo">
                        <label>Imagen actual</label>
                        <div class="contenedor-imagen-editar">
                            <?php if (!empty($comunicado['imagen'])): ?>
                                <img src="<?= base_url('assets/img/comunicados/' . $comunicado['imagen']) ?>"
                                     class="imagen-comunicado-editar">
                            <?php else: ?>
                                <span class="sin-imagen">Sin imagen</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grupo">
                        <label for="imagen_<?= $comunicado['id_comunicado'] ?>">Cambiar imagen (opcional)</label>
                        <input type="file"
                               name="imagen"
                               id="imagen_<?= $comunicado['id_comunicado'] ?>"
                               accept="image/png, image/jpeg, image/jpg, image/webp">
                    </div>

                    <div class="grupo">
                        <label>Destinatarios</label>

                        <div class="checkbox-destino">
                            <label for="edit_rol_todos_<?= $comunicado['id_comunicado'] ?>">
                                <strong>TODOS</strong>
                            </label>
                            <input type="checkbox"
                                   name="roles_destino[]"
                                   value="TODOS"
                                   id="edit_rol_todos_<?= $comunicado['id_comunicado'] ?>"
                                   <?= $todosSeleccionados ? 'checked' : '' ?>>
                        </div>

                        <?php foreach ($roles as $rol): ?>
                            <div class="checkbox-destino">
                                <label for="edit_rol_<?= $comunicado['id_comunicado'] ?>_<?= $rol['id_rol'] ?>">
                                    <?= esc($rol['nombre_rol']) ?>
                                </label>
                                <input type="checkbox"
                                       name="roles_destino[]"
                                       value="<?= $rol['id_rol'] ?>"
                                       id="edit_rol_<?= $comunicado['id_comunicado'] ?>_<?= $rol['id_rol'] ?>"
                                       <?= in_array($rol['id_rol'], $comunicado['roles_destino']) ? 'checked' : '' ?>>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text"
                               value="<?= (int)$comunicado['estado'] === 1 ? 'Activo' : 'Inactivo' ?>"
                               disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>