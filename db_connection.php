<?php
// db_connection.php
$host = 'mysql.railway.internal';
$port = 3306;
$dbname = 'railway';
$user = 'root';
$password = 'kUkYvcPXnXvcQdwiTUPAQLmgIiwnFvfC'; // You can replace this with getenv('MYSQLPASSWORD')

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: echo "Connected successfully";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
