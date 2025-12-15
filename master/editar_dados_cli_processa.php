<?php
if(isset($_POST['cliente_id'])) {
    // Obter o ID do cliente
    $cliente_id = $_POST['cliente_id'];
    try {
        require_once __DIR__ . '/../db/Conexao.php';
        $query = $connect->prepare("SELECT COUNT(*) AS total_cobrancas FROM financeiro2 WHERE idc = :cliente_id AND status = 1");
        $query->bindParam(':cliente_id', $cliente_id);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if($result['total_cobrancas'] > 0) {
            echo 'nao_permitir';
        } else {
            echo 'permitir';
        }
    } catch(PDOException $e) {
        echo 'Erro ao verificar cobrança: ' . $e->getMessage();
    }
} else {
    echo 'ID do cliente não recebido.';
}
?>