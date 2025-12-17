<!--<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>-->

## Sobre 
Aplicação desenvolvida em Laravel 12 para cálculo inteligente de precificação de produtos. O sistema permite inserir múltiplos custos, taxas e o percentual de lucro desejado, calculando automaticamente o preço final ideal de venda. A plataforma realiza o cálculo baseado na fórmula real de markup inverso, considerando o impacto de custos fixos, taxas percentuais e margem de lucro para determinar o valor correto que garante a rentabilidade planejada.
O projeto utiliza Bootstrap, SCSS, Docker e Vite para uma arquitetura moderna, modular e de fácil expansão.

 ## 🚀Recursos principais

Autenticação completa (Laravel Breeze/Jetstream ou Personalizado)
Login, registro, recuperação de senha, verificação de e-mail e gerenciamento de perfil.

💳 Sistema de assinaturas
Integração com Stripe (ou outro gateway), renovação, cancelamento e controle de expiração.

📊 Painel de gerenciamento
Área autenticada para visualizar o status da conta, detalhes da assinatura e acessar funcionalidades da plataforma.

🎨 Interface moderna
Layout construído com Bootstrap 5, customizações em SCSS e build process com Vite.

🐳 Ambiente containerizado
Projeto configurado para rodar com Docker, garantindo padronização no desenvolvimento e facilidade no deploy.

🛠 Arquitetura limpa
Organização clara de controllers, models e migrations seguindo boas práticas atuais do Laravel 12.

## 🧰 Tecnologias utilizadas

| Tecnologia                  | Uso                        |
| --------------------------- | -------------------------- |
| **Laravel 12**              | Framework principal        |
| **PHP 8.2+**                | Linguagem                  |
| **MySQL / MariaDB**         | Banco de dados             |
| **Docker & Docker Compose** | Padronização de ambiente   |
| **Bootstrap 5**             | Interface                  |
| **SCSS**                    | Estilização customizada    |
| **Vite**                    | Compilação de assets       |
| **Alpine.js**               | JS reativo leve            |

## 📦 Instalação
1️⃣ Clone o repositório:
git clone https://github.com/seu-usuario/precificacao-app.git
cd precificacao-app

2️⃣ Configure o ambiente

Copie o arquivo .env.example para .env:

cp .env.example .env


Gere a chave da aplicação:

php artisan key:generate

## 🐳 Rodando com Docker
Suba os containers:
docker-compose up -d

Instale as dependências:
docker exec -it laravel-app composer install
docker exec -it laravel-app npm install

Rode as migrations:
docker exec -it laravel-app php artisan migrate

▶ Rodando o Vite (JS + SCSS)

Dentro do container ou localmente:

npm run dev


Ou para build de produção:

npm run build

## 🎨 Estrutura dos assets

SCSS principal:
resources/css/app.scss
CSS da Dashboard: 
resources/css/app.css

Bootstrap importado via SCSS

Vite configurado no arquivo:
vite.config.js

JavaScript:
resources/js/app.js
JS da Dashboard
resources/js/style.js 


## 🧪 Testes (opcional)
php artisan test

## 📖 Comandos úteis
Limpar cache:
php artisan optimize:clear

Criar migration:
php artisan make:migration create_table_name

## 📚 Estrutura do projeto (resumo)
app/
  Http/
  Models/
bootstrap/
config/
database/
public/
resources/
  css/
  js/
  views/
routes/
  web.php
  api.php

  
## 🧩 Diagrama de Arquitetura da Aplicação 
A aplicação segue uma arquitetura moderna baseada em Laravel 12, com separação clara entre backend, frontend, infraestrutura e integração com serviços externos.
## Fluxo geral

                        ┌──────────────────────────┐
                        │        Client (Web)       │
                        │  Browser + Bootstrap UI   │
                        └──────────────┬───────────┘
                                       │ HTTP/HTTPS
                                       ▼
                         ┌─────────────────────────┐
                         │      Laravel App        │
                         │  (Controllers / Routes) │
                         └─────────────┬──────────┘
                                       │
                                       ▼
                     ┌──────────────────────────────────┐
                     │           Application Layer       │
                     │  • Controllers                    │
                     │  • Policies / Gates               │
                     │  • Middlewares                    │
                     └──────────────────┬───────────────┘
                                        │
                                        ▼
                ┌───────────────────────────────────────────┐
                │               Domain Layer                 │
                │   • Models (User, Subscription, etc.)     │
                │   • Services (Billing, Pricing, Auth)     │
                │   • Regras de negócio                     │
                └───────────────────┬──────────────────────┘
                                    │
                                    ▼
                      ┌──────────────────────────────┐
                      │       Infra / Database        │
                      │  MySQL/MariaDB (Docker)       │
                      │  Migrations / Seeders         │
                      └──────────────────────────────┘


                  ┌─────────────────────────────────────────┐
                  │              Frontend (Vite)             │
                  │   • SCSS (Bootstrap customizado)         │
                  │   • JavaScript (Alpine.js opcional)      │
                  │   • Compilação e HMR                     │
                  └─────────────────────────────────────────┘


                   ┌──────────────────────────────────────┐
                   │           Docker Environment          │
                   │   • PHP-FPM Container                 │
                   │   • Nginx Container                   │
                   │   • Database Container                │
                   │   • Node/Vite                         │
                   └──────────────────────────────────────┘

