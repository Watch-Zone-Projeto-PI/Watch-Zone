<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pacientes</title>
</head>

<body>

    <h1>Gerenciar Pacientes</h1>

    <?php
    // Conectar ao banco de dados MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "watchzone";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta SQL para selecionar todos os pacientes e os nomes dos médicos ou agentes de saúde
        $sql = "SELECT paciente.*, 
                CASE 
                    WHEN medico.nome IS NOT NULL THEN medico.nome 
                    WHEN agentedesaude.nome IS NOT NULL THEN agentedesaude.nome 
                    ELSE 'N/A' 
                END AS responsavel
                FROM paciente 
                LEFT JOIN medico ON paciente.cpf_medico = medico.cpf 
                LEFT JOIN agentedesaude ON paciente.cpf_agente = agentedesaude.cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Verifica se existem pacientes
        if ($stmt->rowCount() > 0) {
            echo "<table class='table'>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Idade</th>
                    <th>Cidade</th>
                    <th>Bairro</th>
                    <th>Responsável pelo cadastro</th>
                    <th>Ação</th>
                </tr>";
            // Loop através de todas as linhas da tabela paciente
            while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['nome']}</td>
                        <td>{$row['cpf']}</td>
                        <td>{$row['idade']}</td>
                        <td>{$row['cidade']}</td>
                        <td>{$row['bairro']}</td>
                        <td>{$row['responsavel']}</td>
                        <td>
                            <a href='index.php?page=editar_paciente&id={$row['id']}' class='btn btn-primary'>Editar</a>
                            <a href='index.php?page=excluir_paciente&id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir este paciente?\")'>Excluir</a>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='text-align:center;'>Nenhum paciente encontrado.</p>";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }

    // Fechar a conexão
    $pdo = null;
    ?>

</body>

</html>