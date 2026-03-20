SGTP - Sistema de Gestão de Transportes Públicos
📋 Sobre o Projeto
O Sistema de Gestão de Transportes Públicos (SGTP) é uma solução tecnológica abrangente desenvolvida para auxiliar empresas e órgãos responsáveis pelo transporte coletivo no planeamento, monitoramento e controle das operações diárias. O sistema atende desde o cadastro de veículos e colaboradores até o monitoramento em tempo real e gestão financeira.

🎯 Objetivos
Fornecer uma plataforma completa para gestão de frotas de transportes públicos

Permitir monitoramento em tempo real da localização dos veículos via GPS

Controlar fluxo de passageiros e gestão de bilhética

Gerar relatórios operacionais, financeiros e de desempenho

Melhorar a eficiência e qualidade dos serviços de transporte

🛠️ Tecnologias Utilizadas
Backend
Laravel 11.x - Framework PHP para desenvolvimento robusto

MySQL 8.0 - Banco de dados relacional

Laravel Breeze - Autenticação e gerenciamento de usuários

Laravel Sanctum - API tokens para integrações

Laravel Telescope - Debug e monitoramento (ambiente desenvolvimento)

Laravel Horizon - Gerenciamento de filas (jobs)

Frontend
Blade - Template engine do Laravel

Tailwind CSS - Framework CSS utilitário

Bootstrap 5 - Componentes UI

Alpine.js - Interatividade leve

Livewire - Componentes dinâmicos

Chart.js - Gráficos e dashboards

Leaflet.js - Mapas e geolocalização

Infraestrutura
Docker - Containerização (opcional)

Redis - Cache e filas

Nginx - Servidor web

GitHub Actions - CI/CD

Ferramentas de Desenvolvimento
PHPUnit - Testes unitários

Laravel Dusk - Testes de navegador

PHPStan - Análise estática

Laravel Pint - Formatação de código

