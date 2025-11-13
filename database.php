<?php
require_once 'config.php';
// session_start();

// Singleton de conexão com o banco
$_pdo_connection = null;

function db()
{
  global $_pdo_connection;
  
  // Se já existe conexão válida, retorna
  if ($_pdo_connection !== null) {
    try {
      $_pdo_connection->query('SELECT 1');
      return $_pdo_connection;
    } catch (PDOException $e) {
      $_pdo_connection = null;
    }
  }
  
  // Define timeout de conexão curto para evitar travamento
  $originalTimeout = ini_get('default_socket_timeout');
  ini_set('default_socket_timeout', 5); // 5 segundos de timeout
  
  try {
    $_pdo_connection = new PDO(
      "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
      DB_USER,
      DB_PASS,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false
      ]
    );
    ini_set('default_socket_timeout', $originalTimeout);
    return $_pdo_connection;
  } catch (PDOException $e) {
    ini_set('default_socket_timeout', $originalTimeout);
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    
    // Retorna erro sem tentar novamente (para não ultrapassar timeout)
    throw new Exception("Falha na conexão com o banco de dados: " . $e->getMessage());
  }
}

function saveUser($googleId, $name, $email, $picture)
{
  $pdo = db();

  $stmt = $pdo->prepare("
        INSERT INTO professores (google_id, nome, email, foto) 
        VALUES (:google_id, :nome, :email, :foto)
        ON DUPLICATE KEY UPDATE 
            nome = :nome, 
            foto = :foto,
            ultimo_login = CURRENT_TIMESTAMP
    ");

  $stmt->execute([
    ':google_id' => $googleId,
    ':nome' => $name,
    ':email' => $email,
    ':foto' => $picture
  ]);

  // Buscar ID com prepared statement ao invés de SQL injection
  $selectStmt = $pdo->prepare("SELECT id FROM professores WHERE google_id = :google_id");
  $selectStmt->execute([':google_id' => $googleId]);
  $result = $selectStmt->fetchColumn();
  
  return $result ?: $pdo->lastInsertId();
}

function getUserById($id)
{
  $pdo = db();
  $stmt = $pdo->prepare("SELECT * FROM professores WHERE id = :id");
  $stmt->execute([':id' => $id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
