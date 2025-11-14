
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="static/css/styles.css">
  <title>Agendar Equipamento</title>
</head>
<body>

<!-- <img src="static/img/image.png" alt="" class="img">
<img src="static/img/image2.png" alt="" class="img-fundo"> -->
  <div class="container-form">
    <form method="POST" action="../funcao_agendamentos.php" class="formulario">
      <div>
        <i class="bi bi-calendar-week"><input type="date" id="data" name="data" class="data"></i>
      </div>
      <div class="container-aulas">
      <!-- <label for="aula-1">Aula 1</label> -->
      <input type="checkbox" name="aulas[]" value="1" id="aula-1">
      <span class="professor-info" id="info-aula-1"></span>
      <!-- <label for="aula-2">Aula 2</label> -->
      <input type="checkbox" name="aulas[]" value="2" id="aula-2">
      <span class="professor-info" id="info-aula-2"></span>
      <!-- <label for="aula-3">Aula 3</label> -->
      <input type="checkbox" name="aulas[]" value="3" id="aula-3">
      <span class="professor-info" id="info-aula-3"></span>
      <!-- <label>Intervalo</label> -->
      <!-- <label for="aula-4">Aula 4</label> -->
      <input type="checkbox" name="aulas[]" value="4" id="aula-4">
      <span class="professor-info" id="info-aula-4"></span>
      <!-- <label for="aula-5">Aula 5</label> -->
      <input type="checkbox" name="aulas[]" value="5" id="aula-5">
      <span class="professor-info" id="info-aula-5"></span>
      <!-- <label for="aula-6">Aula 6</label> -->
      <input type="checkbox" name="aulas[]" value="6" id="aula-6">
      <span class="professor-info" id="info-aula-6"></span>
    </div>
      <button type="submit">Salvar</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
  <script src="static/javascript/flatpick.js"></script>
  <script src="static/javascript/flatpick_e_verificar_disponibilidade.js"></script>
</body>
</html>
