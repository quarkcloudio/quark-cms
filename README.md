## 介绍
基于 QuarkAdmin 的CMS系统，简单、灵活、开源，欢迎使用！

## 系统特性

**内置功能**
* 文章管理
* 单页管理
* 广告位管理
* 友情链接管理
* 导航管理
* 管理员管理
* 用户管理
* 权限系统
* 菜单管理
* 系统配置
* 操作日志
* 附件管理

## 安装

需要安装PHP7.2+ 和 Laravel6.0+；下载最新的安装包解压到WEB目录，并正确配置了WEB环境；

1、重命名.env.example 改为 .env 

2、编辑.env文件，配置数据库信息

3、执行下面的命令完成安装：
``` bash
# 第一步，安装依赖
composer install

# 第二步，然后运行下面的命令完成安装
php artisan quark:install
```
注意: 您需要将php加入到环境变量，如果在执行迁移时发生 `「class not found」` 错误，试着先执行 `composer dump-autoload` 命令后再进行一次。

运行命令的时候，如果遇到了下面的错误:

SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 1000 bytes ...

您可以找到 config 目录下的 database.php 文件，进行更改：
``` php
// 将 strict 改为 false
'strict' => false,
// 将 engine 改为 'InnoDB'
'engine' => 'InnoDB',
```

完成安装后，执行如下命令，快速启动服务：
``` bash
php artisan serve
```
后台地址： http://127.0.0.1:8000/admin/index

默认用户名：administrator 密码：123456

## 技术支持
为了避免打扰作者日常工作，你可以在Github上提交 [Issues](https://github.com/quarkcms/quark-cms/issues)

相关教程，你可以查看 [在线文档](http://www.quarkcms.com/quark-cms/)

## License
QUARKCMS IS NOT FREE,IF YOU WANT TO USE THIS SOFTWARE,PILEASE BUY A LICENSE.