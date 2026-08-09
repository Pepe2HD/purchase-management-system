# 🛒 Purchase Management System

Sistema de gerenciamento de **Solicitações de Compra**, desenvolvido como desafio técnico para vaga de Desenvolvedor PHP.

A aplicação permite cadastrar e gerenciar solicitações de compra, vinculá-las opcionalmente a Ordens de Serviço e controlar uma ou mais Ordens de Pagamento, incluindo pagamentos à vista e parcelados.

---

## 📋 Sobre o projeto

O sistema foi desenvolvido utilizando **Laravel, Livewire e MySQL**, com foco em organização do código, relacionamentos entre entidades, validações e implementação das principais regras de negócio relacionadas a pagamentos e parcelas.

O projeto contempla o fluxo:

```text
Solicitação de Compra
        │
        ├── Ordem de Serviço (opcional)
        │
        └── Ordens de Pagamento
                │
                └── Parcelas
```

Uma solicitação pode possuir nenhuma ou uma Ordem de Serviço e pode possuir uma ou mais Ordens de Pagamento.

Cada Ordem de Pagamento pode ser:

* À vista;
* Parcelada.

---

## 🚀 Tecnologias utilizadas

* **PHP 8.2+**
* **Laravel 12**
* **Livewire 4**
* **MySQL**
* **Blade**
* **Composer**
* **Git**

---

## ✨ Funcionalidades

### Solicitações de Compra

* Criar solicitação;
* Listar solicitações;
* Visualizar detalhes;
* Editar solicitação;
* Excluir solicitação;
* Definir status;
* Vincular opcionalmente uma Ordem de Serviço.

### Ordens de Pagamento

* Criar uma ou mais Ordens de Pagamento para uma solicitação;
* Definir pagamento à vista;
* Definir pagamento parcelado;
* Informar o valor total do pagamento;
* Visualizar os pagamentos vinculados à solicitação.

### Parcelas

* Geração automática das parcelas;
* Definição automática do valor de cada parcela;
* Geração automática das datas de vencimento;
* Status de parcela pendente ou paga;
* Marcação de parcela como paga;
* Visualização das parcelas dentro da respectiva Ordem de Pagamento.

---

## 🧠 Regras de negócio

### Solicitações

Cada solicitação possui:

* Descrição;
* Status;
* Ordem de Serviço opcional.

Os status disponíveis são:

* `pending` — Pendente;
* `approved` — Aprovada;
* `rejected` — Rejeitada.

---

### Ordens de Pagamento

Uma solicitação pode possuir **uma ou mais Ordens de Pagamento**.

Cada pagamento possui:

* Tipo;
* Valor total;
* Solicitação à qual pertence.

Os tipos disponíveis são:

* `cash` — À vista;
* `installment` — Parcelado.

---

### Pagamento à vista

Quando o pagamento é definido como à vista, o sistema gera automaticamente **uma parcela** correspondente ao valor total.

Exemplo:

```text
Pagamento: R$ 1.000,00

Parcela 1: R$ 1.000,00
```

---

### Pagamento parcelado

Quando o pagamento é parcelado, o usuário informa a quantidade de parcelas.

O sistema gera automaticamente as parcelas e distribui o valor total entre elas.

Exemplo:

```text
Valor total: R$ 1.000,00
Quantidade: 3 parcelas

Parcela 1: R$ 333,34
Parcela 2: R$ 333,33
Parcela 3: R$ 333,33
```

A distribuição é realizada considerando os valores em centavos para evitar problemas de arredondamento e garantir que a soma das parcelas seja exatamente igual ao valor total.

---

### Vencimento das parcelas

As datas são geradas automaticamente:

```text
Parcela 1 → data atual
Parcela 2 → +30 dias
Parcela 3 → +60 dias
Parcela 4 → +90 dias
...
```

---

### Status das parcelas

As parcelas são criadas inicialmente como:

```text
pending
```

Após o pagamento, podem ser alteradas para:

```text
paid
```

---

## 🗄️ Estrutura do banco de dados

O banco é composto pelas seguintes tabelas principais:

### `purchase_requests`

Representa uma solicitação de compra.

| Campo              | Descrição                            |
| ------------------ | ------------------------------------ |
| `id`               | Identificador                        |
| `description`      | Descrição da solicitação             |
| `service_order_id` | Ordem de Serviço vinculada, opcional |
| `status`           | Status da solicitação                |
| `created_at`       | Data de criação                      |
| `updated_at`       | Data de atualização                  |

---

### `service_orders`

Representa uma Ordem de Serviço que pode ser vinculada a uma solicitação.

| Campo        | Descrição                  |
| ------------ | -------------------------- |
| `id`         | Identificador              |
| `code`       | Código da Ordem de Serviço |
| `created_at` | Data de criação            |
| `updated_at` | Data de atualização        |

