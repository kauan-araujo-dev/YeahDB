<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YeahDB</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/artista.css">
<?php require_once "includes/cabecalho.php" ?>
    
    <h1>TOGURO E MACAXEIRA</h1>
    <section class="banda">

        <div class="imagem_banda">
            
            <img src="img/dupla_sertaneja.jpg" alt="imagem de TOGURO E MACAXEIRA">

        </div>


        <div class="estilo_da_banda">

            <div>
                <p><b>REGIÃO: </b>São Paulo-SP</p>
            
            <p><b>ESTILOS MUSICAIS: </b>SERTANEJO-MPB</p>
            
            <p>-BLUES</p>
            
            <p><b>CONTATO: </b>@email.com</p>

            <div class="redes_sociais">
                <a href="" target="_blank" class="rede"><i class="fab fa-instagram"></i></a>
                <a href="" target="_blank" class="rede"><i class="fab fa-facebook-f"></i></a>
            </div>
            </div>
            <div class="botao_whatsapp">
                <a href="" target="_blank">
                    FALE COM O ARTISTA <i class="fab fa-whatsapp"></i>
                </a>

            </div>
            <p class="cache">CACHÊ: <span class="valor">$1000,00</span></p>





        </div>
    </section>

    <section id="sobre_a_banda">
        <p class="sobre">
                <b>SOBRE A BANDA</b>
            </p>
            <p class="texto_banda">
                Um encontro único que celebra música, arte e boas energias em um cenário inesquecível.
                Ao som de grandes artistas e com uma atmosfera vibrante, o festival oferece experiências que vão além do
                palco:
                momentos de conexão, cultura e diversão para todas as idades.Prepare-se para curtir o pôr do sol mais
                incrível da sua vida
                ,acompanhado de muita música, gastronomia e atividades especiais que tornam esse evento inesquecível.
            </p>
    </section>

    <div id="conteudo-principal">
        <section id="galeria-artista">
            <div id="bloco-texto">
                <h2 id="titulo-secao">
                    <span>GALERIA</span>
                    <span>DO</span>
                    <span id="destaque-titulo">ARTISTA</span>
                </h2>
            </div>
            <div id="cartao-imagem-principal">
                <img src="img/um festival sertanej.png" alt="Artista tocando em um show com grande público ao pôr do sol." id="imagem-principal">
            </div>
        </section>

        <section id="secao-integrantes">
            <h3 id="subtitulo-integrantes">INTEGRANTES</h3>
            <div id="container-cartoes-integrantes">
                <div id="integrante-cartao-1" class="cartao-integrante">
                    <img src="img/banda_rock.jpg" alt="Toguro cantando no palco." class="imagem-integrante">
                    <div class="informacao-integrante">
                        <p class="nome-integrante">TOGURO</p>
                        <p class="papel-integrante">VOCAL</p>
                    </div>
                </div>
                <div id="integrante-cartao-2" class="cartao-integrante">
                    <img src="img/hiphop.jpg" alt="Macaxeira tocando violão no palco." class="imagem-integrante">
                    <div class="informacao-integrante">
                        <p class="nome-integrante">MACAXEIRA</p>
                        <p class="papel-integrante">VIOLÃO</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="secao-eventos">
            <h3 id="subtitulo-eventos">EVENTOS QUE ESTÃO PARTICIPANDO</h3>
            <div id="container-cartoes-eventos">
                <div id="evento-cartao-1" class="cartao-evento">
                    <img src="img/um festival sertanej.png" alt="Decoração de luzes e palco em um festival ao ar livre." class="imagem-evento">
                    <div class="informacao-evento">
                        <p class="nome-evento">FESTIVAL AO LUAR</p>
                        <p class="detalhes-evento">SERGIPE | 02/11</p>
                    </div>
                </div>
                </div>
        </section>
    </div>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>