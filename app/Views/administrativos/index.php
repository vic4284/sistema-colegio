<?= view('layout/header') ?>

<div class="contenedor">
    <h1>👥 Módulo de Administrativos</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-administrativo">➕ Nuevo Administrativo</a>
    </div>

    <div class="tabla-responsive">

        <form method="get" action="<?= base_url('/administrativos') ?>" class="tabla-controles">
            <div class="tabla-control-izquierda">
                <label>
                    Mostrar
                    <select name="por_pagina">
                        <option value="10" <?= (int)$porPagina === 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= (int)$porPagina === 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= (int)$porPagina === 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= (int)$porPagina === 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    registros
                </label>
            </div>

            <div class="tabla-control-derecha">
                <label for="buscar">Buscar:</label>
                <input type="text"
                       name="buscar"
                       id="buscar"
                       value="<?= esc($buscar) ?>"
                       maxlength="80"
                       placeholder="Buscar...">

                <button type="submit" class="btn btn-buscar">🔍 Buscar</button>

                <?php if (!empty($buscar)): ?>
                    <a href="<?= base_url('/administrativos') ?>" class="btn btn-cancelar">🧹 Limpiar</a>
                <?php endif; ?>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>🆔 ID</th>
                    <th>👤 Nombres</th>
                    <th>👥 Apellidos</th>
                    <th>📞 Teléfono</th>
                    <th>✉️ Correo</th>
                    <th>💼 Cargo</th>
                    <th>🔐 Usuario vinculado</th>
                    <th>📌 Estado</th>
                    <th>📅 Fecha de creación</th>
                    <th>⚙️ Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($administrativos)): ?>
                    <?php foreach ($administrativos as $administrativo): ?>
                        <tr>
                            <td><?= esc($administrativo['id_administrativo']) ?></td>
                            <td><?= esc($administrativo['nombres']) ?></td>
                            <td><?= esc($administrativo['apellidos']) ?></td>
                            <td><?= esc($administrativo['telefono']) ?></td>
                            <td><?= esc($administrativo['correo']) ?></td>
                            <td><?= esc($administrativo['cargo']) ?></td>
                            <td>
                                <?php if ((int)$administrativo['bloqueado_activacion'] === 1): ?>
                                    <span class="estado-inactivo">🔒 Bloqueado</span>
                                <?php elseif (!empty($administrativo['id_usuario'])): ?>
                                    <span class="estado-activo">✅ Cuenta activada</span>
                                <?php else: ?>
                                    <span class="estado-pendiente">🟡 Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$administrativo['estado'] === 1): ?>
                                    <span class="estado-activo">✅ Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">⛔ Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($administrativo['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $administrativo['id_administrativo'] ?>">
                                    ✏️ Editar
                                </a>

                                <?php if ((int)$administrativo['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/administrativos/desactivar/' . $administrativo['id_administrativo']) ?>">
                                        🚫 Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/administrativos/activar/' . $administrativo['id_administrativo']) ?>">
                                        ✅ Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="sin-registros">No existen administrativos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="tabla-pie">
            <div>
                Mostrando <?= esc($desde) ?> a <?= esc($hasta) ?> de <?= esc($totalRegistros) ?> registros
            </div>

            <div class="paginacion">
                <?php
                    $queryBase = [
                        'buscar' => $buscar,
                        'por_pagina' => $porPagina
                    ];
                ?>

                <?php if ($pagina > 1): ?>
                    <a href="<?= base_url('/administrativos?' . http_build_query(array_merge($queryBase, ['pagina' => $pagina - 1]))) ?>">
                        ◀ Anterior
                    </a>
                <?php else: ?>
                    <span>◀ Anterior</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <?php if ($i == $pagina): ?>
                        <span class="pagina-activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= base_url('/administrativos?' . http_build_query(array_merge($queryBase, ['pagina' => $i]))) ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= base_url('/administrativos?' . http_build_query(array_merge($queryBase, ['pagina' => $pagina + 1]))) ?>">
                        Siguiente ▶
                    </a>
                <?php else: ?>
                    <span>Siguiente ▶</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>