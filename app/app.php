<?php


/**
 * 	Класс для работы приложения
 */
class App {
  
  private $conf;
  private $db;
  private $theme;
  
  function __construct(object $conf) {
    $this->conf = $conf;
    
    include_once $this->conf->root . "/lib/dpm.php";
    include_once $this->conf->root . "/lib/theming.php";
    include_once $this->conf->root . "/lib/db.php";
    
    // Объект темизации
    $this->theme = new Theming($this->conf->theming);
    // Объект БД
    $this->db = new DB($this->conf->db);
    $this->checkDB();
  }
  
  // Запуск скрипта
  public function run() {
    if ($_GET && count($_GET)) {
      $this->ajaxProcessGET();
    }
    elseif ($_POST && count($_POST)) {
      $this->ajaxProcessPOST();
    }
    else {
      $this->htmlPageCatalog();
    }
  }
  
  // Завершение скрипта
  public function end() {
    $this->db->close();
  }
  
  
  // Обработка GET
  private function ajaxProcessGET() {
    // ID раздела
    $folder = isset($_GET['folder']) ? (int)$_GET['folder'] : 0;
    // Элементы раздела
    $html_elements = $this->htmlCatalogElements($folder);
    // Вывод элементов
    $this->json_output($html_elements);
  }
  
