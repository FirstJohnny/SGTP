<div align="center">

```
███████╗ ██████╗ ████████╗██████╗ 
██╔════╝██╔════╝ ╚══██╔══╝██╔══██╗
███████╗██║  ███╗   ██║   ██████╔╝
╚════██║██║   ██║   ██║   ██╔═══╝ 
███████║╚██████╔╝   ██║   ██║     
╚══════╝ ╚═════╝    ╚═╝   ╚═╝     
```

### Sistema de Gestão de Transportes Públicos

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Redis](https://img.shields.io/badge/Redis-Cache-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)
[![License](https://img.shields.io/badge/Licença-MIT-green?style=for-the-badge)](LICENSE)

*Plataforma completa para gestão inteligente de frotas e transporte coletivo*

[🚀 Demo](#demo) · [📖 Documentação](#documentação-da-api) · [🐛 Reportar Bug](issues) · [✨ Solicitar Feature](issues)

</div>

---

## 📋 Visão Geral

O **SGTP** é uma solução tecnológica abrangente desenvolvida para empresas e órgãos responsáveis pelo transporte coletivo. A plataforma integra **rastreamento GPS em tempo real**, **bilhetagem digital**, **gestão financeira** e **relatórios analíticos** em um único ecossistema robusto.

```
┌─────────────────────────────────────────────────────────────────┐
│                        SGTP · Arquitetura                       │
├──────────────┬──────────────┬──────────────┬────────────────────┤
│   🗺️ Rotas   │  🚌 Frota    │  🎫 Bilhética │  📊 Financeiro     │
│  & Horários  │  & Veículos  │  & Tarifas   │  & Relatórios      │
├──────────────┴──────────────┴──────────────┴────────────────────┤
│              🔴 GPS Tracking · Tempo Real                        │
├─────────────────────────────────────────────────────────────────┤
│   👥 Motoristas · Fiscais · Cobradores · Operadores             │
├─────────────────────────────────────────────────────────────────┤
│         🔐 Autenticação · 2FA · RBAC · Logs de Auditoria        │
└─────────────────────────────────────────────────────────────────┘
```

---

## ✨ Funcionalidades Principais

| Módulo | Funcionalidades |
|--------|----------------|
| 🗺️ **Operações** | Gestão de rotas, horários, escalas e monitoramento em tempo real |
| 🚌 **Frota** | Cadastro de veículos, documentos, manutenções e abastecimentos |
| 🎫 **Bilhética** | Venda de bilhetes, QR Code, tarifas diferenciadas, fechamento de caixa |
| 👷 **Colaboradores** | Motoristas, fiscais, cobradores, registro de ponto |
| 📡 **GPS** | Rastreamento ao vivo, histórico de percursos, alertas de desvio |
| 💰 **Financeiro** | Receitas, despesas, fluxo de caixa, pagamento de colaboradores |
| 🚨 **Ocorrências** | Registro de incidentes, alertas automáticos, notificações |
| 📈 **Relatórios** | Operacionais, financeiros, desempenho — exportação PDF/Excel |
| 🌐 **API Pública** | Consulta de rotas, horários e feedback de passageiros |

---

## 🛠️ Stack Tecnológica

### Backend
- **[Laravel 11.x](https://laravel.com)** — Framework PHP robusto e elegante
- **[MySQL 8.0](https://mysql.com)** — Banco de dados relacional
- **[Laravel Sanctum](https://laravel.com/docs/sanctum)** — Autenticação via API tokens
- **[Laravel Horizon](https://laravel.com/docs/horizon)** — Gerenciamento de filas Redis
- **[Laravel Telescope](https://laravel.com/docs/telescope)** — Debug e monitoramento (dev)

### Frontend
- **[Blade](https://laravel.com/docs/blade)** — Template engine
- **[Tailwind CSS](https://tailwindcss.com)** + **[Bootstrap 5](https://getbootstrap.com)** — Estilização
- **[Livewire](https://livewire.laravel.com)** — Componentes dinâmicos
- **[Alpine.js](https://alpinejs.dev)** — Interatividade leve
- **[Chart.js](https://chartjs.org)** — Gráficos e dashboards
- **[Leaflet.js](https://leafletjs.com)** — Mapas e geolocalização

### Infraestrutura
- **[Docker](https://docker.com)** — Containerização
- **[Redis](https://redis.io)** — Cache e filas
- **[Nginx](https://nginx.org)** — Servidor web
- **[GitHub Actions](https://github.com/features/actions)** — CI/CD

---

## 🏗️ Estrutura do Projeto

<details>
<summary><b>📁 Ver estrutura completa de diretórios</b></summary>

```
sgtp/
├── app/
│   ├── Console/Commands/
│   │   ├── CheckDocumentExpiration.php      # ⏰ Verifica vencimento de documentos
│   │   ├── GenerateMaintenanceSchedule.php  # 🔧 Gera manutenções preventivas
│   │   ├── SyncGPSData.php                  # 📡 Sincroniza dados GPS
│   │   └── SendDailyReports.php             # 📊 Envia relatórios diários
│   │
│   ├── Http/Controllers/
│   │   ├── Admin/           # 👑 Usuários, permissões, auditoria, configurações
│   │   ├── Operacao/        # 🗺️ Rotas, horários, escalas, monitoramento
│   │   ├── Frota/           # 🚌 Veículos, documentos, manutenções, combustível
│   │   ├── Colaborador/     # 👷 Motoristas, fiscais, cobradores, ponto
│   │   ├── Bilhetica/       # 🎫 Tarifas, vendas, validação, relatórios
│   │   ├── Financeiro/      # 💰 Receitas, despesas, caixa, pagamentos
│   │   ├── Ocorrencia/      # 🚨 Ocorrências e alertas automáticos
│   │   ├── Relatorio/       # 📈 Operacional, financeiro, desempenho, exportação
│   │   ├── Api/V1/          # 🔌 GPS, App Mobile, Integrações externas
│   │   └── Publico/         # 🌐 Consulta, feedback, planejamento de viagens
│   │
│   ├── Models/              # 📦 22 modelos Eloquent completos
│   ├── Services/            # ⚙️ GPS, Notificações, Relatórios, Billing
│   ├── Jobs/                # ⚡ Processamento assíncrono via filas
│   ├── Events/ & Listeners/ # 📣 Eventos de sistema e notificações
│   └── Middleware/          # 🛡️ RBAC, logs de atividade, 2FA
│
├── database/
│   ├── migrations/          # 📄 24 migrations completas
│   ├── seeders/             # 🌱 Dados iniciais e de teste
│   └── factories/           # 🏭 Factories para testes
│
├── resources/views/         # 🎨 Templates Blade organizados por módulo
├── routes/                  # 🛣️ Web, API e Console
└── tests/                   # 🧪 Unit, Feature e Browser (Dusk)
```

</details>

---

## 🗄️ Modelo de Dados

<details>
<summary><b>📊 Ver entidades principais</b></summary>

```
User                    Veiculo                  Motorista
├── id                  ├── id                   ├── id
├── name                ├── placa                ├── user_id
├── email               ├── chassi               ├── nome
├── role*               ├── marca / modelo        ├── bi
├── two_factor_secret   ├── ano / lotacao         ├── carta_conducao
├── is_active           ├── tipo_combustivel      ├── carta_validade
└── last_login          ├── status†              └── status
                        └── km_atual
* admin | gestor_operacoes | gestor_frota
  fiscal | operador_bilhetica | motorista | financeiro
† ativo | inativo | manutencao

Escala                  Bilhete                  GpsTracking
├── veiculo_id          ├── codigo_unico          ├── veiculo_id
├── motorista_id        ├── rota_id               ├── latitude
├── cobrador_id         ├── valor                 ├── longitude
├── rota_id             ├── tipo_passageiro‡      ├── velocidade
├── horario_inicio      ├── data_venda            ├── direcao
├── horario_fim         ├── data_validade         ├── timestamp
└── status              └── status§               └── status_veiculo

‡ normal | estudante | idoso
§ vendido | validado | expirado
```

</details>

---

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP **8.1+**
- Composer
- MySQL **8.0**
- Node.js **18+** e NPM
- Redis *(recomendado)*

### Instalação Rápida

```bash
# 1. Clonar o repositório
git clone https://github.com/seu-usuario/sgtp.git && cd sgtp

# 2. Instalar dependências
composer install && npm install

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 4. Banco de dados
php artisan migrate --seed

# 5. Compilar assets e iniciar
php artisan storage:link
npm run build
php artisan serve
```

### Com Docker

```bash
docker compose up -d
docker compose exec app php artisan migrate --seed
```

### Configurar Filas e Schedule

```bash
# Worker de filas
php artisan queue:work

# Cron (adicionar ao crontab)
* * * * * php /path/artisan schedule:run >> /dev/null 2>&1
```

---

## 🔐 Variáveis de Ambiente

```env
# Aplicação
APP_NAME=SGTP
APP_ENV=production
APP_URL=https://seu-dominio.com

# Banco de dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sgtp
DB_USERNAME=sgtp_user
DB_PASSWORD=senha_segura

# Cache e Filas
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Integração GPS
GPS_API_URL=https://api.gpsprovider.com
GPS_API_KEY=sua_chave_api

# SMS Gateway
SMS_API_KEY=sua_chave_sms
SMS_SENDER=SGTP

# Email
MAIL_MAILER=smtp
MAIL_HOST=seu.smtp.host
MAIL_PORT=587
MAIL_USERNAME=seu@email.com
MAIL_PASSWORD=senha_email
```

---

## 🔌 API REST

A documentação interativa está disponível em `/api/documentation` após a instalação.

### Endpoints Principais

```http
### Autenticação
POST   /api/login
POST   /api/logout

### GPS Tracking
POST   /api/v1/gps/update           # Atualizar posição do veículo
GET    /api/v1/gps/vehicles          # Listar veículos ativos
GET    /api/v1/gps/vehicle/{id}      # Posição de veículo específico

### Bilhética
POST   /api/v1/tickets/sell          # Vender bilhete
GET    /api/v1/tickets/{code}        # Consultar bilhete por código
POST   /api/v1/tickets/validate      # Validar bilhete (QR Code)

### Ocorrências
POST   /api/v1/occurrences           # Registrar ocorrência
GET    /api/v1/occurrences           # Listar ocorrências

### Consulta Pública (sem autenticação)
GET    /api/public/routes            # Rotas disponíveis
GET    /api/public/schedules         # Horários por rota
POST   /api/public/feedback          # Enviar feedback
```

---

## 👥 Papéis e Permissões

| Papel | Descrição | Nível de Acesso |
|-------|-----------|-----------------|
| 👑 **Administrador** | Acesso total ao sistema | ██████████ |
| 🗺️ **Gestor Operações** | Rotas, horários, escalas e monitoramento | ████████░░ |
| 🚌 **Gestor Frota** | Veículos, manutenções e abastecimentos | ███████░░░ |
| 🔍 **Fiscal** | Escalas, ocorrências e validação de bilhetes | █████░░░░░ |
| 🎫 **Operador Bilhética** | Venda de bilhetes e fechamento de caixa | █████░░░░░ |
| 🚌 **Motorista** | Escalas próprias, ponto e ocorrências básicas | ███░░░░░░░ |
| 💰 **Financeiro** | Receitas, despesas e relatórios financeiros | ████░░░░░░ |

---

## 🧪 Testes

```bash
# Suite completa
php artisan test

# Por categoria
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Com relatório de cobertura
php artisan test --coverage

# Testes de navegador (Dusk)
php artisan dusk
```

---

## 📈 Roadmap

```
✅ Fase 1 — MVP
   Autenticação · Usuários · Veículos · Motoristas · Rotas · Bilhetes básicos

🔄 Fase 2 — Monitoramento  (em andamento)
   Integração GPS · Mapa em tempo real · Alertas de desvio · Histórico

⏳ Fase 3 — Bilhética Avançada
   QR Code · App mobile · Pagamento digital · Cartão de transporte

🔮 Fase 4 — Análise & IA
   KPIs avançados · Previsão de demanda · Otimização de rotas · IA preditiva
```

---

## 🤝 Contribuição

Contribuições são muito bem-vindas! Siga os passos:

```bash
# 1. Fork e clone
git clone https://github.com/seu-usuario/sgtp.git

# 2. Criar branch
git checkout -b feature/minha-feature

# 3. Commit com mensagem clara
git commit -m "feat: adicionar validação de QR Code offline"

# 4. Push e Pull Request
git push origin feature/minha-feature
```

> Consulte [CONTRIBUTING.md](CONTRIBUTING.md) para diretrizes detalhadas.

---

## 📄 Licença

Distribuído sob a Licença MIT. Veja [LICENSE](LICENSE) para mais informações.

---

<div align="center">

**SGTP** — Desenvolvido com ❤️ para modernizar o transporte público

[![Made with Laravel](https://img.shields.io/badge/Made%20with-Laravel-FF2D20?style=flat&logo=laravel)](https://laravel.com)

</div>
