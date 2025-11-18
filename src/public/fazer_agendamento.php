<?php
    session_start();

    // 2. Obter o timestamp atual
    $timestamp = time();

    $dias_semana = [
        'Sun' => 'Dom',
        'Mon' => 'Seg',
        'Tue' => 'Ter',
        'Wed' => 'Qua',
        'Thu' => 'Qui',
        'Fri' => 'Sex',
        'Sat' => 'Sáb',
    ];

    $meses = [
        'Jan' => 'Jan',
        'Feb' => 'Fev',
        'Mar' => 'Mar',
        'Apr' => 'Abr',
        'May' => 'Mai',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ago',
        'Sep' => 'Set',
        'Oct' => 'Out',
        'Nov' => 'Nov',
        'Dec' => 'Dez',
    ];

    $dia_semana_en = date('D', $timestamp); // Ex: Tue
    $dia_num       = date('d', $timestamp); // Ex: 18
    $mes_en        = date('M', $timestamp); // Ex: Nov
    $ano           = date('Y', $timestamp); // Ex: 2025

    $dia_semana_pt = $dias_semana[$dia_semana_en];
    $mes_pt        = $meses[$mes_en];

    $data_formatada = $dia_semana_pt . ' ' . $dia_num . ', ' . $mes_pt . ' ' . $ano;

    $mensagem      = null;
    $tipo_mensagem = null;

    if (isset($_SESSION['mensagem']) && isset($_SESSION['tipo_mensagem'])) {
        $mensagem      = $_SESSION['mensagem'];
        $tipo_mensagem = $_SESSION['tipo_mensagem'];

        // Limpe as variáveis de sessão após ler
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
    }

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#DC2626">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="stylesheet" href="static/css/styles.css">
  <title>Agendamento de Equipamento</title>
