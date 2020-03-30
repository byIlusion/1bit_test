<?php
  if (!isset($parent)) $parent = 0;
  if (!isset($created)) $created = 0;
  if (!isset($description)) $description = '';
?>
<div  class="folder list-group-item list-group-item-action"
      data-folder="<?= $fid; ?>"
      data-parent="<?= $parent; ?>"
      data-created="<?= $created; ?>"
      title="<?= $description ?>">
  <div class="folder-info row justify-content-between">
    <span class="col-6">
      <span class="h4"><?= $title; ?></span>
      <?php if ($created > 0): ?>
        <div class="text-size-s text-muted font-italic">
          <span><?= date('d.m.Y H:i:s', $created); ?></span>
          <?php if ($modifed > 0): ?>
            <span class="ml-2">Изм.: <?= date('d.m.Y H:i:s', $modifed); ?></span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </span>
    
    <span class="folder-options col-6 text-right">
      <?php if ($fid > 0): ?>
        <span>
          <button class="folder-btn btn btn-secondary btn-sm ml-5"
              type="button"
              name="edit_folder"
              title="Изменить раздел <?= $title; ?>"
              data-folder="<?= $fid; ?>">Изменить</button>
        </span>
        <span>
          <button class="folder-btn btn btn-secondary btn-sm"
              type="button"
              name="del_folder"
              title="Удалить раздел <?= $title; ?>"
              data-folder="<?= $fid; ?>">Удалить</button>
        </span>
      <?php endif; ?>
      <span>
        <button class="folder-btn btn btn-light btn-sm ml-5"
            type="button"
            name="add_folder"
            title="Добавить новый раздел"
            data-folder="<?= $fid; ?>">+ раздел</button>
      </span>
      <span>
        <button class="folder-btn btn btn-light btn-sm"
            type="button"
            name="add_item"
            title="Добавить новый элемент"
            data-folder="<?= $fid; ?>">+ элемент</button>
      </span>
    </span>
  </div>
</div>
<div class="folder-content pl-2 mt-1 mb-2 hide" data-folder="<?= $fid; ?>"></div>