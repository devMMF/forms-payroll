<?php
    //---- validando variaveis ----//
    $nome = $cpf = $cargo = $tipo_salario = $hr_trabalhada = $salario_base = $numero_dependente = 0;

    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $cargo = $_POST['cargo'];
    $tipo_salario = $_POST['tp_sl'];
    $hr_trabalhada = $_POST['hr_trabalho'];
    $salario_base = $_POST['sl_base'];
    $numero_dependente = $_POST['Dependente'];
    $cargo_diretor = 2000;
    $cargo_coordenador = 1000;
    $cargo_agente = 500;
    $cargo_auxiliar = 150;
    $inss_percentual1 = 0.075;
    $inss_percentual2 = 0.09;
    $inss_percentual3 = 0.12;
    $inss_percentual4 = 0.14;
    $irpf_percentual1 = 0.075;
    $irpf_percentual2 = 0.15;
    $irpf_percentual3 = 0.225;
    $irpf_percentual4 = 0.275;
    $irpf_deducao1 = 142.80;
    $irpf_deducao2 = 354.80;
    $irpf_deducao3 = 636.13;
    $irpf_deducao4 = 869.36;

    if (isset($_POST['sc_urbano'])){
        $sc_urbano = 800;
    }else{
        $sc_urbano = 0;
    }
    if (isset($_POST['sc_rural'])){
        $sc_rural= 500;
    }else{
        $sc_rural= 0;
    }
    if (isset($_POST['ibate'])){
        $ibate = 500;
    }else{
        $ibate = 0;
    }
    if (isset($_POST['araraquara'])){
        $araraquara = 500;
    }else{
        $araraquara = 0;
    }
    if (isset($_POST['distrito'])){
        $distrito = 500;
    }else{
        $distrito = 0;
    }
    
    function salarioBruto($tipo, $base, $hora){   //---- Salario bruto ----//
        if ($tipo == "Horista"){
            $salario = $base * $hora;
        }else {
            $salario = $base;
        }
        return $salario;
    }
    
    function acrescimoCargo($cargo_funcionario, $cargo1, $cargo2, $cargo3, $cargo4){   //---- Acréscimo associado ao cargo ----//
        if ($cargo_funcionario == "Diretor"){
            $acrescimo = $cargo1;
        }
        elseif ($cargo_funcionario == "Coordenador"){
            $acrescimo = $cargo2;
        }
        elseif ($cargo_funcionario == "Agente"){
            $acrescimo = $cargo3;
        }
        else{
            $acrescimo = $cargo4;
        }
        return $acrescimo;
    }
     
    function acrescimoUnidade($unidadeA, $unidadeB, $unidadeC, $unidadeD, $unidadeE){   //---- Acréscimo por unidade ---//
        $acrescimo = $unidadeA + $unidadeB + $unidadeC + $unidadeD + $unidadeE;
        return $acrescimo;
    }
    
    function acrescimoDependente($salario, $dependente){   //---- Acréscimo por dependente ----//
        if ($salario <= 1655.98){
            $dependente = $dependente * 56.47;
        }else{
             $dependente = 0;
        }
        return $dependente;
    }
    
    function descontoINSS($salario, $percentual1, $percentual2, $percentual3, $percentual4){   //---- Desconto INSS ----//
        if ($salario >= 0 and $salario <= 1212){
            $desconto = $salario * $percentual1;
        }
        elseif ($salario >= 1212.01 and $salario <= 2427.35){
            $desconto = ($salario - 1212) * $percentual2;
            $desconto = $desconto + 90.90;
        }
        elseif ($salario >= 2427.36 and $salario <= 3641.03){
            $desconto = ($salario - 2427.35) * $percentual3;
            $desconto = $desconto + 90.90 + 109.38;
        }
        elseif ($salario >= 3641.04 and $salario <= 7087.22){
            $desconto = ($salario - 3641.03) * $percentual4;
            $desconto = $desconto + 90.90 + 109.38 + 145.64;  
        }
        else{
            $desconto = 828.39;    // teto INSS
        }
        return $desconto;
    }

    function descontoIRPF($salario, $percentual1, $percentual2, $percentual3, $percentual4, $decucao1, $decucao2, $decucao3, $decucao4){   //---- Desconto IRPF ----//
        if ($salario < 1903.99){
            $desconto = 0;
        }
        elseif ($salario >= 1903.99 and $salario <= 2826.65){
            $desconto = ($salario * $percentual1) - $decucao1;
            if ($desconto < 0){
                $desconto = 0;
            }else{
                $desconto = $desconto;
            }
        }
        elseif ($salario >= 2826.66 and $salario <= 3751.05){
            $desconto = ($salario * $percentual2) - $decucao2;
            if ($desconto < 0){
                $desconto = 0;
            }else{
                $desconto = $desconto;
            }
        }
        elseif ($salario >= 3751.06 and $salario <= 4664.68){
            $desconto = ($salario * $percentual3) - $decucao3;
            if ($desconto < 0){
                $desconto = 0;
            }else{
                $desconto = $desconto;
            }
        }
        else{
            $desconto = ($salario * $percentual4) - $decucao4;
            if ($desconto < 0){
                $desconto = 0;
            }else{
                $desconto = $desconto;
            }
        }
        return $desconto;
    }

    $salario_bruto = salarioBruto($tipo_salario, $salario_base, $hr_trabalhada);   //$salario_bruto = função que calcula o salario que vai ser base dos calculos//  
    $acrescimo_cargo = acrescimoCargo($cargo, $cargo_diretor, $cargo_coordenador, $cargo_agente, $cargo_auxiliar);   //$acrescimo_cargo = função que verifica qual o cargo do funcionario e atribui valor do bonus//  
    $acrescimo_unidade = acrescimoUnidade($sc_urbano, $sc_rural, $ibate, $araraquara, $distrito);   // $acrescimo_unidade = função que verifica e calcula area de atuamento e valor do bonus da area do funcionario//
    $bruto_parcial = $salario_bruto + $acrescimo_cargo + $acrescimo_unidade;   // $bruto_parcial é a soma de todo o salario até o momento de receber ou não o acrescimo do slário família//

    $acrescimo_dependente = acrescimoDependente($bruto_parcial, $numero_dependente);   //$acrescimo_dependente= função que calcula se salario esta dentro da condição de receber valor por dependente e calcula acrescimo a receber//
    $bruto_total = $bruto_parcial + $acrescimo_dependente;   // salário bruto total pronto para receber os descontos//

    $desconto_inss = descontoINSS($bruto_total, $inss_percentual1, $inss_percentual2, $inss_percentual3, $inss_percentual4);
    $desconto_irpf = descontoIRPF($bruto_total, $irpf_percentual1, $irpf_percentual2, $irpf_percentual3, $irpf_percentual4, $irpf_deducao1, $irpf_deducao2, $irpf_deducao3, $irpf_deducao4);
    $desconto_total = $desconto_inss + $desconto_irpf;   // soma de todos os descontos a serem efetuados//
    $salario_liquido = $bruto_total - $desconto_total;   // salário liquido//
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.php">
    <title>Salário total com descontos</title>
