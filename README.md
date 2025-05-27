# Mini ERP para controle de Pedidos, Produtos, Cupons e Estoque

Este projeto foi desenvolvido em PHP Puro (7.3) com uso de MySQL no backend e Bootstrap no frontend. 
O sistema simula uma loja online com funcionalidades de gerenciamento de produtos, variações, controle de estoque, aplicação de cupons, cálculo de frete, carrinho de compras com sessão e finalização de pedidos com envio de e-mail e integração via webhook.

---

## Tecnologias Utilizadas

- PHP Puro (7.3)
- MySQL
- Bootstrap 5
- HTML/CSS/JS

---

## Funcionalidades

### Tabelas do Banco de Dados
- `produtos`: armazena os produtos cadastrados.
- `variacoes`: armazena as variações de cada produto.
- `estoque`: controla o estoque por produto e variação.
- `cupons`: armazena cupons com regras de uso e validade.
- `pedidos`: armazena os pedidos realizados.
- `pedido_produtos`: armazena os produtos do pedido.

### Cadastro de Produtos
- Cadastro de nome, preço e variações.
- Controle de estoque por variação.
- Edição de produtos e atualização de estoque.

### Carrinho e Pedido
- Botão "Comprar" na tela de produtos adiciona itens ao carrinho via sessão.
- Cálculo de subtotal e aplicação de regras de frete.
- Integração com a API ViaCEP para preenchimento automático do endereço.

### Cupons
- Criação e gerenciamento de cupons com regras de:
  - Valor mínimo de pedido
  - Validade
- Aplicação de cupom no checkout.

### Finalização de Pedido
- Envio de e-mail com os dados do pedido e endereço do cliente.

---

## Webhook

O sistema possui uma rota de webhook que aceita requisições POST com os seguintes campos:

| Campo  | Descrição           |
|--------|---------------------|
| `id`   | ID do pedido        |
| `status` | Novo status a ser atribuído |

### Status suportados

A coluna `status` da tabela `pedidos` é do tipo ENUM com os seguintes valores válidos:

```sql
ENUM('pendente','pago','cancelado','enviado')
```

- **pendente**: Pedido aguardando pagamento ou confirmação.  
- **pago**: Pedido confirmado e pago.  
- **cancelado**: Pedido cancelado — o sistema remove automaticamente o pedido do banco ao receber esse status.  
- **enviado**: Pedido já foi enviado ao cliente.

### Comportamento do Webhook

- Se o `status` recebido for `"cancelado"` → o pedido será excluído do banco.  
- Para os demais status válidos (`pago`, `pendente`, `enviado`), o sistema apenas atualiza o campo `status` do pedido com base no `id` recebido.

---

## Como rodar o projeto

1. Crie um banco de dados MySQL para o projeto.
2. Execute o script SQL `script.sql` que está na raiz do projeto para criar as tabelas necessárias.
3. Configure o arquivo de conexão ao banco com suas credenciais.
4. Abra o projeto no navegador para começar a usar.

---

## Observações

- Utilize PHP 7.3 ou superior.
- Recomendamos o uso do Bootstrap para a interface.
- O sistema usa sessões para controle do carrinho de compras.
- O envio de e-mail requer configuração correta do servidor SMTP.

---