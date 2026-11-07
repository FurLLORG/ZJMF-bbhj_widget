# ZJMF-bbhj_widget

魔方财务前台公益寻亲挂件，启用后在页面左下角注入「宝贝回家」海报，点击查看详情。

## 安装

把`bbhj_widget`目录放到：

```
public/plugins/addons/
```

在后台插件管理中安装并启用，然后按需调整显示范围、海报接口与官网链接。

## 实现

`hooks.php`向`app_end`钩子注册闭包，页面渲染完成后由`BbhjWidgetPlugin::inject()`把挂件插入`</body>`前。

## 说明

海报由第三方接口`https://openapi.dwo.cc/api/babygome`提供，内容以宝贝回家官网为准。

## License

MIT，见`LICENSE`。
