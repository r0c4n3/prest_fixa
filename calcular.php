<?php
$n = isset($_POST['n']) && is_numeric($_POST['n']) ? floatval($_POST['n']) : null;
$j = isset($_POST['j']) && is_numeric($_POST['j']) ? floatval($_POST['j']) : null;
$p = isset($_POST['p']) && is_numeric($_POST['p']) ? floatval($_POST['p']) : null;
$q0 = isset($_POST['q0']) && is_numeric($_POST['q0']) ? floatval($_POST['q0']) : null;

$resultado_n = "Não calculado";
$resultado_j = "Não calculado";
$resultado_p = "Não calculado";
$resultado_q0 = "Não calculado";

if (is_numeric($n) && is_numeric($j) && is_numeric($p)) {
    $q0 = ($p * (1 - pow(1 + $j, -$n))) / $j;
    $resultado_q0 = round($q0, 2);
} elseif (is_numeric($n) && is_numeric($j) && is_numeric($q0)) {
    $p = ($q0 * $j) / (1 - pow(1 + $j, -$n));
    $resultado_p = round($p, 2);
} elseif (is_numeric($n) && is_numeric($p) && is_numeric($q0)) {
    // Cálculo iterativo para a taxa de juros (j)
    $j = 0.1; // Valor inicial para iteração
    $j_anterior = 0;
    $precisao = 0.000001;
    $max_iteracoes = 100;
    $iteracao = 0;

    while (abs($j - $j_anterior) > $precisao && $iteracao < $max_iteracoes) {
        $j_anterior = $j;
        $j = $j - (($p * (1 - pow(1 + $j, -$n))) / $j - $q0) / (($p * $n * pow(1 + $j, -$n - 1)) / $j - ($p * (1 - pow(1 + $j, -$n))) / pow($j, 2));
        $iteracao++;
    }
    if(abs($j - $j_anterior) <= $precisao){
        $resultado_j = round($j, 6);
    } else {
        $resultado_j = "Não foi possivel calcular";
    }

} elseif (is_numeric($j) && is_numeric($p) && is_numeric($q0)) {
    $n = -log(1 - ($q0 * $j) / $p) / log(1 + $j);
    $resultado_n = round($n, 2);
}

// Exibe os resultados
echo "
    <link rel='stylesheet' type='text/css' href='style.css'>
    <div class='container'>
        <h1>Resultados:</h1>
        <p>Número de Meses (n): " . $resultado_n . "</p>
        <p>Taxa de Juros Mensal (j): " . $resultado_j . "</p>
        <p>Valor da Prestação (p): " . $resultado_p . "</p>
        <p>Valor Financiado (q0): " . $resultado_q0 . "</p>
        <a href='index.html'>Voltar</a>
    </div>
";
?>
