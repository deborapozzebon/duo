<?php
    try {
        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "";
        $port = "3306";
        $db = "test";
        $connection = new mysqli($dbhost, $dbuser, $dbpass, $db, $port) or die("Connect failed: %s\n". $connection -> error);

        $queryLongitude = $connection -> query("
            SELECT
                indicadores_respostas.resposta_text AS title,
                COALESCE(
                    (
                        SELECT indicadores_respostas.resposta_text FROM indicadores_respostas
                        LEFT JOIN indicadores_secoes_itens ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Longitude' AND indicadores_respostas.id_indicador = indicadores.id
                    ),
                    (
                        SELECT cidades.longitude FROM cidades INNER JOIN indicadores_respostas 
                        LEFT JOIN indicadores_secoes_itens ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Cidade' AND cidades.cidades_id = CAST(indicadores_respostas.resposta_text AS UNSIGNED) AND indicadores.id = indicadores_respostas.id_indicador   
                    )
                ) AS longitude FROM indicadores
            LEFT JOIN indicadores_respostas ON indicadores_respostas.id_indicador = indicadores.id
            LEFT JOIN indicadores_secoes_itens ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
            WHERE indicadores_secoes_itens.titulo = 'Nome da capacitação' OR (indicadores_secoes_itens.titulo = 'Instituição')
        ");

        $queryLatitude = $connection -> query("
            SELECT
                COALESCE(
                    (
                        SELECT indicadores_respostas.resposta_text FROM indicadores_respostas 
                        LEFT JOIN indicadores_secoes_itens ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Latitude' AND indicadores_respostas.id_indicador = indicadores.id
                    ),
                    (
                        SELECT cidades.latitude FROM cidades INNER JOIN indicadores_respostas 
                        LEFT JOIN indicadores_secoes_itens ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
                        WHERE indicadores_secoes_itens.titulo = 'Cidade' AND cidades.cidades_id = CAST(indicadores_respostas.resposta_text AS UNSIGNED) AND indicadores.id = indicadores_respostas.id_indicador 
                    )
                ) AS latitude FROM indicadores
            LEFT JOIN indicadores_respostas ON indicadores_respostas.id_indicador = indicadores.id
            LEFT JOIN indicadores_secoes_itens ON indicadores_secoes_itens.id = indicadores_respostas.id_secao_item
            WHERE indicadores_secoes_itens.titulo = 'Nome da capacitação' OR (indicadores_secoes_itens.titulo = 'Instituição')
        ");

        $dataLongitude = $queryLongitude -> fetch_all(MYSQLI_ASSOC);
        $dataLatitude = $queryLatitude -> fetch_all(MYSQLI_ASSOC);
        
        $connection -> close();

    } catch(Execption $e) {
        echo 'Falha na conexão com o banco.';
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Duo</title>
</head>
<body>
    <main class="container">
    </br>
        <?php
            if($dataLongitude && $dataLatitude) {
                for ($i=0; $i < sizeof($dataLongitude) ; $i++) { 
                    echo "<div class='card'>"
                    ."<div class='card-body'>"
                      ."<h4 class='card-title'>{$dataLongitude[$i]["title"]}</h4>"
                      ."<p class='card-text'>Longitude: {$dataLongitude[$i]["longitude"]}</p>"
                      ."<p class='card-text'>Latitude: {$dataLatitude[$i]["latitude"]}</p>"
                    ."</div>"
                  ."</div>"
                  ."</br>";
                }
            } else {
                "<div class='alert alert-danger' role='alert'>Não encontrado.</div>";
            }
        ?>
    </main>
</body>
</html>