📁 Estrutura de Diretórios
text
sgtp/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   ├── CheckDocumentExpiration.php      # Verifica vencimento de documentos
│   │   │   ├── GenerateMaintenanceSchedule.php  # Gera manutenções preventivas
│   │   │   ├── SyncGPSData.php                  # Sincroniza dados GPS
│   │   │   └── SendDailyReports.php             # Envia relatórios diários
│   │   └── Kernel.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── UserController.php           # Gestão de usuários
│   │   │   │   ├── RoleController.php           # Gestão de permissões
│   │   │   │   ├── AuditLogController.php       # Logs de auditoria
│   │   │   │   └── SystemConfigController.php   # Configurações do sistema
│   │   │   │
│   │   │   ├── Operacao/
│   │   │   │   ├── RotaController.php           # Gestão de rotas
│   │   │   │   ├── HorarioController.php        # Gestão de horários
│   │   │   │   ├── EscalaController.php         # Gestão de escalas
│   │   │   │   └── MonitoramentoController.php  # Monitoramento em tempo real
│   │   │   │
│   │   │   ├── Frota/
│   │   │   │   ├── VeiculoController.php        # Gestão de veículos
│   │   │   │   ├── DocumentoController.php      # Gestão de documentos
│   │   │   │   ├── ManutencaoController.php     # Gestão de manutenções
│   │   │   │   └── AbastecimentoController.php  # Gestão de combustível
│   │   │   │
│   │   │   ├── Colaborador/
│   │   │   │   ├── MotoristaController.php      # Gestão de motoristas
│   │   │   │   ├── FiscalController.php         # Gestão de fiscais
│   │   │   │   ├── CobradorController.php       # Gestão de cobradores
│   │   │   │   └── PontoController.php          # Registro de ponto
│   │   │   │
│   │   │   ├── Bilhetica/
│   │   │   │   ├── TarifaController.php         # Gestão de tarifas
│   │   │   │   ├── VendaController.php          # Venda de bilhetes
│   │   │   │   ├── ValidacaoController.php      # Validação de bilhetes
│   │   │   │   └── RelatorioController.php      # Relatórios de vendas
│   │   │   │
│   │   │   ├── Financeiro/
│   │   │   │   ├── ReceitaController.php        # Gestão de receitas
│   │   │   │   ├── DespesaController.php        # Gestão de despesas
│   │   │   │   ├── FluxoCaixaController.php     # Fluxo de caixa
│   │   │   │   └── PagamentoController.php      # Pagamento colaboradores
│   │   │   │
│   │   │   ├── Ocorrencia/
│   │   │   │   ├── OcorrenciaController.php     # Registro de ocorrências
│   │   │   │   └── AlertaController.php         # Alertas automáticos
│   │   │   │
│   │   │   ├── Relatorio/
│   │   │   │   ├── OperacionalController.php    # Relatórios operacionais
│   │   │   │   ├── FinanceiroController.php     # Relatórios financeiros
│   │   │   │   ├── DesempenhoController.php     # Relatórios de desempenho
│   │   │   │   └── ExportController.php         # Exportação de dados
│   │   │   │
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── GPSController.php        # API de GPS
│   │   │   │   │   ├── AppController.php        # API para apps mobile
│   │   │   │   │   └── IntegracaoController.php # API para integrações
│   │   │   │   └── WebhookController.php        # Webhooks
│   │   │   │
│   │   │   └── Publico/
│   │   │       ├── ConsultaController.php       # Consulta pública
│   │   │       ├── FeedbackController.php       # Feedback de passageiros
│   │   │       └── PlanejamentoController.php   # Planejamento de viagens
│   │   │
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php                    # Verificação de permissões
│   │   │   ├── LogUserActivity.php              # Log de atividades
│   │   │   └── TwoFactorAuth.php                # Autenticação 2 fatores
│   │   │
│   │   ├── Requests/
│   │   │   ├── Admin/
│   │   │   ├── Frota/
│   │   │   ├── Operacao/
│   │   │   └── Bilhetica/
│   │   │
│   │   └── Resources/
│   │
│   ├── Models/
│   │   ├── User.php                              # Modelo de usuário
│   │   ├── Veiculo.php                           # Veículo
│   │   ├── Motorista.php                         # Motorista
│   │   ├── Cobrador.php                          # Cobrador
│   │   ├── Fiscal.php                            # Fiscal
│   │   ├── Rota.php                              # Rota/linha
│   │   ├── Horario.php                           # Horário
│   │   ├── Escala.php                            # Escala de trabalho
│   │   ├── Bilhete.php                           # Bilhete
│   │   ├── Venda.php                             # Venda de bilhetes
│   │   ├── Validacao.php                         # Validação de bilhetes
│   │   ├── Manutencao.php                        # Manutenção
│   │   ├── Abastecimento.php                     # Abastecimento
│   │   ├── Ocorrencia.php                        # Ocorrência
│   │   ├── Documento.php                         # Documento
│   │   ├── GpsTracking.php                       # Rastreamento GPS
│   │   ├── Receita.php                           # Receita
│   │   ├── Despesa.php                           # Despesa
│   │   ├── FluxoCaixa.php                        # Fluxo de caixa
│   │   ├── Alerta.php                            # Alerta
│   │   ├── Feedback.php                          # Feedback passageiros
│   │   ├── LogAuditoria.php                      # Log de auditoria
│   │   ├── Permissao.php                         # Permissão
│   │   └── Configuracao.php                      # Configuração do sistema
│   │
│   ├── Providers/
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   │
│   ├── Services/
│   │   ├── GPS/
│   │   │   ├── GPSIntegrationService.php        # Integração com API GPS
│   │   │   └── GeocodingService.php              # Serviço de geocodificação
│   │   │
│   │   ├── Notification/
│   │   │   ├── SmsService.php                   # Envio de SMS
│   │   │   ├── PushNotificationService.php      # Push notifications
│   │   │   └── EmailService.php                 # Envio de emails
│   │   │
│   │   ├── Report/
│   │   │   ├── PDFGenerator.php                 # Geração de PDF
│   │   │   └── ExcelGenerator.php               # Geração de Excel
│   │   │
│   │   ├── Billing/
│   │   │   ├── TariffCalculator.php              # Cálculo de tarifas
│   │   │   └── PaymentProcessor.php              # Processamento de pagamentos
│   │   │
│   │   └── Validation/
│   │       ├── DocumentValidator.php             # Validação de documentos
│   │       └── ScheduleValidator.php             # Validação de escalas
│   │
│   ├── Jobs/
│   │   ├── ProcessGPSData.php                    # Processamento de dados GPS
│   │   ├── SendExpirationAlerts.php              # Envio de alertas de vencimento
│   │   ├── GenerateDailyReports.php              # Geração de relatórios diários
│   │   └── SyncExternalData.php                  # Sincronização com sistemas externos
│   │
│   ├── Events/
│   │   ├── VehicleOffRoute.php                   # Evento de veículo fora da rota
│   │   ├── DocumentExpired.php                   # Evento de documento vencido
│   │   ├── NewOccurrence.php                     # Evento de nova ocorrência
│   │   └── TicketValidated.php                   # Evento de bilhete validado
│   │
│   └── Listeners/
│       ├── SendOffRouteAlert.php                 # Envia alerta de desvio
│       ├── NotifyDocumentExpiration.php          # Notifica vencimento
│       ├── LogOccurrence.php                     # Registra ocorrência
│       └── UpdateFleetMetrics.php                # Atualiza métricas
│
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_veiculos_table.php
│   │   ├── 2024_01_01_000002_create_motoristas_table.php
│   │   ├── 2024_01_01_000003_create_cobradores_table.php
│   │   ├── 2024_01_01_000004_create_fiscais_table.php
│   │   ├── 2024_01_01_000005_create_rotas_table.php
│   │   ├── 2024_01_01_000006_create_horarios_table.php
│   │   ├── 2024_01_01_000007_create_escalas_table.php
│   │   ├── 2024_01_01_000008_create_bilhetes_table.php
│   │   ├── 2024_01_01_000009_create_vendas_table.php
│   │   ├── 2024_01_01_000010_create_validacoes_table.php
│   │   ├── 2024_01_01_000011_create_manutencoes_table.php
│   │   ├── 2024_01_01_000012_create_abastecimentos_table.php
│   │   ├── 2024_01_01_000013_create_ocorrencias_table.php
│   │   ├── 2024_01_01_000014_create_documentos_table.php
│   │   ├── 2024_01_01_000015_create_gps_trackings_table.php
│   │   ├── 2024_01_01_000016_create_receitas_table.php
│   │   ├── 2024_01_01_000017_create_despesas_table.php
│   │   ├── 2024_01_01_000018_create_fluxo_caixa_table.php
│   │   ├── 2024_01_01_000019_create_alertas_table.php
│   │   ├── 2024_01_01_000020_create_feedbacks_table.php
│   │   ├── 2024_01_01_000021_create_log_auditoria_table.php
│   │   ├── 2024_01_01_000022_create_permissoes_table.php
│   │   ├── 2024_01_01_000023_create_configuracoes_table.php
│   │   └── 2024_01_01_000024_create_notifications_table.php
│   │
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   ├── PermissoesSeeder.php
│   │   ├── ConfiguracoesSeeder.php
│   │   └── TestDataSeeder.php
│   │
│   └── factories/
│       ├── UserFactory.php
│       ├── VeiculoFactory.php
│       ├── MotoristaFactory.php
│       └── BilheteFactory.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   └── header.blade.php
│   │   │
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── two-factor.blade.php
│   │   │   └── verify-email.blade.php
│   │   │
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── users/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── permissions/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   └── audit/
│   │   │       └── logs.blade.php
│   │   │
│   │   ├── frota/
│   │   │   ├── veiculos/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── documentos/
│   │   │   │   └── index.blade.php
│   │   │   ├── manutencoes/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── calendar.blade.php
│   │   │   └── abastecimentos/
│   │   │       ├── index.blade.php
│   │   │       └── create.blade.php
│   │   │
│   │   ├── operacao/
│   │   │   ├── rotas/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── map.blade.php
│   │   │   ├── horarios/
│   │   │   │   └── index.blade.php
│   │   │   ├── escalas/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── calendar.blade.php
│   │   │   └── monitoramento/
│   │   │       ├── mapa.blade.php
│   │   │       ├── veiculos.blade.php
│   │   │       └── historico.blade.php
│   │   │
│   │   ├── colaboradores/
│   │   │   ├── motoristas/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   └── escala.blade.php
│   │   │   ├── fiscais/
│   │   │   │   └── index.blade.php
│   │   │   └── ponto/
│   │   │       └── registrar.blade.php
│   │   │
│   │   ├── bilhetica/
│   │   │   ├── tarifas/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   ├── vendas/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── fechar-caixa.blade.php
│   │   │   └── validacoes/
│   │   │       └── qrcode.blade.php
│   │   │
│   │   ├── financeiro/
│   │   │   ├── receitas/
│   │   │   │   └── index.blade.php
│   │   │   ├── despesas/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── create.blade.php
│   │   │   └── fluxo-caixa/
│   │   │       └── index.blade.php
│   │   │
│   │   ├── ocorrencias/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── show.blade.php
│   │   │
│   │   ├── relatorios/
│   │   │   ├── operacional/
│   │   │   │   ├── cumprimento-horarios.blade.php
│   │   │   │   ├── desempenho-motoristas.blade.php
│   │   │   │   └── ocupacao-veiculos.blade.php
│   │   │   ├── financeiro/
│   │   │   │   ├── receitas.blade.php
│   │   │   │   ├── despesas.blade.php
│   │   │   │   └── fluxo-caixa.blade.php
│   │   │   └── dashboards/
│   │   │       ├── admin.blade.php
│   │   │       ├── operacional.blade.php
│   │   │       └── financeiro.blade.php
│   │   │
│   │   └── publico/
│   │       ├── consulta/
│   │       │   ├── rotas.blade.php
│   │       │   ├── horarios.blade.php
│   │       │   └── planejar-viagem.blade.php
│   │       └── feedback/
│   │           └── create.blade.php
│   │
│   ├── css/
│   │   ├── app.css
│   │   ├── admin.css
│   │   └── custom.css
│   │
│   ├── js/
│   │   ├── app.js
│   │   ├── admin.js
│   │   ├── maps.js
│   │   ├── charts.js
│   │   └── components/
│   │       ├── gps-tracker.js
│   │       ├── qrcode-scanner.js
│   │       └── notifications.js
│   │
│   └── lang/
│       ├── pt/
│       │   ├── auth.php
│       │   ├── messages.php
│       │   └── validation.php
│       └── en/
│
├── routes/
│   ├── web.php                      # Rotas web (autenticadas)
│   ├── api.php                      # Rotas API (v1)
│   ├── admin.php                    # Rotas administrativas
│   ├── public.php                   # Rotas públicas
│   └── console.php                  # Rotas para comandos
│
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── vendors/
│   └── uploads/
│       ├── documentos/
│       ├── fotos/
│       └── temporario/
│
├── tests/
│   ├── Unit/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Jobs/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Frota/
│   │   ├── Operacao/
│   │   ├── Bilhetica/
│   │   └── Relatorios/
│   └── Browser/
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── cache.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── telescope.php
│   ├── horizon.php
│   ├── gps.php                      # Configurações GPS
│   ├── billing.php                  # Configurações bilhética
│   ├── notification.php             # Configurações notificações
│   └── permissions.php              # Configurações de permissões
│
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
├── phpunit.xml
├── docker-compose.yml
├── Dockerfile
├── README.md
└── LICENSE
🚀 Funcionalidades Principais
👥 Gestão de Atores
Ator	Funcionalidades
Administrador	Gestão de usuários, permissões, auditoria, configurações do sistema
Gestor de Operações	Planejamento de rotas, horários, escalas, monitoramento em tempo real
Gestor de Frota	Cadastro de veículos, manutenções, abastecimentos, documentação
Fiscal	Fiscalização de horários, registro de ocorrências, validação de bilhetes
Operador de Bilhética	Venda de bilhetes, fechamento de caixa, gestão de estoque
Motorista	Visualização de escalas, registro de ponto, reporte de ocorrências
Departamento Financeiro	Gestão de receitas/despesas, fluxo de caixa, pagamentos
Passageiro	Consulta de rotas/horários, feedback, planejamento de viagens
📦 Módulos do Sistema
Gestão de Frota (Veículos)

