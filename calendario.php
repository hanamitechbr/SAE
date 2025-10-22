<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SAE - Sistema de Agendamento de Equipamentos</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="static/css/styles.css">
  
</head>

<body>
  <!-- Header com gradiente verde -->
  <header class="header-gradient">
    <div class="container">
      <div class="row align-items-center py-4">
        <div class="col-12 text-center">
          <i class="bi bi-calendar-check header-icon"></i>
          <h1 class="header-title mb-2">SAE</h1>
          <p class="header-subtitle">Sistema de Agendamento de Equipamentos</p>
        </div>
      </div>
    </div>
  </header>

  <!-- Container principal -->
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-8">

        <!-- Card do formulário -->
        <div class="form-card">
          <!-- Indicador de progresso -->
          <div class="progress-container mb-4">
            <div class="progress-bar-custom">
              <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-steps">
              <div class="progress-step active" data-step="1">
                <div class="step-circle">1</div>
                <span class="step-label">Data</span>
              </div>
              <div class="progress-step" data-step="2">
                <div class="step-circle">2</div>
                <span class="step-label">Aulas</span>
              </div>
              <div class="progress-step" data-step="3">
                <div class="step-circle">3</div>
                <span class="step-label">Equipamento</span>
              </div>
              <div class="progress-step" data-step="4">
                <div class="step-circle">4</div>
                <span class="step-label">Detalhes</span>
              </div>
              <div class="progress-step" data-step="5">
                <div class="step-circle">5</div>
                <span class="step-label">Quantidade</span>
              </div>
            </div>
          </div>

          <!-- Formulário -->
          <form method="POST" action="backend/calendario.php" id="agendamentoForm">

            <!-- Etapa 1: Data e Período -->
            <div class="form-step active" id="etapa1">
              <div class="step-header">
                <i class="bi bi-calendar3"></i>
                <h3>Selecione a Data e Período</h3>
                <p>Escolha quando deseja realizar o agendamento</p>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label for="data-agendamento" class="form-label">
                    <i class="bi bi-calendar-event"></i> Data do Agendamento
                  </label>
                  <input type="date"
                         class="form-control form-control-lg"
                         name="data-agendamento"
                         id="data-agendamento"
                         required>
                </div>

                <div class="col-md-6">
                  <label for="periodo" class="form-label">
                    <i class="bi bi-clock"></i> Período
                  </label>
                  <select name="periodo" id="periodo" class="form-select form-select-lg" required>
                    <option value="0">🌅 Manhã</option>
                    <option value="1">☀️ Tarde</option>
                    <option value="2">🌙 Noite</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Etapa 2: Aulas -->
<div class="form-step" id="etapa2">
  <div class="step-header">
    <i class="bi bi-list-check"></i>
    <h3>Selecione as Aulas</h3>
    <p>Marque os horários que deseja reservar</p>
  </div>

  <div id="aulasDisponiveis"></div>

  <div class="selected-count mt-3" id="selectedCount">
    <i class="bi bi-info-circle"></i> Nenhuma aula selecionada
  </div>
</div>


            <!-- Etapa 3: Equipamento -->
            <div class="form-step" id="etapa3">
              <div class="step-header">
                <i class="bi bi-laptop"></i>
                <h3>Escolha o Equipamento</h3>
                <p>Selecione o tipo de recurso necessário</p>
              </div>

              <div class="equipment-cards">
                <input type="radio" name="equipamentos" value="laboratorio" id="equip-lab" class="btn-check">
                <label class="equipment-card" for="equip-lab">
                  <i class="bi bi-pc-display-horizontal"></i>
                  <h4>Laboratório de Informática</h4>
                  <p>Salas equipadas com computadores</p>
                </label>

                <input type="radio" name="equipamentos" value="guardiao" id="equip-guardiao" class="btn-check">
                <label class="equipment-card" for="equip-guardiao">
                  <i class="bi bi-device-hdd"></i>
                  <h4>Guardião</h4>
                  <p>Notebooks e tablets portáteis</p>
                </label>
              </div>
            </div>

            <!-- Etapa 4: Extras (dinâmico) -->
            <div class="form-step" id="etapa4">
              <div class="step-header">
                <i class="bi bi-gear"></i>
                <h3>Detalhes do Equipamento</h3>
                <p>Especifique qual recurso deseja utilizar</p>
              </div>

              <div id="extra"></div>
            </div>

            <!-- Etapa 5: Quantidade -->
            <div class="form-step" id="etapa5">
              <div class="step-header">
                <i class="bi bi-hash"></i>
                <h3>Quantidade</h3>
                <p>Informe quantos equipamentos você precisa</p>
              </div>

              <div class="quantity-input">
                <label for="quantidade" class="form-label">Número de Equipamentos</label>
                <input type="number"
                       name="quantidade"
                       id="quantidade"
                       class="form-control form-control-lg"
                       placeholder="Ex: 30"
                       min="1"
                       max="40"
                       required>
                <small class="form-text">Máximo: 40 equipamentos</small>
              </div>
            </div>

            <!-- Botões de navegação -->
            <div class="form-navigation">
              <button type="button" class="btn btn-outline-secondary btn-lg" id="btnVoltar">
                <i class="bi bi-arrow-left"></i> Voltar
              </button>
              <button type="button" class="btn btn-success btn-lg" id="btnProximo">
                Próximo <i class="bi bi-arrow-right"></i>
              </button>
              <button type="submit" class="btn btn-success btn-lg" id="btnSubmit" style="display: none;">
                <i class="bi bi-check-circle"></i> Confirmar Agendamento
              </button>
            </div>
          </form>
        </div>

        <!-- Card de resumo -->
        <div class="summary-card mt-4" id="summaryCard" style="display: none;">
          <h4><i class="bi bi-clipboard-check"></i> Resumo do Agendamento</h4>
          <div id="summaryContent"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer-gradient mt-5">
    <div class="container py-4 text-center">
      <p class="mb-0">
        <i class="bi bi-shield-check"></i> SAE - Sistema de Agendamento de Equipamentos
      </p>
      <small>Desenvolvido com <i class="bi bi-heart-fill text-danger"></i></small>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom JS -->
  <script src="static/javascript/scripts.js"></script>
</body>

</html>
