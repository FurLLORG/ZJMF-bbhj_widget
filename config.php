<?php
return [
    "enabled"      => ["title" => "启用挂件", "type" => "radio", "value" => 1],
    "scope"        => [
        "title"   => "显示范围",
        "type"    => "radio",
        "value"   => "home",
        "options" => [
            "home" => "仅首页",
            "site" => "全站前台",
        ],
    ],
    "api_url"      => ["title" => "寻亲海报接口", "type" => "text", "value" => "https://openapi.dwo.cc/api/babygome"],
    "official_url" => ["title" => "宝贝回家官网链接", "type" => "text", "value" => "https://www.baobeihuijia.com/bbhj/"],
    "delay_ms"     => ["title" => "显示延迟(毫秒)", "type" => "text", "value" => 1200],
];
