<?php require 'pages/header.php'; ?>
<?php if (empty($_SESSION['cLogin'])) {
    ?>
    <script type="text/javascript">window.location = 'login.php'</script>
    <?php
    exit;
}
?>
<div class="container">
    <h1>Meus Anúncios</h1>

    <a href="add-anuncio.php" class="btn btn-default">Adicionar Anuncio</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Titulo</th>
                <th>Valor</th>
                <th>Açoes</th>
            </tr>
        </thead>
        <?php 
            require 'classes/anuncios.class.php';
            $a = new Anuncios();
            $anuncios = $a->getMeusAnuncios();

            foreach ($anuncios as $anuncio):
        ?>
            <tr>
                <td>
                    <?php if (isset($anuncio['link'])):?>
                        <img src="assets/images/anuncios/<?php echo $anuncio['link']; ?>" border="0" height="50">
                    <?php else: ?>
                        <img src="assets/images/default.jpg" border="0" height="50">
                    <?php endif; ?>
                </td>
                <td> <?php echo $anuncio['titulo'] ?></td>
                <td>R$ <?php echo number_format($anuncio['valor'], 2); ?></td>
                <td>
                    <a href="editar-anuncio.php?id=<?php echo $anuncio['id']; ?>" class="btn btn-warning">Editar</a>
                    <a href="excluir-anuncio.php?id=<?php echo $anuncio['id']; ?>" class="btn btn-danger">Excluir</a>
                </td>
            </tr>

            <?php endforeach; ?>
    </table>


</div>
<?php require 'pages/footer.php' ?>