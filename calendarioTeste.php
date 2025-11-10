<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="teste.css">
  <title>Agendamentos</title>
</head>

<body>
  <div class="container">
    <form action="">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
      <i class="bi bi-calendar-event-fill">
        <input type="text" id="data" placeholder="Selecionar data">
      </i> <br>
      <div class="aulas-container">
        <label>
          <input type="checkbox" name="aulas[]" value="1">
          <span>Aula 1</span>
        </label>
        <label>
          <input type="checkbox" name="aulas[]" value="2">
          Aula 2
        </label>
        <label>
          <input type="checkbox" name="aulas[]" value="3" disabled>
          Aula 3 (indisponível)
        </label>
        <label>
          <input type="checkbox" name="aulas[]" value="4">
          Aula 4
        </label>
        <label>
          <input type="checkbox" name="aulas[]" value="5" disabled>
          Aula 5 (indisponível)
        </label>
        <label>
          <input type="checkbox" name="aulas[]" value="6">
          Aula 6
        </label>
      </div>
      <select name="equipamentos" id="equip">
        <option value="lab-informatica" data-type="lab">Lab. Informática 01</option>
        <option value="lab-informatica" data-type="lab">Lab. Informática 02</option>
        <option value="lab-informatica" data-type="lab">Lab. Informática 03</option>
        <option value="guardiao-notebook" data-type="guardiao">Notebook</option>
        <option value="guardiao-tablet" data-type="guardiao">Tablet</option>
      </select>
      <script type="text/javascript">
        const equipSelecionado = documen.getElementById('equip');
        equipSelecionado.addEventListener('change'),
          function() {
            const opcaoSelecionada = this.options[this.selectedIndex];
            const tipo = selectedOption.dataset.type;
            if (type === "lab") {
              console.log("prossegue para o agendamento.");
            } else if (type === "guardiao") {
              console.log("seja tablet ou notebook, ele pede quantidade personalizada.")
            }
          }
      </script>
      <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
      <!-- TESTE PARA BLOQUEAR UMA DATA ESPECÍFICA -->
      <script>
        // let data;
        fetch("pegarDataTeste.php")
          .then(res => res.json())
          .then(dados => {
            const data = dados[0].data;
            flatpickr("#data", {
              // minDate: "today",
              // maxDate: new Date().fp_incr(30),
              disable: [
                data,
                // function(date) {
                //   // bloquear fins de semana
                //   return (date.getDay() === 0 || date.getDay() === 6);
                // }
              ],
              dateFormat: "d-m-Y"
            });
          })
        const selecionadas = new Set();
        document.querySelectorAll('#aulas button:not([disabled])').forEach(btn => {
          btn.addEventListener('click', () => {
            const val = btn.dataset.val;
            if (selecionadas.has(val)) {
              selecionadas.delete(val);
              btn.classList.remove('active');
            } else {
              selecionadas.add(val);
              btn.classList.add('active');
            }
            console.log([...selecionadas]);
          });
        });
      </script>
    </form>
  </div>
</body>

</html>