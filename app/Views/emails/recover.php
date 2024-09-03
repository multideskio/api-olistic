<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de conta</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #fff; color: #FFFFFF; margin: 0; padding-top: 30px; padding-bottom: 30px;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #444444; border-radius: 8px; padding: 20px;">
        <tr>
            <td style="text-align: center; padding-bottom: 20px; padding-top: 30px;">
                <h1 style="margin: 0; color: #FFFFFF; font-size: 22px; font-weight: 900;">Recuperação de conta!</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; font-size: 14px;">
                <p style="margin: 0 0 10px;">Olá <?= $name ?>,</p>
                <p style="margin: 0 0 10px;">Para alterar sua senha, clique no botão a baixo, caso você não tenha solicitado, desconsidere esse e-mail.</p>
                <p style="margin: 0;">
                    <a href="<?= site_url('auth/createpass/'.$token) ?>" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #FFFFFF; background-color: #1E90FF; text-decoration: none; border-radius: 5px; text-align: center;">Criar senha</a>
                </p>
                <a href="<?= site_url('auth/createpass/'.$token) ?>" style="color: #888888 !important; font-size: 12px;">
                    <?= site_url('auth/createpass/'.$token) ?>
                </a>
                <p style="margin: 20px 0 10px;">Caso tenha qualquer dúvida ou precise de ajuda, nossa equipe de suporte está sempre disponível para assisti-lo. Basta responder a este e-mail ou visitar nossa <a href="[URL da Central de Ajuda]" style="color: #1E90FF; text-decoration: none;">Central de Ajuda</a>.</p>
                <p style="margin: 0;">Agradecemos por se juntar a nós!</p>
                <p style="margin: 20px 0 0;">Atenciosamente, <br>Equipe [Nome da Empresa]</p>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding-top: 20px; color: #888888;">
                <p style="margin: 0;">Detalhes do Contato:</p>
                <p style="margin: 0;">[Nome da Empresa] <br> [Endereço da Empresa] <br> E-mail de Suporte: [E-mail de Suporte] <br> Telefone: [Telefone]</p>
                <p style="margin: 0; font-size: 12px;"><small>Este é um e-mail automático, por favor não responda a esta mensagem.</small></p>
            </td>
        </tr>
    </table>
</body>
</html>