A relação com `purchase_requests` é opcional.

---

### `payment_orders`

Representa uma Ordem de Pagamento vinculada a uma solicitação.

| Campo                 | Descrição               |
| --------------------- | ----------------------- |
| `id`                  | Identificador           |
| `purchase_request_id` | Solicitação relacionada |
| `type`                | Tipo de pagamento       |
| `total_amount`        | Valor total             |
| `created_at`          | Data de criação         |
| `updated_at`          | Data de atualização     |

---

### `installments`

Representa as parcelas de uma Ordem de Pagamento.

| Campo                | Descrição                      |
| -------------------- | ------------------------------ |
| `id`                 | Identificador                  |
| `payment_order_id`   | Ordem de Pagamento relacionada |
| `installment_number` | Número da parcela              |
| `amount`             | Valor da parcela               |
| `due_date`           | Data de vencimento             |
| `status`             | Status da parcela              |
| `created_at`         | Data de criação                |
| `updated_at`         | Data de atualização            |

---

## 🔗 Relacionamentos

Os relacionamentos principais são:

```text
ServiceOrder
    │
    └── hasMany
            │
            ▼
PurchaseRequest
    │
    └── hasMany
            │
            ▼
PaymentOrder
    │
    └── hasMany
            │
            ▼
Installment
```

Em termos de Eloquent:

```text
PurchaseRequest
    belongsTo → ServiceOrder
    hasMany  → PaymentOrder

PaymentOrder
    belongsTo → PurchaseRequest
    hasMany  → Installment

Installment
    belongsTo → PaymentOrder

ServiceOrder
    hasMany → PurchaseRequest
```

A Ordem de Serviço é opcional, portanto uma solicitação pode existir sem estar vinculada a uma Ordem de Serviço.

---

## ⚙️ Decisões técnicas

### Livewire

O Livewire foi utilizado para implementar componentes interativos, principalmente nas telas de:

* Listagem de solicitações;
* Formulário de solicitações;
* Visualização de solicitações;
* Criação de pagamentos.

Isso permite realizar ações e atualizar partes da interface sem a necessidade de implementar uma API ou utilizar JavaScript para toda a lógica de interação.

---

### Transações de banco

A criação de uma Ordem de Pagamento e suas respectivas parcelas é realizada dentro de uma transação utilizando:

```php
DB::transaction(...)
```

Isso garante que a operação seja atômica.

Caso ocorra algum erro durante a criação das parcelas, a criação do pagamento também é revertida, evitando dados incompletos no banco.

---

### Tratamento de valores monetários

Para a divisão de pagamentos parcelados, os valores são convertidos para centavos antes da divisão.

Isso evita problemas comuns de precisão com números decimais.

Por exemplo:

```text
R$ 1.000,00
        ↓
100.000 centavos
```

A divisão é realizada sobre os centavos e eventuais valores restantes são distribuídos entre as primeiras parcelas.

Dessa forma:

```text
R$ 333,34
R$ 333,33
R$ 333,33
----------------
R$ 1.000,00
```

---

### Eager Loading

Na visualização de uma solicitação, os relacionamentos necessários são carregados utilizando eager loading.

Exemplo:

```php
PurchaseRequest::with([
    'serviceOrder',
    'paymentOrders.installments'
])
```

Isso evita consultas desnecessárias ao banco durante a renderização das informações relacionadas.

---

### Validação

Os formulários possuem validações para garantir a integridade dos dados.

Entre elas:

* Campos obrigatórios;
* Tipos de pagamento válidos;
* Valores maiores que zero;
* Quantidade de parcelas inteira;
* Quantidade de parcelas maior que zero;
* Ordem de Serviço válida quando informada;
* Status válidos.

---

### Segurança

Durante a implementação também foram considerados alguns pontos básicos de segurança do Laravel, como:

* Mass Assignment;
* Validação de dados recebidos;
* CSRF;
* Route Model Binding;
* `findOrFail`;
* Verificação do contexto dos registros manipulados pelo Livewire;
* Escape automático dos dados exibidos nas Views.

---

## 📁 Estrutura principal

A estrutura relevante do projeto segue aproximadamente:

```text
app/
├── Http/
│   └── Controllers/
│       └── PurchaseRequestController.php
│
└── Models/
    ├── Installment.php
    ├── PaymentOrder.php
    ├── PurchaseRequest.php
    └── ServiceOrder.php

database/
└── migrations/
    ├── create_service_orders_table.php
    ├── create_purchase_requests_table.php
    ├── create_payment_orders_table.php
    └── create_installments_table.php

resources/
└── views/
    ├── components/
    │   ├── purchase-request-list.blade.php
    │   ├── purchase-request-form.blade.php
    │   ├── purchase-request-show.blade.php
    │   └── payment-order-form.blade.php
    │
    └── purchase_requests/

routes/
└── web.php
```

