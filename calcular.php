<?php
    $meses = isset($_POST['meses']) && $_POST['meses'] !== '' ? intval($_POST['meses']) : null;
    $taxa_mensal_percentual = isset($_POST['taxa']) && $_POST['taxa'] !== '' ? floatval($_POST['taxa']) : null;
    $prestacao = isset($_POST['prestacao']) && $_POST['prestacao'] !== '' ? floatval($_POST['prestacao']) : null;
    $valor_financiado = isset($_POST['valor_financiado']) && $_POST['valor_financiado'] !== '' ? floatval($_POST['valor_financiado']) : null;

    $campos_preenchidos = 0;
    if ($meses !== null) $campos_preenchidos++;
    if ($taxa_mensal_percentual !== null) $campos_preenchidos++;
    if ($prestacao !== null) $campos_preenchidos++;
    if ($valor_financiado !== null) $campos_preenchidos++;

    if ($campos_preenchidos < 3) {
        $resultado = "<p>Preencha pelo menos três dos quatro campos para realizar o cálculo.</p>";
    } else {
        if ($taxa_mensal_percentual !== null) {
            $taxa_mensal = $taxa_mensal_percentual / 100;
        } else {
            $taxa_mensal = null;
        }

        if ($meses === null) {
            if ($taxa_mensal !== null && $prestacao !== null && $valor_financiado !== null && $taxa_mensal > 0) {
                $i = $taxa_mensal;
                $pv = $valor_financiado;
                $pmt = $prestacao;
                $n = log($pmt / ($pmt - $i * $pv)) / log(1 + $i);
                if (is_nan($n) || !is_finite($n) || $n <= 0) {
                    $resultado = "<p>Não foi possível calcular o número de meses com os dados fornecidos.</p>";
                } else {
                    $resultado = "<p>Número de Meses (estimado): " . round($n, 2) . "</p>";
                }
            } else {
                $resultado = "<p>Preencha o número de meses ou os outros três campos para calcular.</p>";
            }
        } elseif ($taxa_mensal === null) {
            if ($meses !== null && $prestacao !== null && $valor_financiado !== null && $meses > 0) {
                // Cálculo da taxa de juros usando método numérico (aproximação)
                $low = 0;
                $high = 1; // Uma taxa alta como ponto de partida
                for ($j = 0; $j < 100; $j++) { // 100 iterações para boa precisão
                    $mid = ($low + $high) / 2;
                    $pv_calc = $prestacao * ((1 - pow(1 + $mid, -$meses)) / $mid);
                    if (abs($pv_calc - $valor_financiado) < 0.001) {
                        $taxa_calculada = $mid * 100;
                        $resultado = "<p>Taxa de Juros Mensal (estimada): " . round($taxa_calculada, 4) . "%</p>";
                        break;
                    } elseif ($pv_calc > $valor_financiado) {
                        $low = $mid;
                    } else {
                        $high = $mid;
                    }
                    if ($j === 99) {
                        $resultado = "<p>Não foi possível calcular a taxa de juros com precisão.</p>";
                    }
                }
            } else {
                $resultado = "<p>Preencha a taxa de juros ou os outros três campos para calcular.</p>";
            }
        } elseif ($prestacao === null) {
            if ($meses !== null && $taxa_mensal !== null && $valor_financiado !== null && $meses > 0 && $taxa_mensal >= 0) {
                if ($taxa_mensal > 0) {
                    $pmt = $valor_financiado * ($taxa_mensal * pow(1 + $taxa_mensal, $meses)) / (pow(1 + $taxa_mensal, $meses) - 1);
                } elseif ($taxa_mensal == 0) {
                    $pmt = $valor_financiado / $meses;
                }
                $resultado = "<p>Valor da Prestação: R$ " . number_format($pmt, 2, ',', '.') . "</p>";
            } else {
                $resultado = "<p>Preencha o valor da prestação ou os outros três campos para calcular.</p>";
            }
        } elseif ($valor_financiado === null) {
            if ($meses !== null && $taxa_mensal !== null && $prestacao !== null && $meses > 0 && $taxa_mensal >= 0) {
                if ($taxa_mensal > 0) {
                    $pv = $prestacao * (1 - pow(1 + $taxa_mensal, -$meses)) / $taxa_mensal;
                } elseif ($taxa_mensal == 0) {
                    $pv = $prestacao * $meses;
                }
                $resultado = "<p>Valor Financiado: R$ " . number_format($pv, 2, ',', '.') . "</p>";
            } else {
                $resultado = "<p>Preencha o valor financiado ou os outros três campos para calcular.</p>";
            }
        }
    }

    header("Location: calculadora_financiamento.html?resultado=" . urlencode($resultado));
    exit();
?>