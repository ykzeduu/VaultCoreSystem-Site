<?php
/**
 * Processa o upload de uma imagem enviada por <input type="file">.
 *
 * @param string $campo   Nome do campo do formulário (ex: "imagem")
 * @param string $pasta   Pasta destino, relativa à raiz do site (ex: "assets/uploads/produtos")
 * @return array          ['ok' => bool, 'caminho' => string|null, 'erro' => string|null]
 */
function processarUploadImagem(string $campo, string $pasta): array
{
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'caminho' => null, 'erro' => null]; // nenhum arquivo enviado, não é erro
    }

    $arquivo = $_FILES[$campo];

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'caminho' => null, 'erro' => 'Falha ao enviar o arquivo. Tente novamente.'];
    }

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
    ];

    $tipoReal = mime_content_type($arquivo['tmp_name']);
    if (!isset($tiposPermitidos[$tipoReal])) {
        return ['ok' => false, 'caminho' => null, 'erro' => 'Formato de imagem não suportado. Use JPG, PNG, WEBP ou SVG.'];
    }

    $tamanhoMaximo = 5 * 1024 * 1024; // 5 MB
    if ($arquivo['size'] > $tamanhoMaximo) {
        return ['ok' => false, 'caminho' => null, 'erro' => 'A imagem é muito grande (máximo 5 MB).'];
    }

    $extensao = $tiposPermitidos[$tipoReal];
    $nomeUnico = uniqid('img_', true) . '.' . $extensao;

    $pastaAbsoluta = __DIR__ . '/../' . $pasta;
    if (!is_dir($pastaAbsoluta)) {
        mkdir($pastaAbsoluta, 0775, true);
    }

    $destino = rtrim($pastaAbsoluta, '/') . '/' . $nomeUnico;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        return ['ok' => false, 'caminho' => null, 'erro' => 'Não foi possível salvar a imagem no servidor.'];
    }

    return ['ok' => true, 'caminho' => rtrim($pasta, '/') . '/' . $nomeUnico, 'erro' => null];
}
