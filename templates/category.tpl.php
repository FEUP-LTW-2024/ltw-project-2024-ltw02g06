<?php
declare(strict_types=1);
?>

<?php function drawEditCategory(Session $session, Category $category)
{ ?>
  <form id="edit-category-section"
    action="../actions/action_edit_category.php?redirect=<?php echo urlencode('../pages/admin.php'); ?>" method="post">
    <input type="hidden" name="category" value="<?= $category->id ?>">
    <div>
      <h2><?= htmlspecialchars($category->name) ?></h2>
      <div id="edit-category-buttons">
        <button id="edit-category-cancel-btn" type="button">Cancelar<ion-icon name="close"></ion-icon></button>
        <button id="edit-category-submit-btn" type="submit">Confirmar<ion-icon name="checkmark"
            submit></ion-icon></button>
      </div>
    </div>
    <ul>
      <?php
      foreach ($category->attributes as $attribute):
        ?>
        <?php if ($attribute['type'] == 'enum'): ?>
          <li>
            <input type="hidden" class="attribute-id" name="attribute[<?= htmlspecialchars($attribute['id']) ?>][id]"
              value="<?= htmlspecialchars($attribute['id']) ?>">
            <input type="hidden" class="attribute-type" name="attribute[<?= htmlspecialchars($attribute['id']) ?>][type]"
              value="<?= htmlspecialchars($attribute['type']) ?>">
            <input type="hidden" class="attribute-name" name="attribute[<?= htmlspecialchars($attribute['id']) ?>][name]"
              value="<?= htmlspecialchars($attribute['name']) ?>">
            <div>
              <h3><?= htmlspecialchars($attribute['name']) ?></h3>
              <p>Enumeração</p>
              <button onclick="removeLi(this)" type="button"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
            <ul>
              <?php foreach ($attribute['values'] as $value): ?>
                <li>
                  <input type="hidden"
                    name="attribute[<?= htmlspecialchars($attribute['id']) ?>][values][<?= htmlspecialchars($value['id']) ?>][id]"
                    value="<?= htmlspecialchars($value['id']) ?>">
                  <input type="hidden"
                    name="attribute[<?= htmlspecialchars($attribute['id']) ?>][values][<?= htmlspecialchars($value['id']) ?>][value]"
                    value="<?= htmlspecialchars($value['value']) ?>">
                  <p><?= htmlspecialchars($value['value']) ?></p>
                  <button onclick="removeLi(this)" type="button"><ion-icon name="close"></ion-icon></button>
                </li>
              <?php endforeach; ?>
            </ul>
            <div>
              <input class="new-value-input" placeholder="Valor" type="text">
              <button class="add-value-button" type="button"><ion-icon name="add"></ion-icon></button>
            </div>
          </li>
        <?php else: ?>
          <li>
            <input type="hidden" name="attribute[<?= htmlspecialchars($attribute['id']) ?>][id]"
              value="<?= htmlspecialchars($attribute['id']) ?>">
            <input type="hidden" name="attribute[<?= htmlspecialchars($attribute['id']) ?>][type]"
              value="<?= htmlspecialchars($attribute['type']) ?>">
            <input type="hidden" name="attribute[<?= htmlspecialchars($attribute['id']) ?>][name]"
              value="<?= htmlspecialchars($attribute['name']) ?>">
            <div>
              <h3><?= htmlspecialchars($attribute['name']) ?></h3>
              <p>
                <?= $attribute['type'] == 'real' ? 'Número Real' : ($attribute['type'] == 'int' ? 'Número Inteiro' : 'Texto') ?>
              </p>
              <button onclick="removeLi(this)" type="button"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
    <div id="new-attribute-container">
      <input id="new-attribute-input" placeholder="Nome" type="text">
      <select id="new-attribute-type">
        <option value="default" selected>Texto</option>
        <option value="enum">Enumeração</option>
        <option value="real">Número Real</option>
        <option value="int">Número Inteiro</option>
      </select>
      <button id="add-attribute-button" type="button"><ion-icon name="add"></ion-icon></button>
    </div>
  </form>
<?php } ?>