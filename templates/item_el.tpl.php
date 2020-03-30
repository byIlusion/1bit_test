<div class="item list-group-item list-group-item-action"
      data-id="<?= $iid; ?>"
      data-parent="<?= $fid; ?>"
      data-created="<?= $created; ?>"
      data-type="<?= $type; ?>">
  <div class="item-info row justify-content-between">
    <span class="col-8">
      <span class="h5 text-info"><?= htmlspecialchars($title); ?></span>
      <?php if ($created > 0): ?>
        <div class="text-size-s text-muted font-italic">
          <span><?= date('d.m.Y H:i:s', $created); ?></span>
          <?php if ($modifed > 0): ?>
            <span class="ml-2">Изм.: <?= date('d.m.Y H:i:s', $modifed); ?></span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </span>
    
    <span class="item-options col-4 text-right">
      <span>
        <button class="folder-btn btn btn-secondary btn-sm ml-5"
            type="button"
            name="edit_item"
            title="Изменить элемент <?= $title; ?>"
            data-id="<?= $iid; ?>">Изменить</button>
      </span>
      <span>
        <button class="folder-btn btn btn-secondary btn-sm"
            type="button"
            name="del_item"
            title="Удалить элемент <?= $title; ?>"
            data-id="<?= $iid; ?>">Удалить</button>
      </span>
    </span>
  </div>
  <div class="item-content hide"><?= $data; ?></div>
</div>