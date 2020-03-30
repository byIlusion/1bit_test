<div>
  <h1>Тестовое задание для ПервыйБит</h1>
  <h2>Список разделов и элементов</h2>
  <div id="catalog">
    <!--<div>Сортировка</div>-->
    
    <div>
      <?= $catalog; ?>
    </div>
    
  </div>
</div>

<div class="form-folder hide">
  <form method="post">
    <h3 class="mb-3">Добавить раздел</h3>
    <input class="form-control mb-1" type="text" name="title" value="" placeholder="Название раздела" />
    <textarea class="form-control mb-1" name="description" placeholder="Описание"></textarea>
    <select class="form-control mb-1" name="type">
      <option value="news">Новость</option>
      <option value="article">Статья</option>
      <option value="feedback">Отзыв</option>
      <option value="comment">Комментарий</option>
    </select>
    <textarea class="form-control mb-1" name="data" placeholder="Текст" rows="5">Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но это не совсем так. Его корни уходят в один фрагмент классической латыни 45 года н.э., то есть более двух тысячелетий назад. Ричард МакКлинток, профессор латыни из колледжа Hampden-Sydney, штат Вирджиния, взял одно из самых странных слов в Lorem Ipsum, "consectetur", и занялся его поисками в классической латинской литературе. В результате он нашёл неоспоримый первоисточник Lorem Ipsum в разделах 1.10.32 и 1.10.33 книги "de Finibus Bonorum et Malorum" ("О пределах добра и зла"), написанной Цицероном в 45 году н.э. Этот трактат по теории этики был очень популярен в эпоху Возрождения. Первая строка Lorem Ipsum, "Lorem ipsum dolor sit amet..", происходит от одной из строк в разделе 1.10.32</textarea>
    
    <input type="hidden" name="action" value="" />
    <input type="hidden" name="folder" value="" />
    <input type="hidden" name="parent" value="" />
    <button class="btn btn-primary mt-3" type="submit">Добавить</button>
  </form>
</div>