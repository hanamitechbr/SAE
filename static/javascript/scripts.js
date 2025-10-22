// ============================================
// CONFIGURAÇÃO INICIAL E VARIÁVEIS GLOBAIS
// ============================================

/**
 * Objeto principal que gerencia o estado do formulário
 */
const FormManager = {
  currentStep: 1,
  totalSteps: 5,
  formData: {},

  // Elementos do DOM
  elements: {
    form: null,
    steps: [],
    progressFill: null,
    progressSteps: [],
    btnVoltar: null,
    btnProximo: null,
    btnSubmit: null,
    summaryCard: null,
    summaryContent: null,
  },

  /**
   * Inicializa o gerenciador do formulário
   */
  init() {
    console.log('[SAE] 🚀 Inicializando sistema de agendamento...');
    this.cacheElements();
    this.attachEventListeners();
    this.setMinDate();
    this.createAulasCheckboxes();
    this.updateUI();
    console.log('[SAE] ✅ Sistema inicializado com sucesso!');
  },

  /**
   * Armazena referências dos elementos do DOM em cache
   */
  cacheElements() {
    this.elements.form = document.getElementById('agendamentoForm');
    this.elements.steps = document.querySelectorAll('.form-step');
    this.elements.progressFill = document.getElementById('progressFill');
    this.elements.progressSteps = document.querySelectorAll('.progress-step');
    this.elements.btnVoltar = document.getElementById('btnVoltar');
    this.elements.btnProximo = document.getElementById('btnProximo');
    this.elements.btnSubmit = document.getElementById('btnSubmit');
    this.elements.summaryCard = document.getElementById('summaryCard');
    this.elements.summaryContent = document.getElementById('summaryContent');
  },

  /**
   * Anexa todos os event listeners necessários
   */
  attachEventListeners() {
    // Verifica se os elementos existem antes de adicionar listeners
    if (!this.elements.btnProximo) {
      console.error('[SAE] ❌ Botão Próximo não encontrado!');
      return;
    }
    if (!this.elements.btnVoltar) {
      console.error('[SAE] ❌ Botão Voltar não encontrado!');
      return;
    }

    // Navegação
    this.elements.btnProximo.addEventListener('click', () => this.nextStep());
    this.elements.btnVoltar.addEventListener('click', () => this.prevStep());

    // Data
    document
      .getElementById('data-agendamento')
      .addEventListener('change', (e) => {
        this.formData.data = e.target.value;
        console.log('[SAE] 📅 Data selecionada:', this.formData.data);
        this.carregarAgendamentos();
      });

    // Período
    document.getElementById('periodo').addEventListener('change', (e) => {
      const periodos = ['Manhã', 'Tarde', 'Noite'];
      this.formData.periodo = periodos[e.target.value];
      console.log('[SAE] 🕐 Período selecionado:', this.formData.periodo);
      this.carregarAgendamentos();
    });

    // Equipamentos (radio buttons)
    document.querySelectorAll('input[name="equipamentos"]').forEach((radio) => {
      radio.addEventListener('change', (e) =>
        this.handleEquipmentChange(e.target.value)
      );
    });

    // Quantidade
    document.getElementById('quantidade').addEventListener('input', (e) => {
      this.formData.quantidade = e.target.value;
      console.log('[SAE] 🔢 Quantidade definida:', this.formData.quantidade);
    });

    // Submit do formulário
    this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));
  },

  /**
   * Carrega agendamentos e atualiza aulas
   */
  async carregarAgendamentos() {
    const dataInput = document.querySelector('#data-agendamento');
    const periodoSelect = document.querySelector('#periodo');
    const data = dataInput.value;
    const periodo = periodoSelect.value;
    const equipamento = this.formData.equipamento || '';
    const laboratorio = this.formData.laboratorio || '';
    const guardiao = this.formData.guardiao || '';

    if (!data || periodo === '') return;

    try {
      let url = `backend/pegar_agendamentos.php?data=${data}&periodo=${periodo}`;
      if (equipamento) {
        url += `&equipamento=${encodeURIComponent(equipamento)}`;
        if (equipamento === 'laboratorio' && laboratorio) {
          url += `&laboratorio=${encodeURIComponent(laboratorio)}`;
        } else if (equipamento === 'guardiao' && guardiao) {
          url += `&guardiao=${encodeURIComponent(guardiao)}`;
        }
      }
      const response = await fetch(url);
      const agendamentos = await response.json();

      if (agendamentos.error) {
        console.error(agendamentos.error);
        return;
      }

      this.atualizarAulas(agendamentos);
    } catch (error) {
      console.error('Erro ao buscar agendamentos:', error);
    }
  },

  /**
   * Atualiza aulas ocupadas
   */
  atualizarAulas(agendamentos) {
    const checkboxes = document.querySelectorAll('input[name="aulas[]"]');
    checkboxes.forEach((cb) => {
      cb.disabled = false;
      cb.title = '';
      // Reset styling
      const label = cb.nextElementSibling;
      if (label) {
        label.style.color = '';
        label.style.opacity = '';
      }
    });

    const todasAulas = ['1', '2', '3', '4', '5', '6', '7'];
    const aulasOcupadasSet = new Set();

    agendamentos.forEach((a) => {
      const aulasOcupadas = a.aulas;
      const professor = a.nome_professor;

      aulasOcupadas.forEach((aula) => {
        aulasOcupadasSet.add(aula);
        const checkbox = document.querySelector(
          `input[name="aulas[]"][value="${aula}"]`
        );
        if (checkbox) {
          checkbox.disabled = true;
          checkbox.title = `${a.nome_equip} reservado por ${professor}`;
          // Style as red and semi-transparent
          const label = checkbox.nextElementSibling;
          if (label) {
            label.style.color = 'red';
            label.style.opacity = '0.6';
          }
        }
      });
    });

    // Verificar se todas as aulas estão ocupadas
    const aulasOcupadasArray = Array.from(aulasOcupadasSet);
    const dataInput = document.querySelector('#data-agendamento');
    if (aulasOcupadasArray.length === todasAulas.length) {
      dataInput.disabled = true;
      dataInput.setCustomValidity('Todas as aulas estão agendadas neste dia.');
    } else {
      dataInput.disabled = false;
      dataInput.setCustomValidity('');
    }
  },

  /**
   * Cria os checkboxes das aulas inicialmente
   */
  createAulasCheckboxes() {
    const container = document.getElementById('aulasDisponiveis');
    container.innerHTML = ''; // Limpa qualquer conteúdo anterior

    // Cria checkboxes para aulas 1 a 6
    for (let i = 1; i <= 6; i++) {
      const div = document.createElement('div');
      div.className = 'aula-checkbox mb-2';

      const input = document.createElement('input');
      input.type = 'checkbox';
      input.name = 'aulas[]';
      input.value = i.toString();
      input.id = 'aula' + i;
      input.className = 'btn-check';

      const label = document.createElement('label');
      label.className = 'btn btn-outline-success';
      label.htmlFor = 'aula' + i;
      label.innerHTML = `<i class="bi bi-${i}-circle"></i> Aula ${i}`;

      div.appendChild(input);
      div.appendChild(label);
      container.appendChild(div);

      // Listener para atualizar contagem
      input.addEventListener('change', () => this.updateSelectedAulas());
    }

    console.log('[SAE] 📚 Checkboxes das aulas criadas');
  },

  /**
   * Define a data mínima como hoje
   */
  setMinDate() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('data-agendamento').setAttribute('min', today);
  },

  /**
   * Atualiza a contagem de aulas selecionadas
   */
  updateSelectedAulas() {
    const checkboxes = document.querySelectorAll(
      'input[name="aulas[]"]:checked'
    );
    const count = checkboxes.length;
    const selectedCount = document.getElementById('selectedCount');

    // Armazena as aulas selecionadas
    this.formData.aulas = Array.from(checkboxes).map((cb) => cb.value);

    // Atualiza a mensagem
    if (count === 0) {
      selectedCount.innerHTML =
        '<i class="bi bi-info-circle"></i> Nenhuma aula selecionada';
      selectedCount.style.background = '#f3f4f6';
      selectedCount.style.color = '#6b7280';
    } else {
      selectedCount.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${count} aula${
        count > 1 ? 's' : ''
      } selecionada${count > 1 ? 's' : ''}`;
      selectedCount.style.background = '#f0fdf4';
      selectedCount.style.color = '#059669';
    }

    console.log('[SAE] ✅ Aulas selecionadas:', this.formData.aulas);
  },

  /**
   * Manipula a mudança de equipamento e exibe opções extras
   */
  handleEquipmentChange(value) {
    const extra = document.getElementById('extra');
    this.formData.equipamento = value;

    console.log('[SAE] 💻 Equipamento selecionado:', value);

    // Limpa o conteúdo anterior
    extra.innerHTML = '';

    if (value === 'laboratorio') {
      extra.innerHTML = `
        <label for="lab" class="form-label">
          <i class="bi bi-building"></i> Selecione o Laboratório
        </label>
        <select name="laboratorio" id="lab" class="form-select form-select-lg" required>
          <option value="">-- Escolha um laboratório --</option>
          <option value="lab1">🖥️ Laboratório 1</option>
          <option value="lab2">🖥️ Laboratório 2</option>
          <option value="lab3">🖥️ Laboratório 3</option>
        </select>
      `;

      // Event listener para o select de laboratório
      document.getElementById('lab').addEventListener('change', (e) => {
        this.formData.laboratorio = e.target.value;
        console.log(
          '[SAE] 🏢 Laboratório escolhido:',
          this.formData.laboratorio
        );
        this.carregarAgendamentos(); // Recarregar agendamentos quando laboratório muda
      });
    } else if (value === 'guardiao') {
      extra.innerHTML = `
        <label for="guardiao" class="form-label">
          <i class="bi bi-laptop"></i> Selecione o Equipamento
        </label>
        <select name="guardiao" id="guardiao" class="form-select form-select-lg" required>
          <option value="">-- Escolha um equipamento --</option>
          <option value="notebook">💻 Notebook</option>
          <option value="tablet">📱 Tablet</option>
        </select>
      `;

      // Event listener para o select de guardião
      document.getElementById('guardiao').addEventListener('change', (e) => {
        this.formData.guardiao = e.target.value;
        console.log(
          '[SAE] 📱 Equipamento guardião escolhido:',
          this.formData.guardiao
        );
        this.carregarAgendamentos(); // Recarregar agendamentos quando equipamento muda
      });
    }

    // Adiciona animação ao aparecer
    extra.style.animation = 'fadeInUp 0.4s ease';

    // Recarregar agendamentos quando equipamento muda
    this.carregarAgendamentos();
  },

  /**
   * Valida a etapa atual antes de avançar
   */
  validateCurrentStep() {
    switch (this.currentStep) {
      case 1:
        // Valida data e período
        const data = document.getElementById('data-agendamento').value;
        if (!data) {
          this.showAlert('error', 'Por favor, selecione uma data!');
          return false;
        }
        return true;

      case 2:
        // Valida se pelo menos uma aula foi selecionada
        const aulas = document.querySelectorAll(
          'input[name="aulas[]"]:checked'
        );
        if (aulas.length === 0) {
          this.showAlert('warning', 'Selecione pelo menos uma aula!');
          return false;
        }
        return true;

      case 3:
        // Valida se um equipamento foi selecionado
        const equipamento = document.querySelector(
          'input[name="equipamentos"]:checked'
        );
        if (!equipamento) {
          this.showAlert('warning', 'Selecione um tipo de equipamento!');
          return false;
        }
        return true;

      case 4:
        // Valida se a opção extra foi selecionada
        const selectExtra = document.querySelector('#extra select');
        if (selectExtra && !selectExtra.value) {
          this.showAlert('warning', 'Selecione uma opção!');
          return false;
        }
        return true;

      case 5:
        // Valida quantidade
        const quantidade = document.getElementById('quantidade').value;
        if (!quantidade || quantidade < 1 || quantidade > 40) {
          this.showAlert('error', 'Informe uma quantidade válida (1-40)!');
          return false;
        }
        return true;

      default:
        return true;
    }
  },

  /**
   * Avança para a próxima etapa
   */
  nextStep() {
    if (!this.validateCurrentStep()) {
      return;
    }

    if (this.currentStep < this.totalSteps) {
      this.currentStep++;
      this.updateUI();
      console.log('[SAE] ➡️ Avançando para etapa:', this.currentStep);
    }
  },

  /**
   * Volta para a etapa anterior
   */
  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
      this.updateUI();
      console.log('[SAE] ⬅️ Voltando para etapa:', this.currentStep);
    }
  },

  /**
   * Atualiza a interface do usuário
   */
  updateUI() {
    // Atualiza as etapas visíveis
    this.elements.steps.forEach((step, index) => {
      if (index + 1 === this.currentStep) {
        step.classList.add('active');
      } else {
        step.classList.remove('active');
      }
    });

    // Atualiza a barra de progresso
    const progress = (this.currentStep / this.totalSteps) * 100;
    this.elements.progressFill.style.width = `${progress}%`;

    // Atualiza os círculos de progresso
    this.elements.progressSteps.forEach((step, index) => {
      if (index + 1 < this.currentStep) {
        step.classList.add('completed');
        step.classList.remove('active');
      } else if (index + 1 === this.currentStep) {
        step.classList.add('active');
        step.classList.remove('completed');
      } else {
        step.classList.remove('active', 'completed');
      }
    });

    // Atualiza botões de navegação
    this.elements.btnVoltar.style.display =
      this.currentStep === 1 ? 'none' : 'flex';

    if (this.currentStep === this.totalSteps) {
      this.elements.btnProximo.style.display = 'none';
      this.elements.btnSubmit.style.display = 'flex';
      this.showSummary();
    } else {
      this.elements.btnProximo.style.display = 'flex';
      this.elements.btnSubmit.style.display = 'none';
      this.elements.summaryCard.style.display = 'none';
    }

    // Scroll suave para o topo
    window.scrollTo({ top: 0, behavior: 'smooth' });
  },

  /**
   * Exibe o resumo do agendamento
   */
  showSummary() {
    const periodos = { 0: 'Manhã', 1: 'Tarde', 2: 'Noite' };
    const periodoValue = document.getElementById('periodo').value;

    let equipamentoTexto = '';
    if (this.formData.equipamento === 'laboratorio') {
      const labMap = {
        lab1: 'Laboratório 1',
        lab2: 'Laboratório 2',
        lab3: 'Laboratório 3',
      };
      equipamentoTexto =
        labMap[this.formData.laboratorio] || this.formData.laboratorio;
    } else {
      const guardMap = { notebook: 'Notebook', tablet: 'Tablet' };
      equipamentoTexto =
        guardMap[this.formData.guardiao] || this.formData.guardiao;
    }

    this.elements.summaryContent.innerHTML = `
      <p><strong>📅 Data:</strong> ${this.formatDate(this.formData.data)}</p>
      <p><strong>🕐 Período:</strong> ${periodos[periodoValue]}</p>
      <p><strong>📚 Aulas:</strong> ${this.formData.aulas.join(', ')}</p>
      <p><strong>💻 Equipamento:</strong> ${equipamentoTexto}</p>
      <p><strong>🔢 Quantidade:</strong> ${this.formData.quantidade}</p>
    `;

    this.elements.summaryCard.style.display = 'block';
    console.log('[SAE] 📋 Resumo exibido:', this.formData);
  },

  /**
   * Formata a data para exibição
   */
  formatDate(dateString) {
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
  },

  /**
   * Manipula o envio do formulário
   */
  handleSubmit(e) {
    e.preventDefault();

    console.log('[SAE] 📤 Enviando formulário...');
    console.log('[SAE] 📦 Dados completos:', this.formData);

    // Exibe loading
    window.Swal.fire({
      title: 'Processando...',
      html: 'Aguarde enquanto confirmamos seu agendamento',
      allowOutsideClick: false,
      didOpen: () => {
        window.Swal.showLoading();
      },
    });

    // Simula envio (remova isso e descomente o código abaixo para envio real)
    setTimeout(() => {
      window.Swal.close();
      this.showAlert('success', 'Agendamento realizado com sucesso! 🎉');

      // Reseta o formulário após 2 segundos
      setTimeout(() => {
        this.resetForm();
      }, 2000);
    }, 1500);

    // DESCOMENTE PARA ENVIO REAL AO BACKEND:
    /*
    const formData = new FormData(this.elements.form);
    
    fetch('backend/calendario.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      window.Swal.close();
      if (data.success) {
        this.showAlert('success', 'Agendamento realizado com sucesso! 🎉');
        setTimeout(() => this.resetForm(), 2000);
      } else {
        this.showAlert('error', data.message || 'Erro ao realizar agendamento');
      }
    })
    .catch(error => {
      window.Swal.close();
      console.error('[SAE] ❌ Erro:', error);
      this.showAlert('error', 'Erro ao conectar com o servidor');
    });
    */
  },

  /**
   * Reseta o formulário para o estado inicial
   */
  resetForm() {
    this.elements.form.reset();
    this.currentStep = 1;
    this.formData = {};
    this.updateUI();
    this.updateSelectedAulas();
    console.log('[SAE] 🔄 Formulário resetado');
  },

  /**
   * Exibe alertas personalizados com SweetAlert2
   */
  showAlert(type, message) {
    const icons = {
      success: '✅',
      error: '❌',
      warning: '⚠️',
      info: 'ℹ️',
    };

    window.Swal.fire({
      icon: type,
      title: icons[type] + ' ' + message,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', window.Swal.stopTimer);
        toast.addEventListener('mouseleave', window.Swal.resumeTimer);
      },
    });

    console.log(`[SAE] ${icons[type]} ${message}`);
  },
};

// ============================================
// INICIALIZAÇÃO QUANDO O DOM ESTIVER PRONTO
// ============================================
document.addEventListener('DOMContentLoaded', () => {
  FormManager.init();
});

// ============================================
// MENSAGENS DE SESSÃO (PHP)
// ============================================
// Descomente se estiver usando PHP com sessões:
/*
<?php if (isset($_SESSION['mensagem_sucesso'])): ?>
  FormManager.showAlert('success', '<?php echo $_SESSION['mensagem_sucesso']; ?>');
  <?php unset($_SESSION['mensagem_sucesso']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['mensagem_erro'])): ?>
  FormManager.showAlert('error', '<?php echo $_SESSION['mensagem_erro']; ?>');
  <?php unset($_SESSION['mensagem_erro']); ?>
<?php endif; ?>
*/
