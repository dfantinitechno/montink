🛒 Mini ERP para Controle de Pedidos, Produtos, Cupons e Estoque
Este projeto foi desenvolvido em PHP Puro (7.3) com MySQL no backend e Bootstrap no frontend.
O sistema simula uma loja online com funcionalidades de gerenciamento de produtos, variações, controle de estoque, aplicação de cupons, cálculo de frete, carrinho de compras via sessão, finalização de pedidos com envio de e-mail e integração via webhook.

🚀 Tecnologias Utilizadas
PHP Puro (7.3)

MySQL

Bootstrap 5

HTML / CSS / JS

📦 Funcionalidades
Tabelas do Banco de Dados
produtos: armazena os produtos cadastrados

variacoes: armazena as variações de cada produto

estoque: controla o estoque por produto e variação

cupons: armazena cupons com regras de uso e validade

pedidos: armazena os pedidos realizados

pedido_produtos: armazena os produtos de cada pedido

Cadastro de Produtos
Cadastro de nome, preço e variações

Controle de estoque por variação

Edição de produtos e atualização de estoque

Carrinho e Pedido
Botão "Comprar" na tela de produtos adiciona itens ao carrinho via sessão

Cálculo de subtotal e aplicação das regras de frete

Integração com API ViaCEP para preenchimento automático do endereço

Cupons
Criação e gerenciamento de cupons com regras de:

Valor mínimo de pedido

Validade

Aplicação de cupom no checkout

Finalização de Pedido
Envio de e-mail com os dados do pedido e endereço do cliente

Webhook
O sistema possui uma rota de webhook que aceita requisições POST com os seguintes campos:

id (ID do pedido)

status (novo status a ser atribuído)

🟡 Status suportados:
A coluna status da tabela pedidos é do tipo ENUM com os seguintes valores válidos:

'pendente': Pedido aguardando pagamento ou confirmação

'pago': Pedido confirmado e pago

'cancelado': Pedido cancelado — o sistema remove automaticamente o pedido do banco ao receber esse status

'enviado': Pedido já foi enviado ao cliente

🔁 Comportamento do Webhook:

Se status = "cancelado" → o pedido será excluído do banco

Para os demais status válidos (pago, pendente, enviado), o sistema atualiza apenas o campo status do pedido com base no id recebido