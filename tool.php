<?php

// 版本判断
if( version_compare(PHP_VERSION, "7.0.0", "<") ){
  exit("PHP Version >= 7.0 needed.");
}

// 检测运行模式
define('IS_CLI', php_sapi_name() == 'cli');

// 读取配置文件
if (file_exists('./.config')) {
  // 读取自定义配置
  $config = parse_ini_file('./.config');
}else if(file_exists('./.config.example')){
  // 读取默认配置
  $config = parse_ini_file('./.config.example');
}else{
  // 空配置
  $config = [];
}

// 检查配置文件版本
if (!isset($config['cfg_ver']) || $config['cfg_ver'] < 1) {
  exit('不支持的配置文件（配置文件版本过低）');
}

// 配置文件更新（环境变量覆盖）
foreach ($config as $key => $value) {
  if ($e = getenv('FB_'.strtoupper($key))) {
    $config[$key] = $e;
  }
}

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
$dirs = scan(isset($config['dir']) ? $config['dir'] : '.', $except);

// 转换JSON
$dirs = json_encode($dirs);

// 保存map.json
$map = str_replace('"', '\"', $dirs);

// 读取模板
$tpl = file_get_contents('./assets/index.tpl');

// map
$tpl = str_replace('{{__MAP__}}', $map, $tpl);

// 标题设置
if (isset($config['title'])) {
  $tpl = str_replace('{{__TITLE__}}', isset($config['title']) ? $config['title'] : 'File Browser', $tpl);
}
if (isset($config['subtitle_link'])) {
  $tpl = str_replace('{{__SUBTITLE_LINK__}}', isset($config['subtitle_link']) ? $config['subtitle_link'] : 'https://github.com/file-browser/php-file-browser', $tpl);
}
if (isset($config['subtitle_text'])) {
  $tpl = str_replace('{{__SUBTITLE_TEXT__}}', isset($config['subtitle_text']) ? $config['subtitle_text'] : 'file-browser/php-file-browser', $tpl);
}

// 视频下载按钮显示
if (isset($config['video_download_btn']) && $config['video_download_btn'] === '') {
  $tpl = str_replace('{{__VIDEO_DOWNLOAD_BTN__}}', 'false', $tpl);
}else{
  $tpl = str_replace('{{__VIDEO_DOWNLOAD_BTN__}}', 'true', $tpl);
}

// 音频下载按钮显示
if (isset($config['audio_download_btn']) && $config['audio_download_btn'] === '') {
  $tpl = str_replace('{{__AUDIO_DOWNLOAD_BTN__}}', 'false', $tpl);
}else{
  $tpl = str_replace('{{__AUDIO_DOWNLOAD_BTN__}}', 'true', $tpl);
}

// 关于与鸣谢
if (isset($config['akm_link'])) {
  $tpl = str_replace('{{__AKM_LINK__}}', isset($config['akm_link']) ? $config['akm_link'] : 'https://github.com/file-browser/php-file-browser', $tpl);
}
if (isset($config['akm_text'])) {
  $tpl = str_replace('{{__AKM_TEXT__}}', isset($config['akm_text']) ? $config['akm_text'] : 'file-browser/php-file-browser', $tpl);
}

// 压缩
if (isset($config['compress']) && $config['compress'] === '') {
  $tpl = str_replace('{{__AUDIO_DOWNLOAD_BTN__}}', 'false', $tpl);
}else{
  // 去除js注释
  $tpl = preg_replace("/(?:^|\n|\r)\s*\/\/.*(?:\r|\n|$)/", '', $tpl);
  $tpl = preg_replace("/\s{2,}/", ' ', $tpl);
}

// 生成静态文件与运行
if (IS_CLI === FALSE) {
  echo $tpl;
}else{
  // 相对路径不允许设置index.html保存位置
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
