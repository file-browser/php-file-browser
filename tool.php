<?php

// 当前版本
define('VERSION', '2.0.0');

// 远程模板版本
define('TPL_VERSION', '2.0.0');

// 配置文件需求版本
define('CFG_VERSION', 2);

// 版本判断
if( version_compare(PHP_VERSION, "7.0.0", "<") ){
  exit("PHP Version >= 7.0 needed.");
}

// 检测运行模式
define('IS_CLI', php_sapi_name() == 'cli');

// 读取配置文件
global $config;
if (file_exists(__DIR__.'/.config')) {
  // 读取自定义配置
  $config = parse_ini_file(__DIR__.'/.config');
}else if(file_exists(__DIR__.'/.config.example')){
  // 读取默认配置
  $config = parse_ini_file(__DIR__.'/.config.example');
}else{
  // 无配置文件
  exit('Config file needed.');
}

// 检查配置文件版本
if (isset($config['cfg_ver']) && $config['cfg_ver'] < CFG_VERSION) {
  exit('The version of configuration file is lower than reqirements, version ' . CFG_VERSION . ' needed.');
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

// 错误flag
$error = false;
// 读取模板
if (isset($config['tpl_path']) && file_exists($config['tpl_path'])) {
  $tpl = file_get_contents('./assets/index.tpl');
}else if( isset($config['remote_tpl_allow']) && $config['remote_tpl_allow'] === 'yes' ){
  // 是否强制使用源路径
  if( !isset($config['remote_tpl_path_force']) || $config['remote_tpl_path_force'] === 'no' ){
    // 从CDN获取远程模板
    if( isset($config['cdn_remote_tpl_path']) && !empty($config['cdn_remote_tpl_path']) ){
      $tpl = fetch_remote_tpl($config['cdn_remote_tpl_path'], $error, TPL_VERSION);
      // 获取CDN远程模板失败
      if ($error !== false) {
        if (IS_CLI) print($error);
      }
    }else{
      // 未设置CDN模板路径
      $error = 'Failed to generate CDN link.';
      if (IS_CLI) print($error);
    }
  }
  // 未成功从CDN获取模板，尝试获取远程模板
  if ($error !== false) {
    if ( isset($config['remote_tpl_path']) && !empty($config['remote_tpl_path']) ){
      $tpl = fetch_remote_tpl($config['remote_tpl_path'], $error, TPL_VERSION);
      if ($error !== false) {
        if (IS_CLI) print($error);
        // 无法获取模板
        exit($error);
      }
    }else {
      // 未设置远程模板路径
      exit('Failed to fetch remote template from repo.');
    }
  }
}else{
  exit('Template file needed.');
}

// map
$tpl = str_replace('{{__MAP__}}', $map, $tpl);

// version
$tpl = str_replace('{{__VERSION__}}', VERSION, $tpl);

// 标题设置
if (isset($config['title'])) {
  $tpl = str_replace('{{__TITLE__}}', $config['title'] ?? 'File Browser', $tpl);
}
if (isset($config['subtitle_link'])) {
  $tpl = str_replace('{{__SUBTITLE_LINK__}}', $config['subtitle_link'] ?? 'https://github.com/file-browser/php-file-browser', $tpl);
}
if (isset($config['subtitle_text'])) {
  $tpl = str_replace('{{__SUBTITLE_TEXT__}}', $config['subtitle_text'] ?? 'file-browser/php-file-browser', $tpl);
}

// 视频下载按钮显示
if (isset($config['video_download_btn']) && $config['video_download_btn'] === 'yes') {
  $tpl = str_replace('{{__VIDEO_DOWNLOAD_BTN__}}', 'true', $tpl);
}else{
  $tpl = str_replace('{{__VIDEO_DOWNLOAD_BTN__}}', 'false', $tpl);
}

// 音频下载按钮显示
if (isset($config['audio_download_btn']) && $config['audio_download_btn'] === 'yes') {
  $tpl = str_replace('{{__AUDIO_DOWNLOAD_BTN__}}', 'true', $tpl);
}else{
  $tpl = str_replace('{{__AUDIO_DOWNLOAD_BTN__}}', 'false', $tpl);
}

// 关于与鸣谢
if (isset($config['akm_link'])) {
  $tpl = str_replace('{{__AKM_LINK__}}', $config['akm_link'] ?? 'https://github.com/file-browser/php-file-browser', $tpl);
}
if (isset($config['akm_text'])) {
  $tpl = str_replace('{{__AKM_TEXT__}}', $config['akm_text'] ?? 'file-browser/php-file-browser', $tpl);
}

// CDN下载基础路径
if (isset($config['cdn_jsdelivr']) && $config['cdn_jsdelivr'] === 'yes' && $repo = getenv('FB_CORE_REPO')) {
  $version = $config['cdn_jsdelivr_version'] ?? 'latest';
  $tpl = str_replace('{{__REPO__}}', $repo.'@'.$version, $tpl);
  $tpl = str_replace('{{__ENABLE_CDN__}}', 'true', $tpl);
}else{
  $tpl = str_replace('{{__ENABLE_CDN__}}', 'false', $tpl);
}

// 压缩
if (isset($config['compress']) && $config['compress'] === 'yes') {
  // 去除js注释
  $tpl = preg_replace("/(?:^|\n|\r)\s*\/\/.*(?:\r|\n|$)/", '', $tpl);
  // 清除多余空白符
  $tpl = preg_replace("/\s{2,}/", ' ', $tpl);
}

// 生成静态文件与运行
if (IS_CLI === FALSE) {
  echo $tpl;
}else{
  // 相对路径不允许设置index.html保存位置
  $filename = isset($config['static_file']) && !empty($config['static_file']) ? $config['static_file'] : './index.html';
  file_put_contents($filename, $tpl);
}

/**
 * 目录遍历
 * @param  string path        不含结尾'/'
 * @param  array  except
 * @param  bool   recurse     是否在递归中
 * @return array
 */
function scan(string $path, array $except = [], bool $recurse = false) : array {
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

/**
 * 获取远程模板
 * @param  string base_url
 * @param  string &error
 * @param  string version
 * @return string
 */
function fetch_remote_tpl(string $baseurl, string &$error, string $version = TPL_VERSION) : string {
  // 初始化模板
  $tpl = "";
  // 初始化错误提示：无错误
  $error = false;
  $_origin_error = "Fetch remote template file error.";
  // 初始化模板获取地址
  $url = $baseurl.'/'.$version.'/'.'index.tpl';
  // 尝试获取模板
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  $res = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($httpCode === 200) {
    // 模板获取成功
    $tpl = $res;
  }else{
    // 模板获取失败
    $error = $_origin_error . "({$httpCode})";
  }
  return $tpl;
}
?>
