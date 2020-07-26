<?php
// 检测运行模式
define('IS_CLI', php_sapi_name() == 'cli');

// 读取配置文件
$config = parse_ini_file('./.config');
/**
 * 根目录排开文件
 * @param  array except
 */
$except = [];
if (isset($config['except'])) {
  $tmp = $config['except'];
  $tmp = explode(',', $tmp);
  foreach ($tmp as $key => $value) {
    $except[] = trim($value);
  }
}

// 扫描目录
$dirs = scan('.', $except);

// 转换JSON
$dirs = json_encode($dirs);

// 保存map.json
file_put_contents('./assets/map.json', $dirs);

// 读取模板
$tpl = file_get_contents('./assets/index.tpl');

// 标题设置
if (isset($config['title'])) {
  $tpl = str_replace('{{__TITLE__}}', $config['title'], $tpl);
}
if (isset($config['subtitle_link'])) {
  $tpl = str_replace('{{__SUBTITLE_LINK__}}', $config['subtitle_link'], $tpl);
}
if (isset($config['subtitle_text'])) {
  $tpl = str_replace('{{__SUBTITLE_TEXT__}}', $config['subtitle_text'], $tpl);
}

// 关于与鸣谢
if (isset($config['akm_link'])) {
  $tpl = str_replace('{{__AKM_LINK__}}', $config['akm_link'], $tpl);
}
if (isset($config['akm_text'])) {
  $tpl = str_replace('{{__AKM_TEXT__}}', $config['akm_text'], $tpl);
}

// 生成静态文件与运行
if (IS_CLI === FALSE) {
  echo $tpl;
}else{
  file_put_contents('./index.html', $tpl);
}

/**
 * 目录遍历
 * @param  string path        不含结尾'/'
 * @param  array  except
 * @param  bool   recurse     是否在递归中
 */
function scan($path, $except = [], $recurse = false) {
  $_dirs = scandir($path);
  $dirs = [];
  foreach ($_dirs as $key => $value) {
    if ($value == '.' || $value == '..' || $value == '.git') {
      // 基础排除项
      continue;
    }else if ($recurse === false && in_array($value, $except)) {
      // 额外排除
      continue;
    }else if (!is_dir($path.'/'.$value)) {
      // 非目录不再遍历
      $dirs[] = $value;
    }else{
      // 目录再次遍历
      $dirs[$value] = scan($path.'/'.$value, [], true);
    }
  }
  return $dirs;
}
?>
