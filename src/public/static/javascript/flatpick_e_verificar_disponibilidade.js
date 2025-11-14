// Util: formata Date para 'YYYY-MM-DD' (formato esperado pelo PHP)
function formatDateToYMD(dateObj) {
  const year = dateObj.getFullYear();
  const month = String(dateObj.getMonth() + 1).padStart(2, '0');
  const day = String(dateObj.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

// Inicializa o flatpickr no input #data

let diasBloqueados = [];

const fp = flatpickr('#data', {
  // Formato do valor real do input (visível se você ler input.value)
  dateFormat: 'd-m-Y',
  // Mostra um campo alternativo bonito e mantém o original oculto
  altInput: true,
  altFormat: 'D d, M Y', // ex.: Thu 13, Nov 2025
  locale: 'pt',

  // Datas desabilitadas fixas (exemplo); você pode atualizar dinamicamente depois
  disable: ['14-11-2025'],

  // Callback disparado quando o usuário escolhe uma data
  onChange: function (selectedDates, dateStr, instance) {
    // selectedDates[0] é um objeto Date da data escolhida
    if (selectedDates.length > 0) {
      const ymd = formatDateToYMD(selectedDates[0]); // '2025-11-13'
      verificarDisponibilidade(ymd); // Chama o AJAX com o formato correto
    }
  },
});

function verificarDisponibilidade(dataYMD) {
  // Monta a URL com a data no formato YYYY-MM-DD (compatível com seu PHP)
  const url = `../buscar_disponibilidade.php?data=${encodeURIComponent(
    dataYMD
  )}`;

  fetch(url)
    .then((response) => {
      if (!response.ok) throw new Error('Erro na rede ou no servidor.');
      return response.json();
    })
    .then((data) => {
      // 1) Reset: libera todos os checkboxes e limpa texto
      for (let i = 1; i <= 6; i++) {
        const checkbox = document.getElementById(`aula-${i}`);
        const infoSpan = document.getElementById(`info-aula-${i}`);
        if (checkbox) {
          checkbox.disabled = false;
          checkbox.checked = false;
        }
        if (infoSpan) {
          infoSpan.textContent = '';
          infoSpan.style.color = '';
        }
      }

      // 2) Se o dia está totalmente ocupado, bloqueia tudo e avisa
      if (data.dia_bloqueado === true) {
        for (let i = 1; i <= 6; i++) {
          const checkbox = document.getElementById(`aula-${i}`);
          const infoSpan = document.getElementById(`info-aula-${i}`);
          if (checkbox) checkbox.disabled = true;
          if (infoSpan) {
            infoSpan.textContent = ' (Dia totalmente ocupado)';
            infoSpan.style.color = 'red';
          }
        }
        // Opcional: adiciona a data ao "disable" do flatpickr dinamicamente
        // fp.set('disable', [...fp.config.disable, instance.input.value]); // cuidado com formato
        return; // já bloqueou tudo
      }

      // 3) Bloqueia individualmente as aulas ocupadas
      if (data.aulas_ocupadas && typeof data.aulas_ocupadas === 'object') {
        Object.keys(data.aulas_ocupadas).forEach((aulaNum) => {
          const aulaInfo = data.aulas_ocupadas[aulaNum];
          const checkbox = document.getElementById(`aula-${aulaNum}`);
          const infoSpan = document.getElementById(`info-aula-${aulaNum}`);

          if (aulaInfo && aulaInfo.ocupada) {
            if (checkbox) {
              checkbox.disabled = true; // Desabilita a seleção
              checkbox.checked = false; // Garante desmarcado
            }
            if (infoSpan) {
              infoSpan.textContent = ` (Ocupada por: ${aulaInfo.professor})`;
              infoSpan.style.color = 'red';
            }
          }
        });
      }
    })
    .catch((error) => {
      console.error('Erro na requisição AJAX:', error);
      alert('Houve um erro ao buscar a disponibilidade.');
    });
}
