<?php
namespace addons\bbhj_widget;

/**
 * 宝贝回家公益寻亲信息挂件
 *
 * 安装并启用后，自动向站点前台首页（可配置为全站）注入一个固定在左下角的
 * 公益寻亲海报挂件。挂件通过第三方公开接口随机展示寻亲海报，点击可查看大图。
 *
 * 实现原理：hooks.php 在 app_init 阶段向 app_end 钩子注册监听闭包，
 * 每个 HTML 请求渲染完成后由 inject() 将挂件代码插入到 </body> 之前。
 * 无需修改任何主题模板，卸载插件后不残留任何文件改动。
 */
class BbhjWidgetPlugin extends \app\admin\lib\Plugin
{
    public $info = [
        "name"        => "BbhjWidget",
        "title"       => "宝贝回家公益寻亲信息挂件",
        "description" => "在网站左下角展示宝贝回家公益寻亲海报挂件，点击可查看海报详情，助力寻亲",
        "status"      => 1,
        "author"      => "FurLL",
        "version"     => "1.0.0",
        "module"      => "addons",
        "lang"        => [
            "chinese"    => "宝贝回家公益寻亲信息挂件",
            "chinese_tw" => "寶貝回家公益尋親信息掛件",
            "english"    => "Baby Come Home Public Welfare Widget",
        ],
    ];

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * 将挂件注入到当前 HTML 响应中（由 hooks.php 注册的 app_end 闭包调用）
     */
    public static function inject($response)
    {
        if (!($response instanceof \think\Response)) {
            return;
        }

        $config = self::config();
        if (empty($config["enabled"])) {
            return;
        }

        if (!self::shouldInject($config)) {
            return;
        }

        try {
            $content = $response->getContent();
        } catch (\Throwable $e) {
            return;
        }
        if (!is_string($content) || stripos($content, "</body>") === false) {
            return;
        }

        $widget = self::widgetHtml($config);
        if ($widget === "") {
            return;
        }

        $newContent = str_ireplace("</body>", $widget . "</body>", $content, $count);
        if ($count > 0) {
            $response->content($newContent);
        }
    }

    /**
     * 判断当前请求是否需要注入
     */
    private static function shouldInject($config)
    {
        $path = isset($_SERVER["REQUEST_URI"]) ? (string) parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) : "";
        $path = rtrim($path, "/");
        if ($path === "") {
            $path = "/";
        }

        try {
            $module = \think\facade\Request::module();
        } catch (\Throwable $e) {
            $module = "";
        }
        if ($module === "admin" || $module === "api") {
            return false;
        }

        if (function_exists("adminAddress")) {
            $adminPath = "/" . trim(adminAddress(), "/");
            if ($adminPath !== "/" && ($path === $adminPath || strpos($path, $adminPath . "/") === 0)) {
                return false;
            }
        }

        foreach (["/addons", "/install", "/api", "/admin"] as $skip) {
            if ($skip !== "/" && strpos($path, $skip) === 0) {
                return false;
            }
        }

        if ($config["scope"] === "home") {
            $homePaths = ["/", "/index", "/index.html"];
            if (!in_array($path, $homePaths, true)) {
                return false;
            }
        }

        return true;
    }

    private static $configCache = null;

    /**
     * 读取插件配置（数据库优先，未保存时回退到 config.php 默认值）
     */
    private static function config()
    {
        if (self::$configCache !== null) {
            return self::$configCache;
        }

        self::$configCache = self::defaultConfig();
        try {
            $json = \think\Db::name("plugin")->where("name", self::PLUGIN_NAME)->value("config");
            if (!empty($json) && $json !== "null") {
                $saved = json_decode((string) $json, true);
                if (is_array($saved)) {
                    self::$configCache = array_merge(self::$configCache, $saved);
                }
            }
        } catch (\Throwable $e) {
            // 数据库不可用时使用默认配置
        }

        return self::$configCache;
    }

    private static function defaultConfig()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . "config.php";
        if (is_file($file)) {
            $temp = include $file;
            if (is_array($temp)) {
                $result = [];
                foreach ($temp as $key => $value) {
                    $result[$key] = isset($value["value"]) ? $value["value"] : "";
                }
                return $result;
            }
        }
        return [
            "enabled"      => 1,
            "scope"        => "home",
            "api_url"      => "https://openapi.dwo.cc/api/babygome",
            "official_url" => "https://www.baobeihuijia.com/bbhj/",
            "delay_ms"     => 1200,
        ];
    }

    /**
     * 生成挂件 HTML（读取模板文件并替换占位符）
     */
    private static function widgetHtml($config)
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR . "frontend.html";
        if (!is_file($file)) {
            return "";
        }
        $html = (string) file_get_contents($file);
        if ($html === "") {
            return "";
        }
        return str_replace(
            ["{api_url}", "{official_url}", "{delay_ms}"],
            [$config["api_url"], $config["official_url"], max(0, intval($config["delay_ms"]))],
            $html
        );
    }

    private const PLUGIN_NAME = "BbhjWidget";
}
