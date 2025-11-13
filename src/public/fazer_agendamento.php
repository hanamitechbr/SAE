
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <title>Agendar Equipamento</title>
</head>
<body>
  <form method="POST" action="../funcao_agendamentos.php">
    <input type="date" id="data" name="data">

    <div>
    <label for="aula-1">Aula 1</label>
    <input type="checkbox" name="aulas[]" value="1" id="aula-1">
    <span class="professor-info" id="info-aula-1"></span>

    <label for="aula-2">Aula 2</label>
    <input type="checkbox" name="aulas[]" value="2" id="aula-2">
    <span class="professor-info" id="info-aula-2"></span>

    <label for="aula-3">Aula 3</label>
    <input type="checkbox" name="aulas[]" value="3" id="aula-3">
    <span class="professor-info" id="info-aula-3"></span>

    <label>Intervalo</label>

    <label for="aula-4">Aula 4</label>
    <input type="checkbox" name="aulas[]" value="4" id="aula-4">
    <span class="professor-info" id="info-aula-4"></span>

    <label for="aula-5">Aula 5</label>
    <input type="checkbox" name="aulas[]" value="5" id="aula-5">
    <span class="professor-info" id="info-aula-5"></span>

    <label for="aula-6">Aula 6</label>
    <input type="checkbox" name="aulas[]" value="6" id="aula-6">
    <span class="professor-info" id="info-aula-6"></span>
</div>

    <button type="submit">Salvar</button>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
  <script>
    flatpickr("#data", {
      dateFormat: "d-m-Y",
      locale: "pt",
      altInput: "true",
      altFormat: "D m, Y",

      disable: [
        "14-11-2025"
      ],
    });


    let diasBloqueados = [];

    const fp = flatpickr("#id_do_seu_campo_data", {
    dateFormat: "Y-m-d", // Formato da data para envio
    disable: diasBloqueados, // Inicializa com o array
    onChange: function(selectedDates, dateStr, instance) {
        if (dateStr) {
            // Chama a função para verificar a disponibilidade ao selecionar uma data
            verificarDisponibilidade(dateStr);
        }
    },
    // Função para renderizar o calendário (útil se você quiser buscar bloqueios ao carregar o mês)
    // onDayCreate: function(dObj, d, fp, dayElem) { ... }
});

function verificarDisponibilidade(data) {
    const url = '../buscar_disponibilidade.php?data=' + data; // Chama o script PHP

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede ou no servidor.');
            }
            return response.json();
        })
        .then(data => {
            // 1. Desbloqueia e limpa todas as aulas primeiro
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

            // 2. Se o dia está totalmente bloqueado (opcional, para feedback)
            if (data.dia_bloqueado) {
                alert("Dia totalmente ocupado! Por favor, escolha outra data.");
                // Você pode adicionar a data ao array diasBloqueados e chamar fp.setDate(null)
            }

            // 3. Bloqueia as aulas ocupadas individualmente
            if (data.aulas_ocupadas) {
                for (const aulaNum in data.aulas_ocupadas) {
                    const aulaInfo = data.aulas_ocupadas[aulaNum];
                    const checkbox = document.getElementById(`aula-${aulaNum}`);
                    const infoSpan = document.getElementById(`info-aula-${aulaNum}`);

                    if (aulaInfo.ocupada) {
                        if (checkbox) {
                            checkbox.disabled = true; // Bloqueia a seleção
                            checkbox.checked = false;
                        }
                        if (infoSpan) {
                            infoSpan.textContent = ` (Ocupada por: ${aulaInfo.professor})`; // Mostra o professor
                            infoSpan.style.color = 'red';
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Erro na requisição AJAX:', error);
            alert('Houve um erro ao buscar a disponibilidade.');
        });
}
  </script>
</body>
</html>
