<?php
$n = isset($_POST['n']) ? $_POST['n'] : null;
$j = isset($_POST['j']) ? $_POST['j'] : null;
$p = isset($_POST['p']) ? $_POST['p'] : null;
$q0 = isset($_POST['q0']) ? $_POST['q0'] : null;

if (is_numeric($n) && is_numeric($j) && is_numeric($p)) {
    $q0 = ($p * (1 - pow(1 + $j, -$n))) / $j;
} elseif (is_numeric($n) && is_numeric($j) && is_numeric($q0)) {
    $p = ($q0 * $j) / (1 - pow(1 + $j, -$n));
} elseif (is_numeric($n) && is_numeric($p) && is_numeric($q0)) {
    $j = 0.01; // Valor inicial para iteração
    $j_anterior = 0;
    while (abs($j - $j_anterior) > 0.000001) {
        $j_anterior = $j;
        $j = $j - (($p * (1 - pow(1 + $j, -$n))) / $j - $q0) / (($p * $n * pow(1 + $j, -$n - 1)) / $j - ($p * (1 - pow(1 + $j, -$n))) / pow($j, 2));
    }
} elseif (is_numeric($j) && is_numeric($p) && is_numeric($q0)) {
    $n = -log(1 - ($q0 * $j) / $p) / log(1 + $j);
}

// Exibe os resultados
echo "
    <link rel='stylesheet' type='text/css' href='style.css'>
    <div class='container'>
        <h1>Resultados:</h1>
        <p>Número de Meses (n): " . (is_numeric($n) ? round($n, 2) : "Não calculado") . "</p>
        <p>Taxa de Juros Mensal (j): " . (is_numeric($j) ? round($j, 4) : "Não calculado") . "</p>
        <p>Valor da Prestação (p): " . (is_numeric($p) ? round($p, 2) : "Não calculado") . "</p>
        <p>Valor Financiado (q0): " . (is_numeric($q0) ? round($q0, 2) : "Não calculado") . "</p>
        <a href='index.html'>Voltar</a>
    </div>
";
?>