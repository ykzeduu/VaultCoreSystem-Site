<?php
/**
 * INSTALADOR — roda uma única vez para criar as tabelas no banco.
 *
 * Como usar:
 * 1. Configure as variáveis de ambiente no Render (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD)
 * 2. Defina também uma variável SETUP_KEY com uma senha só sua, ex: "abc123"
 * 3. Depois do deploy, acesse: https://seu-site.onrender.com/setup-db.php?key=abc123
 * 4. Se der tudo certo, vai aparecer "Tabelas criadas com sucesso"
 * 5. IMPORTANTE: depois de rodar com sucesso, APAGUE este arquivo do projeto
 *    (ou pelo menos troque o SETUP_KEY), pra ninguém mais conseguir acessar essa URL.
 */

$chaveEsperada = getenv('SETUP_KEY');
$chaveRecebida = $_GET['key'] ?? '';

if (!$chaveEsperada || $chaveRecebida !== $chaveEsperada) {
    http_response_code(403);
    die('Acesso negado. Configure a variável de ambiente SETUP_KEY e acesse com ?key=SUACHAVE');
}

/* Conecta SEM especificar o banco ainda, porque ele pode nem existir de fato */
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '4000';
$dbName = getenv('DB_NAME') ?: 'vaultcore';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        ]
    );
} catch (PDOException $e) {
    die('Erro ao conectar no servidor do banco: ' . $e->getMessage());
}

/* Cria o banco se ainda não existir, e passa a usá-lo */
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
$pdo->exec("USE `{$dbName}`");
echo "<p style='font-family:sans-serif'>Banco '{$dbName}' pronto (criado agora ou já existia).</p>";

$schemaPath = __DIR__ . '/schema.sql';
if (!file_exists($schemaPath)) {
    die('Arquivo schema.sql não encontrado.');
}

$sql = file_get_contents($schemaPath);

// Remove comentários de linha e divide em comandos separados por ;
$sql = preg_replace('/--.*$/m', '', $sql);
$comandos = array_filter(array_map('trim', explode(';', $sql)));

echo "<pre style='font-family:monospace;padding:20px;'>";
$sucesso = 0;
foreach ($comandos as $comando) {
    if ($comando === '') continue;
    try {
        $pdo->exec($comando);
        echo "OK: " . substr($comando, 0, 60) . "...\n";
        $sucesso++;
    } catch (PDOException $e) {
        echo "ERRO em: " . substr($comando, 0, 60) . "...\n";
        echo "   -> " . $e->getMessage() . "\n";
    }
}
echo "</pre>";
echo "<h2 style='font-family:sans-serif'>Concluído: {$sucesso} comando(s) executado(s).</h2>";
echo "<p style='font-family:sans-serif;color:red'><b>Agora apague este arquivo (setup-db.php) do projeto por segurança.</b></p>";
