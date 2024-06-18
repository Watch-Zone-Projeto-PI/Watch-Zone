<?php
// Verifique se o ID do paciente foi fornecido na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID do paciente não fornecido.";
    exit;
}

// Conectar ao banco de dados MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "watchzone";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Excluir o paciente do banco de dados
    $sql = "DELETE FROM paciente WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();

    echo "Paciente excluído com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

// Fechar a conexão
$pdo = null;
?>