<?php function drawFilters()
{ ?>
  <!-- This is currently static - TODO make dinamic: 
        - Call db and get categories and corresponding attributes -->
  <div id="filters">

    <h2>Filtros</h2>
    <ol>
      <li class="search-filter">
        <p>Categoria</p>
        <select name="category" id="category">
          <option value="any" selected>Mostrar tudo</option>
          <option value="cars">Carros</option>
          <option value="technology">Tecnologia</option>
          <option value="fashion">Roupa</option>
        </select>
      </li>
      <li class="search-filter">
        <p>Preço</p>
        <div class="range-filter">
          <input type="number" name="price-range-from" id="price-range-from" placeholder="De">
          <input type="number" name="price-range-to" id="price-range-to" placeholder="Até">
        </div>
      </li>
      <li class="search-filter">
        <p>Marca</p>
        <select name="brand" id="brand">
          <option value="any" selected>Mostrar tudo</option>
          <option value="Abarth">Abarth</option>
          <option value="Audi">Audi</option>
          <option value="Volkswagen">Volkswagen</option>
        </select>
      </li>
      <li class="search-filter">
        <p>Segmento</p>
        <select name="segment" id="segment">
          <option value="any" selected>Mostrar tudo</option>
          <option value="Cabrio">Cabrio</option>
          <option value="Carrinha">Carrinha</option>
          <option value="Citadinho">Citadinho</option>
        </select>
      </li>
      <li class="search-filter">
        <p>Modelo</p>
        <select name="model" id="model">
          <option value="any" selected>Mostrar tudo</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
        </select>
      </li>
      <li class="search-filter">
        <p>Mês de registo</p>
        <select name="register-month" id="register-month">
          <option value="any" selected>Mostrar tudo</option>
          <option value="1">Janeiro</option>
          <option value="2">Fevereiro</option>
          <option value="3">Março</option>
        </select>
      </li>
      <li class="search-filter">
        <p>Ano</p>
        <div class="range-filter">
          <input type="number" name="year-range-from" id="year-range-from" placeholder="De">
          <input type="number" name="year-range-to" id="year-range-to" placeholder="Até">
        </div>
      </li>
      <li class="search-filter">
        <p>Condição</p>
        <select name="condition" id="condition">
          <option value="any" selected>Mostrar tudo</option>
          <option value="1">Usado</option>
          <option value="2">Novo</option>
          <option value="3">Março</option>
        </select>
      </li>
    </ol>

  </div>
<?php } ?>