<?php function drawFilters(array $categories, ?int $selectedCategory, array $attributes)
{ ?>
  <div id="filters">

    <h2>Filtros</h2>
    <ol id="filters-list">
      <li>
        <label for="category">Categoria</label>
        <select name="category" id="category">
          <option value="all" <?= !$selectedCategory ? "selected" : "" ?>>
            Mostrar Tudo
          </option>
          <?php foreach ($categories as $category): ?>
            <option value=<?= $category->id ?>     <?= $category->id == $selectedCategory ? "selected" : "" ?>>
              <?= $category->name ?>
            </option>
          <?php endforeach; ?>
        </select>
      </li>
      <li>
        <label for="price-range">Preço</label>
        <div name="price-range" class="range-filter" id="price-range">
          <input class="real" type="text" name="search['price']['from']" placeholder="De">
          <input class="real" type="text" name="search['price']['to']" placeholder="Até">
        </div>
      </li>
      <?php if ($selectedCategory): ?>
        <?php foreach ($categories[$selectedCategory]->attributes as $attribute): ?>
          <li>
            <label for="<?= $attribute["id"] ?>"><?= $attribute["name"] ?></label>

            <?php if ($attribute["type"] == "int"): ?>
              <div class="range-filter" id=<?= $attribute["id"] ?> name="search[attributes][<?= $attribute["id"] ?>]">
                <input class="int" type="text" name="search[attributes][<?= $attribute["id"] ?>][from]" placeholder="De">
                <input class="int" type="text" name="search[attributes][<?= $attribute["id"] ?>][to]" placeholder="Até">
              </div>

            <?php elseif ($attribute["type"] == "real"): ?>
              <div class="range-filter" id=<?= $attribute["id"] ?> name="search[attributes][<?= $attribute["id"] ?>]">
                <input class="real" type="text" name="search[attributes][<?= $attribute["id"] ?>][from]" placeholder="De">
                <input class="real" type="text" name="search[attributes][<?= $attribute["id"] ?>][to]" placeholder="Até">
              </div>

            <?php elseif ($attribute["type"] == "default"): ?>
              <input type="text" name="search[attributes][<?= $attribute["id"] ?>]" id=<?= $attribute["id"] ?>
                placeholder="<?= $attribute["name"] ?>">

            <?php elseif ($attribute["type"] == "enum"): ?>
              <select name="search[attributes][<?= $attribute["id"] ?>]" id=<?= $attribute["id"] ?>>
                <option value="all" <?= !$attributes[$attribute['id']] ? "selected" : "" ?>>
                  Mostrar Tudo
                </option>
                <?php foreach ($attribute["values"] as $value): ?>
                  <option value="<?= $value["value"] ?>" <?= $attributes[$attribute['id']] == $value["value"] ? "selected" : "" ?>>
                    <?= $value["value"] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ol>

  </div>
<?php } ?>


<?php function drawFilters2()
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