Cadastro de veículos com dados completos

Gestão de documentos (seguro, inspeção)

Controle de manutenções preventivas/corretivas

Registro de abastecimentos

Gestão de Colaboradores

Cadastro de motoristas, cobradores e fiscais

Controle de documentação (carteira de motorista)

Atribuição de escalas

Registro de ponto

Gestão Operacional

Cadastro de rotas e pontos de paragem

Definição de horários e frequências

Criação de escalas de trabalho

Monitoramento de cumprimento de horários

Monitoramento em Tempo Real (GPS)

Rastreamento de veículos no mapa

Alertas de desvio de rota

Histórico de percursos

Status dos veículos (circulando/parado/fora de rota)

Bilhética e Controle de Passageiros

Definição de tarifas por rota/tipo de passageiro

Venda de bilhetes (físicos/eletrônicos)

Validação via QR Code

Controle de lotação

Financeiro

Consolidação de receitas

Registro de despesas

Fluxo de caixa

Processamento de pagamentos

Manutenção e Ocorrências

Registro de ocorrências (avarias, acidentes)

Agendamento de manutenções

Histórico de manutenções

Relatórios

Cumprimento de horários

Desempenho de motoristas

Ocupação de veículos

Dashboards com KPIs

📊 Modelo de Dados
Principais Entidades
text
User (extends Authenticatable)
├── id
├── name
├── email
├── password
├── role (admin, gestor_operacoes, gestor_frota, fiscal, operador_bilhetica, motorista, financeiro)
├── two_factor_secret
├── two_factor_recovery_codes
├── is_active
└── last_login