## 📡 Fluxo Geral

1. O usuário acessa a interface via Browser.
2. O request é atendido pelo Nginx (Docker).
3. O Laravel processa a requisição:
   - Middlewares → Controllers → Services → Models.
4. O banco de dados é acessado via Models (Eloquent).
5. A resposta retorna para o Browser com UI renderizada em Blade.
6. Assets compilados pelo Vite (CSS/JS) fazem a camada visual interagir.

## Sistema de assinaturas 
                                ┌──────────────────────────┐
                                │          Usuário         │
                                │ (Escolhe um plano)       │
                                └────────────┬─────────────┘
                                             │ GET /planos
                                             ▼
                   ┌───────────────────────────────────────────────────┐
                   │                     Laravel 12                    │
                   └───────────────────────┬───────────────────────────┘
                                           │
                                           ▼
                          ┌───────────────────────────────────────┐
                          │             Rotas (web.php)           │
                          └──────────────────┬────────────────────┘
                                             │
                                             ▼
              ┌────────────────────────────────────────────────────────────────┐
              │                       PlanController                            │
              └──────────────────────────┬─────────────────────────────────────┘
                                         │
                                         │ 1. Lista Planos
                                         │ 2. Exibe página de aquisição do plano
                                         ▼
                         ┌───────────────────────────────────────────┐
                         │   View Blade: planos/index.blade.php     │
                         └─────────────────────┬─────────────────────┘
                                               │ Usuário clica em "Assinar"
                                               ▼
                                ┌────────────────────────────────────┐
                                │  Rota: plan.start_acquisition      │
                                └──────────────┬────────────────────┘
                                               │
                                               ▼
                       ┌────────────────────────────────────────────────────┐
                       │               StartAcquisitionController            │
                       └──────────────────────────┬─────────────────────────┘
                                                  │
                                                  │ 1. Recebe o plano
                                                  │ 2. Verifica se o usuário está logado
                                                  │ 3. Envia o usuário para o pagamento
                                                  ▼
                             ┌──────────────────────────────────────────────┐
                             │  View Blade: checkout/payment.blade.php      │
                             └────────────────────┬─────────────────────────┘
                                                  │
                                                  │ Usuário clica “Pagar”
                                                  ▼
                          ┌────────────────────────────────────────────────┐
                          │              Stripe API (Checkout)             │
                          └───────────────────────┬────────────────────────┘
                                                  │ Retorna sucesso
                                                  ▼
                   ┌───────────────────────────────────────────────────────────┐
                   │     SubscriptionController (webhook ou return handler)   │
                   └──────────────────────────┬────────────────────────────────┘
                                              │
                                              │ Cria registro em:
                                              │     subscriptions
                                              │
                                              ▼
                         ┌──────────────────────────────────────────────────┐
                         │     Tabela: subscriptions                        │
                         │--------------------------------------------------│
                         │ id                                               │
                         │ user_id                                          │
                         │ plan_id                                          │
                         │ stripe_id (opcional)                             │
                         │ status (active/canceled/pending)                 │
                         │ subscription_expires_at                          │
                         └──────────────────────────┬───────────────────────┘
                                                    │
                                                    ▼
                         ┌──────────────────────────────────────────────────┐
                         │           DashboardController                    │
                         └──────────────────────────┬───────────────────────┘
                                                    │ Carrega assinatura
                                                    ▼
                       ┌────────────────────────────────────────────────────────┐
                       │ View Blade: dashboard.blade.php                        │
                       │   - Exibe assinatura ativa                             │
                       │   - Data de expiração                                  │
                       │   - Botões gerenciar/cancelar                          │
                       └────────────────────────────────────────────────────────┘
                       ## 🔷 Pagamento via PIX (Mercado Pago)

O módulo de pagamento via PIX foi implementado utilizando a **API de Pagamentos do Mercado Pago (Checkout Transparente)**.

### Fluxo resumido:
1. O usuário clica em **"Gerar QR Code PIX"**
2. A aplicação envia a requisição para o backend
3. O backend cria um pagamento PIX no Mercado Pago
4. O Mercado Pago retorna o QR Code
5. O QR Code é exibido para o usuário
6. O pagamento fica com status **pendente** até a confirmação

