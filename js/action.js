(function($){
  let folderTree = [0];
  const progressbar = '<div><img src="/img/loading-2.gif" height="60" /></div>';
  
  function LOG(name, data) {
    msg = {};
    msg[name] = data;
    console.log(msg);
  }
  
  // Запрос раздела
  function getFolder(folder = 0) {
    checkFolders(folder);
//    $('.folder-content[data-folder="' + folder + '"]').show().html(progressbar);
    $.ajax({
  		async: true,
  		type: "GET",
      dataType: 'json',
  		timeout: 30000,
  		url: '/',
  		data: {folder: folder},
  		success: function(data) {
  			changeContent(folder, data);
  		},
  		error: function(jqXHR, exception) {
  			console.log('Error:');
  			console.log(jqXHR);
  		},
      complete: function() {
      }
  	});
  }
  
  // Проверка иерархии каталога
  function checkFolders(folder) {
    let parent = parseInt($('.folder[data-folder="' + folder + '"]').attr('data-parent'));
    if (folder > 0) {
      fIndex = folderTree.indexOf(folder);
      pIndex = folderTree.indexOf(parent);
      if (fIndex != -1) {
        hideFolders(folderTree.slice(fIndex));
        folderTree = folderTree.slice(0, fIndex);
      }
      else if (pIndex != folderTree.length - 1) {
        hideFolders(folderTree.slice(pIndex + 1));
        folderTree = folderTree.slice(0, pIndex + 1);
        folderTree.push(folder);
      }
      else {
        folderTree.push(folder);
      }
    }
    else {
      fIndex = folderTree.indexOf(folder);
      hideFolders(folderTree.slice(1));
      folderTree = folderTree.slice(0, 1);
    }
  }
  // Развернуть раздел
  function showFolder(folder) {
    $('.folder-content[data-folder="' + folder + '"]').slideDown(300, () => {
      $('.folder[data-folder="' + folder + '"]').addClass('opened');
    });
  }
  // Свернуть раздела
  function hideFolders(folders) {
    folders.forEach((el) => {
      $('.folder[data-folder="' + el + '"]').removeClass('opened');
      $('.folder-content[data-folder="' + el + '"]').slideUp(200);
    });
  }
  
  
  // Добавить/Изменить раздел
  function folderSend(data) {
    let action = data.action;
    let folder = data.folder;
    let parent = data.parent;
    $.ajax({
  		async: true,
  		type: "POST",
      dataType: 'json',
  		timeout: 30000,
  		url: '/',
  		data: data,
  		success: function(data) {
        if (data) {
          let selector_el = (action == 'edit_folder' || action == 'del_folder')
              ? '.folder[data-folder="' + folder + '"]'
              : '.item[data-id="' + folder + '"]';
          let oldElement = $(selector_el);
          
          if (data.content) {
            if (action == 'edit_folder' || action == 'edit_item') {
              $(oldElement).after(data.content);
              $(selector_el).addClass($(oldElement).attr('class'));
              $(oldElement).remove();
            }
            if (action == 'add_folder' || action == 'add_item') {
              $('.folder-content[data-folder="' + folder + '"]').append(data.content);
            }
          }
          
          if ((action == 'del_folder' || action == 'del_item') && data === true) {
            $(oldElement).remove();
            $('.folder-content[data-folder="' + folder + '"]').remove();
          }
          
          if (data.error && data.msg)
            alert(data.msg);
        }
  		},
  		error: function(jqXHR, exception) {
  			console.log('Error:');
  			console.log(jqXHR);
  		},
      complete: function() {
      }
  	});
  }
  
  
  // Изменить контент
  function changeContent(folder, data) {
    if (data && data.content) {
      $('.folder-content[data-folder="' + folder + '"]').html(data.content);
      $('.folder[data-folder="' + folder + '"]').addClass('loaded');
      showFolder(folder);
    }
  }
  
  
  // Показать форму
  function showForm(el) {
    let folder_el = $('.folder[data-folder="' + $(el).attr('data-folder') + '"]')
    let item_el = $('.item[data-id="' + $(el).attr('data-id') + '"]')
    
    $('.form-folder').find('input[name="title"]').show();
    $('.form-folder h3').text($(el).attr('title'));
    $('.form-folder').find('input[name="folder"]').val($(folder_el).attr('data-folder'));
    $('.form-folder').find('input[name="parent"]').val($(folder_el).attr('data-parent'));
    $('.form-folder').find('input[name="action"]').val($(el).attr('name'));
    
    // Форма добавления раздела
    if ($(el).attr('name') == 'add_folder') {
      $('.form-folder').find('textarea[name="description"]').show();
      $('.form-folder').find('select[name="type"]').hide().val('');
      $('.form-folder').find('textarea[name="data"]').hide().val('');
      $('.form-folder').find('button[type="submit"]').text('Добавить');
    }
    // Форма изменения раздела
    if ($(el).attr('name') == 'edit_folder') {
      $('.form-folder').find('input[name="folder"]').val($(folder_el).attr('data-folder'));
      $('.form-folder').find('input[name="parent"]').val($(folder_el).attr('data-parent'));
      $('.form-folder').find('input[name="title"]').val($(folder_el).find('span.h4').text());
      $('.form-folder').find('textarea[name="description"]').show().val($(folder_el).attr('title'));
      $('.form-folder').find('select[name="type"]').hide().val('');
      $('.form-folder').find('textarea[name="data"]').hide().val('');
      $('.form-folder').find('button[type="submit"]').text('Сохранить');
    }
    // Форма удаления раздела
    if ($(el).attr('name') == 'del_folder') {
      $('.form-folder').find('input[name="folder"]').val($(folder_el).attr('data-folder'));
      $('.form-folder').find('input[name="title"]').hide().val('');
      $('.form-folder').find('textarea[name="description"]').hide();
      $('.form-folder').find('select[name="type"]').hide().val('');
      $('.form-folder').find('textarea[name="data"]').hide().val('');
      $('.form-folder').find('button[type="submit"]').text('Удалить');
    }
    // Форма добавления элемента
    if ($(el).attr('name') == 'add_item') {
      $('.form-folder').find('textarea[name="description"]').hide();
      $('.form-folder').find('select[name="type"]').show();
      $('.form-folder').find('textarea[name="data"]').show();
      $('.form-folder').find('button[type="submit"]').text('Добавить');
    }
    // Форма изменения элемента
    if ($(el).attr('name') == 'edit_item') {
      $('.form-folder').find('input[name="folder"]').val($(item_el).attr('data-id'));
      $('.form-folder').find('input[name="parent"]').val($(item_el).attr('data-parent'));
      $('.form-folder').find('input[name="title"]').val($(item_el).find('span.h5').text());
      $('.form-folder').find('textarea[name="description"]').hide().val('');
      $('.form-folder').find('select[name="type"]').show().val($(item_el).attr('data-type'));
      $('.form-folder').find('textarea[name="data"]').show().val($(item_el).find('.item-content').text());
      $('.form-folder').find('button[type="submit"]').text('Сохранить');
    }
    // Форма удаления элемента
    if ($(el).attr('name') == 'del_item') {
      $('.form-folder').find('input[name="folder"]').val($(item_el).attr('data-id'));
      $('.form-folder').find('input[name="title"]').hide().val('');
      $('.form-folder').find('textarea[name="description"]').hide();
      $('.form-folder').find('select[name="type"]').hide().val('');
      $('.form-folder').find('textarea[name="data"]').hide().val('');
      $('.form-folder').find('button[type="submit"]').text('Удалить');
    }
    
    $('.form-folder').fadeIn(300);
  }

  $(document).ready(function(e){
    getFolder();
    
    
    // Переход на подраздел
    $(document).on('click', '.folder', function(e){
      let folder = parseInt($(this).attr('data-folder'));
      if (!$(e.target).hasClass('btn') && !$(this).hasClass('loaded')) {
        getFolder(folder);
      }
      else {
        if ($(e.target).hasClass('btn')) {
          return false;
        }
        if ($(this).hasClass('loaded')) {
          showFolder(folder);
        }
        checkFolders(folder);
      }
    });
    
    // Фокус на элемент
    $(document).on('click', '.item', function(e){
      if ($(this).hasClass('opened') || $(e.target).hasClass('btn'))
        return false;
      $(this).find('.item-content').slideDown(300, () => {$(this).addClass('opened');});
    });
    $(document).click(function(e){
      $('.item.opened').removeClass('opened').find('.item-content').slideUp(300);
    });
    
    
    // Форма на манипуляции с разделом
    $(document).on('click', '.folder-btn', function(){
      showForm(this);
    });
    
    // Отправка формы
    $('.form-folder form').submit(function(e){
      e.preventDefault();
      // собираем строку запроса
      let data = $(this).serializeArray();
      let postData = {};
      data.forEach((el, i) => {
        postData[el.name] = el.value;
      });
      folderSend(postData);
      $('.form-folder').fadeOut(300);
      
      $('.form-folder').find('input[name="title"]').val('');
      $('.form-folder').find('textarea[name="description"]').val('');
      $('.form-folder').find('textarea[name="data"]').val('');
      return false;
    });
    
    // Закрытие формы
    $('.form-folder').click(function(e){
      if (this == e.target) {
        $('.form-folder').fadeOut(300);
      }
    });
    
  });



})(jQuery);