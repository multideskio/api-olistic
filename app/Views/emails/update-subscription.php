<h1>Conta atualizada!</h1>
<p style="margin: 0 0 10px;">Olá <?= htmlspecialchars($name) ?>,</p>
<p style="margin: 0 0 10px;">Sua conta foi atualizada.</p>
<p style="margin: 0 0 10px;">Abaixo estão os detalhes da sua nova conta:</p>
<p style="margin: 0 0 10px;"><strong>Nome de Usuário:</strong> <?= htmlspecialchars($email) ?></p>
<p style="margin: 0 0 10px;"><strong>Link da plataforma:</strong> <a href="<?= site_url() ?>" class="support-link"><?= site_url() ?></a></p>
<p style="margin: 0;">
    <a href="<?= site_url('api/v1/login?magiclink=' . $token) ?>" class="btn-primary">ACESSAR SUA CONTA</a>
</p>
<p style="margin: 20px 0 10px;">Caso tenha qualquer dúvida ou precise de ajuda, nossa equipe de suporte está sempre disponível para assisti-lo. Basta responder a este e-mail ou visitar nossa <a href="[URL da Central de Ajuda]" class="support-link">Central de Ajuda</a>.</p>
<p style="margin: 0;">Agradecemos por se juntar a nós!</p>
<p style="margin: 20px 0 0;">Atenciosamente, <br>Equipe [Nome da Empresa]</p>