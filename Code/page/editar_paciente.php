<?php

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "watchzone";
    protected $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}

class Paciente extends Database {
    public function getPatientById($id) {
        try {
            $stmt = $this->getConnection()->prepare("SELECT * FROM paciente WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao obter paciente: " . $e->getMessage();
        }
    }

    public function updatePatient($id, $nome, $cpf, $cidade, $bairro) {
        try {
            $stmt = $this->getConnection()->prepare("UPDATE paciente SET nome = :nome, cpf = :cpf, cidade = :cidade, bairro = :bairro WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Erro ao atualizar paciente: " . $e->getMessage();
        }
        return false;
    }
}

$paciente = new Paciente();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_GET['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $cidade = $_POST['cidade'];
    $bairro = $_POST['bairro'];

    if ($paciente->updatePatient($id, $nome, $cpf, $cidade, $bairro)) {
        echo "Paciente atualizado com sucesso!";
    }
}

$id = $_GET['id'];
$patient = $paciente->getPatientById($id);
if ($patient) {
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        a.button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        a.button:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Editar Paciente</h1>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo $patient['nome']; ?>" required>
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo $patient['cpf']; ?>" required>
            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" value="<?php echo $patient['cidade']; ?>" required>
            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairro" value="<?php echo $patient['bairro']; ?>" required>
            <button type="submit">Salvar</button>
            <a href="index.php?page=gerenciar_paciente" class="button">Voltar</a>
        </form>
    </div>

</body>

</html>
<?php
} else {
    echo "Paciente não encontrado.";
}
?>