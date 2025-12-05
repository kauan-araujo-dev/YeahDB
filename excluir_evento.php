<?php
    require_once "src/Database/Conecta.php";
    require_once "src/Helpers/Utils.php";
    require_once "src/Models/Eventos.php";
    require_once "src/Models/IntegranteEvento.php";
    require_once "src/Models/FotoEvento.php";
    require_once "src/Services/EventoServicos.php";
    require_once "src/Services/EstilosMusicaisServicos.php";
    require_once "src/Services/AutenticarServico.php";
    session_start();
    $eventoServico = new EventoServicos();

    $id = Utils::sanitizar($_GET['id'], 'inteiro');

    if(!$id) Utils::redirecionarPara('meus_eventos.php');
?>