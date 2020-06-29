var loading;
var loading_counter = 0;
var loading_text;
var nav = '';
var list;

$(function(){
  loading_text = $('#loading').html();
  loading = setInterval(function(){
    if (loading_counter >= 3) {
      loading_counter = 0;
      $('#loading').html(loading_text);
    }else{
      $('#loading').html($('#loading').html() + '.');
    }
    loading_counter ++;
  }, 200);
  $.getJSON('./assets/map.json', function(data){
    clearInterval(loading);
    $('#loading').hide();
    list = data;
    console.log(list);
    opendir();
  });
});

function opendir(path = '') {
  console.log('open: ' + path);
  $('#list').html('');
  if (path == '') {
    arr = list;
    $('#nav').html('当前位置：' + $('#nav_item').html().replace('{{__NAME__}}', '根目录').replace('{{__PATH__}}', ''));
  }else{
    var sp = path.split('/');
    var arr = list;
    var nav_path = '';
    var nav_item = '';
    $('#nav').html('当前位置：' + $('#nav_item').html().replace('{{__NAME__}}', '根目录').replace('{{__PATH__}}', ''));
    $.each(sp, function(key, value){
      nav_path = (nav_path == '' ? nav_path : nav_path + '/') + value;
      nav_item = '/ ' + value;
      $('#nav').html($('#nav').html() + $('#nav_item').html().replace('{{__NAME__}}', nav_item).replace('{{__PATH__}}', nav_path));
      arr = arr[value];
    });
  }
  $.each(arr, function(key, value) {
    if (typeof value == 'object' || Array.isArray(value)) {
      tpl = $('#list_dir').html();
      tpl = tpl.replace(/{{__ICON__}}/g, 'fa-folder');
      tpl = tpl.replace(/{{__PATH__}}/g, (path == '' ? path : path + '/') + key);
      tpl = tpl.replace(/{{__NAME__}}/g, key);
      $('#list').append(tpl);
    }
  });
  $.each(arr, function(key, value) {
    if (typeof value != 'object' && !Array.isArray(value)) {
      tpl = $('#list_item').html();
      tpl = tpl.replace(/{{__ICON__}}/g, 'fa-file');
      tpl = tpl.replace(/{{__PATH__}}/g, (path == '' ? './' : './' + path + '/') + value);
      tpl = tpl.replace(/{{__NAME__}}/g, value);
      $('#list').append(tpl);
    }
  });
}

function download(path) {

}