  // Обработка POST
  private function ajaxProcessPOST() {
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'add_folder':
          $this->ajaxAddFolder();
          break;
          
        case 'edit_folder':
          $this->ajaxUpdateFolder();
          break;
          
        case 'del_folder':
          $this->ajaxDeleteFolder();
          break;
          
        case 'add_item':
          $this->ajaxAddItem();
          break;
          
        case 'edit_item':
          $this->ajaxUpdateItem();
          break;
          
        case 'del_item':
          $this->ajaxDeleteItem();
          break;
          
        default:
          $this->json_output(FALSE);
          break;
      }
    }
    else {
      $this->json_output(FALSE);
    }
  }
  
  // AJAX. Добавление раздела
  private function ajaxAddFolder() {
    if ($folder = $this->addFolder()) {
      $this->json_output(array('content' => $this->htmlFolders(array($folder))));
    }
    else {
      $this->json_output(FALSE);
    }
  }
  
  // AJAX. Обновление раздела
  private function ajaxUpdateFolder() {
    if ($this->updateFolder()) {
      $folder = $this->getFolder((int)$_POST['folder']);
      $this->json_output(array('content' => $this->htmlFolders($folder)));
    }
    else {
      $this->json_output(FALSE);
    }
  }
  
  // AJAX. Удаление раздела
  private function ajaxDeleteFolder() {
    if (isset($_POST['folder']) && $_POST['folder'] > 0) {
      if ($this->getFolders((int)$_POST['folder']) || $this->getItems((int)$_POST['folder'])) {
        $this->json_output(array('error' => TRUE, 'msg' => 'Нельзя удалить, т.к. есть дочерние элементы'));
      }
      else {
        if ($result = $this->deleteFolder()) {
          $this->json_output($result);
        }
        else {
          $this->json_output(FALSE);
        }
      }
    }
  }
  
  // AJAX. Добавление элемента
  private function ajaxAddItem() {
    if ($items = $this->addItem()) {
      $this->json_output(array('content' => $this->htmlItems(array($items))));
    }
    else {
      $this->json_output(FALSE);
    }
  }
  
  // AJAX. Обновление элемента
  private function ajaxUpdateItem() {
    if ($this->updateItem()) {
      $items = $this->getItem((int)$_POST['folder']);
      $this->json_output(array('content' => $this->htmlItems($items), 'item' => $items));
    }
    else {
      $this->json_output(FALSE);
    }
  }
  
  // AJAX. Удаление элемента
  private function ajaxDeleteItem() {
    if ($result = $this->deleteItem()) {
      $this->json_output($result);
    }
    else {
      $this->json_output(FALSE);
    }
  }
  
  
  // Контент страницы
  private function htmlPageCatalog() {
    // Контент
    // Корневой раздел
    $folder_data = array(
      'title' => 'Каталог',
      'fid' => 0,
    );
    $folder_root = $this->theme->theme('folder_el', $folder_data);
    // Контент страницы
    $catalog_data = array(
      'catalog' => $folder_root,
    );
    $content = $this->theme->theme('catalog', $catalog_data);

    // Данные для вывода страницы
    $html_data = array(
      'title' => 'Тестовое задание ПервыйБит. Реализация меню',
      'content' => $content,
    );
    // Вовод страницы
    print $this->theme->theme('html', $html_data);
  }
  
  // Запрос каталога
  private function htmlCatalogElements($parent = 0) {
    $content = '';
    // Запрос разделов и элементов
    $folders = $this->htmlFolders($this->getFolders($parent));
    $items = $this->htmlItems($this->getItems($parent));
    if ($folders) $content .= $folders;
    if ($items) $content .= $items;
    // Проверка наличия категории
    if (!$folders && !$items) {
      $content = array('<div>--- пусто ---</div>');
    }
    $result = array(
      'content' => $content
    );
    return $result;
  }
  
  // HTML разделов
  private function htmlFolders($folders) {
    $content = array();
    if (count($folders)) {
      foreach ($folders as $folder) {
        $content[] = $this->theme->theme('folder_el', $folder);
      }
      return implode(PHP_EOL, $content);
    }
    return FALSE;
  }
  
  // Array. HTML элементов
  private function htmlItems($items) {
    $content = array();
    if (count($items)) {
      foreach ($items as $item) {
        $content[] = $this->theme->theme('item_el', $item);
      }
      return implode(PHP_EOL, $content);
    }
    return FALSE;
  }
  
  
  // Вывод данных в JSON
  private function json_output($output) {
    print json_encode($output);
  }
  
  
  // Запрос разделов
  private function getFolder($folder) {
    $sql = "SELECT * FROM `folders` WHERE `fid` = $folder";
	  return $this->db->db_query($sql);
  }
  // Запрос разделов
  private function getFolders($parent) {
    $sql = "SELECT * FROM `folders` WHERE `parent` = $parent";
	  return $this->db->db_query($sql);
  }
  // Запрос элементов
  private function getItem($iid) {
    $sql = "SELECT * FROM `folders_items` WHERE `iid` = $iid";
	  return $this->db->db_query($sql);
  }
  // Запрос элементов
  private function getItems($parent) {
    $sql = "SELECT * FROM `folders_items` WHERE `fid` = $parent";
	  return $this->db->db_query($sql);
  }
  
  // Добавить раздел
  private function addFolder() {
    if (isset($_POST['folder']) && isset($_POST['title']) && $_POST['title'] != '') {
      $fields = array(
        'title' => $_POST['title'],
        'created' => time(),
        'modifed' => 0,
        'description' => $_POST['description'],
        'parent' => (int)$_POST['folder'],
      );
      // Если добавление прошло успешно, то вернем данные категории
      if ($this->db->db_insert('folders', $fields)) {
        $fields['fid'] = $this->db->insert_id;
        return $fields;
      }
      else {
        return FALSE;
      }
    }
    return NULL;
  }
  // Добавить элемент
  private function addItem() {
    if (isset($_POST['folder']) && isset($_POST['title']) && $_POST['title'] != '' && isset($_POST['type'])) {
      $fields = array(
        'fid' => (int)$_POST['folder'],
        'title' => $_POST['title'],
        'created' => time(),
        'modifed' => 0,
        'type' => $_POST['type'],
        'data' => $_POST['data'],
      );
      if ($this->db->db_insert('folders_items', $fields)) {
        $fields['iid'] = $this->db->insert_id;
        return $fields;
      }
      else {
        return FALSE;
      }
    }
    return NULL;
  }
  
  // Обновить раздел
  private function updateFolder() {
    if (isset($_POST['folder']) && $_POST['folder'] > 0 && isset($_POST['parent']) && isset($_POST['title']) && $_POST['title'] != '') {
      $fields = array(
        'title' => $_POST['title'],
        'modifed' => time(),
        'description' => $_POST['description'],
        'parent' => (int)$_POST['parent'],
      );
      $conditions = array(
        'fid' => (int)$_POST['folder'],
      );
      if ($this->db->db_update('folders', $fields, $conditions)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    return NULL;
  }
  // Обновить элемент
  private function updateItem() {
    if (isset($_POST['folder']) && $_POST['folder'] > 0 && isset($_POST['parent']) && isset($_POST['title']) && $_POST['title'] != '' && isset($_POST['type'])) {
      $fields = array(
        'fid' => (int)$_POST['parent'],
        'title' => mysqli_real_escape_string($this->db, $_POST['title']),
        'modifed' => time(),
        'type' => $_POST['type'],
        'data' => mysqli_real_escape_string($this->db, $_POST['data']),
      );
      $conditions = array(
        'iid' => (int)$_POST['folder'],
      );
      if ($this->db->db_update('folders_items', $fields, $conditions)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    return NULL;
  }
  
  // Добавить раздел
  private function deleteFolder() {
    if (isset($_POST['folder']) && $_POST['folder'] > 0) {
      $conditions = array(
        'fid' => (int)$_POST['folder'],
      );
      if ($this->db->db_delete('folders', $conditions)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    return NULL;
  }
  // Добавить раздел
  private function deleteItem() {
    if (isset($_POST['folder']) && $_POST['folder'] > 0) {
      $conditions = array(
        'iid' => (int)$_POST['folder'],
      );
      if ($this->db->db_delete('folders_items', $conditions)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    return NULL;
  }
  
  
  
  
  

  // Проверка/создание таблиц в БД
  private function checkDB() {
    // Есть ли параметр со схемами для БД и соответствующий файл
    if (isset($this->conf->db->schemas_path) && file_exists($this->conf->db->schemas_path)) {
      // Подключаем схемы
      include_once $this->conf->db->schemas_path;
      // Есть ли необходимая функция для запроса схем таблиц
      if (function_exists('schema')) {
        // Запрос схем таблиц
        $schemas = schema();
        if (count($schemas)) {
          foreach ($schemas as $table_name => $schema) {
            if (!$this->db->checkTableInDB($table_name)) {
              $this->db->createTableFromSchema($table_name, $schema);
            }
          }
        }
      }
    }
  }

}


?>