    <?php require_once "src/Services/AutenticarServico.php";?>
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/cabecalho.css">
    <link rel="stylesheet" href="css/rodape.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">


    </head>

    <body>
        <header id="cabecalho">
            <nav id="menu_principal">
                <a href="index.php" class="logo_site">
                    <img src="img/logo_YeahDB.png" alt="Logo YeahDB" />
                </a>
                <ul class="lista_menu">
                    <li><a href="#">EVENTOS</a></li>
                    <li><a href="#">CATEGORIAS</a></li>
                    <li><a href="#">ARTISTAS</a></li>
                </ul>
                <?php
                AutenticarServico::iniciarSecao();
                if (!isset($_SESSION['id'])) {
                ?>
                    <div>
                        <a href="cadastrar.php" class="botao_acesso">CADASTRAR</a>
                        <a href="login.php" class="botao_acesso">ACESSAR</a>
                    </div>
                <?php } else { ?>
                    <div id="meu-perfil">
                        <p>Olá <?= $_SESSION['nome'] ?>, </p>
                        <a href="minha_conta.php">MINHA CONTA</a>
                    </div>
                    <?php } ?>
            </nav>
        </header>

        <?php

        ?>