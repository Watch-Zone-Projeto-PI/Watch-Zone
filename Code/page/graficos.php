<?php
// Verifica se o usuário não está logado, se não estiver, redireciona para a página de login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "watchzone";
    protected $conn;

    public function __construct() {
        // Estabelece a conexão com o banco de dados
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Verifica a conexão
        if ($this->conn->connect_error) {
            die("Erro de conexão: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

class Graficos extends Database {
    public function getCasosPorCidade() {
        $sql = "SELECT cidade, bairro, COUNT(*) as quantidade FROM paciente GROUP BY cidade, bairro";
        $result = $this->getConnection()->query($sql);

        $cidades = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $cidade = $row["cidade"];
                $bairro = $row["bairro"];
                $quantidade = $row["quantidade"];

                if (!isset($cidades[$cidade])) {
                    $cidades[$cidade] = array();
                }

                $cidades[$cidade][$bairro] = $quantidade;
            }
        }

        return $cidades;
    }
}

$graficos = new Graficos();
$cidades = $graficos->getCasosPorCidade();
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
</head>

<body>
    <h1>Gráficos por Cidade</h1>

    <?php foreach ($cidades as $cidade => $bairros) { ?>
        <div class="chart-container">
            <h2><?php echo $cidade; ?></h2>
            <canvas id="chart_<?php echo str_replace(' ', '_', $cidade); ?>" width="400" height="400"></canvas>
            <script>
                var ctx_<?php echo str_replace(' ', '_', $cidade); ?> = document.getElementById('chart_<?php echo str_replace(' ', '_', $cidade); ?>').getContext('2d');
                var myChart_<?php echo str_replace(' ', '_', $cidade); ?> = new Chart(ctx_<?php echo str_replace(' ', '_', $cidade); ?>, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_keys($bairros)); ?>,
                        datasets: [{
                            label: 'Quantidade de casos',
                            data: <?php echo json_encode(array_values($bairros)); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0,
                                ticks: {
                                    stepSize: 1
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