Veiculo
├── id
├── placa
├── chassi
├── marca
├── modelo
├── ano
├── lotacao
├── tipo_combustivel
├── status (ativo, inativo, manutencao)
├── km_atual
└── ...

Motorista
├── id
├── user_id
├── nome
├── bi
├── carta_conducao
├── carta_validade
├── contato
└── status

Rota
├── id
├── nome
├── trajeto (geometria)
├── pontos_paragem (json)
├── distancia_km
└── duracao_estimada

Escala
├── id
├── veiculo_id
├── motorista_id
├── cobrador_id
├── rota_id
├── horario_inicio
├── horario_fim
└── status

Bilhete
├── id
├── codigo_unico
├── rota_id
├── valor
├── tipo_passageiro (normal, estudante, idoso)
├── data_venda
├── data_validade
├── status (vendido, validado, expirado)
└── ponto_venda_id

GpsTracking
├── id
├── veiculo_id
├── latitude
├── longitude
├── velocidade
├── direcao
├── timestamp
└── status_veiculo
🔧 Instalação e Configuração
Pré-requisitos
PHP 8.1 ou superior

Composer

MySQL 8.0

Node.js 18+ e NPM

Redis (opcional, para filas e cache)

Passos para Instalação
bash
# 1. Clonar o repositório
git clone https://github.com/seu-usuario/sgtp.git
cd sgtp

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências Node.js
npm install

