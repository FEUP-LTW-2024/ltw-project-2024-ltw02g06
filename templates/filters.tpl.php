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
            <option value=<?= htmlspecialchars((string) $category->id) ?>     <?= $category->id == $selectedCategory ? "selected" : "" ?>>
              <?= htmlspecialchars($category->name) ?>
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
            <label
              for="<?= htmlspecialchars((string) $attribute["id"]) ?>"><?= htmlspecialchars($attribute["name"]) ?></label>

            <?php if ($attribute["type"] == "int"): ?>
              <div class="range-filter" id=<?= htmlspecialchars((string) $attribute["id"]) ?>
                name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>]">
                <input class="int" type="text"
                  name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>][from]" placeholder="De">
                <input class="int" type="text" name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>][to]"
                  placeholder="Até">
              </div>

            <?php elseif ($attribute["type"] == "real"): ?>
              <div class="range-filter" id=<?= htmlspecialchars((string) $attribute["id"]) ?>
                name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>]">
                <input class="real" type="text"
                  name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>][from]" placeholder="De">
                <input class="real" type="text"
                  name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>][to]" placeholder="Até">
              </div>

            <?php elseif ($attribute["type"] == "default"): ?>
              <input type="text" name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>]"
                id=<?= htmlspecialchars((string) $attribute["id"]) ?> placeholder="<?= htmlspecialchars($attribute["name"]) ?>">

            <?php elseif ($attribute["type"] == "enum"): ?>
              <select name="search[attributes][<?= htmlspecialchars((string) $attribute["id"]) ?>]"
                id=<?= htmlspecialchars((string) $attribute["id"]) ?>>
                <option value="all" <?= !$attributes[$attribute['id']] ? "selected" : "" ?>>
                  Mostrar Tudo
                </option>
                <?php foreach ($attribute["values"] as $value): ?>
                  <option value="<?= htmlspecialchars($value["value"]) ?>" <?= $attributes[$attribute['id']] == $value["value"] ? "selected" : "" ?>>
                    <?= htmlspecialchars($value["value"]) ?>
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