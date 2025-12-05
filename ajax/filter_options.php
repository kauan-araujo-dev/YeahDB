<?php
require_once __DIR__ . '/../src/Database/Conecta.php';

header('Content-Type: application/json; charset=utf-8');

$allowedSources = ['eventos', 'artistas'];
$allowedFields = ['estado', 'cidade', 'estilo'];

$source = isset($_GET['source']) ? $_GET['source'] : null;
$field = isset($_GET['field']) ? $_GET['field'] : null;

if (!in_array($source, $allowedSources) || !in_array($field, $allowedFields)) {
    echo json_encode([]);
    exit;
}

$pdo = Conecta::getConexao();

$state = isset($_GET['state']) ? trim($_GET['state']) : null;
$city = isset($_GET['city']) ? trim($_GET['city']) : null;

try {
    if ($source === 'eventos') {
        if ($field === 'estado') {
            $stmt = $pdo->prepare("SELECT DISTINCT estado FROM eventos WHERE estado IS NOT NULL AND estado != '' ORDER BY estado ASC");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($rows);
            exit;
        }

        if ($field === 'cidade') {
            $sql = "SELECT DISTINCT cidade FROM eventos WHERE cidade IS NOT NULL AND cidade != ''";
            $params = [];
            if ($state) {
                $sql .= " AND estado = :estado";
                $params[':estado'] = $state;
            }
            $sql .= " ORDER BY cidade ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($rows);
            exit;
        }

        if ($field === 'estilo') {
            $sql = "SELECT DISTINCT em.nome
                    FROM evento_estilo ee
                    JOIN estilo_musical em ON em.id = ee.id_estilo
                    JOIN eventos e ON e.id = ee.id_evento
                    WHERE 1=1";
            $params = [];
            if ($state) {
                $sql .= " AND e.estado = :estado";
                $params[':estado'] = $state;
            }
            if ($city) {
                $sql .= " AND e.cidade = :cidade";
                $params[':cidade'] = $city;
            }
            $sql .= " ORDER BY em.nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($rows);
            exit;
        }
    }

    if ($source === 'artistas') {
        if ($field === 'estado') {
            $stmt = $pdo->prepare("SELECT DISTINCT estado FROM artistas WHERE estado IS NOT NULL AND estado != '' ORDER BY estado ASC");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($rows);
            exit;
        }

        if ($field === 'cidade') {
            $sql = "SELECT DISTINCT cidade FROM artistas WHERE cidade IS NOT NULL AND cidade != ''";
            $params = [];
            if ($state) {
                $sql .= " AND estado = :estado";
                $params[':estado'] = $state;
            }
            $sql .= " ORDER BY cidade ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($rows);
            exit;
        }

        if ($field === 'estilo') {
            $sql = "SELECT DISTINCT em.nome
                    FROM artista_estilo ae
                    JOIN estilo_musical em ON em.id = ae.id_estilo
                    JOIN artistas a ON a.id = ae.id_artista
                    WHERE 1=1";
            $params = [];
            if ($state) {
                $sql .= " AND a.estado = :estado";
                $params[':estado'] = $state;
            }
            if ($city) {
                $sql .= " AND a.cidade = :cidade";
                $params[':cidade'] = $city;
            }
            $sql .= " ORDER BY em.nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($rows);
            exit;
        }
    }

    echo json_encode([]);
} catch (\Throwable $e) {
    echo json_encode([]);
}