Este fluxo permite total controle do pagamento sem redirecionamento para páginas externas.

## 🧩 Diagrama de Arquitetura — Pagamento PIX

```text
┌──────────────┐
│   Usuário    │
│ (Navegador)  │
└──────┬───────┘
       │ Clique em "Gerar PIX"
       ▼
┌──────────────────────┐
│   View (Blade)       │
│ JavaScript (Fetch)   │
└──────┬───────────────┘
       │ POST /payment/pix
       ▼
┌──────────────────────┐
│ PaymentController    │
│ createPix()          │
└──────┬───────────────┘
       │ SDK Mercado Pago
       ▼
┌──────────────────────┐
│ Mercado Pago API     │
│ Pagamento PIX        │
└──────┬───────────────┘
       │ QR Code PIX
       ▼
┌──────────────────────┐
│ Retorno JSON         │
│ (qr_code / base64)   │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ Exibição do QR Code  │
│ na Interface         │
└──────────────────────┘


## Cálculo
                              ┌──────────────────────────┐
                              │        Usuário           │
                              │(insere custos, taxas,    │
                              │ lucro e nome do produto) │
                              └─────────────┬────────────┘
                                            │ Request
                                            ▼
                       ┌─────────────────────────────────────────┐
                       │              Laravel 12                  │
                       └───────────────────┬─────────────────────┘
                                           │
                                           ▼
                           ┌─────────────────────────────────┐
                           │        Rotas (web.php)          │
                           └───────────────┬─────────────────┘
                                           │ encaminha para
                                           ▼
                     ┌────────────────────────────────────────────┐
                     │        CalculoController@create            │
                     └────────────────────────┬───────────────────┘
                                              │
                                              ▼
                   ┌────────────────────────────────────────────────┐
                   │      Lógica de Cálculo (Markup Inverso)        │
                   └────────────────────────────────────────────────┘
# Cálculo do Valor do Produto

Este fluxo descreve como a aplicação calcula o valor total e o custo unitário de um produto, considerando custos, taxas, margem de lucro e quantidade de unidades.

1) Recebe os dados do formulário:
nome_produto
custos[] (lista de custos do produto)
taxas[] (lista de taxas aplicáveis)
lucro% (margem desejada)
qtdeProd (quantidade de unidades, inteiro ≥ 1)

2) Limpa valores vazios:
custos = array_filter(custos)
taxas  = array_filter(taxas)

3) Valida quantidade de unidades:
Se qtdeProd não existir ou for menor que 1, define qtdeProd = 1
Garante que não haja divisão por zero

4) Calcula os valores:
custo_total = soma(custos)
taxa_total = soma(taxas)

5) Converte para decimal:
taxa_decimal  = taxa_total / 100
lucro_decimal = lucro / 100


6) Calcula denominador:
denominador = 1 - taxa_decimal - lucro_decimal

7) Calcula valor do produto somente se denominador > 0:
if denominador > 0:
    valor_produto = custo_total / denominador
else:
    valor_produto = 0

8) Calcula custo unitário por unidade:
custo_unitario = valor_produto / qtdeProd

9) Retorna os valores para a View:
valor_produto (total do produto)
custo_unitario (por unidade)
qtdeProd (quantidade)



                                              │
                                              ▼
                        ┌────────────────────────────────────┐
                        │              View Blade            │
                        │ (formulário + resultado do cálculo)│
                        └──────────────────┬─────────────────┘
                                            │
                                            ▼
                     ┌──────────────────────────────────────────────┐
                     │         Layout (Bootstrap + SCSS + Vite)     │
                     │  - Navbar / Menu                             │
                     │  - Estilos compilados pelo Vite              │
                     │  - Scripts (JS + Alpine opcional)            │
                     └─────────────────────────┬────────────────────┘
                                               │ compila assets
                                               ▼
                              ┌───────────────────────────────────┐
                              │       Vite + Node Modules        │
                              │  - Compila SCSS → CSS            │
                              │  - Importa Bootstrap             │
                              │  - Importa scripts JS            │
                              └──────────────────┬────────────────┘
                                                 │
                                                 ▼
                               ┌────────────────────────────────┐
                               │            Docker              │
                               │  - Container PHP-FPM           │
                               │  - Container Nginx             │
                               │  - Container MySQL             │
                               └────────────────────────────────┘


✅ Resumo do Fluxo

Usuário envia os custos, taxas e lucro

Controller processa, aplica o markup inverso e calcula o preço final

View exibe o resultado

Vite compila SCSS, Bootstrap e JS

Tudo roda dentro do ambiente Docker

&copy; Rosiane C S Passos - Todos os direitos reservados 
