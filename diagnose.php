<?php
echo "<h2>Diagnóstico do Sistema SAE</h2>";

// Teste 1: Variáveis de ambiente
echo "<h3>1. Variáveis de Ambiente</h3>";
require_once 'config.php';
echo "<pre>";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_PASS: " . (DB_PASS ? '***' : '(vazio)') . "\n";
echo "GOOGLE_CLIENT_ID: " . (GOOGLE_CLIENT_ID ? substr(GOOGLE_CLIENT_ID, 0, 10) . '...' : '(não definido)') . "\n";
echo "</pre>";

// Teste 2: Conectividade de socket
echo "<h3>2. Teste de Socket</h3>";
$socket = @fsockopen(DB_HOST, 3306, $errno, $errstr, 5);
if ($socket) {
    echo "✅ Socket conectado com sucesso em " . DB_HOST . ":3306\n";
    fclose($socket);
} else {
    echo "❌ Falha ao conectar no socket: $errstr ($errno)\n";
}

// Teste 3: Conexão com MySQL via PDO
echo "<h3>3. Teste de Conexão PDO</h3>";
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Conexão PDO estabelecida com sucesso!\n";
    
    // Teste se consegue listar bancos
    $result = $pdo->query("SHOW DATABASES;");
    $databases = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    echo "Bancos de dados disponíveis:\n";
    foreach ($databases as $db) {
        echo "  - $db\n";
    }
    echo "</pre>";
    
    // Testa conexão com banco específico
    if (in_array(DB_NAME, $databases)) {
        echo "✅ Banco '" . DB_NAME . "' existe\n";
        
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Listar tabelas
        $result = $pdo->query("SHOW TABLES;");
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        echo "<pre>";
        echo "Tabelas no banco:\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
        echo "</pre>";
        
    } else {
        echo "❌ Banco '" . DB_NAME . "' não existe\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro na conexão PDO:\n";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

// Teste 4: Teste da função db() customizada
echo "<h3>4. Teste da Função db()</h3>";
try {
    require_once 'src/database.php';
    $conn = db();
    $result = $conn->query("SELECT 1");
    echo "✅ Função db() funcionando!\n";
} catch (Exception $e) {
    echo "❌ Erro na função db():\n";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

echo "<h3>✅ Diagnóstico Completo</h3>";
?>
