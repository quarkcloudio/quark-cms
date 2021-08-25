## 安装模块

1、修改modules_statuses.json的Wechat配置项为true
``` json
{
    "Wechat": true
}
```

2、执行下面的命令完成安装：
``` bash
# 第一步，运行下面的命令安装
php artisan module:migrate Wechat

# 第二步，然后填充数据
php artisan module:seed Wechat
```