</head>
<body>
    <h1>ATHENA DESENVOLVIMENTO DE SISTEMAS LTDA</h1>
    <h2>DEMONSTRATIVO DE PAGAMENTO</h2>
    <table>
        <tr>
            <td width="70px"><b>Nome:</b></td>
            <td><?php echo ($nome);?></td>
        </tr>
        <tr>
            <td width="70px"><b>Cargo:</b></td>
            <td><?php echo ($cargo);?></td>
        </tr>
        <tr>
            <td><b>Código</b></td>
            <td><b>Descrição</b></td>
            <td><b>Vencimentos</b></td>
            <td><b>Descontos</b></td>
        </tr>
        <tr>
            <td>101</td>
            <td>Salário Bruto</td>
            <td><?php echo ("R$".$salario_bruto = number_format($salario_bruto, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td>909</td>
            <td>Gratificação por Cargo</td>
            <td><?php echo ("R$".$acrescimo_cargo = number_format($acrescimo_cargo, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td>987</td>
            <td>Adicional por Setor de Atuação</td>
            <td><?php echo ("R$".$acrescimo_unidade = number_format($acrescimo_unidade, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td>650</td>
            <td>Salário Família</td>
            <td><?php echo ("R$".$acrescimo_dependente = number_format($acrescimo_dependente, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td>973</td>
            <td colspan="2">INSS</td>
            <td><?php echo ("R$".$desconto_inss = number_format($desconto_inss, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td>987</td>
            <td colspan="2">IRPF sobre salário</td>
            <td><?php echo ("R$".$desconto_irpf = number_format($desconto_irpf, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td colspan="2"><b>TOTAL DE VENCIMENTOS</b></td>
            <td><?php echo ("R$".$$bruto_total = number_format($bruto_total, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td colspan="3"><b>TOTAL DE DESCONTOS</b></td>
            <td><?php echo ("R$".$desconto_total = number_format($desconto_total, 2, '.', ''));?></td>
        </tr>
        <tr>
            <td colspan="2"><b>SALÁRIO LÍQUIDO</b></td>
            <td><?php echo ("R$".$salario_liquido = number_format($salario_liquido, 2, '.', ''));?></td>
        </tr>
    </table>
</body>
</html>