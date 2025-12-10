    <?php require_once "src/Services/AutenticarServico.php"; ?>
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/cabecalho.css">
    <link rel="stylesheet" href="css/rodape.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="shortcut icon" href="img/logo_YeahDB.png" type="image/x-icon">
    <script src="js/menu-mobile.js" defer></script>


    </head>

    <body>
        <header id="cabecalho">
            <nav id="menu_principal">
                <a href="index.php" class="logo_site">
                    <img src="img/logo_YeahDB.png" alt="Logo YeahDB" />
                </a>
                <ul class="lista_menu">
                    <li><a href="pagina_eventos.php">EVENTOS</a></li>
                    <li><a href="pagina_categorias.php">CATEGORIAS</a></li>
                    <li><a href="pagina_artistas.php">ARTISTAS</a></li>
                </ul>
                <?php
                AutenticarServico::iniciarSecao();
                if (!isset($_SESSION['id'])) {
                ?>
                    <div id="login-cadastro">
                        <a href="cadastrar.php" class="botao_acesso">CADASTRAR</a>
                        <a href="login.php" class="botao_acesso">ACESSAR</a>
                    </div>
                <?php } else { ?>

                    <button class="btn btn-primary"id="burger-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                        <div></div>
                        <div></div>
                        <div></div>
                    </button>

                    <!-- Offcanvas -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasRightLabel">
                                <p>Olá <span><?= htmlspecialchars($_SESSION['nome']) ?></span> </p>
                            </h5>
                        </div>
                        <div class="offcanvas-body">
                            <a href="minha_conta.php">MINHA CONTA</a>
                        </div>
                    </div>
                <?php } ?>
            </nav>
        </header>

        <?php

        ?>