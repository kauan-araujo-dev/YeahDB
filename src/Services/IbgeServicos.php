<?php
class IbgeServicos
{
    public function listarEstados(): array
    {
        $url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome";

       $json = $this->curlGet($url);
        if (!$json) return [];

        $dados = json_decode($json, true);

        // Formato padronizado: UF + Nome
        return array_map(fn($estado) => [
            'sigla' => $estado['sigla'],
            'nome'  => $estado['nome']
        ], $dados);
    }

    public function listarCidadesPorEstado($uf)
{
    $uf = trim($uf);

    // garante que NUNCA venha algo como “SP - São Paulo”
    if (strpos($uf, " ") !== false) {
        $uf = substr($uf, 0, strpos($uf, " "));
    }

    $url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios";

    $json = @file_get_contents($url);

    if (!$json) {
        return [];
    }

    $dados = json_decode($json, true);

    $cidades = array_map(fn($c) => $c['nome'], $dados);

    sort($cidades);

    return $cidades;
}

private function curlGet($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
}
