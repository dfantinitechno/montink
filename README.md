# 🛒 Sistema de Pedidos com Estoque, Cupons e Checkout

Este projeto foi desenvolvido em **PHP Puro** com uso de **MySQL** no backend e **Bootstrap** no frontend. O sistema simula uma loja online com funcionalidades completas de gerenciamento de produtos, controle de estoque, aplicação de cupons, cálculo de frete, carrinho de compras com sessão e finalização de pedidos com envio de e-mail e integração via webhook.

---

## 🚀 Tecnologias Utilizadas

- PHP Puro (sem frameworks)
- MySQL
- Bootstrap 5
- HTML/CSS/JS
- API ViaCEP
- PHPMailer

---

## 📦 Funcionalidades

### ✅ Tabelas do Banco de Dados
- `produtos`: armazena os produtos cadastrados.
- `estoques`: controla o estoque por produto e variação.
- `cupons`: armazena cupons com regras de uso e validade.
- `pedidos`: armazena os pedidos realizados.

---

### ✅ Tela de Cadastro de Produtos
- Cadastro de nome, preço e variações (ex: tamanho, cor).
- Controle de estoque por variação.
- Edição de produtos e atualização de estoque.

---

### ✅ Carrinho e Pedido
- Botão "Comprar" na tela de produtos adiciona itens ao carrinho via sessão.
- Cálculo de subtotal e aplicação de regras de frete:
  - Subtotal entre **R$52,00** e **R$166,59** → Frete **R$15,00**
  - Subtotal acima de **R$200,00** → **Frete grátis**
  - Outros casos → Frete **R$20,00**
- Integração com a **API ViaCEP** para preenchimento automático do endereço.

---

### ✅ Cupons
- Criação e gerenciamento de cupons com regras de:
  - Valor mínimo de pedido
  - Validade
- Aplicação automática de cupom válido no checkout.

---

### ✅ Finalização de Pedido
- Envio de e-mail com os dados do pedido e endereço do cliente.
- Exibição da listagem de pedidos realizados com:
  - Subtotal
  - Desconto
  - Frete
  - Total
  - Cupom aplicado
  - Status do pedido

---

### ✅ Webhook
- Rota que recebe atualizações de status de pedido via `POST` com:
  - `id` do pedido
  - `status`
- Se o status for `"cancelado"`, o pedido é excluído.
- Para outros status, o sistema apenas atualiza o campo `status`.

---