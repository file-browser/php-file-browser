# php-file-browser

基于PHP开发的可生成静态页面的在线文件浏览器

[![GitHub forks](https://img.shields.io/github/forks/file-browser/php-file-browser?style=flat-square)](https://github.com/file-browser/php-file-browser/network)
[![GitHub issues](https://img.shields.io/github/issues/file-browser/php-file-browser?style=flat-square)](https://github.com/file-browser/php-file-browser/issues)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/file-browser/php-file-browser?style=flat-square)

![DEMO](https://cdn.jsdelivr.net/gh/file-browser/pages@latest/demo.png)

## 特性介绍

- 现代化设计风格
- 极简操作
- 无刷新更新页面
- 可过滤显示文件与文件夹
- 可自定义标题
- 可自定义子标题
- 可自定义关于与鸣谢
- 可设置是否显示多媒体下载按钮
- 支持环境变量设置
- 支持扫描目录自定义
- 支持代码压缩
- 支持远程模板（仅推荐在静态模式使用）
- 支持index.html文件保存位置自定义

## 依赖

- PHP > 7.0

## 环境变量设置

- 所有`.config.example`文件中的设置项都可以覆盖设置
- 使用全部大写的设置项键值加上前缀`FB_`即可覆盖

例如配置项 title
```
KEY: FB_TITLE
VALUE: 欲设置的标题
```

## 静态模式使用方法（推荐）

**自建**

- 修改`.config`配置
- 增加文件至根目录
- 运行`tool.php`生成`index.html`文件

**GitHub Action**

- 修改`.config`配置
- 增加文件至根目录
- 提交`GitHub`
- 等待`Action`提交`gh-pages`分支
- 开启`Pages`

## 动态模式使用方法

- 修改`.config`配置
- 将`tool.php`重命名为`index.php`

## 国内访问加速

- 配置`Action`
- 登录`vercel.com`
- 导入项目
- 绑定域名
- 将该域名设置分支为`gh-pages`
- 再次触发Deploy即可生效
