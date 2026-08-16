<?php

use think\facade\Hook;

/**
 * 宝贝回家公益寻亲信息挂件 — 钩子注册
 *
 * 本文件在应用初始化（app_init，InitHookBehavior）阶段被加载，
 * 仅在插件已安装且处于启用状态时才会执行。
 *
 * 注册 app_end 钩子：页面渲染完成后将挂件代码插入 HTML 的 </body> 之前。
 * 使用闭包避免额外实例化插件类，收到响应对象为 think\Response 时由
 * BbhjWidgetPlugin::inject() 完成注入，非 HTML 响应（JSON/API）会被跳过。
 */
Hook::add("app_end", function ($response) {
    \addons\bbhj_widget\BbhjWidgetPlugin::inject($response);
});