Os componentes Livewire utilizam a estrutura de componentes Single File Components (SFC) do Livewire.

---

# 🛠️ Instalação

## Pré-requisitos

Antes de executar o projeto, certifique-se de possuir instalado:

* PHP 8.2 ou superior;
* Composer;
* MySQL;
* Git.

---

## 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
```

Entrar na pasta do projeto:

```bash
cd purchase-management-system
```

---

## 2. Instalar as dependências

```bash
composer install
```

---

## 3. Configurar o ambiente

Copie o arquivo `.env.example`:

### Windows

```bash
copy .env.example .env
```

### Linux/macOS

```bash
cp .env.example .env
```

Depois gere a chave da aplicação:

```bash
php artisan key:generate
```

---

## 4. Configurar o banco de dados

Crie um banco de dados MySQL.

Exemplo:

```text
purchase_management_system
```

Depois configure as informações no arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=purchase_management_system
DB_USERNAME=root
DB_PASSWORD=
```

Ajuste `DB_USERNAME` e `DB_PASSWORD` de acordo com sua instalação do MySQL.

---

## 5. Executar as migrations

```bash
php artisan migrate
```

Isso criará as tabelas necessárias para o funcionamento do sistema.

---

## 6. Iniciar a aplicação

```bash
php artisan serve
```

A aplicação estará disponível, por padrão, em:

```text
http://127.0.0.1:8000
```

---

# 🧪 Testes

Para executar a suíte de testes:

```bash
php artisan test
```

Também é possível verificar as rotas disponíveis utilizando:

```bash
php artisan route:list
```

---

# 🔎 Testando o fluxo principal

Uma sequência recomendada para testar a aplicação é:

### 1. Criar uma solicitação

Acesse a tela de criação e informe:

* Descrição;
* Status;
* Ordem de Serviço, caso exista.

---

### 2. Visualizar a solicitação

Na listagem, acesse a opção de visualização.

A tela deverá apresentar os dados da solicitação e suas Ordens de Pagamento.

---

### 3. Criar pagamento à vista

Selecione:

```text
Tipo: À vista
```

Informe um valor, por exemplo:

```text
R$ 1.000,00
```

O sistema deverá gerar automaticamente uma parcela de:

```text
R$ 1.000,00
```

---

### 4. Criar pagamento parcelado

Selecione:

```text
Tipo: Parcelado
```

Informe:

```text
Valor: R$ 1.000,00
Parcelas: 3
```

O sistema deverá gerar:

```text
Parcela 1 → R$ 333,34
Parcela 2 → R$ 333,33
Parcela 3 → R$ 333,33
```

---

### 5. Marcar uma parcela como paga

Na tela de visualização da solicitação, utilize a ação:

```text
Marcar como pago
```

A parcela deverá mudar de:

```text
Pendente
```

para:

```text
Pago
```

---

# 📌 Observações

A Ordem de Serviço é uma entidade utilizada para demonstrar o relacionamento opcional com uma Solicitação de Compra.

O desafio não exige um CRUD específico de Ordens de Serviço. Portanto, caso o banco esteja inicialmente vazio, o campo de Ordem de Serviço exibirá apenas a opção:

```text
Nenhuma
```

Isso não impede o funcionamento das Solicitações de Compra.

Para testes, registros de Ordem de Serviço podem ser inseridos diretamente no banco ou através do Tinker.

Exemplo:

```bash
php artisan tinker
```

```php
\App\Models\ServiceOrder::create(['code' => 'OS-001']);
\App\Models\ServiceOrder::create(['code' => 'OS-002']);
```

Após isso, as Ordens de Serviço estarão disponíveis para seleção no formulário de Solicitação de Compra.

---

# 📈 Possíveis melhorias futuras

Como o objetivo deste projeto é atender ao escopo do desafio técnico, algumas funcionalidades não foram implementadas para manter a solução simples e objetiva.

Como possíveis evoluções:

* Autenticação de usuários;
* Controle de permissões;
* CRUD de Ordens de Serviço;
* Paginação;
* Filtros e busca de solicitações;
* Dashboard;
* Histórico de alterações;
* Notificações;
* Testes automatizados mais abrangentes;
* Controle de vencimento das parcelas;
* Relatórios financeiros.

Essas funcionalidades não são necessárias para o escopo atual do desafio.

---

# 👨‍💻 Autor

**Pedro Henrique**

Projeto desenvolvido como parte de um desafio técnico para avaliação de conhecimentos em desenvolvimento PHP/Laravel.
