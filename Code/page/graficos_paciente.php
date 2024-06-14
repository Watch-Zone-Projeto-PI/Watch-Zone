<?php

// Conectar ao banco de dados MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "watchzone";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Consulta ao banco de dados
$sql = "SELECT cidade, bairro, COUNT(*) as quantidade FROM paciente GROUP BY cidade, bairro";
$result = $conn->query($sql);

// 3. Processamento dos dados
$cidades = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cidade = $row["cidade"];
        $cep = $row["bairro"];
        $quantidade = $row["quantidade"];

        if (!isset($cidades[$cidade])) {
            $cidades[$cidade] = array();
        }

        $cidades[$cidade][$cep] = $quantidade;
    }
}

// 4. Passar os dados para o JavaScript
echo "<script>";
echo "var dados = " . json_encode($cidades) . ";";
echo "</script>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos por Cidade</title>
    <!-- Importa a biblioteca Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 45%; /* Define a largura das divs que contêm os gráficos */
            display: inline-block; /* Faz com que as divs fiquem uma ao lado da outra */
            margin-right: 5%; /* Adiciona um espaço entre as divs */
            margin-bottom: 20px; /* Adiciona um espaço abaixo de cada div */
            vertical-align: top; /* Alinha as divs no topo */
        }

        @media only screen and (max-width: 768px) {
            .chart-container {
                width: 100%; /* Define a largura para ocupar toda a largura da tela em dispositivos móveis */
                margin-right: 0; /* Remove a margem entre as divs */
            }
        }
    </style>

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
    <a href="login.php" class="btn w-100 py-2" style="background-color: #36b9cc;">Login</a>
    <h1>Gráficos por Cidade</h1>

    <?php
    // Loop para criar um gráfico para cada cidade
    foreach ($cidades as $cidade => $ceps) {
    ?>

        <div class="chart-container">
            <h2><?php echo $cidade; ?></h2>
            <canvas id="chart_<?php echo str_replace(' ', '_', $cidade); ?>" width="400" height="400"></canvas>

            <script>
                // Dados do gráfico para a cidade atual
                var ctx_<?php echo str_replace(' ', '_', $cidade); ?> = document.getElementById('chart_<?php echo str_replace(' ', '_', $cidade); ?>').getContext('2d');
                var myChart_<?php echo str_replace(' ', '_', $cidade); ?> = new Chart(ctx_<?php echo str_replace(' ', '_', $cidade); ?>, {
                    type: 'bar', // Alterado para gráfico de barra
                    data: {
                        labels: <?php echo json_encode(array_keys($ceps)); ?>,
                        datasets: [{
                            label: 'Quantidade de casos',
                            data: <?php echo json_encode(array_values($ceps)); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 1)', // Define a cor de fundo como sólida
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0, // Garante que apenas números inteiros serão exibidos no eixo Y
                                ticks: {
                                    stepSize: 1 // Define o incremento para 1 (números inteiros)
                                }
                            }
                        }
                    }
                });
            </script>
        </div>

    <?php } ?>

</body>

</html>