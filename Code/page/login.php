<?php
session_start();

class Login {
    private $db_host = 'localhost';
    private $db_user = 'root';
    private $db_password = '';
    private $db_name = 'watchzone';
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function verificarLogin() {
        if (isset($_SESSION['user'])) {
            header("Location: index.php");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $cpf = $_POST['cpf'];
            $password = $_POST['password'];
            $userType = $_POST['userType'];

            try {
                $sql = "";
                $column = "";
                if ($userType === "agente" || $userType === "medico") {
                    $table = ($userType === "agente") ? "agentedesaude" : "medico";
                    $sql = "SELECT * FROM $table WHERE cpf = ? AND senha = ?";
                    $column = "cpf";
                }

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$cpf, $password]);
                $user = $stmt->fetch();

                if ($user) {
                    $_SESSION['user'] = $user[$column];
                    $_SESSION['nome'] = $user['nome'];
                    $_SESSION['tipo'] = $userType;
                    $_SESSION['login_type'] = $column;
                    $_SESSION['cpf_crm'] = $user[$column];
                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['error'] = "CPF e senha inválidos.";
                    header("Location: login.php");
                    exit;
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erro ao consultar o banco de dados: " . $e->getMessage();
                header("Location: login.php");
                exit;
            }
        }
    }
}

$login = new Login();
$login->verificarLogin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Watch Zone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
    <!-- Custom styles for this template-->
    <link href="../css/login.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto text-center">
        <form method="POST">
            <img style="margin-left: -25px; filter: drop-shadow(10px 7px 10px #36b9cc);" src="../img/logo.png" alt="" width="350" height="200">
            <h1 class="h3 mb-3 fw-normal">Login</h1>

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error']; ?>
                </div>
            <?php } ?>

            <div class="form-floating">
                <input type="text" class="form-control" id="cpf" name="cpf" required>
                <label for="cpf">CPF</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-select" id="userType" name="userType" required>
                    <option value="agente">Agente de Saúde</option>
                    <option value="medico">Médico</option>
                </select>
                <label for="userType">Tipo de Usuário</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" required>
                <label for="password">Senha</label>
            </div>

            <button class="btn w-100 py-2" style="background-color: #36b9cc;" type="submit">Entrar</button>
            <br><br>
            <a href="graficos_paciente.php" class="btn w-100 py-2" style="background-color: #36b9cc;">Gráficos</a>

            <p class="mt-5 mb-3 text-body-secondary">&copy; 2017–2024</p>
        </form>
    </main>
</body>

</html>
