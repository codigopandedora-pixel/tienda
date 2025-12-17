<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $fecha_devolucion = $_POST['fecha_devolucion'];
    $observaciones = trim($_POST['observaciones']);

    $stmt = $conn->prepare("UPDATE prestamos SET fecha_devolucion = ?, devuelto = 1, observaciones = ? WHERE id = ?");
    $stmt->bind_param("ssi", $fecha_devolucion, $observaciones, $id);
    $stmt->execute();
    $stmt->close();
}

$db->close();
header('Location: ../index.php');
?>