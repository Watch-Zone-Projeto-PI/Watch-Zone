<?php

class Cadastro {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "watchzone";
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function verificarAutenticacao() {
        if (!isset($_SESSION['cpf_crm'])) {
            header("Location: login.php");
            exit;
        }
    }

    public function cadastrarPaciente($nome, $cpf, $cidade, $bairro) {
        try {
            $sql = "INSERT INTO paciente (nome, cpf, cidade, bairro, cpf_agente, cpf_medico) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $cpf_usuario = $_SESSION['cpf_crm'];
            $tipo_usuario = $_SESSION['tipo'];
            if ($tipo_usuario === 'agente') {
                $stmt->execute([$nome, $cpf, $cidade, $bairro, $cpf_usuario, null]);
            } elseif ($tipo_usuario === 'medico') {
                $stmt->execute([$nome, $cpf, $cidade, $bairro, null, $cpf_usuario]);
            }
            $_SESSION['message'] = "Paciente cadastrado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erro ao cadastrar paciente.";
        }
    }

    public function limparCPF($cpf) {
        return preg_replace("/[^0-9]/", "", $cpf);
    }
}

$cadastro = new Cadastro();
$cadastro->verificarAutenticacao();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cpf = isset($_POST['cpf']) ? $cadastro->limparCPF($_POST['cpf']) : null;
    $cidade = $_POST['cidade'];
    $bairro = $_POST['bairro'];

    $cadastro->cadastrarPaciente($nome, $cpf, $cidade, $bairro);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cadastro</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['message'])): ?>
                            <div class="alert <?php echo $_SESSION['message'] == 'Paciente cadastrado com sucesso!' ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                <?php echo $_SESSION['message']; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Paciente</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf">
                            </div>
                            <div class="mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <select class="form-select" id="cidade" name="cidade" required>
                                    <option value="">Selecione a cidade</option>
                                    <option value="Araras">Araras</option>
                                    <option value="Leme">Leme</option>
                                    <option value="Rio Claro">Rio Claro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <select class="form-select" id="bairro" name="bairro" required>
                                    <option value="">Selecione o bairro</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Atualizar os bairros com base na cidade selecionada
        document.getElementById('cidade').addEventListener('change', function() {
            var cidade = this.value;
            var bairroSelect = document.getElementById('bairro');
            bairroSelect.innerHTML = ''; // Limpa os bairros existentes
            // Adiciona os bairros correspondentes à cidade selecionada
            switch(cidade) {
                case 'Araras':
                    bairroSelect.innerHTML += '<option value="Área Rural de Araras">Área Rural de Araras</option>';
                    bairroSelect.innerHTML += '<option value="Bom Jesus">Bom Jesus</option>';
                    bairroSelect.innerHTML += '<option value="Campinho">Campinho</option>';
                    bairroSelect.innerHTML += '<option value="Center Martini">Center Martini</option>';
                    bairroSelect.innerHTML += '<option value="Centro">Centro</option>';
                    bairroSelect.innerHTML += '<option value="Chácara Daltro">Chácara Daltro</option>';
                    bairroSelect.innerHTML += '<option value="Chácara Recreio Vila Rica">Chácara Recreio Vila Rica</option>';
                    bairroSelect.innerHTML += '<option value="Chácaras de Recreio Colina Verde">Chácaras de Recreio Colina Verde</option>';
                    bairroSelect.innerHTML += '<option value="Chácaras Granja São Francisco">Chácaras Granja São Francisco</option>';
                    bairroSelect.innerHTML += '<option value="Condomínio Palmeiras de Piratininga">Condomínio Palmeiras de Piratininga</option>';
                    bairroSelect.innerHTML += '<option value="Condomínio Residencial Alto das Araras">Condomínio Residencial Alto das Araras</option>';
                    bairroSelect.innerHTML += '<option value="Condomínio Villagio Las Palmas">Condomínio Villagio Las Palmas</option>';
                    bairroSelect.innerHTML += '<option value="Condomínio Villagio Loretto">Condomínio Villagio Loretto</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Habitacional Heitor Villa Lobos">Conjunto Habitacional Heitor Villa Lobos</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Habitacional Narciso Gomes">Conjunto Habitacional Narciso Gomes</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Residencial Prefeito Professor Jair Della Colleta">Conjunto Residencial Prefeito Professor Jair Della Colleta</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Residencial Prefeito Professor Milton Severino">Conjunto Residencial Prefeito Professor Milton Severino</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Residencial Prefeito Warley Colombini">Conjunto Residencial Prefeito Warley Colombini</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Campinho B">Desmembramento Campinho B</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Chácaras Zago">Desmembramento Chácaras Zago</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento de Alcebíades Franzini e Outros">Desmembramento de Alcebíades Franzini e Outros</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Fachini">Desmembramento Fachini</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Gino Rodolfo Bolognesi">Desmembramento Gino Rodolfo Bolognesi</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial I Professor Jair Della Colleta">Distrito Industrial I Professor Jair Della Colleta</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial II Guilherme Buck Júnior">Distrito Industrial II Guilherme Buck Júnior</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial III Jacob Maretto">Distrito Industrial III Jacob Maretto</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial V">Distrito Industrial V</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial VI">Distrito Industrial VI</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Municipal Industrial IV Adolpho Matthiesen">Distrito Municipal Industrial IV Adolpho Matthiesen</option>';
                    bairroSelect.innerHTML += '<option value="Jardim 15 de Agosto">Jardim 15 de Agosto</option>';
                    bairroSelect.innerHTML += '<option value="Jardim 8 de Abril">Jardim 8 de Abril</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Abolição de Lourenço Dias">Jardim Abolição de Lourenço Dias</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Alto da Colina">Jardim Alto da Colina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Alto das Araras">Jardim Alto das Araras</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Anhangüera">Jardim Anhangüera</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Bela Vista">Jardim Bela Vista</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Belvedere">Jardim Belvedere</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Boa Esperança">Jardim Boa Esperança</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Buzolin">Jardim Buzolin</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Campestre">Jardim Campestre</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Campos Verdes">Jardim Campos Verdes</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Cândida">Jardim Cândida</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Celina">Jardim Celina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Chácara Araruna">Jardim Chácara Araruna</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Copacabana">Jardim Copacabana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Costa Verde">Jardim Costa Verde</option>';
                    bairroSelect.innerHTML += '<option value="Jardim da Colina">Jardim da Colina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Dalla Costa">Jardim Dalla Costa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Araras I">Jardim das Araras I</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Araras III">Jardim das Araras III</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Flores">Jardim das Flores</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Nações">Jardim das Nações</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Nações II">Jardim das Nações II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Palmeiras">Jardim das Palmeiras</option>';
                    bairroSelect.innerHTML += '<option value="Jardim do Filtro">Jardim do Filtro</option>';
                    bairroSelect.innerHTML += '<option value="Jardim do Lago">Jardim do Lago</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Dom Bosco">Jardim Dom Bosco</option>';
                    bairroSelect.innerHTML += '<option value="Jardim dos Eucalíptos">Jardim dos Eucalíptos</option>';
                    bairroSelect.innerHTML += '<option value="Jardim dos Ypês">Jardim dos Ypês</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Encosta do Sol">Jardim Encosta do Sol</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Esmeralda">Jardim Esmeralda</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Esplanada">Jardim Esplanada</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Florença">Jardim Florença</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Francisco Buzolin">Jardim Francisco Buzolin</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Geny Mercatelli">Jardim Geny Mercatelli</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Haise Maria">Jardim Haise Maria</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Itália">Jardim Itália</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Itamaraty">Jardim Itamaraty</option>';
                    bairroSelect.innerHTML += '<option value="Jardim José Ometto I">Jardim José Ometto I</option>';
                    bairroSelect.innerHTML += '<option value="Jardim José Ometto II">Jardim José Ometto II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim José Ometto III">Jardim José Ometto III</option>';
                    bairroSelect.innerHTML += '<option value="Jardim José Ometto IV">Jardim José Ometto IV</option>';
                    bairroSelect.innerHTML += '<option value="Jardim José Ometto V">Jardim José Ometto V</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Luiza Maria">Jardim Luiza Maria</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Marabá">Jardim Marabá</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Maria Lúcia">Jardim Maria Lúcia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Maria Rosa">Jardim Maria Rosa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Mário Leite de Castro">Jardim Mário Leite de Castro</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Monte Verde">Jardim Monte Verde</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Morumbi">Jardim Morumbi</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Morumbi II">Jardim Morumbi II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Myrian">Jardim Myrian</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nossa Senhora Aparecida">Jardim Nossa Senhora Aparecida</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nossa Senhora de Fátima">Jardim Nossa Senhora de Fátima</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Araras">Jardim Nova Araras</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Europa">Jardim Nova Europa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Olinda">Jardim Nova Olinda</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Rosana">Jardim Nova Rosana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Suissa">Jardim Nova Suissa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Oswaldo Buzolin">Jardim Oswaldo Buzolin</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Ouro Verde">Jardim Ouro Verde</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Ouro Verde II">Jardim Ouro Verde II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Piratininga">Jardim Piratininga</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Planalto">Jardim Planalto</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Portal do Parque">Jardim Portal do Parque</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Portal do Sol">Jardim Portal do Sol</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Pousada dos Barões">Jardim Pousada dos Barões</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Presidente Tancredo Neves">Jardim Presidente Tancredo Neves</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Alvorada">Jardim Residencial Alvorada</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Flamboyant">Jardim Residencial Flamboyant</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Itapuã">Jardim Residencial Itapuã</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Lago Azul">Jardim Residencial Lago Azul</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Lagoa">Jardim Residencial Lagoa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Pedras Preciosas">Jardim Residencial Pedras Preciosas</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Rollo">Jardim Rollo</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Rosana">Jardim Rosana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Catarina">Jardim Santa Catarina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Cruz">Jardim Santa Cruz</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Efigênia">Jardim Santa Efigênia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Marta">Jardim Santa Marta</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Olívia II">Jardim Santa Olívia II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Rosa">Jardim Santa Rosa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santo André">Jardim Santo André</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Conrado">Jardim São Conrado</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São João">Jardim São João</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Luiz">Jardim São Luiz</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Nicolau">Jardim São Nicolau</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Pedro">Jardim São Pedro</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Sobradinho">Jardim Sobradinho</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Sobradinho e Furnas">Jardim Sobradinho e Furnas</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Tangará">Jardim Tangará</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Tarumã">Jardim Tarumã</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Terras de Carolina">Jardim Terras de Carolina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Terras de Santa Elisa">Jardim Terras de Santa Elisa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Universitário">Jardim Universitário</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Vista Alegre">Jardim Vista Alegre</option>';
                    bairroSelect.innerHTML += '<option value="Jardins de Samantha I">Jardins de Samantha I</option>';
                    bairroSelect.innerHTML += '<option value="Jardins de Samantha II">Jardins de Samantha II</option>';
                    bairroSelect.innerHTML += '<option value="Jardins de Samantha III">Jardins de Samantha III</option>';
                    bairroSelect.innerHTML += '<option value="Novo Jardim Cândida">Novo Jardim Cândida</option>';
                    bairroSelect.innerHTML += '<option value="Parque Alvorada">Parque Alvorada</option>';
                    bairroSelect.innerHTML += '<option value="Parque Cidade Jardim">Parque Cidade Jardim</option>';
                    bairroSelect.innerHTML += '<option value="Parque das Árvores">Parque das Árvores</option>';
                    bairroSelect.innerHTML += '<option value="Parque Dom Pedro">Parque Dom Pedro</option>';
                    bairroSelect.innerHTML += '<option value="Parque Industrial">Parque Industrial</option>';
                    bairroSelect.innerHTML += '<option value="Parque Portal das Laranjeiras">Parque Portal das Laranjeiras</option>';
                    bairroSelect.innerHTML += '<option value="Parque Santa Cândida">Parque Santa Cândida</option>';
                    bairroSelect.innerHTML += '<option value="Parque Terras de Santa Olívia">Parque Terras de Santa Olívia</option>';
                    bairroSelect.innerHTML += '<option value="Parque Tiradentes">Parque Tiradentes</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Bosque de Versalles">Residencial Bosque de Versalles</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Jardim América">Residencial Jardim América</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Jardim Paulista">Residencial Jardim Paulista</option>';
                    bairroSelect.innerHTML += '<option value="Residêncial Morada do Sol">Residêncial Morada do Sol</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Santa Mônica">Residencial Santa Mônica</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Vila Inglesa">Residencial Vila Inglesa</option>';
                    bairroSelect.innerHTML += '<option value="Sítios de Recreio Independência">Sítios de Recreio Independência</option>';
                    bairroSelect.innerHTML += '<option value="Vila Bressan">Vila Bressan</option>';
                    bairroSelect.innerHTML += '<option value="Vila Candinha">Vila Candinha</option>';
                    bairroSelect.innerHTML += '<option value="Vila Dona Rosa Zurita">Vila Dona Rosa Zurita</option>';
                    bairroSelect.innerHTML += '<option value="Vila Europa">Vila Europa</option>';
                    bairroSelect.innerHTML += '<option value="Vila Madalena de Canossa">Vila Madalena de Canossa</option>';
                    bairroSelect.innerHTML += '<option value="Vila Michelin">Vila Michelin</option>';
                    bairroSelect.innerHTML += '<option value="Vila Pastorello">Vila Pastorello</option>';
                    bairroSelect.innerHTML += '<option value="Vila Queiroz">Vila Queiroz</option>';
                    bairroSelect.innerHTML += '<option value="Vila Rodini">Vila Rodini</option>';
                    bairroSelect.innerHTML += '<option value="Vila Santo Antônio">Vila Santo Antônio</option>';
                    bairroSelect.innerHTML += '<option value="Vila São Jorge">Vila São Jorge</option>';
                    break;
                case 'Leme':
                    bairroSelect.innerHTML += '<option value="Arcindo Rinaldi">Arcindo Rinaldi</option>';
                    bairroSelect.innerHTML += '<option value="Área Rural de Leme">Área Rural de Leme</option>';
                    bairroSelect.innerHTML += '<option value="Barra Funda">Barra Funda</option>';
                    bairroSelect.innerHTML += '<option value="Centro">Centro</option>';
                    bairroSelect.innerHTML += '<option value="Chácara Saúde">Chácara Saúde</option>';
                    bairroSelect.innerHTML += '<option value="Cidade Jardim">Cidade Jardim</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Habitacional Ferdinando Marchi">Conjunto Habitacional Ferdinando Marchi</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Habitacional Francisco Coelho">Conjunto Habitacional Francisco Coelho</option>';
                    bairroSelect.innerHTML += '<option value="Conjunto Habitacional Victório Bonfanti">Conjunto Habitacional Victório Bonfanti</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Davi Comi">Desmembramento Davi Comi</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Orlando Anteghini">Desmembramento Orlando Anteghini</option>';
                    bairroSelect.innerHTML += '<option value="Desmembramento Vila Nova">Desmembramento Vila Nova</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial">Distrito Industrial</option>';
                    bairroSelect.innerHTML += '<option value="Estrada Municipal Caju">Estrada Municipal Caju</option>';
                    bairroSelect.innerHTML += '<option value="Estrada Particular Ibicatu">Estrada Particular Ibicatu</option>';
                    bairroSelect.innerHTML += '<option value="Fazenda Capitólio">Fazenda Capitólio</option>';
                    bairroSelect.innerHTML += '<option value="Fazenda Palmeiras">Fazenda Palmeiras</option>';
                    bairroSelect.innerHTML += '<option value="Fazenda Santo Antônio">Fazenda Santo Antônio</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Adelina">Jardim Adelina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Alto da Boa Vista">Jardim Alto da Boa Vista</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Alto da Glória">Jardim Alto da Glória</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Alvorada">Jardim Alvorada</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Amália">Jardim Amália</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Ana Lúcia">Jardim Ana Lúcia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Angélica">Jardim Angélica</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Anteghini">Jardim Anteghini</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Ariana">Jardim Ariana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Bonsucesso">Jardim Bonsucesso</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Capitólio">Jardim Capitólio</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Casarão">Jardim Casarão</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Clube do Bosque">Jardim Clube do Bosque</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Coloninha Cláudia">Jardim Coloninha Cláudia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Dibi">Jardim Dibi</option>';
                    bairroSelect.innerHTML += '<option value="Jardim do Bosque">Jardim do Bosque</option>';
                    bairroSelect.innerHTML += '<option value="Jardim do Sol">Jardim do Sol</option>';
                    bairroSelect.innerHTML += '<option value="Jardim dos Ypês">Jardim dos Ypês</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Eldorado">Jardim Eldorado</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Eloísa">Jardim Eloísa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Empyreo">Jardim Empyreo</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Eroísi">Jardim Eroísi</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Florença">Jardim Florença</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Governador">Jardim Governador</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Graminha">Jardim Graminha</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Imperial">Jardim Imperial</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Imperial II">Jardim Imperial II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Isabel Cristina">Jardim Isabel Cristina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Jequitibá">Jardim Jequitibá</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Juana">Jardim Juana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Letícia">Jardim Letícia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Lívia">Jardim Lívia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Era">Jardim Nova Era</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Granada">Jardim Nova Granada</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Leme">Jardim Nova Leme</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Santa Rita">Jardim Nova Santa Rita</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Novo Horizonte">Jardim Novo Horizonte</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Palmeiras">Jardim Palmeiras</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Portal do Bosque">Jardim Portal do Bosque</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Presidente">Jardim Presidente</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Primavera">Jardim Primavera</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Renascença">Jardim Renascença</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Altos da Santa Rita">Jardim Residencial Altos da Santa Rita</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Cambuhy">Jardim Residencial Cambuhy</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Crishmara">Jardim Residencial Crishmara</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Mariana">Jardim Residencial Mariana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Pavani">Jardim Residencial Pavani</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Quáglia">Jardim Residencial Quáglia</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Santa Maria">Jardim Residencial Santa Maria</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Residencial Saulo">Jardim Residencial Saulo</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Royal Ville">Jardim Royal Ville</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Inês">Jardim Santa Inês</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Marta">Jardim Santa Marta</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Paula">Jardim Santa Paula</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santa Rita">Jardim Santa Rita</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Santana">Jardim Santana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Francisco">Jardim São Francisco</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Joaquim">Jardim São Joaquim</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São José">Jardim São José</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Rafael">Jardim São Rafael</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Serelepe">Jardim Serelepe</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Silvana">Jardim Silvana</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Travagin">Jardim Travagin</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Tufanin">Jardim Tufanin</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Universitário">Jardim Universitário</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Vanessa">Jardim Vanessa</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Vila Rica">Jardim Vila Rica</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Vila Suíça">Jardim Vila Suíça</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Vila Verde">Jardim Vila Verde</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Vista Alegre">Jardim Vista Alegre</option>';
                    bairroSelect.innerHTML += '<option value="Loteamento Grossklauss">Loteamento Grossklauss</option>';
                    bairroSelect.innerHTML += '<option value="Morada Sol">Morada Sol</option>';
                    bairroSelect.innerHTML += '<option value="Parque Residencial Itamaraty">Parque Residencial Itamaraty</option>';
                    bairroSelect.innerHTML += '<option value="Parque São Manoel">Parque São Manoel</option>';
                    bairroSelect.innerHTML += '<option value="Pólo Industrial Paulo Kinock II">Pólo Industrial Paulo Kinock II</option>';
                    bairroSelect.innerHTML += '<option value="Quinta do Vale Verde">Quinta do Vale Verde</option>';
                    bairroSelect.innerHTML += '<option value="Recanto da Colina">Recanto da Colina</option>';
                    bairroSelect.innerHTML += '<option value="Recanto do Sol">Recanto do Sol</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Rocco Lenci">Residencial Rocco Lenci</option>';
                    bairroSelect.innerHTML += '<option value="Retiro Velho">Retiro Velho</option>';
                    bairroSelect.innerHTML += '<option value="Serelepe">Serelepe</option>';
                    bairroSelect.innerHTML += '<option value="Taquari">Taquari</option>';
                    bairroSelect.innerHTML += '<option value="Taquari Ponte">Taquari Ponte</option>';
                    bairroSelect.innerHTML += '<option value="Vale Verde">Vale Verde</option>';
                    bairroSelect.innerHTML += '<option value="Vila Bancária">Vila Bancária</option>';
                    bairroSelect.innerHTML += '<option value="Vila Bela Vista">Vila Bela Vista</option>';
                    bairroSelect.innerHTML += '<option value="Vila Bom Jesus">Vila Bom Jesus</option>';
                    bairroSelect.innerHTML += '<option value="Vila Hilsdorf">Vila Hilsdorf</option>';
                    bairroSelect.innerHTML += '<option value="Vila Joest">Vila Joest</option>';
                    bairroSelect.innerHTML += '<option value="Vila Nova">Vila Nova</option>';
                    bairroSelect.innerHTML += '<option value="Vila Rauter">Vila Rauter</option>';
                    bairroSelect.innerHTML += '<option value="Vila Santa Maria">Vila Santa Maria</option>';
                    bairroSelect.innerHTML += '<option value="Vila Santucci">Vila Santucci</option>';
                    bairroSelect.innerHTML += '<option value="Vila São João">Vila São João</option>';
                    bairroSelect.innerHTML += '<option value="Vila São Jorge">Vila São Jorge</option>';
                    bairroSelect.innerHTML += '<option value="Vila Sumaré">Vila Sumaré</option>';
                    bairroSelect.innerHTML += '<option value="Vila Terezinha">Vila Terezinha</option>';
                    bairroSelect.innerHTML += '<option value="Vila Zarif">Vila Zarif</option>';
                    bairroSelect.innerHTML += '<option value="Villagio DItalia">Villagio DItalia</option>';
                    break;
                case 'Rio Claro':
                    bairroSelect.innerHTML += '<option value="Arco-Íris">Arco-Íris</option>';
                    bairroSelect.innerHTML += '<option value="Bairro do Estádio">Bairro do Estádio</option>';
                    bairroSelect.innerHTML += '<option value="Bandeirante I">Bandeirante I</option>';
                    bairroSelect.innerHTML += '<option value="Bandeirante II">Bandeirante II</option>';
                    bairroSelect.innerHTML += '<option value="Bela Vista">Bela Vista</option>';
                    bairroSelect.innerHTML += '<option value="Benjamin de Castro">Benjamin de Castro</option>';
                    bairroSelect.innerHTML += '<option value="Boa Esperança">Boa Esperança</option>';
                    bairroSelect.innerHTML += '<option value="Boa Morte">Boa Morte</option>';
                    bairroSelect.innerHTML += '<option value="Boa Vista">Boa Vista</option>';
                    bairroSelect.innerHTML += '<option value="Bonsucesso">Bonsucesso</option>';
                    bairroSelect.innerHTML += '<option value="Brasília I">Brasília I</option>';
                    bairroSelect.innerHTML += '<option value="Brasília II">Brasília II</option>';
                    bairroSelect.innerHTML += '<option value="Centro">Centro</option>';
                    bairroSelect.innerHTML += '<option value="Chácara Boa Vista">Chácara Boa Vista</option>';
                    bairroSelect.innerHTML += '<option value="Chácara Lusa">Chácara Lusa</option>';
                    bairroSelect.innerHTML += '<option value="Cidade Claret">Cidade Claret</option>';
                    bairroSelect.innerHTML += '<option value="Cidade Jardim">Cidade Jardim</option>';
                    bairroSelect.innerHTML += '<option value="Cidade Nova">Cidade Nova</option>';
                    bairroSelect.innerHTML += '<option value="Consolação">Consolação</option>';
                    bairroSelect.innerHTML += '<option value="Copacabana">Copacabana</option>';
                    bairroSelect.innerHTML += '<option value="Distrito Industrial">Distrito Industrial</option>';
                    bairroSelect.innerHTML += '<option value="Florença">Florença</option>';
                    bairroSelect.innerHTML += '<option value="Floridiana">Floridiana</option>';
                    bairroSelect.innerHTML += '<option value="Granja Regina">Granja Regina</option>';
                    bairroSelect.innerHTML += '<option value="Guanabara I">Guanabara I</option>';
                    bairroSelect.innerHTML += '<option value="Guanabara II">Guanabara II</option>';
                    bairroSelect.innerHTML += '<option value="Inocoop">Inocoop</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Alfredo Karan">Jardim Alfredo Karan</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Anhanguera">Jardim Anhanguera</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Araucária">Jardim Araucária</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Azul">Jardim Azul</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Bela Vista">Jardim Bela Vista</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Botânico">Jardim Botânico</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Centenário">Jardim Centenário</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Cherveson">Jardim Cherveson</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Cidade Azul">Jardim Cidade Azul</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Claret">Jardim Claret</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Condutta">Jardim Condutta</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Flores">Jardim das Flores</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Paineiras">Jardim das Paineiras</option>';
                    bairroSelect.innerHTML += '<option value="Jardim das Palmeiras">Jardim das Palmeiras</option>';
                    bairroSelect.innerHTML += '<option value="Jardim do Ipê">Jardim do Ipê</option>';
                    bairroSelect.innerHTML += '<option value="Jardim do Trevo">Jardim do Trevo</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Donangela">Jardim Donangela</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Esmeralda">Jardim Esmeralda</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Hipódromo">Jardim Hipódromo</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Independência">Jardim Independência</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Inocoop">Jardim Inocoop</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Ipanema">Jardim Ipanema</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Itapuã">Jardim Itapuã</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Leblon">Jardim Leblon</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Maria Cristina">Jardim Maria Cristina</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Matheus Maniero">Jardim Matheus Maniero</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Mirassol">Jardim Mirassol</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Nova Rio Claro">Jardim Nova Rio Claro</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Novo I">Jardim Novo I</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Novo II">Jardim Novo II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Novo Horizonte">Jardim Novo Horizonte</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Parque Residencial">Jardim Parque Residencial</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Paulista I">Jardim Paulista I</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Paulista II">Jardim Paulista II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Porto Fino">Jardim Porto Fino</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Portugal">Jardim Portugal</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Primavera">Jardim Primavera</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Progresso">Jardim Progresso</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Progresso II">Jardim Progresso II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Quitandinha">Jardim Quitandinha</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Rio Claro">Jardim Rio Claro</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Caetano">Jardim São Caetano</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São João">Jardim São João</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Paulo I">Jardim São Paulo I</option>';
                    bairroSelect.innerHTML += '<option value="Jardim São Paulo II">Jardim São Paulo II</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Shangrilá">Jardim Shangrilá</option>';
                    bairroSelect.innerHTML += '<option value="Jardim Village">Jardim Village</option>';
                    bairroSelect.innerHTML += '<option value="Kennedy">Kennedy</option>';
                    bairroSelect.innerHTML += '<option value="Mãe Preta">Mãe Preta</option>';
                    bairroSelect.innerHTML += '<option value="Nossa Senhora da Saúde">Nossa Senhora da Saúde</option>';
                    bairroSelect.innerHTML += '<option value="Nova Veneza">Nova Veneza</option>';
                    bairroSelect.innerHTML += '<option value="Novo Wenzel">Novo Wenzel</option>';
                    bairroSelect.innerHTML += '<option value="Olímpico">Olímpico</option>';
                    bairroSelect.innerHTML += '<option value="Orestes Armando Giovanni">Orestes Armando Giovanni</option>';
                    bairroSelect.innerHTML += '<option value="Panorama">Panorama</option>';
                    bairroSelect.innerHTML += '<option value="Parque das Indústrias">Parque das Indústrias</option>';
                    bairroSelect.innerHTML += '<option value="Parque dos Eucaliptos">Parque dos Eucaliptos</option>';
                    bairroSelect.innerHTML += '<option value="Parque São Jorge">Parque São Jorge</option>';
                    bairroSelect.innerHTML += '<option value="Parque São Conrado">Parque São Conrado</option>';
                    bairroSelect.innerHTML += '<option value="Parque Universitário">Parque Universitário</option>';
                    bairroSelect.innerHTML += '<option value="Recanto Paraíso">Recanto Paraíso</option>';
                    bairroSelect.innerHTML += '<option value="Recanto São Carlos">Recanto São Carlos</option>';
                    bairroSelect.innerHTML += '<option value="Recreio das Águas Claras">Recreio das Águas Claras</option>';
                    bairroSelect.innerHTML += '<option value="Residencial Campestre Vila Rica">Residencial Campestre Vila Rica</option>';
                    bairroSelect.innerHTML += '<option value="Residencial dos Bosques">Residencial dos Bosques</option>';
                    bairroSelect.innerHTML += '<option value="Saibreiro">Saibreiro</option>';
                    bairroSelect.innerHTML += '<option value="Santa Clara">Santa Clara</option>';
                    bairroSelect.innerHTML += '<option value="Santa Cruz">Santa Cruz</option>';
                    bairroSelect.innerHTML += '<option value="Santa Eliza">Santa Eliza</option>';
                    bairroSelect.innerHTML += '<option value="Santa Maria">Santa Maria</option>';
                    bairroSelect.innerHTML += '<option value="Santana">Santana</option>';
                    bairroSelect.innerHTML += '<option value="São Benedito">São Benedito</option>';
                    bairroSelect.innerHTML += '<option value="São José">São José</option>';
                    bairroSelect.innerHTML += '<option value="São Miguel">São Miguel</option>';
                    bairroSelect.innerHTML += '<option value="Terra Nova">Terra Nova</option>';
                    bairroSelect.innerHTML += '<option value="Vila Alemã">Vila Alemã</option>';
                    bairroSelect.innerHTML += '<option value="Vila Anhanguera">Vila Anhanguera</option>';
                    bairroSelect.innerHTML += '<option value="Vila Aparecida">Vila Aparecida</option>';
                    bairroSelect.innerHTML += '<option value="Vila Bela">Vila Bela</option>';
                    bairroSelect.innerHTML += '<option value="Vila Cristina">Vila Cristina</option>';
                    bairroSelect.innerHTML += '<option value="Vila do Rádio">Vila do Rádio</option>';
                    bairroSelect.innerHTML += '<option value="Vila Elizabeth (B.N.H.)">Vila Elizabeth (B.N.H.)</option>';
                    bairroSelect.innerHTML += '<option value="Vila Horto Florestal">Vila Horto Florestal</option>';
                    bairroSelect.innerHTML += '<option value="Vila Indaiá">Vila Indaiá</option>';
                    bairroSelect.innerHTML += '<option value="Vila Industrial">Vila Industrial</option>';
                    bairroSelect.innerHTML += '<option value="Vila Martins">Vila Martins</option>';
                    bairroSelect.innerHTML += '<option value="Vila Nova">Vila Nova</option>';
                    bairroSelect.innerHTML += '<option value="Vila Olinda">Vila Olinda</option>';
                    bairroSelect.innerHTML += '<option value="Vila Operária">Vila Operária</option>';
                    bairroSelect.innerHTML += '<option value="Vila Paulista">Vila Paulista</option>';
                    bairroSelect.innerHTML += '<option value="Vila Romana">Vila Romana</option>';
                    bairroSelect.innerHTML += '<option value="Vila Santo Antônio">Vila Santo Antônio</option>';
                    bairroSelect.innerHTML += '<option value="Vila São José">Vila São José</option>';
                    bairroSelect.innerHTML += '<option value="Vila Verde">Vila Verde</option>';
                    bairroSelect.innerHTML += '<option value="Wenzel">Wenzel</option>';
                    break;
                default:
                    break;
            }
        });
    </script>
</body>
</html>