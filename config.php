<?php
/**
 * Conexão única com o banco de dados (TiDB Serverless - compatível com MySQL).
 *
 * NUNCA coloque usuário/senha direto aqui. Configure como variáveis de
 * ambiente no Render (Dashboard > seu serviço > Environment):
 *
 *   DB_HOST     = gateway01.xx-xxxxx.prod.aws.tidbcloud.com
 *   DB_PORT     = 4000
 *   DB_NAME     = vaultcore
 *   DB_USER     = xxxxxxx.root
 *   DB_PASSWORD = ********
 *
 * Esses valores você pega no painel do TiDB Cloud, em "Connect".
 * O TiDB Serverless exige SSL, por isso o certificado abaixo é habilitado.
 */

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '4000';
$dbName = getenv('DB_NAME') ?: 'vaultcore';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_SSL_CA       => true, // TiDB Serverless exige conexão TLS
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    // Em produção não mostramos o erro real na tela, só registramos.
    error_log('Erro de conexão com o banco: ' . $e->getMessage());
    die('<h2 style="font-family:sans-serif;text-align:center;margin-top:80px">
        Não foi possível conectar ao banco de dados.<br>
        <small style="color:#888">Verifique as variáveis de ambiente DB_HOST, DB_USER, DB_PASSWORD no Render.</small>
    </h2>');
}
