<?php

namespace App\Helpers;

class EmailHelper
{
    public static function gerarConteudoEmailPedido(array $pedido, array $produtos): string
    {
        $html = '
        <html>
            <body style="font-family: Arial; max-width: 600px; margin: auto;">
                <h2>Seu pedido #' . htmlspecialchars($pedido['pedido_id']) . ' foi finalizado</h2>
                <p><strong>Data:</strong> ' . htmlspecialchars($pedido['data_criacao']) . '</p>

                <h4>Endereço de Entrega</h4>
                <p>' . nl2br(htmlspecialchars($pedido['endereco_completo'])) . '</p>

                <h4>Produtos Comprados</h4>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($produtos as $p) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($p['nome']) . '</td>
                    <td>' . (int)$p['quantidade'] . '</td>
                    <td>R$ ' . number_format($p['preco'], 2, ',', '.') . '</td>
                    <td>R$ ' . number_format($p['subtotal'], 2, ',', '.') . '</td>
                </tr>';
        }

        $html .= '
                    </tbody>
                </table>

                <h4>Valores do Pedido</h4>
                <p><strong>Subtotal:</strong> R$ ' . number_format($pedido['subtotal'], 2, ',', '.') . '</p>
                <p><strong>Frete:</strong> R$ ' . number_format($pedido['frete'], 2, ',', '.') . '</p>';

        if (!empty($pedido['cupom_codigo'])) {
            $html .= '<p><strong>Cupom Aplicado:</strong> ' . htmlspecialchars($pedido['cupom_codigo']) . '</p>';
            $html .= '<p><strong>Desconto:</strong> - R$ ' . number_format($pedido['desconto'], 2, ',', '.') . '</p>';
        }

        $html .= '
                <p><strong>Total Final:</strong> R$ ' . number_format($pedido['total'], 2, ',', '.') . '</p>

                <p style="margin-top: 2rem;">Obrigado pela sua compra!<br>Equipe Montink</p>
            </body>
        </html>';

        return $html;
    }

    public static function enviarEmailConfirmacao(array $pedido, array $produtos, string $destinatario): bool
    {
        $assunto = "Confirmação de Compra - Pedido #{$pedido['pedido_id']}";
        $corpo = self::gerarConteudoEmailPedido($pedido, $produtos);

        $headers = "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Montink <vendas@montink.com>\r\n";
        $headers .= "Reply-To: vendas@montink.com\r\n";

        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            // Ambiente dev: retorna true simulando envio bem-sucedido, sem gerar arquivo ou abrir página
            return true;
        }

        return mail($destinatario, $assunto, $corpo, $headers);
    }
}
