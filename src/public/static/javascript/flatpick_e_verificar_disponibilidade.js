// Utility: format Date to 'YYYY-MM-DD'
function formatDateToYMD(dateObj) {
  const year = dateObj.getFullYear();
  const month = String(dateObj.getMonth() + 1).padStart(2, '0');
  const day = String(dateObj.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

// Initialize Flatpickr
const fp = flatpickr('#data', {
  dateFormat: 'd-m-Y',
  defaultDate: 'today',
  altInput: true,
  altFormat: 'l d, F Y',
  locale: 'pt',
  minDate: 'today',
  disable: ['14-11-2025'],
  onChange: function (selectedDates) {
    if (selectedDates.length > 0) {
      const ymd = formatDateToYMD(selectedDates[0]);
      updateSummary();
      verificarDisponibilidade(ymd);
    }
  },
});

// Verify availability
function verificarDisponibilidade(dataYMD) {
  const url = `../buscar_disponibilidade.php?data=${encodeURIComponent(
    dataYMD
  )}`;

  fetch(url)
    .then((response) => {
      if (!response.ok) throw new Error('Erro -- Verifique se o Apache está iniciado ou se você está conectado à internet.');
      return response.json();
    })
    .then((data) => {
      // Reset all checkboxes
      for (let i = 1; i <= 6; i++) {
        const checkbox = document.getElementById(`aula-${i}`);
        const infoSpan = document.getElementById(`info-aula-${i}`);
        if (checkbox) {
          checkbox.disabled = false;
          checkbox.checked = false;
        }
        if (infoSpan) {
          infoSpan.textContent = '';
        }
      }

      // Check if day is fully blocked
      if (data.dia_bloqueado === true) {
        for (let i = 1; i <= 6; i++) {
          const checkbox = document.getElementById(`aula-${i}`);
          const infoSpan = document.getElementById(`info-aula-${i}`);
          if (checkbox) checkbox.disabled = true;
          if (infoSpan) {
            infoSpan.textContent = '(Dia totalmente ocupado)';
          }
        }
        return;
      }

      // Disable individual occupied classes
      if (data.aulas_ocupadas && typeof data.aulas_ocupadas === 'object') {
        Object.keys(data.aulas_ocupadas).forEach((aulaNum) => {
          const aulaInfo = data.aulas_ocupadas[aulaNum];
          const checkbox = document.getElementById(`aula-${aulaNum}`);
          const infoSpan = document.getElementById(`info-aula-${aulaNum}`);

          if (aulaInfo && aulaInfo.ocupada) {
            if (checkbox) {
              checkbox.disabled = true;
              checkbox.checked = false;
            }
            if (infoSpan) {
              infoSpan.textContent = `(Ocupada por: ${aulaInfo.professor})`;
            }
          }
        });
      }
    })
    .catch((error) => {
      console.error('Erro na requisição:', error);
      alert('Houve um erro ao buscar a disponibilidade.');
    });
}

// Update summary card
function updateSummary() {
  const dataInput = document.getElementById('data');
  const turnoLabels = {
    manha: 'Manhã',
    tarde: 'Tarde',
    noite: 'Noite',
  };

  // Update date
  const dateValue = dataInput.value;

  if (dateValue) {
    // SEPARANDO a string d-m-Y (ex: "18-11-2025")
    const [day, month, year] = dateValue.split('-');

    // CRIANDO a data de forma robusta.
    // Usamos o formato YYYY-MM-DD para garantir que o JS interprete corretamente.
    // 'month' é subtraído por 1 porque os meses em JS vão de 0 (janeiro) a 11 (dezembro).
    const date = new Date(Number(year), Number(month) - 1, Number(day));

    // Verifica se a data é válida antes de formatar
    if (!isNaN(date.getTime())) {
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      };
      // Garante que o idioma seja português do Brasil para o formato completo
      const formattedDate = date.toLocaleDateString('pt-BR', options);

      document.getElementById('summary-data').textContent = formattedDate;
    } else {
      // Lida com o caso de a string de data ser inválida
      document.getElementById('summary-data').textContent = 'Data inválida';
    }
  } else {
    document.getElementById('summary-data').textContent = 'Não selecionada';
  }

  // Update shifts
  const selectedTurnos = [];
  ['manha', 'tarde', 'noite'].forEach((turno) => {
    if (document.getElementById(turno).checked) {
      selectedTurnos.push(turnoLabels[turno]);
    }
  });
  document.getElementById('summary-turnos').textContent =
    selectedTurnos.length > 0 ? selectedTurnos.join(', ') : 'Nenhum';

  // Update classes
  const selectedAulas = [];
  for (let i = 1; i <= 6; i++) {
    if (document.getElementById(`aula-${i}`).checked) {
      selectedAulas.push(`Aula ${i}`);
    }
  }
  document.getElementById('summary-aulas').textContent =
    selectedAulas.length > 0 ? selectedAulas.join(', ') : 'Nenhuma';
}

// Event listeners for real-time summary update
document.getElementById('manha').addEventListener('change', updateSummary);
document.getElementById('tarde').addEventListener('change', updateSummary);
document.getElementById('noite').addEventListener('change', updateSummary);

for (let i = 1; i <= 6; i++) {
  document
    .getElementById(`aula-${i}`)
    .addEventListener('change', updateSummary);
}

// Add keyboard support
document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' && e.target.closest('.aula-label')) {
    e.preventDefault();
    e.target.closest('.aula-checkbox').click();
  }
});
