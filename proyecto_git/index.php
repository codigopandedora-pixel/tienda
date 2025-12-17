<?php
require_once 'php/database.php';

$db = new Database();
$conn = $db->getConnection();

// Filtros
$filtro = $_GET['filtro'] ?? 'todos';
$orden = $_GET['orden'] ?? 'fecha_prestamo DESC';

$query = "SELECT * FROM prestamos WHERE devuelto != 2";  // Excluir eliminados
if ($filtro === 'prestamo') $query .= " AND devuelto = 0";
if ($filtro === 'devueltos') $query .= " AND devuelto = 1";
$query .= " ORDER BY $orden";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Control de Préstamos de Equipos</h1>

        <!-- Formulario Agregar Préstamo -->
        <div class="card mb-4">
            <div class="card-header">Agregar Préstamo</div>
            <div class="card-body">
                <form action="/control_prestamos/php/guardarPrestamos.php" method="POST" id="formPrestamo">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Equipo</label>
                            <input type="text" name="equipo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Serial</label>
                            <input type="text" name="serial" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tipo de Equipo</label>
                            <select name="tipo_equipo" class="form-select">
                                <option value="">Seleccionar</option>
                                <option value="herramienta">Herramienta</option>
                                <option value="activo_red">Activo de Red</option>
                                <option value="portatil">Portátil</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Aprendiz</label>
                            <input type="text" name="aprendiz" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Ficha</label>
                            <input type="text" name="ficha" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Instructor</label>
                            <input type="text" name="instructor" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Fecha de Préstamo</label>
                            <input type="date" name="fecha_prestamo" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Préstamo</button>
                </form>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-3">
            <a href="?filtro=todos&orden=fecha_prestamo DESC" class="btn btn-secondary">Todos</a>
            <a href="?filtro=prestamo&orden=fecha_prestamo DESC" class="btn btn-warning">En Préstamo</a>
            <a href="?filtro=devueltos&orden=fecha_prestamo DESC" class="btn btn-success">Devueltos</a>
            <select onchange="location.href='?filtro=<?php echo $filtro; ?>&orden=' + this.value" class="form-select d-inline-block w-auto">
                <option value="fecha_prestamo DESC">Ordenar por Fecha</option>
                <option value="tipo_equipo ASC">Ordenar por Tipo</option>
            </select>
        </div>

        <!-- Lista de Préstamos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Aprendiz</th>
                    <th>Fecha Préstamo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['equipo']); ?> (<?php echo htmlspecialchars($row['tipo_equipo']); ?>)</td>
                        <td><?php echo htmlspecialchars($row['aprendiz']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_prestamo']); ?></td>
                        <td><?php echo $row['devuelto'] ? 'Devuelto' : 'En Préstamo'; ?></td>
                        <td>
                            <?php if (!$row['devuelto']): ?>
                                <button class="btn btn-success btn-sm" onclick="marcarDevuelto(<?php echo $row['id']; ?>)">Marcar Devuelto</button>
                            <?php endif; ?>
                            <a href="/control_prestamos/php/eliminarPrestamo.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para Marcar Devuelto -->
    <div class="modal fade" id="modalDevuelto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/control_prestamos/php/ActualizarPrestamo.php" method="POST">
                    <div class="modal-header">
                        <h5>Marcar como Devuelto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="prestamoId">
                        <div class="mb-3">
                            <label>Fecha de Devolución</label>
                            <input type="date" name="fecha_devolucion" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Observaciones</label>
                            <textarea name="observaciones" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/assets/app.js"></script>
</body>
</html>
<?php $db->close(); ?> combia la estetica