# 4. Copiar arquivo de ambiente
cp .env.example .env

# 5. Configurar .env com dados do banco e serviços
# DB_DATABASE=sgtp
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Gerar chave da aplicação
php artisan key:generate

# 7. Executar migrations e seeders
php artisan migrate --seed

# 8. Criar link simbólico para storage
php artisan storage:link

# 9. Compilar assets
npm run build

# 10. Iniciar servidor
php artisan serve
Configurações Adicionais
bash
# Configurar filas (recomendado)
php artisan queue:table
php artisan migrate

# Iniciar worker de filas
php artisan queue:work

# Configurar schedule (cron)
# * * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1

🔐 Variáveis de Ambiente Importantes
env
# APP
APP_NAME=SGTP
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sgtp
DB_USERNAME=root
DB_PASSWORD=

# Cache/Queue
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# GPS Integration
GPS_API_URL=https://api.gpsprovider.com
GPS_API_KEY=your_api_key

# SMS Gateway
SMS_API_KEY=your_sms_api_key
SMS_SENDER=SGTP

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
🧪 Testes
bash
# Executar todos os testes
php artisan test

# Testes unitários
php artisan test --testsuite=Unit

# Testes de feature
php artisan test --testsuite=Feature

# Testes com cobertura
php artisan test --coverage
📚 Documentação da API
A documentação da API está disponível em /api/documentation após instalação.

Endpoints Principais
http
# Autenticação
POST   /api/login
POST   /api/logout
POST   /api/register

# GPS Tracking
POST   /api/v1/gps/update
GET    /api/v1/gps/vehicles
GET    /api/v1/gps/vehicle/{id}

# Bilhetica
POST   /api/v1/tickets/validate
GET    /api/v1/tickets/{code}
POST   /api/v1/tickets/sell

# Ocorrências
POST   /api/v1/occurrences
GET    /api/v1/occurrences

# Consultas Públicas
GET    /api/public/routes
GET    /api/public/schedules
POST   /api/public/feedback
👥 Papéis e Permissões
Papel	Permissões
Administrador	CRUD completo em todas as entidades, gestão de usuários, configurações
Gestor Operações	CRUD rotas/horários/escalas, visualização de relatórios, monitoramento
Gestor Frota	CRUD veículos, manutenções, abastecimentos
Fiscal	Visualização de escalas, registro de ocorrências, validação de bilhetes
Operador Bilhética	Venda de bilhetes, fechamento de caixa
Motorista	Visualização de escalas próprias, registro de ponto, ocorrências básicas
Financeiro	Visualização de receitas/despesas, relatórios financeiros
📈 Roadmap
Fase 1 (MVP)
Autenticação e gestão de usuários

Cadastro de veículos e motoristas

Gestão de rotas e horários

Venda de bilhetes básica

Fase 2 (Monitoramento)
Integração com GPS

Mapa em tempo real

Alertas de desvio

Histórico de percursos

Fase 3 (Bilhética Avançada)
QR Code para validação

App mobile para validação

Integração com sistemas de pagamento

Cartão de transporte

Fase 4 (Análise e IA)
Dashboards com KPIs

Previsão de demanda

Otimização de rotas

Relatórios preditivos

🤝 Contribuição
Faça um fork do projeto

Crie uma branch para sua feature (git checkout -b feature/AmazingFeature)

Commit suas mudanças (git commit -m 'Add some AmazingFeature')

Push para a branch (git push origin feature/AmazingFeature)

Abra um Pull Request