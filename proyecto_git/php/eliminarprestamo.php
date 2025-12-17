eliminar préstamo
<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("UPDATE prestamos SET devuelto = 2 WHERE id = ?");  // 2 = eliminado (borrado suave)
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$db->close();
header('Location: ../index.php');
?>