<?php
$basePath = dirname(dirname(dirname(__DIR__)));

return [
    'debug' => true,
    'symbiosis' => true, // Режим симбиоза, если включен и фреймворк не найдет обработчик,
                         // то он ничего не вернет и основной фреймворк смодет сам обработать запрос
    'default_host' => 'localhost',// для консоли , но ее пока нет
    'uri_prefix' => 'dissonance', // Префикс в котором работет фреймворк, если пустой то работае от корня
    'base_path' => $basePath, // базовая папка проекта
    'assets_prefix' => '/assets',
    'storage_path' =>  $basePath . '/storage', // Если убрать то кеш отключится
    'packages_paths' => [
        $basePath . '/vendor', // Папка для приложений
    ],
    'bootstrappers' => [
        \Dissonance\Develop\Bootstrap\DebugBootstrap::class,// Приложение develop
        \Dissonance\Bootstrap\EventBootstrap::class,
        \Dissonance\SimpleCacheFilesystem\Bootstrap::class,
        \Dissonance\PackagesLoaderFilesystem\Bootstrap::class,
        \Dissonance\Packages\PackagesBootstrap::class,
        \Dissonance\Packages\ResourcesBootstrap::class,
        \Dissonance\Apps\Bootstrap::class,
        \Dissonance\Http\Bootstrap::class,
        \Dissonance\HttpKernel\Bootstrap::class,
        \Dissonance\CacheRouting\Bootstrap::class,
        \Dissonance\ViewBlade\Bootstrap::class,
    ],
    'providers' => [
        \Dissonance\Http\Cookie\CookiesProvider::class,
       \Dissonance\RequestPrefixMiddleware\Provider::class,
        \Dissonance\SettlementsRouting\Provider::class,
        \Dissonance\Session\NativeProvider::class,
    ],
    'providers_exclude' => [
        \Dissonance\Routing\Provider::class,
    ],
];