</head>
<body>
  <div class="container-wrapper">

    <!-- <div id="mensagem-alerta" style="color: red; margin-top: 10px; display: none;">
    Apenas uma opção pode ser selecionada.
    </div> -->


    <!-- Header -->
    <!-- <header class="header">
      <div class="header-content">
        <h1 class="header-title">
          <i class="bi bi-calendar-check"></i>
          Agendamento de Equipamento
        </h1>
        <p class="header-subtitle">Selecione a data, turno e aulas disponíveis</p>
      </div>
    </header> -->

    <!-- Main Content -->
    <main class="main-content">
      <div class="form-container">
        <form method="POST" action="../funcao_agendamentos.php" class="formulario">

          <!-- Section: Data -->
          <section class="form-section">
            <div class="section-header">
              <h2 class="section-title">
                <i class="bi bi-calendar-week"></i>
                Selecione a Data
              </h2>
            </div>
            <div class="input-wrapper">
              <input type="date" id="data" name="data" class="date-input" required>
              <span class="input-icon"><i class="bi bi-calendar3"></i></span>
            </div>
          </section>

          <!-- Section: Turnos -->
          <section class="form-section">
            <div class="section-header">
              <h2 class="section-title">
                <i class="bi bi-clock"></i>
                Selecione o Turno
              </h2>
            </div>
            <div class="turnos-grid">
              <label class="turno-label">
                <input type="checkbox" name="selecao" id="manha" class="turno-checkbox" onclick="handleCheckboxClick(this)">
                <span class="turno-content">
                  <i class="bi bi-sun"></i>
                  <div>
                    <div class="turno-name">Manhã</div>
                    <div class="turno-time">07:00 - 12:00</div>
                  </div>
                </span>
              </label>

              <label class="turno-label">
                <input type="checkbox" name="selecao" id="tarde" class="turno-checkbox" onclick="handleCheckboxClick(this)">
                <span class="turno-content">
                  <i class="bi bi-brightness-high"></i>
                  <div>
                    <div class="turno-name">Tarde</div>
                    <div class="turno-time">13:00 - 18:00</div>
                  </div>
                </span>
              </label>

              <label class="turno-label">
                <input type="checkbox" name="selecao" id="noite" class="turno-checkbox" onclick="handleCheckboxClick(this)">
                <span class="turno-content">
                  <i class="bi bi-moon-stars"></i>
                  <div>
                    <div class="turno-name">Noite</div>
                    <div class="turno-time">19:00 - 23:00</div>
                  </div>
                </span>
              </label>
            </div>
          </section>

          <!-- Section: Aulas -->
          <section class="form-section">
            <div class="section-header">
              <h2 class="section-title">
                <i class="bi bi-book"></i>
                Selecione as Aulas
              </h2>
              <p class="section-desc">Escolha uma ou mais aulas disponíveis</p>
            </div>
            <div class="aulas-grid">
              <label class="aula-label" data-aula="1">
                <input type="checkbox" name="aulas[]" value="1" id="aula-1" class="aula-checkbox">
                <div class="aula-box">
                  <div class="aula-number">Aula 1</div>
                  <div class="aula-time">08:00 - 09:00</div>
                  <span class="professor-info" id="info-aula-1"></span>
                </div>
              </label>

              <label class="aula-label" data-aula="2">
                <input type="checkbox" name="aulas[]" value="2" id="aula-2" class="aula-checkbox">
                <div class="aula-box">
                  <div class="aula-number">Aula 2</div>
                  <div class="aula-time">09:00 - 10:00</div>
                  <span class="professor-info" id="info-aula-2"></span>
                </div>
              </label>

              <label class="aula-label" data-aula="3">
                <input type="checkbox" name="aulas[]" value="3" id="aula-3" class="aula-checkbox">
                <div class="aula-box">
                  <div class="aula-number">Aula 3</div>
                  <div class="aula-time">10:00 - 11:00</div>
                  <span class="professor-info" id="info-aula-3"></span>
                </div>
              </label>

              <label class="aula-label" data-aula="4">
                <input type="checkbox" name="aulas[]" value="4" id="aula-4" class="aula-checkbox">
                <div class="aula-box">
                  <div class="aula-number">Aula 4</div>
                  <div class="aula-time">13:00 - 14:00</div>
                  <span class="professor-info" id="info-aula-4"></span>
                </div>
              </label>

              <label class="aula-label" data-aula="5">
                <input type="checkbox" name="aulas[]" value="5" id="aula-5" class="aula-checkbox">
                <div class="aula-box">
                  <div class="aula-number">Aula 5</div>
                  <div class="aula-time">14:00 - 15:00</div>
                  <span class="professor-info" id="info-aula-5"></span>
                </div>
              </label>

              <label class="aula-label" data-aula="6">
                <input type="checkbox" name="aulas[]" value="6" id="aula-6" class="aula-checkbox">
                <div class="aula-box">
                  <div class="aula-number">Aula 6</div>
                  <div class="aula-time">15:00 - 16:00</div>
                  <span class="professor-info" id="info-aula-6"></span>
                </div>
              </label>
            </div>
          </section>

          <!-- Submit Button -->
          <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle"></i>
            Confirmar Agendamento
          </button>
        </form>
      </div>

      <!-- Summary Card -->
      <aside class="summary-card">
        <h3 class="summary-title">
          <i class="bi bi-info-circle"></i>
          Resumo da Seleção
        </h3>
        <div class="summary-content">
          <div class="summary-item">
            <span class="summary-label">Data:</span>
            <span class="summary-value" id="summary-data">Não selecionada</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Turnos:</span>
            <span class="summary-value" id="summary-turnos">Nenhum</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Aulas:</span>
            <span class="summary-value" id="summary-aulas">Nenhuma</span>
          </div>
        </div>
      </aside>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <p>&copy; 2025 SARA. Todos os direitos reservados.</p>
      <p>Developed by - Alef</p>
    </footer>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script src="static/javascript/flatpick_e_verificar_disponibilidade.js"></script>


  <script>
    // import 'react-toastify/dist/ReactToastify.css';

    // Verifique se há uma mensagem PHP a ser exibida
    const phpMessage = "<?php echo $mensagem; ?>";
    const phpMessageType = "<?php echo $tipo_mensagem; ?>";

    if (phpMessage && phpMessageType) {
        let backgroundColor = "";
        if (phpMessageType === "success") {
            backgroundColor = "#0B8E5A";
        } else if (phpMessageType === "error") {
            backgroundColor = "linear-gradient(to right, #ff416c, #ff4b2b)";
        }

        Toastify({
            text: phpMessage,
            duration: 3000,
            close: true,
            gravity: "top", // `top` or `bottom`
            position: "right", // `left`, `center` or `right`
            backgroundColor: backgroundColor,
            stopOnFocus: true, // Prevents dismissing of toast on hover
        }).showToast();
    }

    function handleCheckboxClick(clickedCheckbox) {
  const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selecao"]');

  if (clickedCheckbox.checked) {
    // Se a checkbox clicada foi marcada, desabilita as outras
    checkboxes.forEach(checkbox => {
      if (checkbox !== clickedCheckbox) {
        checkbox.disabled = true;
      }
    });
  } else {
    // Se a checkbox clicada foi desmarcada, reabilita todas
    checkboxes.forEach(checkbox => {
      checkbox.disabled = false;
    });
  }
}

  </script>
</body>
</html>
