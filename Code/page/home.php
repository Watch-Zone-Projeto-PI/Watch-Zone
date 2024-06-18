<?php
class Home {
    private $db_host = 'localhost';
    private $db_user = 'root';
    private $db_password = '';
    private $db_name = 'watchzone';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Handle connection error
            echo "Connection error: " . $e->getMessage();
            die();
        }
    }

    public function verificarLogin() {
        if (!isset($_SESSION['user'])) {
            header("Location: login.php");
            exit;
        }
    }

    public function obterInfectados() {
        $sql = "SELECT COUNT(*) AS total_infectados FROM paciente";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_infectados'];
    }

    public function obterCidadesAfetadas() {
        $sql = "SELECT cidade, COUNT(*) AS qtd_ocorrencias
                FROM paciente
                GROUP BY cidade
                ORDER BY qtd_ocorrencias DESC";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $cidades = [];
        $total_infectados = $this->obterInfectados();

        foreach ($results as $result) {
            $cidade = $result['cidade'];
            $qtd_ocorrencias = $result['qtd_ocorrencias'];
            $porcentagem = ($qtd_ocorrencias / $total_infectados) * 100;
            $cidades[] = [
                'cidade' => $cidade,
                'porcentagem' => round($porcentagem, 2)
            ];
        }

        return $cidades;
    }

    public function obterMediaIdadePacientes() {
        $sql = "SELECT ROUND(AVG(idade), 0) AS media_idade
                FROM paciente
                WHERE idade IS NOT NULL";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['media_idade'];
    }

    public function obterTextoSobreProjeto() {
        // Este método pode ser modificado conforme necessário
        return "Este projeto se dedica à análise e compreensão da recente epidemia de dengue. A dengue representa um desafio global de saúde pública, afetando milhões de pessoas todos os anos em todo o mundo. Nosso objetivo é investigar os principais fatores que contribuíram para a disseminação desta doença em nossa região específica, além de desenvolver estratégias para mitigar seus impactos e prevenir crises futuras.";
    }
}

// Instanciar a classe Home
$home = new Home();
$home->verificarLogin();

// Obter dados necessários
$total_infectados = $home->obterInfectados();
$cidades_afetadas = $home->obterCidadesAfetadas();
$media_idade = $home->obterMediaIdadePacientes();
$texto_sobre_projeto = $home->obterTextoSobreProjeto();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        .progress {
            height: 20px;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Card - Cidade mais Afetada -->
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Cidade mais afetada</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($cidades_afetadas[0]['cidade']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-city fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Média de Idade dos Pacientes -->
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Média de Idade dos Pacientes</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo round($media_idade, 2); ?> anos</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-child fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total de Infectados -->
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Infectados</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_infectados); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-skull-crossbones fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Cidades Afetadas</h6>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($cidades_afetadas as $cidade): ?>
                                        <h4 class="small font-weight-bold"><?php echo $cidade['cidade']; ?> <span class="float-right"><?php echo $cidade['porcentagem']; ?>%</span></h4>
                                        <div class="progress mb-4">
                                            <div class="progress-bar bg-danger" role="progressbar"
                                                style="width: <?php echo $cidade['porcentagem']; ?>%" aria-valuenow="<?php echo $cidade['porcentagem']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Sobre o projeto</h6>
                                </div>
                                <div class="card-body">
                                    <p><?php echo $texto_sobre_projeto; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

</body>

</html>
