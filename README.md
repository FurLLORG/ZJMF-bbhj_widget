# 宝贝回家公益寻亲信息挂件

安装并启用后，自动向网站前台注入一个固定在**左下角**的公益寻亲海报挂件，点击可查看海报详情，助力寻亲。

- 作者：FurLL
- 版本：1.0.0
- 适用：魔方财务系统（ThinkCMF 插件体系，addons 模块）

<img width="1767" height="943" alt="image" src="https://github.com/user-attachments/assets/ed2b4afe-5674-41aa-b2ee-40cfaecc01bb" />


## 安装

1. 将 `bbhj_widget` 目录（或 `bbhj_widget.zip` 解压后的内容）上传到 `public/plugins/addons/`
2. 登录后台 → 应用 → 应用市场/插件管理，找到"宝贝回家公益寻亲信息挂件"并安装、启用
3. 进入插件设置，可按需调整显示范围、海报接口与官网链接

## 实现说明

插件在 `hooks.php` 中向 `app_end` 钩子注册闭包（该文件仅在插件已安装且启用时才会被加载）。每个 HTML 请求渲染完成后，`BbhjWidgetPlugin::inject()` 会将挂件代码插入 `</body>` 之前

## 文件结构

```
bbhj_widget/
├── BbhjWidgetPlugin.php    插件主类（info / install / uninstall / inject 逻辑）
├── hooks.php               注册 app_end 钩子
├── config.php              插件后台配置项
└── template/
    └── frontend.html       挂件 HTML / CSS / JS（含占位符）
```

## 免责声明

海报由第三方公开接口 `https://openapi.dwo.cc/api/babygome` 提供，内容可能随来源更新而变化。本插件不对信息的实时性、完整性或准确性作保证，请以宝贝回家官方网站发布的信息为准。
