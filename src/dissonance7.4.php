<?php


namespace Dissonance\Container\Traits {
    /**
     * Trait ArrayAccessTrait
     * @package Dissonance\Container
     *
     * @method bool has(string $key)
     * @uses \Dissonance\Container\BaseContainerInterface::has()
     * @uses BaseContainerTrait::has()
     *
     * @method mixed|null get(string $key)
     * @uses \Dissonance\Container\BaseContainerInterface::get()
     * @uses BaseContainerTrait::get()
     *
     * @method void set(string $key, $value)
     * @uses \Dissonance\Container\BaseContainerInterface::set()
     * @uses BaseContainerTrait::set()
     *
     * @method bool delete(string $key)
     * @uses \Dissonance\Container\BaseContainerInterface::delete()
     * @uses BaseContainerTrait::delete()
     */
    trait ArrayAccessTrait
    {
        /**
         * Get an item at a given offset.
         *
         * @param mixed $key
         * @return mixed
         */
        public function offsetExists($key)
        {
            return $this->has($key);
        }

        /**
         * Get an item at a given offset.
         *
         * @param mixed $key
         * @return mixed
         */
        public function offsetGet($key)
        {
            return $this->get($key);
        }

        /**
         * Set the item at a given offset.
         *
         * @param mixed $key
         * @param mixed $value
         * @return void
         */
        public function offsetSet($key, $value)
        {
            $this->set($key, $value);
        }

        /**
         * Unset the item at a given offset.
         *
         * @param string $key
         * @return void
         */
        public function offsetUnset($key)
        {
            $this->delete($key);
        }
    }

    /**
     * Trait ArrayContainerTrait
     * @package Dissonance\Support\Traits
     */
    trait BaseContainerTrait
    {
        /**
         * A special method for returning data by reference and managing it out
         * @return \ArrayAccess|array
         * @todo: Can do protected, on the one hand it is convenient, but to give everyone in a row to manage is not correct!?
         *
         */
        public abstract function &getContainerItems();

        /**
         * @param string $key
         *
         * @return mixed|null
         */
        public function get(string $key)
        {
            $default = \func_num_args() === 2 ? \func_get_arg(1) : null;
            $items =& $this->getContainerItems();
            return isset($items[$key]) ? $items[$key] : (is_callable($default) ? $default() : $default);
        }

        /**
         * @param string $key
         *
         * @return bool
         */
        public function has(string $key): bool
        {
            $items =& $this->getContainerItems();
            return isset($items[$key]);
        }

        public function set($key, $value): void
        {
            $items =& $this->getContainerItems();
            $items[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function delete(string $key): bool
        {
            $items =& $this->getContainerItems();
            unset($items[$key]);
            /// PS_UNRESERVE_PREFIX_eval('echo 646;');
            return true;
        }
    }

    /**
     * Trait MagicObjectTrait
     * @package Dissonance\Container
     *
     * @method bool has($key)
     * @uses BaseContainerInterface::has()
     *
     * @method mixed|null get($key, $default = null)
     * @uses BaseContainerInterface::get()
     *
     * @method void set($key, $value)
     * @uses BaseContainerInterface::set()
     *
     * @method void remove($keys)
     * @uses BaseContainerInterface::delete()
     */
    trait MagicAccessTrait
    {
        public function __get($key)
        {
            return $this->get($key);
        }

        public function __set(string $key, $value): void
        {
            $this->set($key, $value);
        }

        public function __unset(string $key): void
        {
            $this->remove($key);
        }

        public function __isset($key): bool
        {
            return $this->has($key);
        }

        /**
         * Special get Method with default
         * @param $key
         * @param null $default
         * @return mixed|null
         */
        public function __invoke($key, $default = null)
        {
            return $this->has($key) ? $this->get($key) : (\is_callable($default) ? $default() : $default);
        }
    }

    /**
     * Trait ArrayAccessTrait
     * @package Dissonance\Container
     *
     * @method bool has(string $key)
     * @uses \Dissonance\Container\BaseContainerInterface::has()
     * @uses BaseContainerTrait::has()
     *
     * @method mixed|null get(string $key)
     * @uses \Dissonance\Container\BaseContainerInterface::get()
     * @uses BaseContainerTrait::get()
     *
     * @method void set(string $key, $value)
     * @uses \Dissonance\Container\BaseContainerInterface::set()
     * @uses BaseContainerTrait::set()
     *
     * @method bool delete(string $key)
     * @uses \Dissonance\Container\BaseContainerInterface::delete()
     * @uses BaseContainerTrait::delete()
     */
    trait MultipleAccessTrait
    {
        /**
         * @param iterable $keys array keys
         * @return array
         */
        public function getMultiple(iterable $keys)
        {
            $result = [];
            foreach ($keys as $key) {
                $result[$key] = $this->get($key);
            }
            return $result;
        }

        /**
         * Set array of key / value pairs.
         *
         * @param iterable $values [ key => value, key2=> val2]
         *
         * @return void
         * @uses set()
         */
        public function setMultiple(iterable $values): void
        {
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
        }

        /**
         * @param iterable $keys keys array [key1,key2,....]
         *
         * @return bool
         */
        public function deleteMultiple(iterable $keys): bool
        {
            $result = true;
            foreach ($keys as $key) {
                if (!$this->delete($key)) {
                    $result = false;
                }
            }
            return $result;
        }
    }

    trait SingletonTrait
    {
        /**
         * The current globally available container (if any).
         *
         * @var static
         */
        protected static $instance;

        /**
         * Set the globally available instance of the container.
         *
         * @return static
         */
        public static function getInstance()
        {
            return is_null(static::$instance) ? static::$instance = new static() : static::$instance;
        }
    }
}

namespace Dissonance\Contracts\Routing {

    use Dissonance\Routing\Router;

    interface AppRoutingInterface
    {
    }

    /**
     * Interface RouteInterface
     * @package Dissonance\Routing
     */
    interface RouteInterface
    {
    }

    /**
     * Class Router
     * @package Dissonance\Routing
     *
     */
    interface RouterFactoryInterface
    {
    }

    /**
     * Interface RouterInterface
     * @package Dissonance\Routing
     */
    interface RouterInterface
    {
    }

    interface UrlGeneratorInterface
    {
    }
}

namespace Dissonance\Contracts\Support {
    interface ArrayableInterface
    {
    }

    interface JsonableInterface
    {
    }

    interface RenderableInterface
    {
    }
}

namespace Dissonance\Events {
    class CacheClear
    {
        protected $path = null;

        public function __construct(string $path = null)
        {
            $this->path = $path;
        }

        public function getPath()
        {
            return $this->path;
        }
    }
}

namespace _DS {

    use Dissonance\Config;

    if (!function_exists('data_fill')) {
        /**
         * Fill in data where it's missing.
         *
         * @param mixed $target
         * @param string|array $key
         * @param mixed $value
         * @return mixed
         */
        function data_fill(&$target, $key, $value)
        {
            return data_set($target, $key, $value, false);
        }
    }
    if (!function_exists('with')) {
        /**
         * Return the given value, optionally passed through the given callback.
         *
         * @param mixed $value
         * @param callable|null $callback
         * @return mixed
         */
        function with($value, callable $callback = null)
        {
            return is_null($callback) ? $value : $callback($value);
        }
    }
    if (!function_exists('value')) {
        /**
         * Return the default value of the given value.
         *
         * @param mixed $value
         * @return mixed
         */
        function value($value)
        {
            return is_callable($value) ? $value() : $value;
        }
    }
    //if (!function_exists('throw_if')) {
    //    /**
    //     * Throw the given exception if the given condition is true.
    //     *
    //     * @param mixed $condition
    //     * @param \Throwable|string $exception
    //     * @param array ...$parameters
    //     * @return mixed
    //     *
    //     * @throws \Throwable
    //     */
    //    function throw_if($condition, $exception, ...$parameters)
    //    {
    //        if ($condition) {
    //            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
    //        }
    //
    //        return $condition;
    //    }
    //}
    //if (!function_exists('throw_unless')) {
    //    /**
    //     * Throw the given exception unless the given condition is true.
    //     *
    //     * @param mixed $condition
    //     * @param \Throwable|string $exception
    //     * @param array ...$parameters
    //     * @return mixed
    //     * @throws \Throwable
    //     */
    //    function throw_unless($condition, $exception, ...$parameters)
    //    {
    //        if (!$condition) {
    //            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
    //        }
    //
    //        return $condition;
    //    }
    //}
    //if (!function_exists('title_case')) {
    //    /**
    //     * Convert a value to title case.
    //     *
    //     * @param string $value
    //     * @return string
    //     *
    //     * @deprecated Str::title() should be used directly instead. Will be removed in Laravel 5.9.
    //     */
    //    function title_case($value)
    //    {
    //        return Str::title($value);
    //    }
    //}
    if (!function_exists('transform')) {
        /**
         * Transform the given value if it is present.
         *
         * @param mixed $value
         * @param callable $callback
         * @param mixed $default
         * @return mixed|null
         */
        function transform($value, callable $callback, $default = null)
        {
            if (filled($value)) {
                return $callback($value);
            }
            if (is_callable($default)) {
                return $default($value);
            }
            return $default;
        }
    }
    if (!function_exists('unserialize64')) {
        /**
         * unserialization  with base64 decode
         *
         * @param mixed $str
         * @param array $options
         *
         * @return mixed
         *
         */
        function unserialize64(string $str, array $options = [])
        {
            return \unserialize(\base64_decode($str), $options);
        }
    }
    if (!function_exists('serialize64')) {
        /**
         * Serialization with base64 encode
         *
         * @param mixed $value
         * @return string serialized and base64 converted string
         *
         */
        function serialize64($value)
        {
            return \base64_encode(\serialize($value));
        }
    }
    if (!function_exists('snake_case')) {
        /**
         * Convert a string to snake case.
         *
         * @param string $value
         * @param string $delimiter
         * @return string
         *
         * @deprecated Str::snake() should be used directly instead. Will be removed in Laravel 5.9.
         */
        function snake_case($value, $delimiter = '_')
        {
            return Str::snake($value, $delimiter);
        }
    }

    use Dissonance\Support\Arr;
    use Dissonance\Support\Collection;

    if (!function_exists('preg_replace_array')) {
        /**
         * Replace a given pattern with each value in the array in sequentially.
         *
         * @param string $pattern
         * @param array $replacements
         * @param string $subject
         * @return string
         */
        function preg_replace_array($pattern, array $replacements, $subject)
        {
            return preg_replace_callback($pattern, function () use (&$replacements) {
                foreach ($replacements as $key => $value) {
                    return array_shift($replacements);
                }
            }, $subject);
        }
    }
    if (!function_exists('filled')) {
        /**
         * Determine if a value is "filled".
         *
         * @param mixed $value
         * @return bool
         */
        function filled($value)
        {
            return !blank($value);
        }
    }
    if (!function_exists('ends_with')) {
        /**
         * Determine if a given string ends with a given substring.
         *
         * @param string $haystack
         * @param string|array $needles
         * @return bool
         * @uses \Dissonance\Support\Str::endsWith()
         * @deprecated \Dissonance\Str::endsWith() should be used directly instead. Will be removed in Laravel 5.9.
         */
        function ends_with($haystack, $needles)
        {
            return Str::endsWith($haystack, $needles);
        }
    }
    if (!function_exists('data_set')) {
        /**
         * Set an item on an array or object using dot notation.
         *
         * @param mixed $target
         * @param string|array $key
         * @param mixed $value
         * @param bool $overwrite
         * @return mixed
         */
        function data_set(&$target, $key, $value, $overwrite = true)
        {
            $segments = is_array($key) ? $key : explode('.', $key);
            if (($segment = array_shift($segments)) === '*') {
                if (!Arr::accessible($target)) {
                    $target = [];
                }
                if ($segments) {
                    foreach ($target as &$inner) {
                        data_set($inner, $segments, $value, $overwrite);
                    }
                } elseif ($overwrite) {
                    foreach ($target as &$inner) {
                        $inner = $value;
                    }
                }
            } elseif (Arr::accessible($target)) {
                if ($segments) {
                    if (!Arr::exists($target, $segment)) {
                        $target[$segment] = [];
                    }
                    data_set($target[$segment], $segments, $value, $overwrite);
                } elseif ($overwrite || !Arr::exists($target, $segment)) {
                    $target[$segment] = $value;
                }
            } elseif (is_object($target)) {
                if ($segments) {
                    if (!isset($target->{$segment})) {
                        $target->{$segment} = [];
                    }
                    data_set($target->{$segment}, $segments, $value, $overwrite);
                } elseif ($overwrite || !isset($target->{$segment})) {
                    $target->{$segment} = $value;
                }
            } else {
                $target = [];
                if ($segments) {
                    data_set($target[$segment], $segments, $value, $overwrite);
                } elseif ($overwrite) {
                    $target[$segment] = $value;
                }
            }
            return $target;
        }
    }
    if (!function_exists('data_get')) {
        /**
         * Get an item from an array or object using "dot" notation.
         *
         * @param mixed $target
         * @param string|array|int $key
         * @param mixed $default
         * @return mixed
         */
        function data_get($target, $key, $default = null)
        {
            if (is_null($key)) {
                return $target;
            }
            $key = is_array($key) ? $key : explode('.', $key);
            while (!is_null($segment = array_shift($key))) {
                if ($segment === '*') {
                    if ($target instanceof Collection) {
                        $target = $target->all();
                    } elseif (!is_array($target)) {
                        return value($default);
                    }
                    $result = [];
                    foreach ($target as $item) {
                        $result[] = data_get($item, $key);
                    }
                    return in_array('*', $key) ? Arr::collapse($result) : $result;
                }
                if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                    $target = $target[$segment];
                } elseif (is_object($target) && isset($target->{$segment})) {
                    $target = $target->{$segment};
                } else {
                    return value($default);
                }
            }
            return $target;
        }
    }

    use Psr\Http\Message\ResponseInterface;
    use Dissonance\Contracts\Http\HttpKernelInterface;

    if (!function_exists('collect')) {
        /**
         * Create a collection from the given value.
         *
         * @param mixed $value
         * @return Collection
         */
        function collect($value = null)
        {
            return new Collection($value);
        }
    }

    use Dissonance\Support\Str;

    if (!function_exists('class_basename')) {
        /**
         * Get the class "basename" of the given object / class.
         *
         * @param string|object $class
         * @return string
         */
        function class_basename($class)
        {
            $class = is_object($class) ? get_class($class) : $class;
            return basename(str_replace('\\', '/', $class));
        }
    }
    if (!function_exists('camel_case')) {
        /**
         * Convert a value to camel case.
         *
         * @param string $value
         * @return string
         *
         * @deprecated Str::camel() should be used directly instead. Will be removed in Laravel 5.9.
         */
        function camel_case($value)
        {
            return Str::camel($value);
        }
    }
    if (!function_exists('route')) {
        /**
         * Generate the URL to a named route.
         *
         * @param array|string $name
         * @param mixed $parameters
         * @param bool $absolute
         * @return string
         */
        function route($name, $parameters = [], $absolute = true)
        {
            return app('url')->route($name, $parameters, $absolute);
        }
    }
    if (!function_exists('event')) {
        /**
         * Run event
         *
         * @param object $event
         *
         * @return object $event
         */
        function event(object $event)
        {
            return app('events')->dispatch($event);
        }
    }
    if (!function_exists('config')) {
        /**
         * Get Config data
         * @param string|null $key
         * @param null $default
         *
         * @return Config|null|mixed
         */
        function config(string $key = null, $default = null)
        {
            $config = app('config');
            return is_null($key) ? $config : ($config->has($key) ? $config->get($key) : $default);
        }
    }
    function app($abstract = null, array $parameters = null)
    {
        $core = \Dissonance\Core::getInstance();
        if (is_null($abstract)) {
            return $core;
        }
        return is_null($parameters) ? $core->get($abstract) : $core->make($abstract, $parameters);
    }
    const DS = DIRECTORY_SEPARATOR;
    function response(int $code = 200, \Throwable $exception = null): ResponseInterface
    {
        return app(HttpKernelInterface::class)->response($code, $exception);
    }
}

namespace Dissonance\Providers {

    use Dissonance\Container\ServiceContainerInterface;
    use Dissonance\Packages\Contracts\PackagesRepositoryInterface;

    class ProvidersRepository
    {
        const EXCLUDE = 0;
        const ACTIVE = 1;
        const DEFER = 2;
        /**
         * @var array
         * [class => bool (active flag),... ]
         */
        protected $providers = [];
        /**
         * @var array [serviceClassName => ProviderClassName]
         */
        protected $defer_services = [];
        protected $loaded = false;

        /**
         * @param string|string[] $items
         */
        public function add(array $items, $flag = self::ACTIVE)
        {
            $providers =& $this->providers;
            foreach ($items as $v) {
                $v = ltrim($v, '\\');
                $providers[$v] = isset($providers[$v]) ? $providers[$v] | $flag : $flag;
            }
        }

        /**
         * @param string|string[] $items
         */
        public function exclude(array $items)
        {
            $this->add($items, self::EXCLUDE);
        }

        /**
         * @param array $items = [ProviderClasName => [Service1,Service2]]
         */
        protected function defer(array $items)
        {
            $providers = [];
            foreach ($items as $provider => $services) {
                $providers[] = $provider;
                foreach ($services as $v) {
                    $this->defer_services[\ltrim($v)] = $provider;
                }
            }
            $this->add($providers, self::DEFER);
        }

        /**
         * @return array
         */
        public function all()
        {
            return $this->providers;
        }

        public function isDefer($service)
        {
            return isset($this->defer_services[\ltrim($service)]);
        }

        /**
         * @param ServiceContainerInterface $app
         * @param array $force_providers
         * @param array $force_exclude
         */
        public function load(ServiceContainerInterface $app, array $force_providers = [], array $force_exclude = [])
        {
            if (!$this->loaded) {
                foreach ($app[PackagesRepositoryInterface::class]->getPackages() as $config) {
                    $this->add(isset($config['providers']) ? (array)$config['providers'] : []);
                    $this->defer(isset($config['defer']) ? (array)$config['defer'] : []);
                    $this->exclude(isset($config['providers_exclude']) ? (array)$config['providers_exclude'] : []);
                }
            }
            $this->exclude($force_exclude);
            foreach ($force_providers as $v) {
                $this->providers[ltrim($v, '\\')] = self::ACTIVE;
            }
            /**
             * @var \Dissonance\Container\ServiceProviderInterface $provider
             */
            foreach ($this->providers as $provider => $mask) {
                if (!($mask & (self::DEFER | self::EXCLUDE))) {
                    $app->register($provider);
                }
            }
            $app->setDeferred($this->defer_services);
        }

        public function __wakeup()
        {
            $this->loaded = true;
        }
    }
}

namespace Dissonance\Mimetypes {
    /**
     * abbreviating standard names
     */
    const A = 'application/';
    const I = 'image/';
    const T = 'text/';
    /**
     * Для отдачи файлов ресурсов нет необходимости описывать все 2000 расширений
     *
     */
    class MimeTypesMini
    {
        protected static $mime_types = [
            'txt' => T . 'plain',
            'htm' => T . 'html',
            'html' => T . 'html',
            'php' => T . 'html',
            'css' => T . 'css',
            'js' => A . 'javascript',
            'json' => A . 'json',
            'jsonld' => A . 'ld+json',
            'xml' => A . 'xml',
            'swf' => A . 'x-shockwave-flash',
            'flv' => 'video/x-flv',
            'csv' => T . 'csv',
            // images
            'png' => I . 'png',
            'jpe' => I . 'jpeg',
            'jpeg' => I . 'jpeg',
            'jpg' => I . 'jpeg',
            'gif' => I . 'gif',
            'bmp' => I . 'bmp',
            'ico' => I . 'vnd.microsoft.icon',
            'tiff' => I . 'tiff',
            'tif' => I . 'tiff',
            'svg' => I . 'svg+xml',
            'svgz' => I . 'svg+xml',
            // archives
            'zip' => A . 'zip',
            'rar' => A . 'x-rar-compressed',
            'exe' => A . 'x-msdownload',
            'msi' => A . 'x-msdownload',
            'cab' => A . 'vnd.ms-cab-compressed',
            'tar.gz' => A . 'x-compressed-tar',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',
            // adobe
            'pdf' => A . 'pdf',
            'psd' => I . 'vnd.adobe.photoshop',
            'ai' => A . 'postscript',
            'eps' => A . 'postscript',
            'ps' => A . 'postscript',
            // ms office
            'doc' => A . 'msword',
            'rtf' => A . 'rtf',
            'xls' => A . 'vnd.ms-excel',
            'ppt' => A . 'vnd.ms-powerpoint',
            // open office
            'odt' => A . 'vnd.oasis.opendocument.text',
            'ods' => A . 'vnd.oasis.opendocument.spreadsheet',
        ];

        public function getExtensionsPattern(array $extensions)
        {
            $pattern = '';
            foreach ($extensions as $v) {
                $pattern .= preg_quote($v, '/') . '|';
            }
            return trim($pattern, '|');
        }

        /**
         * @param string $path
         * @param array|null $allowed_extensions
         */
        public function findExtension(string $path, array $allowed_extensions = null)
        {
            if (!$allowed_extensions) {
                $allowed_extensions = array_keys(static::$mime_types);
            }
            usort($allowed_extensions, function ($a, $b) {
                return substr_count($a, '.') <=> substr_count($b, '.');
            });
            return preg_match('/(' . $this->getExtensionsPattern($allowed_extensions) . ')$/i', $path, $m) ? $m[1] : false;
        }

        /**
         * @param string $path
         * @return string
         */
        public function getMimeType(string $path): string
        {
            $ext = $this->findExtension($path);
            return $ext && isset(static::$mime_types[$ext]) ? static::$mime_types[$ext] : A . 'octet-stream';
        }
    }
}

namespace Dissonance\Packages\Contracts {

    use Psr\Http\Message\StreamInterface;
    use Dissonance\Contracts\CoreInterface;

    /**
     * Interface AssetsRepositoryInterface
     * @package Dissonance\Packages
     */
    interface AssetsRepositoryInterface
    {
    }

    interface PackagesLoaderInterface
    {
    }

    interface PackagesRepositoryInterface
    {
    }

    /**
     * Interface ResourcesRepositoryInterface
     * @package Dissonance\Packages
     */
    interface ResourcesRepositoryInterface
    {
    }

    interface TemplateCompilerInterface
    {
    }

    /**
     * Interface TemplatesRepositoryInterface
     * @package Dissonance\Packages
     */
    interface TemplatesRepositoryInterface
    {
    }
}

namespace Dissonance\Container {

    use ReflectionMethod;

    interface CachedContainerInterface extends \Serializable
    {
    }

    use Dissonance\Contracts\App\ApplicationInterface;
    use Dissonance\Providers\ProvidersRepository;
    use Throwable;
    use Psr\Container\NotFoundExceptionInterface;
    use Dissonance\Support\Arr;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Container\Traits\MultipleAccessTrait;
    use Closure;
    use Dissonance\Container\Traits\ArrayAccessTrait;
    use ReflectionFunction;
    use InvalidArgumentException;
    use Psr\Container\ContainerInterface;
    use Psr\Container\ContainerExceptionInterface;

    /**
     * Interface BaseContainerInterface
     *
     * A less strict, augmented implementation.
     *
     * The name of the interface is specially different, so as not to be confused with the interface from PSR.
     * Using aliases is not recommended.
     *
     * @package Dissonance\Container
     *
     * Extenders this interface
     * @see ArrayContainerInterface
     * @see MultipleAccessInterface
     * @see MagicAccessInterface
     *
     */
    interface BaseContainerInterface
    {
    }

    /**
     * Describes the basic interface of a factory.
     *
     * @package Dissonance\Container
     *
     * @author Matthieu Napoli <matthieu@mnapoli.fr>
     * @refactor Sergey Surkov <dissonancephp@gmail.com>
     */
    interface FactoryInterface
    {
    }

    /**
     * Interface ArrayContainerInterface
     * @package Dissonance\Container
     *
     * @see \Dissonance\Container\MagicAccessTrait  realisation trait (package: dissonance/container-traits)
     */
    interface MagicAccessInterface
    {
    }

    trait SubContainerTrait
    {
        use DeepGetterTrait, ArrayAccessTrait, MethodBindingsTrait, ContextualBindingsTrait;

        /**
         * @var DIContainerInterface|null
         */
        protected $app = null;
        protected $aliases = [];
        protected $instances = [];
        protected $abstractAliases = [];
        protected $reboundCallbacks = [];
        protected $bindings = [];
        protected $resolved = [];
        protected $extenders = [];

        public function call($callback, array $parameters = [], string $defaultMethod = null)
        {
            return BoundMethod::call($this, $callback, $this->bindParameters($parameters), $defaultMethod);
        }

        public function bindParameters(&$parameters)
        {
            $di = DIContainerInterface::class;
            if (!isset($parameters[$di])) {
                $parameters[$di] = $this;
            }
            return $parameters;
        }

        public function make(string $abstract, array $parameters = [])
        {
            return $this->resolve($abstract, $parameters);
        }

        /**
         * @param string $key
         * @return bool
         * @todo: нужно тестировать правильность работы с родительским
         */
        public function has(string $key): bool
        {
            return isset($this->bindings[$key]) || isset($this->instances[$key]) || isset($this->aliases[$key]) || $this->app->has($this->getAlias($key));
        }

        public function set($key, $value): void
        {
            $this->bind($key, $value instanceof \Closure ? $value : function () use ($value) {
                return $value;
            });
        }

        public function delete(string $key): bool
        {
            unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key], $this->aliases[$key], $this->abstractAliases[$key]);
            return true;
        }

        public function bound($abstract)
        {
            return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || isset($this->aliases[$abstract]) || $this->app->bound($abstract);
        }

        public function alias(string $abstract, string $alias)
        {
            if ($alias === $abstract) {
                throw new \LogicException("[{$abstract}] is aliased to itself.");
            }
            $this->aliases[$alias] = $abstract;
            $this->abstractAliases[$abstract][] = $alias;
        }

        /**
         * Fire the "rebound" callbacks for the given abstract type.
         *
         * @param string $abstract
         * @return void
         */
        protected function rebound($abstract)
        {
            $instance = $this->make($abstract);
            foreach (isset($this->reboundCallbacks[$abstract]) ? $this->reboundCallbacks[$abstract] : [] as $callback) {
                call_user_func($callback, $this, $instance);
            }
        }

        /**
         * Register a binding with the container.
         *
         * @param string $abstract
         * @param \Closure|string|null $concrete
         * @param bool $shared
         * @return void
         */
        public function bind(string $abstract, $concrete = null, bool $shared = false): void
        {
            unset($this->instances[$abstract], $this->aliases[$abstract]);
            if (!$concrete) {
                $concrete = $abstract;
            }
            $this->bindings[$abstract] = ['concrete' => function ($container, $parameters = []) use ($abstract, $concrete, $shared) {
                /**
                 * @var Container $container
                 */
                if ($concrete instanceof \Closure) {
                    $instance = $concrete($this, $parameters);
                    foreach ($this->getExtenders($abstract) as $v) {
                        $instance = $v($instance);
                    }
                } else {
                    if ($abstract == $concrete) {
                        $container->setContainersStack($this);
                        $instance = $container->build($concrete);
                        $container->popCurrentContainer();
                    } else {
                        $instance = $this->app->resolve($concrete, $parameters, $raiseEvents = false);
                    }
                }
                $this->resolved[$abstract] = true;
                if ($shared) {
                    $this->instances[$abstract] = $instance;
                }
                return $instance;
            }, 'shared' => $shared];
            // If the abstract type was already resolved in this container we'll fire the
            // rebound listener so that any objects which have already gotten resolved
            // can have their copy of the object updated via the listener callbacks.
            if ($this->resolved($abstract)) {
                $this->rebound($abstract);
            }
        }

        /**
         * Bind a new callback to an abstract's rebind event.
         *
         * @param string $abstract
         * @param \Closure $callback
         * @return mixed
         */
        public function rebinding(string $abstract, Closure $callback)
        {
            $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;
            if ($this->bound($abstract)) {
                return $this->make($abstract);
            }
        }

        public function resolve(string $abstract, array $parameters = [], bool $raiseEvents = true)
        {
            $alias = $this->getAlias($abstract);
            if (!$parameters && isset($this->instances[$alias])) {
                return $this->instances[$alias];
            }
            if (isset($this->bindings[$alias])) {
                return $this->app->build($this->bindings[$alias]['concrete']);
            }
            return $this->app->resolve($alias, $this->bindParameters($parameters), $raiseEvents);
        }

        public function build($concrete)
        {
            return $this->app->build($concrete);
        }

        public function bindIf(string $abstract, $concrete = null, bool $shared = false)
        {
            if (!$this->bound($abstract)) {
                $this->bind($abstract, $concrete, $shared);
            }
        }

        public function singleton(string $abstract, $concrete = null, string $alias = null)
        {
            $this->bind($abstract, $concrete, true);
            if (is_string($alias)) {
                $this->alias($abstract, $alias);
            }
            return $this;
        }

        /**
         * "Extend" an abstract type in the container.
         *
         * @param string $abstract
         * @param \Closure $closure
         * @return void
         *
         * @throws \InvalidArgumentException
         */
        public function extend(string $abstract, Closure $closure): void
        {
            $abstract = $this->getAlias($abstract);
            if (isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $closure($this->instances[$abstract], $this);
                $this->rebound($abstract);
            } else {
                $this->extenders[$abstract][] = $closure;
                if ($this->resolved($abstract)) {
                    $this->rebound($abstract);
                }
            }
        }

        /**
         * Get the extender callbacks for a given type.
         *
         * @param string $abstract
         * @return array
         */
        public function getExtenders(string $abstract)
        {
            return $this->extenders[$this->getAlias($abstract)] ?? [];
        }

        /**
         * Remove all of the extender callbacks for a given type.
         *
         * @param string $abstract
         * @return void
         */
        public function forgetExtenders(string $abstract)
        {
            unset($this->extenders[$this->getAlias($abstract)]);
        }

        public function getAlias(string $abstract): string
        {
            if (!isset($this->aliases[$abstract])) {
                return $this->app->getAlias($abstract);
            }
            return $this->getAlias($this->aliases[$abstract]);
        }

        public function instance(string $abstract, $instance, string $alias = null)
        {
            if (isset($this->aliases[$abstract])) {
                foreach ($this->abstractAliases as $abstr => $aliases) {
                    foreach ($aliases as $index => $als) {
                        if ($als == $abstract) {
                            unset($this->abstractAliases[$abstr][$index]);
                        }
                    }
                }
            }
            $isBound = $this->bound($abstract);
            unset($this->aliases[$abstract]);
            // We'll check to determine if this type has been bound before, and if it has
            // we will fire the rebound callbacks registered with the container and it
            // can be updated with consuming classes that have gotten resolved here.
            $this->instances[$abstract] = $instance;
            if ($isBound) {
                $this->rebound($abstract);
            }
            if ($alias) {
                $this->alias($abstract, $alias);
            }
            return $instance;
        }

        /**
         * @param string $concrete
         * @param string $abstract
         * @param $implementation
         *
         * @todo: нужно сделать отдельно от родителя!!
         */
        public function addContextualBinding(string $concrete, string $abstract, $implementation): void
        {
            $this->app->addContextualBinding($concrete, $abstract, $implementation);
        }

        public function when($concrete): ContextualBindingBuilder
        {
            return $this->app->when($concrete);
        }

        public function factory(string $abstract): Closure
        {
            return function () use ($abstract) {
                return $this->make($abstract);
            };
        }

        public function clear(): void
        {
            $this->aliases = [];
            $this->abstractAliases = [];
        }

        /**
         * Determine if the given abstract type has been resolved.
         *
         * @param string $abstract
         * @return bool
         */
        public function resolved(string $abstract)
        {
            if ($this->isAlias($abstract)) {
                $abstract = $this->getAlias($abstract);
            }
            return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]) || $this->app->resolved($abstract);
        }

        public function resolving($abstract, callable $callback = null)
        {
            if (is_string($abstract)) {
                $abstract = $this->getAlias($abstract);
            }
            $this->app->resolving($abstract, $callback);
        }

        /**
         * Register a new after resolving callback for all types.
         *
         * @param \Closure|string $abstract
         * @param callable|null $callback
         * @return void
         */
        public function afterResolving($abstract, callable $callback = null)
        {
            if (is_string($abstract)) {
                $abstract = $this->getAlias($abstract);
            }
            $this->app->afterResolving($abstract, $callback);
        }

        /**
         * Determine if a given string is an alias.
         *
         * @param string $name
         * @return bool
         */
        public function isAlias(string $name): bool
        {
            return isset($this->aliases[$name]) || $this->app->isAlias($name);
        }

        /**
         * Get aliases for abstract binding
         *
         * @param string $abstract
         * @return array|null
         */
        public function getAbstractAliases(string $abstract): ?array
        {
            return $this->abstractAliases[$abstract] ?? null;
        }
    }

    /**
     * Interface ServiceProvider
     * @package Dissonance\Container
     * @property  array $app  = [
     *       'config' => new \Dissonance\Config(),
     *       'router' => new \Dissonance\Contracts\Routing\Router(),
     *       'apps' => new \Dissonance\Contracts\Apps\AppsRepository(),
     *
     *
     *
     * ]
     */
    interface ServiceProviderInterface
    {
    }

    trait ServiceContainerTrait
    {
        /**
         * @var DIContainerInterface
         */
        protected $dependencyInjectionContainer;
        /**
         * All of the registered service providers.
         *
         * @var ServiceProvider[]
         */
        protected $serviceProviders = [];
        /**
         * The names of the loaded service providers.
         *
         * @var array
         */
        protected $loadedProviders = [];
        protected $defer_services = [];
        /**
         * Indicates if the application has "booted".
         *
         * @var bool
         */
        protected $booted = false;

        public function isDeferService(string $service): bool
        {
            return isset($this->defer_services[\ltrim($service)]);
        }

        public function loadDefer(string $service): bool
        {
            $class = \ltrim($service);
            if (isset($this->defer_services[$class])) {
                $this->register($this->defer_services[$class]);
                return true;
            }
            return false;
        }

        /**
         * @param array $items
         * @used-by ProvidersRepository::load()
         */
        public function setDeferred(array $services)
        {
            $this->defer_services = $services;
        }

        /**
         * Register a service provider with the application.
         *
         * @param ServiceProviderInterface|string $provider
         * @param bool $force
         * @return ServiceProviderInterface
         */
        public function register($provider, $force = false)
        {
            /**
             * @var ServiceProviderInterface $provider
             */
            if (($registered = $this->getProvider($provider)) && !$force) {
                return $registered;
            }
            // If the given "provider" is a string, we will resolve it, passing in the
            // application instance automatically for the developer. This is simply
            // a more convenient way of specifying your service provider classes.
            if (is_string($provider)) {
                $provider = $this->resolveProvider($provider);
            }
            if (method_exists($provider, 'register')) {
                $provider->register();
            }
            // If there are bindings / singletons set as properties on the provider we
            // will spin through them and register them with the application, which
            // serves as a convenience layer while registering a lot of bindings.
            if (property_exists($provider, 'bindings')) {
                foreach ($provider->bindings() as $key => $value) {
                    $this->bind($key, $value);
                }
            }
            if (property_exists($provider, 'singletons')) {
                foreach ($provider->singletons() as $key => $value) {
                    $this->singleton($key, $value);
                }
            }
            if (property_exists($provider, 'aliases')) {
                foreach ($provider->aliases() as $key => $value) {
                    $this->singleton($key, $value);
                }
            }
            $this->markAsRegistered($provider);
            // If the application has already booted, we will call this boot method on
            // the provider class so it has an opportunity to do its boot logic and
            // will be ready for any usage by this developer's application logic.
            if ($this->booted) {
                $this->bootProvider($provider);
            }
            return $provider;
        }

        /**
         * Boot the application's service providers.
         *
         * @return void
         */
        public function boot()
        {
            if ($this->booted) {
                return;
            }
            array_walk($this->serviceProviders, function ($p) {
                $this->bootProvider($p);
            });
            $this->booted = true;
        }

        /**
         * Boot the given service provider.
         *
         * @param ServiceProvider $provider
         * @return mixed
         */
        protected function bootProvider(
            /*allow use all classes ServiceProviderInterface*/
            $provider
        )
        {
            if (method_exists($provider, 'boot')) {
                return $this->dependencyInjectionContainer->call([$provider, 'boot']);
            }
        }

        /**
         * Resolve a service provider instance from the class name.
         *
         * @param string $provider
         * @return ServiceProvider
         */
        public function resolveProvider($provider)
        {
            return new $provider($this->dependencyInjectionContainer);
        }

        /**
         * Mark the given provider as registered.
         *
         * @param ServiceProvider $provider
         * @return void
         */
        protected function markAsRegistered($provider)
        {
            $class = $this->getClass($provider);
            $this->serviceProviders[$class] = $provider;
            $this->loadedProviders[$class] = true;
        }

        /**
         * Get the registered service provider instance if it exists.
         *
         * @param ServiceProvider|string $provider
         * @return ServiceProvider|null
         */
        public function getProvider($provider)
        {
            $providers =& $this->serviceProviders;
            $name = $this->getClass($provider);
            return isset($providers[$name]) ? $providers[$name] : null;
        }

        /**
         * @param string |object $provider
         * @return false|string
         */
        protected function getClass($provider)
        {
            return \is_string($provider) ? \ltrim($provider, '\\') : \get_class($provider);
        }

        /**
         * Get the registered service provider instances if any exist.
         *
         * @param ServiceProvider|string $provider
         * @return array
         */
        public function getProviders($provider)
        {
            $name = $this->getClass($provider);
            return \array_filter($this->serviceProviders, function ($value) use ($name) {
                return $value instanceof $name;
            }, ARRAY_FILTER_USE_BOTH);
        }
    }

    interface ServiceContainerInterface
    {
    }

    /**
     * Interface ArrayContainerInterface
     * @package Dissonance\Container
     *
     * @see \Dissonance\Container\Traits\BaseContainerTrait
     * @see \Dissonance\Container\Traits\ArrayAccessTrait  and ArrayAccess realisation trait
     */
    interface ArrayContainerInterface extends BaseContainerInterface, \ArrayAccess
    {
    }

    trait MethodBindingsTrait
    {
        /**
         * The container's method bindings.
         *
         * @var \Closure[]
         */
        protected $methodBindings = [];

        /**
         * Determine if the container has a method binding.
         *
         * @param string $method
         * @return bool
         */
        public function hasMethodBinding($method)
        {
            return isset($this->methodBindings[$method]);
        }

        /**
         * Bind a callback to resolve with Container::call.
         *
         * @param array|string $method
         * @param \Closure $callback
         * @return void
         */
        public function bindMethod($method, $callback)
        {
            $this->methodBindings[$this->parseBindMethod($method)] = $callback;
        }

        /**
         * Get the method to be bound in class@method format.
         *
         * @param array|string $method
         * @return string
         */
        protected function parseBindMethod($method)
        {
            if (is_array($method)) {
                return $method[0] . '@' . $method[1];
            }
            return $method;
        }

        /**
         * Get the method binding for the given method.
         *
         * @param string $method
         * @param mixed $instance
         * @return mixed
         */
        public function callMethodBinding($method, $instance)
        {
            return call_user_func($this->methodBindings[$method], $instance, $this);
        }
    }

    /**
     * Interface DependencyInjectionInterface
     * @package Dissonance\Container
     */
    interface DIContainerInterface extends ArrayContainerInterface, ContainerInterface, FactoryInterface
    {
    }

    /**
     * Trait DeepGetter
     * @package Dissonance\Container
     */
    trait DeepGetterTrait
    {
        /**
         * @param string $key - Возможно использование доступа внутри объекта через точку , если объект использет {@see \ArrayAccess,\Psr\Container\ContainerInterface}
         * Например: 'config.providers' вернет массив провайдеров из объекта \Dissonance\Config
         * @param null $default
         * @return mixed|null
         *
         */
        public function get(string $key)
        {
            /**
             * @var DIContainerInterface $this
             */
            $delimiter = '::';
            $key = false !== strpos($key, $delimiter) ? explode($delimiter, $key) : $key;
            try {
                if (is_array($key)) {
                    $c = $key[0];
                    $k = $key[1];
                    $data = $this->make($c);
                    // todo: exception?
                    if ($data instanceof ContainerInterface) {
                        return $data->get($k);
                    } elseif ($data instanceof \ArrayAccess && $data->offsetExists($k)) {
                        return $data->offsetGet($k);
                    } elseif (is_array($data) && array_key_exists($k, $data)) {
                        return $data[$k];
                    }
                    throw new NotFoundException($k, $data);
                }
                try {
                    return $this->make($key);
                    // todo: exception?
                } catch (\Exception $e) {
                    if (!$this->has($key)) {
                        throw new NotFoundException($key, $this);
                    }
                }
            } catch (ContainerException $e) {
                if ($e instanceof NotFoundExceptionInterface && \func_num_args() === 2) {
                    return \func_get_arg(1);
                }
                throw $e;
            }
        }

        /**
         * Менее строгий метод получения данных из контейнера, если ключ не существует вернет NULL по умолчанию
         *
         * @param $key
         * @param null $default
         * @return mixed|null
         * @throws ContainerException|BindingResolutionException|\Exception
         *
         */
        public function __invoke($key, $default = null)
        {
            return $this->get($key, $default);
        }
    }

    /**
     * Trait ConceptualBindingsTrait
     * @package Dissonance\Container
     *
     * Use only in @see DIContainerInterface
     */
    trait ContextualBindingsTrait
    {
        /**
         * The contextual binding map.
         *
         * @var string[][]
         */
        public $contextual = [];

        /**
         * Define a contextual binding.
         *
         * @param array|string $concrete
         * @return ContextualBindingBuilder
         */
        public function when($concrete): ContextualBindingBuilder
        {
            /**
             * @var DIContainerInterface $this
             */
            $aliases = [];
            $concrete = is_array($concrete) ? $concrete : [$concrete];
            foreach ($concrete as $c) {
                $aliases[] = $this->getAlias($c);
            }
            return new ContextualBindingBuilder($this, $aliases);
        }

        /**
         * Add a contextual binding to the container.
         *
         * @param string $concrete
         * @param string $abstract
         * @param mixed $implementation
         * @return void
         */
        public function addContextualBinding(string $concrete, string $abstract, $implementation): void
        {
            /**
             * @var DIContainerInterface|ContextualBindingsInterface $this
             */
            $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
        }

        /**
         * Get the contextual concrete binding for the given abstract.
         *
         * @param string $for_building
         * @param string $need
         *
         * @return \Closure|mixed|null
         */
        public function getContextualConcrete(string $for_building, string $need)
        {
            /**
             * @var DIContainerInterface|ContextualBindingsInterface $this
             */
            if (isset($this->contextual[$for_building][$need])) {
                return $this->contextual[$for_building][$need];
            }
            $aliases = $this->getAbstractAliases($need);
            // Next we need to see if a contextual binding might be bound under an alias of the
            // given abstract type. So, we will need to check if any aliases exist with this
            // type and then spin through them and check for contextual bindings on these.
            if (empty($aliases)) {
                return null;
            }
            foreach ($aliases as $alias) {
                if (isset($this->contextual[$for_building][$alias])) {
                    return $this->contextual[$for_building][$alias];
                }
            }
        }
    }

    interface ContextualBindingsInterface
    {
    }

    trait ContainerTrait
    {
        use ArrayAccessTrait, DeepGetterTrait, MultipleAccessTrait;

        /**
         * An array of the types that have been resolved.
         *
         * @var bool[]
         */
        protected $resolved = [];
        /**
         * The container's bindings.
         *
         * @var array[]
         */
        protected $bindings = [];
        /**
         * The container's shared instances.
         *
         * @var object[]
         */
        protected $instances = [];
        /**
         * The registered type aliases.
         *
         * @var string[]
         */
        protected $aliases = [];
        /**
         * The registered aliases keyed by the abstract name.
         *
         * @var array[]
         *
         * @used-by alias()
         */
        protected $abstractAliases = [];
        /**
         * The extension closures for services.
         *
         * @var array[]
         */
        protected $extenders = [];
        /**
         * All of the registered tags.
         *
         * @var array[]
         */
        protected $tags = [];
        /**
         * The stack of concretions currently being built.
         *
         * @var array[]
         */
        protected $buildStack = [];
        /**
         * The parameter override stack.
         *
         * @var array[]
         */
        protected $with = [];
        /**
         * The contextual binding map.
         *
         * @var array[]
         */
        public $contextual = [];
        /**
         * @var string |null
         */
        protected $current_build = null;
        /**
         * All of the registered rebound callbacks.
         *
         * @var array[]
         */
        protected $reboundCallbacks = [];
        /**
         * All of the global resolving callbacks.
         *
         * @var \Closure[]
         */
        protected $globalResolvingCallbacks = [];
        /**
         * All of the global after resolving callbacks.
         *
         * @var \Closure[]
         */
        protected $globalAfterResolvingCallbacks = [];
        /**
         * All of the resolving callbacks by class type.
         *
         * @var array[]
         */
        protected $resolvingCallbacks = [];
        /**
         * All of the after resolving callbacks by class type.
         *
         * @var array[]
         */
        protected $afterResolvingCallbacks = [];
        /**
         * @var DIContainerInterface[]
         */
        protected $containersStack = [];

        public function has(string $key): bool
        {
            return $this->bound($key);
        }

        public function set(string $key, $value): void
        {
            $this->bind($key, $value instanceof \Closure ? $value : function () use ($value) {
                return $value;
            });
        }

        public function delete(string $key): bool
        {
            unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
        }

        /**
         * Determine if the given abstract type has been bound.
         *
         * @param string $abstract
         * @return bool
         */
        public function bound(string $abstract)
        {
            return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || $this->isAlias($abstract);
        }

        /**
         * Determine if the given abstract type has been resolved.
         *
         * @param string $abstract
         * @return bool
         */
        public function resolved(string $abstract)
        {
            $abstract = $this->getAlias($abstract);
            return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
        }

        /**
         * Determine if a given type is shared.
         *
         * @param string $abstract
         * @return bool
         */
        public function isShared(string $abstract)
        {
            return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared'] === true;
        }

        /**
         * Determine if a given string is an alias.
         *
         * @param string $name
         * @return bool
         */
        public function isAlias(string $name)
        {
            return isset($this->aliases[$name]);
        }

        /**
         * Register a binding with the container.
         *
         * @param string $abstract
         * @param \Closure|string|null $concrete
         * @param bool $shared
         * @return void
         */
        public function bind(string $abstract, $concrete = null, bool $shared = false): void
        {
            unset($this->instances[$abstract], $this->aliases[$abstract]);
            // If no concrete type was given, we will simply set the concrete type to the
            // abstract type. After that, the concrete type to be registered as shared
            // without being forced to state their classes in both of the parameters.
            if (is_null($concrete)) {
                $concrete = $abstract;
            }
            // If the factory is not a Closure, it means it is just a class name which is
            // bound into this container to the abstract type and we will just wrap it
            // up inside its own Closure to give us more convenience when extending.
            if (!$concrete instanceof \Closure) {
                $concrete = $this->getClosure($abstract, $concrete);
            }
            $this->bindings[$abstract] = ['concrete' => $concrete, 'shared' => $shared];
            // If the abstract type was already resolved in this container we'll fire the
            // rebound listener so that any objects which have already gotten resolved
            // can have their copy of the object updated via the listener callbacks.
            if ($this->resolved($abstract)) {
                $this->rebound($abstract);
            }
        }
        /*
         *  public function bindClosure(string $abstract, $concrete = null, bool $shared = false)
         {
             $this->bind($abstract, $concrete, $shared);
         }
        */
        /**
         * Get the Closure to be used when building a type.
         *
         * @param string $abstract
         * @param string $concrete
         * @return \Closure
         *
         * @todo protected?
         */
        public function getClosure(string $abstract, string $concrete)
        {
            return function ($container, $parameters = []) use ($abstract, $concrete) {
                /**
                 * @var \Dissonance\Container\DIContainerInterface $container
                 */
                if ($abstract === $concrete) {
                    return $container->build($concrete);
                }
                return $container->resolve($concrete, $parameters, $raiseEvents = false);
            };
        }

        /**
         * Register a binding if it hasn't already been registered.
         *
         * @param string $abstract
         * @param \Closure|string|null $concrete
         * @param bool $shared
         * @return $this
         */
        public function bindIf(string $abstract, $concrete = null, bool $shared = false)
        {
            if (!$this->bound($abstract)) {
                $this->bind($abstract, $concrete, $shared);
            }
            return $this;
        }

        /**
         * Register a shared binding in the container.
         *
         * @param string $abstract
         * @param \Closure|string|null $concrete
         * @param string|null $alias
         * @return static
         */
        public function singleton(string $abstract, $concrete = null, string $alias = null)
        {
            $this->bind($abstract, $concrete, true);
            if (is_string($alias)) {
                $this->alias($abstract, $alias);
            }
            return $this;
        }

        /**
         * "Extend" an abstract type in the container.
         *
         * @param string $abstract
         * @param \Closure $closure
         * @return void
         *
         * @throws \InvalidArgumentException
         */
        public function extend(string $abstract, Closure $closure): void
        {
            $abstract = $this->getAlias($abstract);
            if (isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $closure($this->instances[$abstract], $this);
                $this->rebound($abstract);
            } else {
                $this->extenders[$abstract][] = $closure;
                if ($this->resolved($abstract)) {
                    $this->rebound($abstract);
                }
            }
        }

        /**
         * Register an existing instance as shared in the container.
         *
         * @param string $abstract
         * @param mixed $instance
         * @param null|string $alias
         * @return mixed
         */
        public function instance(string $abstract, $instance, string $alias = null)
        {
            if (isset($this->aliases[$abstract])) {
                foreach ($this->abstractAliases as $abstr => $aliases) {
                    foreach ($aliases as $index => $alias) {
                        if ($alias == $abstract) {
                            unset($this->abstractAliases[$abstr][$index]);
                        }
                    }
                }
            }
            $isBound = $this->bound($abstract);
            unset($this->aliases[$abstract]);
            // We'll check to determine if this type has been bound before, and if it has
            // we will fire the rebound callbacks registered with the container and it
            // can be updated with consuming classes that have gotten resolved here.
            $this->instances[$abstract] = $instance;
            if ($isBound) {
                $this->rebound($abstract);
            }
            if ($alias) {
                $this->alias($abstract, $alias);
            }
            return $instance;
        }

        public function alias(string $abstract, string $alias)
        {
            if ($alias === $abstract) {
                throw new \LogicException("[{$abstract}] is aliased to itself.");
            }
            $this->aliases[$alias] = $abstract;
            $this->abstractAliases[$abstract][] = $alias;
        }

        /**
         * Get aliases for abstract binding
         *
         * @param string $abstract
         * @return array|null
         */
        public function getAbstractAliases(string $abstract): ?array
        {
            return $this->abstractAliases[$abstract] ?? null;
        }

        /**
         * Bind a new callback to an abstract's rebind event.
         *
         * @param string $abstract
         * @param \Closure $callback
         * @return mixed
         */
        public function rebinding(string $abstract, Closure $callback)
        {
            $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;
            if ($this->bound($abstract)) {
                return $this->make($abstract);
            }
        }

        /**
         * Refresh an instance on the given target and method.
         *
         * @param string $abstract
         * @param mixed $target
         * @param string $method
         * @return mixed
         */
        public function refresh($abstract, $target, $method)
        {
            return $this->rebinding($abstract, function ($app, $instance) use ($target, $method) {
                $target->{$method}($instance);
            });
        }

        /**
         * Fire the "rebound" callbacks for the given abstract type.
         *
         * @param string $abstract
         * @return void
         */
        protected function rebound(string $abstract)
        {
            $instance = $this->make($abstract);
            foreach (isset($this->reboundCallbacks[$abstract]) ? $this->reboundCallbacks[$abstract] : [] as $callback) {
                call_user_func($callback, $this, $instance);
            }
        }

        /**
         * Wrap the given closure such that its dependencies will be injected when executed.
         *
         * @param \Closure $callback
         * @param array $parameters
         * @param string|null $defaultMethod
         * @return \Closure
         */
        public function wrap(\Closure $callback, array $parameters = [], $defaultMethod = null)
        {
            return function () use ($callback, $parameters, $defaultMethod) {
                return $this->call($callback, $parameters, $defaultMethod);
            };
        }

        /**
         * Call the given Closure / class@method and inject its dependencies.
         *
         * @param callable|string $callback
         * @param array $parameters
         * @param string|null $defaultMethod
         * @return mixed
         */
        public function call($callback, array $parameters = [], string $defaultMethod = null)
        {
            return BoundMethod::call($this, $callback, $parameters, $defaultMethod);
        }

        /**
         * Get a closure to resolve the given type from the container.
         *
         * @param string $abstract
         * @return \Closure
         */
        public function factory(string $abstract): Closure
        {
            return function () use ($abstract) {
                return $this->make($abstract);
            };
        }

        /**
         * Resolve the given type from the container.
         *
         * @param string $abstract
         * @param array $parameters
         * @return mixed
         *
         */
        public function make(string $abstract, array $parameters = [])
        {
            return $this->resolve($abstract, $parameters);
        }

        public function setContainersStack(DIContainerInterface $container)
        {
            $this->containersStack[] = $container;
        }

        public function popCurrentContainer()
        {
            array_pop($this->containersStack);
        }

        /**
         * Resolve the given type from the container.
         *
         * @param string $abstract
         * @param array $parameters
         * @param bool $raiseEvents
         * @return mixed
         *
         * @throws BindingResolutionException
         */
        public function resolve(string $abstract, array $parameters = [], bool $raiseEvents = true)
        {
            if (empty($parameters)) {
                $container = !empty($this->containersStack) ? end($this->containersStack) : null;
                if ($container && $container instanceof $abstract) {
                    return $container;
                }
            }
            $abstract = $this->getAlias($abstract);
            $interface = DIContainerInterface::class;
            if (isset($parameters[$interface])) {
                if ($abstract === $interface) {
                    return $parameters[$interface];
                }
                $this->containersStack[] = $parameters[$interface];
                unset($parameters[$interface]);
            } else {
                $this->containersStack[] = $this;
            }
            // todo: test current_build var
            $conceptual_concrete = $this->current_build ? $this->getContextualConcrete($this->current_build, $abstract) : null;
            $needsContextualBuild = !empty($parameters) || !is_null($conceptual_concrete);
            // If an instance of the type is currently being managed as a singleton we'll
            // just return an existing instance instead of instantiating new instances
            // so the developer can keep using the same objects instance every time.
            if (!$needsContextualBuild) {
                if (isset($this->instances[$abstract])) {
                    return $this->instances[$abstract];
                }
            }
            $this->with[] = $parameters;
            $concrete = !empty($conceptual_concrete) ? $conceptual_concrete : (isset($this->bindings[$abstract]) ? $this->bindings[$abstract]['concrete'] : ($this instanceof ServiceContainerInterface && $this->loadDefer($abstract) && isset($this->bindings[$this->getAlias($abstract)]) ? $this->bindings[$this->getAlias($abstract)]['concrete'] : $abstract));
            // We're ready to instantiate an instance of the concrete type registered for
            // the binding. This will instantiate the types, as well as resolve any of
            // its "nested" dependencies recursively until all have gotten resolved.
            if ($this->isBuildable($concrete, $abstract)) {
                $object = $this->build($concrete);
            } else {
                $object = $this->make($concrete);
            }
            // If we defined any extenders for this type, we'll need to spin through them
            // and apply them to the object being built. This allows for the extension
            // of services, such as changing configuration or decorating the object.
            foreach ($this->getExtenders($abstract) as $extender) {
                $object = $extender($object, $this);
            }
            // If the requested type is registered as a singleton we'll want to cache off
            // the instances in "memory" so we can return it later without creating an
            // entirely new instance of an object on each subsequent request for it.
            if ($this->isShared($abstract) && !$needsContextualBuild) {
                $this->instances[$abstract] = $object;
            }
            if ($raiseEvents) {
                $this->fireResolvingCallbacks($abstract, $object);
            }
            // Before returning, we will also set the resolved flag to "true" and pop off
            // the parameter overrides for this build. After those two things are done
            // we will be ready to return back the fully constructed class instance.
            $this->resolved[$abstract] = true;
            array_pop($this->with);
            return $object;
        }

        /**
         * Get the contextual concrete binding for the given abstract.
         *
         * @param string $for_building
         * @param string $need The name of the class ('\MySpace\ClassName') or variable ('$var_name') to build the dependency on.
         * @return \Closure|mixed|null
         */
        protected function getContextualConcrete(string $for_building, string $need)
        {
            $current_container = end($this->containersStack);
            return $current_container instanceof ContextualBindingsInterface ? $current_container->getContextualConcrete($for_building, $need) : null;
        }

        /**
         * Determine if the given concrete is buildable.
         *
         * @param string|\Closure $concrete
         * @param string $abstract
         * @return bool
         */
        protected function isBuildable($concrete, string $abstract)
        {
            return $concrete === $abstract || $concrete instanceof \Closure;
        }

        /**
         * Instantiate a concrete instance of the given type.
         *
         * @param string $concrete
         * @return mixed
         *
         * @throws BindingResolutionException|ContainerException
         */
        public function build($concrete)
        {
            // If the concrete type is actually a Closure, we will just execute it and
            // hand back the results of the functions, which allows functions to be
            // used as resolvers for more fine-tuned resolution of these objects.
            if ($concrete instanceof \Closure) {
                return $concrete($this, $this->getLastParameterOverride());
            }
            try {
                $reflector = new \ReflectionClass($concrete);
            } catch (\Exception $e) {
                throw new ContainerException("Target [{$concrete}] is not instantiable and key not exists in container data!");
            }
            // If the type is not instantiable, the developer is attempting to resolve
            // an abstract type such as an Interface or Abstract Class and there is
            // no binding registered for the abstractions so we need to bail out.
            if (!$reflector->isInstantiable()) {
                if (!empty($this->buildStack)) {
                    $previous = implode(', ', $this->buildStack);
                    $message = "Target [{$concrete}] is not instantiable while building [{$previous}].";
                } else {
                    $message = "Target [{$concrete}] is not instantiable.";
                }
                throw new ContainerException($message);
            }
            $this->buildStack[] = $concrete;
            $this->current_build = $concrete;
            $constructor = $reflector->getConstructor();
            // If there are no constructors, that means there are no dependencies then
            // we can just resolve the instances of the objects right away, without
            // resolving any other types or dependencies out of these containers.
            if (is_null($constructor)) {
                array_pop($this->buildStack);
                $this->current_build = end($this->buildStack);
                return new $concrete();
            }
            $dependencies = $constructor->getParameters();
            // Once we have all the constructor's parameters we can create each of the
            // dependency instances and then use the reflection instances to make a
            // new instance of this class, injecting the created dependencies in.
            $instances = $this->resolveDependencies($dependencies);
            array_pop($this->buildStack);
            $this->current_build = end($this->buildStack);
            return $reflector->newInstanceArgs($instances);
        }

        /**
         * Resolve all of the dependencies from the ReflectionParameters.
         *
         * @param array|\ReflectionParameter[] $dependencies
         * @return array
         *
         * @throws BindingResolutionException
         */
        protected function resolveDependencies(array $dependencies)
        {
            $results = [];
            foreach ($dependencies as $k => $dependency) {
                // If this dependency has a override for this particular build we will use
                // that instead as the value. Otherwise, we will continue with this run
                // of resolutions and let reflection attempt to determine the result.
                if ($this->hasParameterOverride($dependency, $k)) {
                    $results[] = $this->getParameterOverride($dependency, $k);
                    continue;
                }
                // If the class is null, it means the dependency is a string or some other
                // primitive type which we can not resolve since it is not a class and
                // we will just bomb out with an error since we have no-where to go.
                $results[] = is_null(Reflection::getParameterClassName($dependency)) ? $this->resolvePrimitive($dependency) : $this->resolveClass($dependency);
            }
            return $results;
        }

        /**
         * Determine if the given dependency has a parameter override.
         *
         * @param \ReflectionParameter $dependency
         * @param int|null $param_number
         * @return bool
         */
        protected function hasParameterOverride(\ReflectionParameter $dependency, $param_number = null)
        {
            $params = $this->getLastParameterOverride();
            return array_key_exists($dependency->name, $params) || null !== $param_number && array_key_exists($param_number, $params);
        }

        /**
         * Get a parameter override for a dependency.
         *
         * @param \ReflectionParameter $dependency
         * @param int|null $param_number
         * @return mixed
         */
        protected function getParameterOverride(\ReflectionParameter $dependency, $param_number = null)
        {
            $params = $this->getLastParameterOverride();
            if (array_key_exists($dependency->name, $params)) {
                return $params[$dependency->name];
            } elseif (null !== $param_number && array_key_exists($param_number, $params)) {
                return $params[$param_number];
            } elseif (($class = Reflection::getParameterClassName($dependency)) && array_key_exists($class, $params)) {
                return $params[$class];
            }
            /*elseif (null !== $param_number && array_key_exists($param_number, $value_params)) {
                  return $value_params[$param_number];
              }*/
            return null;
        }

        /**
         * Get the last parameter override.
         *
         * @return array
         */
        protected function getLastParameterOverride()
        {
            return !empty($this->with) ? end($this->with) : [];
        }

        /**
         * Resolve a non-class hinted primitive dependency.
         *
         * @param \ReflectionParameter $parameter
         * @return mixed
         *
         * @throws BindingResolutionException
         */
        protected function resolvePrimitive(\ReflectionParameter $parameter)
        {
            if ($this->current_build && !is_null($concrete = $this->getContextualConcrete($this->current_build, '$' . $parameter->name))) {
                return $concrete instanceof \Closure ? $concrete($this) : $concrete;
            }
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \ArgumentCountError("Unresolvable dependency resolving [{$parameter}] in class {$parameter->getDeclaringClass()->getName()}::{$parameter->getDeclaringFunction()->getName()}");
        }

        /**
         * Resolve a class based dependency from the container.
         *
         * @param \ReflectionParameter $parameter
         * @return mixed
         *
         * @throws BindingResolutionException
         */
        protected function resolveClass(\ReflectionParameter $parameter)
        {
            try {
                $container = end($this->containersStack);
                $class = Reflection::getParameterClassName($parameter);
                return $container ? $container->make($class) : $this->make($class);
            } catch (BindingResolutionException $e) {
                if ($parameter->isOptional()) {
                    return $parameter->getDefaultValue();
                }
                throw $e;
            }
        }

        /**
         * Register a new resolving callback.
         *
         * @param \Closure|string $abstract
         * @param callable|null $callback closure or Invokable object
         * @return void
         */
        public function resolving($abstract, callable $callback = null)
        {
            if (is_string($abstract)) {
                $abstract = $this->getAlias($abstract);
            }
            if (is_null($callback) && is_callable($abstract)) {
                $this->globalResolvingCallbacks[] = $abstract;
            } else {
                $this->resolvingCallbacks[$abstract][] = $callback;
            }
        }

        /**
         * Register a new after resolving callback for all types.
         *
         * @param \Closure|string $abstract
         * @param callable|null $callback closure or Invokable object
         * @return void
         */
        public function afterResolving($abstract, callable $callback = null)
        {
            if (is_string($abstract)) {
                $abstract = $this->getAlias($abstract);
            }
            if (is_callable($abstract) && is_null($callback)) {
                $this->globalAfterResolvingCallbacks[] = $abstract;
            } else {
                $this->afterResolvingCallbacks[$abstract][] = $callback;
            }
        }

        /**
         * Fire all of the resolving callbacks.
         *
         * @param string $abstract
         * @param mixed $object
         * @return void
         */
        protected function fireResolvingCallbacks(string $abstract, $object)
        {
            $this->fireResolvingByData($abstract, $object, $this->globalResolvingCallbacks, $this->resolvingCallbacks);
            $this->fireResolvingByData($abstract, $object, $this->globalAfterResolvingCallbacks, $this->afterResolvingCallbacks);
        }

        protected function fireResolvingByData(string $abstract, $object, array $global_callbacks = [], array $types_callbacks = [])
        {
            if (!empty($global_callbacks)) {
                $this->fireCallbackArray($object, $global_callbacks);
            }
            $callbacks = $this->getCallbacksForType($abstract, $object, $types_callbacks);
            if (!empty($callbacks)) {
                $this->fireCallbackArray($object, $callbacks);
            }
        }

        /**
         * Get all callbacks for a given type.
         *
         * @param string $abstract
         * @param mixed $value
         * @param array $callbacksPerType
         *
         * @return array
         */
        protected function getCallbacksForType(string $abstract, $value, array $callbacksPerType)
        {
            $results = [];
            foreach ($callbacksPerType as $type => $callbacks) {
                if ($type === $abstract || is_object($value) && $value instanceof $type) {
                    $results = array_merge($results, $callbacks);
                }
            }
            return $results;
        }

        /**
         * Fire an array of callbacks with an object.
         *
         * @param mixed $object
         * @param array $callbacks
         * @return void
         */
        protected function fireCallbackArray($object, array $callbacks)
        {
            foreach ($callbacks as $callback) {
                $callback($object, $this);
            }
        }

        /**
         * Get the container's bindings.
         *
         * @return array
         */
        public function getBindings()
        {
            return $this->bindings;
        }

        /**
         * Get the alias for an abstract if available.
         *
         * @param string $abstract
         * @return string
         */
        public function getAlias(string $abstract): string
        {
            /*  if (!is_string($abstract) || !isset($this->aliases[$abstract])) {
                    return $abstract;
                }*/
            while (isset($this->aliases[$abstract])) {
                $abstract = $this->aliases[$abstract];
            }
            return $abstract;
            //return $this->getAlias($this->aliases[$abstract]);
        }

        /**
         * Get the extender callbacks for a given type.
         *
         * @param string $abstract
         * @return array
         */
        public function getExtenders(string $abstract)
        {
            $container = !empty($this->containersStack) ? end($this->containersStack) : null;
            return $container instanceof CoreInterface ? $this->extenders[$this->getAlias($abstract)] ?? [] : $container->getExtenders($abstract);
        }

        /**
         * Remove all of the extender callbacks for a given type.
         *
         * @param string $abstract
         * @return void
         */
        public function forgetExtenders(string $abstract)
        {
            unset($this->extenders[$this->getAlias($abstract)]);
        }

        /**
         * Drop all of the stale instances and aliases.
         *
         * @param string $abstract
         * @return void
         */
        protected function dropStaleInstances($abstract)
        {
            unset($this->instances[$abstract], $this->aliases[$abstract]);
        }

        /**
         * Remove a resolved instance from the instance cache.
         *
         * @param string $abstract
         * @return void
         */
        public function forgetInstance($abstract)
        {
            unset($this->instances[$abstract]);
        }

        /**
         * Clear all of the instances from the container.
         *
         * @return void
         */
        public function forgetInstances()
        {
            $this->instances = [];
        }

        /**
         * Flush the container of all bindings and resolved instances.
         *
         * @return void
         */
        public function clear(): void
        {
            $this->aliases = [];
            $this->resolved = [];
            $this->bindings = [];
            $this->instances = [];
            $this->abstractAliases = [];
        }
    }

    /**
     * Interface MultipleAccessInterface
     *
     * @package Dissonance\Container
     *
     */
    interface MultipleAccessInterface
    {
    }

    class Reflection
    {
        /**
         * @param \ReflectionParameter $parameter
         * @return string|null
         */
        public static function getParameterClassName(\ReflectionParameter $parameter): ?string
        {
            if (\PHP_VERSION_ID >= 70000) {
                $type = $parameter->getType();
                if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                    return null;
                }
                $name = $type->getName();
                if (!is_null($class = $parameter->getDeclaringClass())) {
                    if ($name === 'self') {
                        return $class->getName();
                    }
                    if ($name === 'parent' && ($parent = $class->getParentClass())) {
                        return $parent->getName();
                    }
                }
                return $name;
            } else {
                return $parameter->getClass() ? $parameter->getClass()->getName() : null;
            }
        }
    }

    class BoundMethod
    {
        /**
         * Call the given \Closure or className@methodName and inject its dependencies.
         *
         * @param \Dissonance\Container\DIContainerInterface $container
         * @param callable|string $callback
         * @param array $parameters
         * @param string|null $defaultMethod
         * @return mixed
         *
         * @throws \ReflectionException
         * @throws \InvalidArgumentException
         */
        public static function call($container, $callback, array $parameters = [], $defaultMethod = null)
        {
            if (static::isCallableWithAtSign($callback) || $defaultMethod) {
                return static::callClass($container, $callback, $parameters, $defaultMethod);
            }
            return static::callBoundMethod($container, $callback, function () use ($container, $callback, $parameters) {
                return call_user_func_array($callback, static::getMethodDependencies($container, $callback, $parameters));
            });
        }

        /**
         * Call a string reference to a class using Class@method syntax.
         *
         * @param \Dissonance\Container\DIContainerInterface $container
         * @param string $target
         * @param array $parameters
         * @param string|null $defaultMethod
         * @return mixed
         *
         * @throws \InvalidArgumentException
         */
        protected static function callClass($container, $target, array $parameters = [], $defaultMethod = null)
        {
            $segments = explode('@', $target);
            // We will assume an @ sign is used to delimit the class name from the method
            // name. We will split on this @ sign and then build a callable array that
            // we can pass right back into the "call" method for dependency binding.
            $method = count($segments) === 2 ? $segments[1] : $defaultMethod;
            if (is_null($method)) {
                throw new InvalidArgumentException('Method not provided.');
            }
            return static::call($container, [$container->make($segments[0]), $method], $parameters);
        }

        /**
         * Call a method that has been bound to the container.
         *
         * @param \Dissonance\Container\DIContainerInterface $container
         * @param callable $callback
         * @param mixed $default
         * @return mixed
         */
        protected static function callBoundMethod($container, $callback, $default)
        {
            if (!is_array($callback)) {
                return $default instanceof \Closure ? $default() : $default;
            }
            // Here we need to turn the array callable into a Class@method string we can use to
            // examine the container and see if there are any method bindings for this given
            // method. If there are, we can call this method binding callback immediately.
            $method = static::normalizeMethod($callback);
            if ($container->hasMethodBinding($method)) {
                return $container->callMethodBinding($method, $callback[0]);
            }
            return $default instanceof \Closure ? $default() : $default;
        }

        /**
         * Normalize the given callback into a Class@method string.
         *
         * @param callable $callback
         * @return string
         */
        protected static function normalizeMethod($callback)
        {
            $class = is_string($callback[0]) ? $callback[0] : get_class($callback[0]);
            return "{$class}@{$callback[1]}";
        }

        /**
         * Get all dependencies for a given method.
         *
         * @param \Dissonance\Container\DIContainerInterface $container
         * @param callable|string $callback
         * @param array $parameters
         * @return array
         *
         * @throws \ReflectionException
         */
        protected static function getMethodDependencies($container, $callback, array $parameters = [])
        {
            $dependencies = [];
            foreach (static::getCallReflector($callback)->getParameters() as $parameter) {
                static::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies);
            }
            return array_merge(array_values($dependencies), array_values($parameters));
            //return $dependencies;
        }

        /**
         * Get the proper reflection instance for the given callback.
         *
         * @param callable|string $callback
         * @return \ReflectionFunctionAbstract
         *
         * @throws \ReflectionException
         */
        protected static function getCallReflector($callback)
        {
            if (is_string($callback) && strpos($callback, '::') !== false) {
                $callback = explode('::', $callback);
            }
            return is_array($callback) ? new ReflectionMethod($callback[0], $callback[1]) : new ReflectionFunction($callback);
        }

        /**
         * Get the dependency for the given call parameter.
         *
         * @param \Dissonance\Container\DIContainerInterface $container
         * @param \ReflectionParameter $parameter
         * @param array $parameters
         * @param array $dependencies
         * @return void
         */
        protected static function addDependencyForCallParameter(DIContainerInterface $container, \ReflectionParameter $parameter, array &$parameters, &$dependencies)
        {
            if (array_key_exists($parameter->name, $parameters)) {
                $dependencies[] = $parameters[$parameter->name];
                unset($parameters[$parameter->name]);
            } elseif ($class = Reflection::getParameterClassName($parameter)) {
                if (array_key_exists($class, $parameters)) {
                    $dependencies[] = $parameters[$class];
                    unset($parameters[$class]);
                } else {
                    $dependencies[] = $container->make($class);
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new BindingResolutionException('Parameter [' . $parameter->getName() . '] is not find!');
            }
        }

        /**
         * Determine if the given string is in Class@method syntax.
         *
         * @param mixed $callback
         * @return bool
         */
        public static function isCallableWithAtSign($callback)
        {
            return is_string($callback) && strpos($callback, '@') !== false;
        }
    }

    class ContextualBindingBuilder
    {
        /**
         * The underlying container instance.
         *
         * @var \Dissonance\Container\DIContainerInterface
         */
        protected $container;
        /**
         * The concrete instance.
         *
         * @var string|array
         */
        protected $concrete;
        /**
         * The abstract target.
         *
         * @var string
         */
        protected $needs;

        /**
         * Create a new contextual binding builder.
         *
         * @param \Dissonance\Container\DIContainerInterface $container
         * @param string|array $concrete
         * @return void
         */
        public function __construct(DIContainerInterface $container, $concrete)
        {
            $this->concrete = $concrete;
            $this->container = $container;
        }

        /**
         * Define the abstract target that depends on the context.
         *
         * @param string $abstract
         * @return $this
         */
        public function needs(string $abstract)
        {
            $this->needs = $abstract;
            return $this;
        }

        /**
         * Define the implementation for the contextual binding.
         *
         * @param \Closure|mixed $implementation
         * @return void
         */
        public function give($implementation)
        {
            $concretes = $this->concrete;
            foreach (!empty($concretes) ? (array)$concretes : [] as $concrete) {
                $this->container->addContextualBinding($concrete, $this->needs, $implementation);
            }
        }
    }

    class ContainerException extends \Exception implements ContainerExceptionInterface
    {
    }

    class BindingResolutionException extends ContainerException
    {
        //
    }

    /**
     * Class ServiceProvider
     * @package Dissonance
     * @property  DIContainerInterface|array $app  = [
     *       'config' => new \Dissonance\Config(),
     *       'router' => new \Dissonance\Contracts\Routing\Router(),
     *       'apps' => new \Dissonance\Contracts\Apps\AppsRepository(),
     *       'events' => new \Dissonance\Contracts\Events\Dispatcher(),
     *       'listeners' => new \Dissonance\Event\ListenerProvider()
     * ]
     */
    class ServiceProvider implements ServiceProviderInterface
    {
        /**
         * @var DIContainerInterface| [ 'config' => new \Dissonance\Config() ]
         */
        protected $app = null;

        public function __construct(DIContainerInterface $app)
        {
            $this->app = $app;
        }

        /**
         * @return void
         * @phpcompressor-delete
         */
        public function register(): void
        {
        }

        /**
         * @return void
         * @phpcompressor-delete
         */
        public function boot(): void
        {
        }

        /**
         * Возвращает массив привязок
         *
         * Вы можете описать данный метод, чтобы массово вернуть фабричные методы для создания для объектов
         * return [
         *      ClassName::class => function($dependencies){return new ClassName();},
         *      TwoClass:class   => function($data){return new TwoClass($data);},
         * ]
         *
         * @return array| \Closure[]
         * @phpcompressor-delete
         */
        public function bindings(): array
        {
            return [];
        }

        /**
         * Возвращает массив привязок
         *
         * Вы можете описать данный метод, чтобы массово вернуть фабричные методы для создания для объектов
         * return [
         *      ClassName::class => function($dependencies){return new ClassName();},
         *      TwoClass:class   => function($data){return new TwoClass($data);},
         * ]
         *
         * @return string[]| \Closure[]
         * @phpcompressor-delete
         */
        public function singletons(): array
        {
            return [];
        }

        /**
         * @return string[]
         */
        public function aliases(): array
        {
            return [];
        }
    }

    class Container implements DIContainerInterface
    {
        use MethodBindingsTrait, ContainerTrait;
    }

    class NotFoundException extends ContainerException implements NotFoundExceptionInterface
    {
        public function __construct($key, $container, $code = 1384, Throwable $previous = null)
        {
            $message = 'Not found key [' . $key . '] in (' . (is_object($container) ? get_class($container) : gettype($container)) . ')!';
            parent::__construct($message, $code, $previous);
        }
    }
}

namespace Dissonance\Contracts {

    use Dissonance\Container\DIContainerInterface;
    use Dissonance\Container\ServiceContainerInterface;
    use Dissonance\Contracts\Http\HttpKernelInterface;
    use Dissonance\HttpKernel\HttpKernel;

    /**
     * Allowed only in Core container!
     * Interface BootstrapInterface
     * @package Dissonance\Contracts
     */
    interface BootstrapInterface
    {
    }

    interface RunnerInterface
    {
    }

    /**
     * Interface CoreInterface
     * @package Dissonance\Contracts
     * @var $this = [
     *     'request' => new Psr\Http\Message\ServerRequestInterface()
     * ];
     */
    interface CoreInterface extends DIContainerInterface, ServiceContainerInterface
    {
    }
}

namespace Dissonance\Apps {

    use Dissonance\Container\DIContainerInterface;
    use Dissonance\Contracts\App\AppConfigInterface;
    use Dissonance\Contracts\App\ApplicationInterface;
    use Dissonance\App\AppConfig;
    use Dissonance\App\Application;
    use Dissonance\Container\CachedContainerInterface;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresDispatcher;
    use Dissonance\Packages\Contracts\PackagesRepositoryInterface;
    use Dissonance\Packages\PackagesRepository;
    use Dissonance\Routing\AppsRoutesRepository;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Contracts\BootstrapInterface;
    use function _DS\app;

    interface AppsRepositoryInterface
    {
    }

    class Bootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            // Apps repository
            if ($app instanceof CachedContainerInterface) {
                $app->cached(AppsRepositoryInterface::class);
            }
            if (!$app->bound(AppsRepositoryInterface::class)) {
                $app->singleton(AppsRepositoryInterface::class, function ($app) {
                    $apps_repository = new AppsRepository();
                    foreach ($app[PackagesRepositoryInterface::class]->getPackages() as $config) {
                        $app = isset($config['app']) ? $config['app'] : null;
                        if (is_array($app)) {
                            $apps_repository->addApp($app);
                        }
                    }
                    return $apps_repository;
                });
            }
            $app->alias(AppsRepositoryInterface::class, 'apps');
            /**
             * @used-by  \Dissonance\Routing\Provider::boot()
             * or
             * @used-by  \Dissonance\SettlementsRouting\Provider::register()
             */
            $app['listeners']->add(AppsRoutesRepository::class, function (AppsRoutesRepository $event, AppsRepositoryInterface $appsRepository) {
                foreach ($appsRepository->enabled() as $v) {
                    $provider = $v->getRoutingProvider();
                    if ($provider && class_exists($provider)) {
                        $event->append(new $provider($v));
                    }
                }
            });
            /*   $app['listeners']->add(PackagesRepository::class, function (PackagesRepository $event) use ($app) {
                     $apps_repository = $app[AppsRepositoryInterface::class];
                     foreach ($event->getPackages() as $config) {
                         $app = isset($config['app']) ? $config['app'] : null;
                         if (is_array($app)) {
                             $apps_repository->addApp($app);
                         }
                    }
                 });*/
        }
    }

    class AppsRepository implements AppsRepositoryInterface
    {
        /**
         * @var \Dissonance\Core|\Dissonance\Container\DIContainerInterface|null
         */
        protected $app;
        /**
         * @var ApplicationInterface[]
         */
        protected $apps = [];
        /**
         * @var AppConfigInterface[][]
         */
        protected $apps_plugins = [];
        /**
         * @var AppConfigInterface[]
         */
        protected $apps_config = [];
        protected $disabled_apps = [];

        public function disableApps(array $ids)
        {
            $this->disabled_apps = array_merge($this->disabled_apps, array_combine($ids, $ids));
        }

        /**
         *
         * Фреймворк принимает пакеты композера в качестве приложений и компонентов или просто архив зарегистрированный
         * в системе как приложение(будет сайт).
         * Система предполагает многоуровневую зависимость приложений и пакетов, как пример: есть приложение визуального редактора Tiny,
         * для него есть плагин для редактирования изображений , для редактора изображений есть фича(кнопка), например размытие  лиц на фото.
         *
         * @param array $config = [
         *     'id' => 'app_id_string', // Register short app id or use composer package name
         *     'title' => 'App title',
         *     'parent_app' => 'parent_app_id', //  Parent app id or package name
         *     'description' => 'App description....',
         *     'routing' => '\\Dissonance\\App\\Core\\Routing', // class name implements {@see \Dissonance\Contracts\Routing\AppRoutingInterface}
         *     'controllers_namespace' => '\\Dissonance\\App\\Core\\Controllers', // Your base controllers namespace
         *     'version' => '1.0.2',
         *     'providers' => [    // Providers of your app
         *       '\\Dissonance\\App\\Core\\Providers\\FilesProvider',
         *       '\\Dissonance\\App\\Core\\Providers\\AppsUpdaterProvider',
         *      ],
         *
         *     // .... and your advanced params
         * ]
         * @return void
         */
        public function addApp(array $config)
        {
            if (!empty($config['id'])) {
                $parent_app = $config['parent_app'] ?? null;
                $id = $config['id'];
                $app_config = new AppConfig($config);
                $this->apps_config[$id] = $app_config;
                if ($parent_app) {
                    $this->apps_plugins[$parent_app][$id] = $app_config;
                }
            }
        }

        public function normalizeId(string $id)
        {
            return str_replace('/', '_', $id);
        }

        /**
         * @param string $id
         * @return ApplicationInterface|null
         */
        public function get(string $id): ?ApplicationInterface
        {
            if (isset($this->apps[$id])) {
                return $this->apps[$id];
            }
            if (isset($this->apps_config[$id])) {
                $config = $this->apps_config[$id];
                $app = app(isset($config['app_class']) ? $config['app_class'] : Application::class, ['app' => isset($config['parent_app']) ? $this->get($config['parent_app']) : app(), 'config' => $config]);
                return $this->apps[$id] = $app;
            }
            throw new \Exception("Application with id [{$id}] is not exists!");
        }

        /**
         * @param string $id
         * @return AppConfigInterface|null
         */
        public function getConfig(string $id): ?AppConfigInterface
        {
            return isset($this->apps_config[$id]) ? $this->apps_config[$id] : null;
        }

        /**
         * @param string $id
         * @return ApplicationInterface|null
         */
        public function getBootedApp(string $id): ?ApplicationInterface
        {
            $app = $this->get($id);
            if ($app) {
                $app->bootstrap();
            }
            return $app;
        }

        /**
         * @param string $id
         * @return bool
         */
        public function has(string $id): bool
        {
            return isset($this->apps_config[$id]);
        }

        /**
         * @return array|string[]
         */
        public function getIds()
        {
            return array_keys($this->apps_config);
        }

        /**
         * @return AppConfigInterface[]
         */
        public function getConfigs(): array
        {
            return $this->apps_config;
        }

        /**
         * @return \Dissonance\Contracts\App\ApplicationInterface[]
         *
         * @throws \Exception
         */
        public function enabled(): array
        {
            return $this->all();
        }

        /**
         * @param string $id
         * @return array|AppConfigInterface[]
         */
        public function getPlugins(string $id): array
        {
            return isset($this->apps_plugins[$id]) ? $this->apps_plugins[$id] : [];
        }

        /**
         * @return \Dissonance\Contracts\App\AppConfigInterface[]
         *
         * @throws \Exception
         */
        public function all(): array
        {
            return $this->apps_config;
        }

        public function __sleep()
        {
            return ['apps_config', 'apps_plugins'];
        }
    }
}

namespace Dissonance\Support {

    use ArrayAccess;
    use InvalidArgumentException;
    use Dissonance\Contracts\Support\ArrayableInterface;
    use Dissonance\Contracts\Support\JsonableInterface;
    use Dissonance\Container\Traits\ArrayAccessTrait;
    use Dissonance\Container\Traits\MagicAccessTrait;
    use Traversable;
    use ArrayIterator;
    use function _DS\value;

    trait CollectionTrait
    {
        use ArrayAccessTrait;
        use MagicAccessTrait;

        /**
         * The items contained in the collection.
         *
         * @var array
         */
        protected $items = [];

        /**
         * Create a new collection.
         *
         * @param mixed $items
         * @return void
         */
        public function __construct($items = [])
        {
            $this->items = $this->getArrayableItems($items);
        }

        /**
         * Create a new collection instance if the value isn't one already.
         *
         * @param mixed $items
         * @return static
         */
        public static function create($items = [])
        {
            return new static($items);
        }

        /**
         * Wrap the given value in a collection if applicable.
         *
         * @param mixed $value
         * @return static
         */
        public static function wrap($value)
        {
            return new static($value instanceof self ? $value : Arr::wrap($value));
        }

        /**
         * Get the underlying items from the given collection if applicable.
         *
         * @param array|static $value
         * @return array
         */
        public static function unwrap($value)
        {
            return $value instanceof self ? $value->all() : $value;
        }

        /**
         * Get all of the items in the collection.
         *
         * @return array
         */
        public function all()
        {
            return $this->items;
        }

        /**
         * Run a filter over each of the items.
         *
         * @param callable|null $callback
         * @return static
         */
        public function filter(callable $callback = null)
        {
            return new static($callback ? Arr::where($this->items, $callback) : array_filter($this->items));
        }

        /**
         * Apply the callback if the value is truthy.
         *
         * @param bool $value
         * @param callable $callback
         * @param callable $default
         * @return static|mixed
         */
        public function when($value, callable $callback, callable $default = null)
        {
            if ($value) {
                return $callback($this, $value);
            } elseif ($default) {
                return $default($this, $value);
            }
            return $this;
        }

        /**
         * Get an operator checker callback.
         *
         * @param string $key
         * @param string $operator
         * @param mixed $value
         * @return \Closure
         */
        protected function operatorForWhere($key, $operator = null, $value = null)
        {
            $args = func_num_args();
            if ($args < 3) {
                $value = $args < 2 ? true : $operator;
                $operator = '=';
            }
            return function ($item) use ($key, $operator, $value) {
                $retrieved = data_get($item, $key);
                $strings = array_filter([$retrieved, $value], function ($value) {
                    return is_string($value) || is_object($value) && method_exists($value, '__toString');
                });
                if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                    return in_array($operator, ['!=', '<>', '!==']);
                }
                switch ($operator) {
                    default:
                    case '=':
                    case '==':
                        return $retrieved == $value;
                    case '!=':
                    case '<>':
                        return $retrieved != $value;
                    case '<':
                        return $retrieved < $value;
                    case '>':
                        return $retrieved > $value;
                    case '<=':
                        return $retrieved <= $value;
                    case '>=':
                        return $retrieved >= $value;
                    case '===':
                        return $retrieved === $value;
                    case '!==':
                        return $retrieved !== $value;
                }
            };
        }

        /**
         * Get an item from the collection by key.
         *
         * @param mixed $key
         * @param mixed $default
         * @return mixed
         */
        public function get(string $key, $default = null)
        {
            return Arr::get($this->items, $key, $default);
        }

        /**
         * Get the first item from the collection.
         *
         * @param callable|null $callback
         * @param mixed $default
         * @return mixed
         */
        public function first(callable $callback = null, $default = null)
        {
            return Arr::first($this->items, $callback, $default);
        }

        /**
         * Get the first item from the collection.
         *
         * @param callable|null $callback
         * @param mixed $default
         * @return mixed
         */
        public function last(callable $callback = null, $default = null)
        {
            return Arr::last($this->items, $callback, $default);
        }

        /**
         * Determine if an item exists in the collection by key.
         *
         * @param array|string $key
         * @return bool
         */
        public function has(string $key): bool
        {
            return Arr::has($this->items, $key);
        }

        /**
         * Set the item at a given offset.
         *
         * @param mixed $key
         * @param mixed $value
         * @return void
         */
        public function set($key, $value): void
        {
            if (is_null($key)) {
                $this->items[] = $value;
            } else {
                Arr::set($this->items, $key, $value);
            }
        }

        /**
         * Determine if an item exists in the collection by key.
         *
         * @param array|string $keys
         * @return void
         */
        public function remove($keys): void
        {
            Arr::forget($this->items, $keys);
        }

        /**
         * Determine if the collection is empty or not.
         *
         * @return bool
         */
        public function isEmpty()
        {
            return empty($this->items);
        }

        /**
         * Determine if the given value is callable, but not a string.
         *
         * @param mixed $value
         * @return bool
         */
        protected function useAsCallable($value)
        {
            return !is_string($value) && is_callable($value);
        }

        /**
         * Get the keys of the collection items.
         *
         * @return static
         */
        public function keys()
        {
            return new static(array_keys($this->items));
        }

        /**
         * Run a map over each of the items.
         *
         * @param callable $callback
         * @param bool $replace_keys - use 2 param in your callback f($item, $key){return [$item, $key]}
         * @return static
         */
        public function map(callable $callback, $replace_keys = false)
        {
            $keys = array_keys($this->items);
            $items = array_map($callback, $this->items, $keys);
            if ($replace_keys) {
                $tmp = [];
                foreach ($items as $item) {
                    $tmp[$item[1]] = $item[0];
                }
                return new static($tmp);
            }
            return new static(array_combine($keys, $items));
        }

        /**
         * Transform each item in the collection using a callback.
         *
         * @param callable $callback
         * @return static
         */
        public function transform(callable $callback)
        {
            $this->items = $this->map($callback)->all();
            return $this;
        }

        /**
         * Map the values into a new class.
         *
         * @param string $class
         * @return static
         */
        public function mapInto($class)
        {
            return $this->map(function ($value, $key) use ($class) {
                return new $class($value, $key);
            });
        }

        /**
         * Merge the collection with the given items.
         *
         * @param mixed $items
         * @return static
         */
        public function merge($items)
        {
            return new static(array_merge($this->items, $this->getArrayableItems($items)));
        }

        /**
         * Create a collection by using this collection for keys and another for its values.
         *
         * @param mixed $values
         * @return static
         */
        public function combine($values)
        {
            return new static(array_combine($this->all(), $this->getArrayableItems($values)));
        }

        /**
         * Union the collection with the given items.
         *
         * @param mixed $items
         * @return static
         */
        public function union($items)
        {
            return new static($this->items + $this->getArrayableItems($items));
        }

        /**
         * Get and remove the last item from the collection.
         *
         * @return mixed
         */
        public function pop()
        {
            return array_pop($this->items);
        }

        /**
         * Push an item onto the beginning of the collection.
         *
         * @param mixed $value
         * @param mixed $key
         * @return $this
         */
        public function prepend($value, $key = null)
        {
            $this->items = Arr::prepend($this->items, $value, $key);
            return $this;
        }

        /**
         * Add an item to the collection.
         *
         * @param mixed $item
         * @return $this
         */
        public function add($item)
        {
            $this->set(null, $item);
            return $this;
        }

        /**
         * Push an item onto the end of the collection.
         *
         * @param mixed $value
         * @return $this
         */
        public function push($value)
        {
            return $this->add($value);
        }

        /**
         * Get and remove an item from the collection.
         *
         * @param mixed $key
         * @param mixed $default
         * @return mixed
         */
        public function pull($key, $default = null)
        {
            return Arr::pull($this->items, $key, $default);
        }

        /**
         * Put an item in the collection by key.
         *
         * @param mixed $key
         * @param mixed $value
         * @return $this
         */
        public function put($key, $value)
        {
            $this->set($key, $value);
            return $this;
        }

        /**
         * Search the collection for a given value and return the corresponding key if successful.
         *
         * @param mixed $value
         * @param bool $strict
         * @return mixed
         */
        public function search($value, $strict = false)
        {
            if (!$this->useAsCallable($value)) {
                return array_search($value, $this->items, $strict);
            }
            foreach ($this->items as $key => $item) {
                if (call_user_func($value, $item, $key)) {
                    return $key;
                }
            }
            return false;
        }

        /**
         * Create a collection of all elements that do not pass a given truth test.
         *
         * @param callable|mixed $callback
         * @return static
         */
        public function reject($callback)
        {
            if ($this->useAsCallable($callback)) {
                return $this->filter(function ($value, $key) use ($callback) {
                    return !$callback($value, $key);
                });
            }
            return $this->filter(function ($item) use ($callback) {
                return $item != $callback;
            });
        }

        /**
         * Get and remove the first item from the collection.
         *
         * @return mixed
         */
        public function shift()
        {
            return array_shift($this->items);
        }

        /**
         * Split a collection into a certain number of groups.
         *
         * @param int $numberOfGroups
         * @return static
         */
        public function split($numberOfGroups)
        {
            if ($this->isEmpty()) {
                return new static();
            }
            $groups = new static();
            $groupSize = floor($this->count() / $numberOfGroups);
            $remain = $this->count() % $numberOfGroups;
            $start = 0;
            for ($i = 0; $i < $numberOfGroups; $i++) {
                $size = $groupSize;
                if ($i < $remain) {
                    $size++;
                }
                if ($size) {
                    $groups->push(new static(array_slice($this->items, $start, $size)));
                    $start += $size;
                }
            }
            return $groups;
        }

        /**
         * Chunk the underlying collection array.
         *
         * @param int $size
         * @return static
         */
        public function chunk($size)
        {
            if ($size <= 0) {
                return new static();
            }
            $chunks = [];
            foreach (array_chunk($this->items, $size, true) as $chunk) {
                $chunks[] = new static($chunk);
            }
            return new static($chunks);
        }

        /**
         * Slice the underlying collection array.
         *
         * @param int $offset
         * @param int|null $length
         * @return static
         */
        public function slice($offset, $length = null)
        {
            return new static(array_slice($this->items, $offset, $length, true));
        }

        /**
         * Sort through each item with a callback.
         *
         * @param callable|null $callback
         * @return static
         */
        public function sort(callable $callback = null)
        {
            $items = $this->items;
            $callback ? uasort($items, $callback) : asort($items);
            return new static($items);
        }

        /**
         * Return only unique items from the collection array.
         *
         * @param string|callable|null $key
         * @param bool $strict
         * @return static
         */
        public function unique($key = null, $strict = false)
        {
            $callback = $this->valueRetriever($key);
            $exists = [];
            return $this->reject(function ($item, $key) use ($callback, $strict, &$exists) {
                if (in_array($id = $callback($item, $key), $exists, $strict)) {
                    return true;
                }
                $exists[] = $id;
            });
        }

        /**
         * Sort the collection keys.
         *
         * @param int $options
         * @param bool $descending
         * @return static
         */
        public function sortKeys($options = SORT_REGULAR, $descending = false)
        {
            $items = $this->items;
            $descending ? krsort($items, $options) : ksort($items, $options);
            return new static($items);
        }

        /**
         * Get a value retrieving callback.
         *
         * @param callable|string|null $value
         * @return callable
         */
        protected function valueRetriever($value)
        {
            if ($this->useAsCallable($value)) {
                return $value;
            }
            return function ($item) use ($value) {
                return data_get($item, $value);
            };
        }

        /**
         * Pad collection to the specified length with a value.
         *
         * @param int $size
         * @param mixed $value
         * @return static
         */
        public function pad($size, $value)
        {
            return new static(array_pad($this->items, $size, $value));
        }

        /**
         * Reverse items order.
         *
         * @return static
         */
        public function reverse()
        {
            return new static(array_reverse($this->items, true));
        }

        /**
         * Get the collection of items as a plain array.
         *
         * @return array
         */
        public function toArray()
        {
            return array_map(function ($value) {
                return $value instanceof ArrayableInterface ? $value->toArray() : $value;
            }, $this->items);
        }

        /**
         * Convert the object into something JSON serializable.
         *
         * @return array
         */
        public function jsonSerialize()
        {
            return array_map(function ($value) {
                if ($value instanceof \JsonSerializable) {
                    return $value->jsonSerialize();
                } elseif ($value instanceof JsonableInterface) {
                    return \json_decode($value->toJson(), true);
                } elseif ($value instanceof ArrayableInterface) {
                    return $value->toArray();
                }
                return $value;
            }, $this->items);
        }

        /**
         * Get the collection of items as JSON.
         *
         * @param int $options
         * @return string
         */
        public function toJson($options = 0)
        {
            return \json_encode($this->jsonSerialize(), $options);
        }

        /**
         * Get an iterator for the items.
         *
         * @return \ArrayIterator
         */
        public function getIterator()
        {
            return new ArrayIterator($this->items);
        }

        /**
         * Count the number of items in the collection.
         *
         * @return int
         */
        public function count()
        {
            return count($this->items);
        }

        /**
         * Convert the collection to its string representation.
         *
         * @return string
         */
        public function __toString()
        {
            return $this->toJson();
        }

        /**
         * Results array of items from Collection or Arrayable.
         *
         * @param mixed $items
         * @return array
         */
        protected function getArrayableItems($items)
        {
            if (is_array($items)) {
                return $items;
            } elseif ($items instanceof self) {
                return $items->all();
            } elseif ($items instanceof ArrayableInterface) {
                return $items->toArray();
            } elseif ($items instanceof JsonableInterface) {
                return \json_decode($items->toJson(), true);
            } elseif ($items instanceof \JsonSerializable) {
                return $items->jsonSerialize();
            } elseif ($items instanceof Traversable) {
                return iterator_to_array($items);
            }
            return (array)$items;
        }
    }

    class Str
    {
        /**
         * The cache of snake-cased words.
         *
         * @var array
         */
        protected static $snakeCache = [];
        /**
         * The cache of camel-cased words.
         *
         * @var array
         */
        protected static $camelCache = [];
        /**
         * The cache of studly-cased words.
         *
         * @var array
         */
        protected static $studlyCache = [];

        /**
         * Return the remainder of a string after a given value.
         *
         * @param string $subject
         * @param string $search
         * @return string
         */
        public static function after($subject, $search)
        {
            return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
        }

        /**
         * Get the portion of a string before a given value.
         *
         * @param string $subject
         * @param string $search
         * @return string
         */
        public static function before($subject, $search)
        {
            return $search === '' ? $subject : explode($search, $subject)[0];
        }

        /**
         * Convert a value to camel case.
         *
         * @param string $value
         * @return string
         */
        public static function camel($value)
        {
            if (isset(static::$camelCache[$value])) {
                return static::$camelCache[$value];
            }
            return static::$camelCache[$value] = lcfirst(static::studly($value));
        }

        /**
         * Determine if a given string contains a given substring.
         *
         * @param string $haystack
         * @param string|array $needles
         * @return bool
         */
        public static function contains($haystack, $needles)
        {
            foreach ((array)$needles as $needle) {
                if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                    return true;
                }
            }
            return false;
        }

        /**
         * @param $value
         * @param string $delimiter
         * @return string|string[]
         */
        public static function sc($value, $delimiter = '::')
        {
            return static::splitClass($value, $delimiter);
        }

        public static function splitClass($value, $delimiter = '::')
        {
            return false !== strpos($value, $delimiter) ? explode('::', $value) : $value;
        }

        /**
         * Determine if a given string ends with a given substring.
         *
         * @param string $haystack
         * @param string|array $needles
         * @return bool
         */
        public static function endsWith($haystack, $needles)
        {
            foreach ((array)$needles as $needle) {
                if (substr($haystack, -strlen($needle)) === (string)$needle) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Cap a string with a single instance of a given value.
         *
         * @param string $value
         * @param string $cap
         * @return string
         */
        public static function finish($value, $cap)
        {
            return preg_replace('/(?:' . preg_quote($cap, '/') . ')+$/u', '', $value) . $cap;
        }

        /**
         * Determine if a given string matches a given pattern.
         *
         * @param string|array $pattern
         * @param string $value
         * @return bool
         */
        public static function is($pattern, $value)
        {
            $patterns = Arr::wrap($pattern);
            if (empty($patterns)) {
                return false;
            }
            foreach ($patterns as $pattern) {
                // If the given value is an exact match we can of course return true right
                // from the beginning. Otherwise, we will translate asterisks and do an
                // actual pattern match against the two strings to see if they match.
                if ($pattern == $value) {
                    return true;
                }
                $pattern = preg_quote($pattern, '#');
                // Asterisks are translated into zero-or-more regular expression wildcards
                // to make it convenient to check if the strings starts with the given
                // pattern such as "library/*", making any string check convenient.
                $pattern = str_replace('\\*', '.*', $pattern);
                if (preg_match('#^' . $pattern . '\\z#u', $value) === 1) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Return the length of the given string.
         *
         * @param string $value
         * @param string $encoding
         * @return int
         */
        public static function length($value, $encoding = null)
        {
            return mb_strlen($value, $encoding);
        }

        /**
         * Limit the number of characters in a string.
         *
         * @param string $value
         * @param int $limit
         * @param string $end
         * @return string
         */
        public static function limit($value, $limit = 100, $end = '...')
        {
            if (mb_strwidth($value, 'UTF-8') <= $limit) {
                return $value;
            }
            return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
        }

        /**
         * Convert the given string to lower-case.
         *
         * @param string $value
         * @return string
         */
        public static function lower($value)
        {
            return mb_strtolower($value, 'UTF-8');
        }

        /**
         * Generate a more truly "random" alpha-numeric string.
         *
         * @param int $length
         * @return string
         */
        public static function random($length = 16)
        {
            $string = '';
            while (($len = strlen($string)) < $length) {
                $size = $length - $len;
                $bytes = random_bytes($size);
                $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
            }
            return $string;
        }

        /**
         * Begin a string with a single instance of a given value.
         *
         * @param string $value
         * @param string $prefix
         * @return string
         */
        public static function start($value, $prefix)
        {
            return $prefix . preg_replace('/^(?:' . preg_quote($prefix, '/') . ')+/u', '', $value);
        }

        /**
         * Convert the given string to upper-case.
         *
         * @param string $value
         * @return string
         */
        public static function upper($value)
        {
            return mb_strtoupper($value, 'UTF-8');
        }

        /**
         * Convert the given string to title case.
         *
         * @param string $value
         * @return string
         */
        public static function title($value)
        {
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        }

        /**
         * Convert a string to snake case.
         *
         * @param string $value
         * @param string $delimiter
         * @return string
         */
        public static function snake($value, $delimiter = '_')
        {
            $key = $value;
            if (isset(static::$snakeCache[$key][$delimiter])) {
                return static::$snakeCache[$key][$delimiter];
            }
            if (!ctype_lower($value)) {
                $value = preg_replace('/\\s+/u', '', ucwords($value));
                $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
            }
            return static::$snakeCache[$key][$delimiter] = $value;
        }

        /**
         * Convert a value to studly caps case.
         *
         * @param string $value
         * @return string
         */
        public static function studly($value)
        {
            $key = $value;
            if (isset(static::$studlyCache[$key])) {
                return static::$studlyCache[$key];
            }
            $value = ucwords(str_replace(['-', '_'], ' ', $value));
            return static::$studlyCache[$key] = str_replace(' ', '', $value);
        }

        /**
         * Returns the portion of string specified by the start and length parameters.
         *
         * @param string $string
         * @param int $start
         * @param int|null $length
         * @return string
         */
        public static function substr($string, $start, $length = null)
        {
            return mb_substr($string, $start, $length, 'UTF-8');
        }

        /**
         * Make a string's first character uppercase.
         *
         * @param string $string
         * @return string
         */
        public static function ucfirst($string)
        {
            return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
        }
    }

    class Arr
    {
        /**
         * Determine whether the given value is array accessible.
         *
         * @param mixed $value
         * @return bool
         */
        public static function accessible($value)
        {
            return is_array($value) || $value instanceof ArrayAccess;
        }

        /**
         * Add an element to an array using "dot" notation if it doesn't exist.
         *
         * @param array $array
         * @param string $key
         * @param mixed $value
         * @return array
         */
        public static function add($array, $key, $value)
        {
            if (is_null(static::get($array, $key))) {
                static::set($array, $key, $value);
            }
            return $array;
        }

        /**
         * Collapse an array of arrays into a single array.
         *
         * @param array $array
         * @return array
         */
        public static function collapse($array)
        {
            $results = [];
            foreach ($array as $values) {
                if ($values instanceof Collection) {
                    $values = $values->all();
                } elseif (!is_array($values)) {
                    continue;
                }
                $results = array_merge($results, $values);
            }
            return $results;
        }

        /**
         * Divide an array into two arrays. One with keys and the other with values.
         *
         * @param array $array
         * @return array
         */
        public static function divide($array)
        {
            return [array_keys($array), array_values($array)];
        }

        /**
         * Flatten a multi-dimensional associative array with dots.
         *
         * @param array $array
         * @param string $prepend
         * @return array
         */
        public static function dot($array, $prepend = '')
        {
            $results = [];
            foreach ($array as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
                } else {
                    $results[$prepend . $key] = $value;
                }
            }
            return $results;
        }

        /**
         * Get all of the given array except for a specified array of keys.
         *
         * @param array $array
         * @param array|string $keys
         * @return array
         */
        public static function except($array, $keys)
        {
            static::forget($array, $keys);
            return $array;
        }

        /**
         * Determine if the given key exists in the provided array.
         *
         * @param \ArrayAccess|array $array
         * @param string|int $key
         * @return bool
         */
        public static function exists($array, $key)
        {
            if ($array instanceof ArrayAccess) {
                return $array->offsetExists($key);
            }
            return array_key_exists($key, $array);
        }

        /**
         * Return the first element in an array passing a given truth test.
         *
         * @param array $array
         * @param callable|null $callback
         * @param mixed $default
         * @return mixed
         */
        public static function first($array, callable $callback = null, $default = null)
        {
            foreach ($array as $key => $value) {
                if (!is_callable($callback) || call_user_func($callback, $value, $key)) {
                    return $value;
                }
            }
            return value($default);
        }

        /**
         * Return the last element in an array passing a given truth test.
         *
         * @param array $array
         * @param callable|null $callback
         * @param mixed $default
         * @return mixed
         */
        public static function last($array, callable $callback = null, $default = null)
        {
            /*if (is_null($callback)) {
                  return empty($array) ? value($default) : end($array);
              }*/
            return is_null($callback) ? empty($array) ? value($default) : end($array) : static::first(array_reverse($array, true), $callback, $default);
        }

        /**
         * Flatten a multi-dimensional array into a single level.
         *
         * @param array $array
         * @param int $depth
         * @return array
         */
        public static function flatten($array, $depth = INF)
        {
            $result = [];
            foreach ($array as $item) {
                $item = $item instanceof Collection ? $item->all() : $item;
                if (!is_array($item)) {
                    $result[] = $item;
                } elseif ($depth === 1) {
                    $result = array_merge($result, array_values($item));
                } else {
                    $result = array_merge($result, static::flatten($item, $depth - 1));
                }
            }
            return $result;
        }

        /**
         * Remove one or many array items from a given array using "dot" notation.
         *
         * @param array $array
         * @param array|string $keys
         * @return void
         */
        public static function forget(&$array, $keys)
        {
            $original =& $array;
            $keys = (array)$keys;
            if (count($keys) === 0) {
                return;
            }
            foreach ($keys as $key) {
                // if the exact key exists in the top-level, remove it
                if (static::exists($array, $key)) {
                    unset($array[$key]);
                    continue;
                }
                $parts = explode('.', $key);
                // clean up before each pass
                $array =& $original;
                while (count($parts) > 1) {
                    $part = array_shift($parts);
                    if (isset($array[$part]) && is_array($array[$part])) {
                        $array =& $array[$part];
                    } else {
                        continue 2;
                    }
                }
                unset($array[array_shift($parts)]);
            }
        }

        /**
         * Get an item from an array using "dot" notation.
         *
         * @param \ArrayAccess|array $array
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public static function get($array, $key, $default = null)
        {
            if (!static::accessible($array)) {
                return value($default);
            }
            if (is_null($key)) {
                return $array;
            }
            if (static::exists($array, $key)) {
                return $array[$key];
            }
            if (strpos($key, '.') === false) {
                return isset($array[$key]) ? $array[$key] : value($default);
            }
            foreach (explode('.', $key) as $segment) {
                if (static::accessible($array) && static::exists($array, $segment)) {
                    $array = $array[$segment];
                } else {
                    return value($default);
                }
            }
            return $array;
        }

        /**
         * Check if an item or items exist in an array using "dot" notation.
         *
         * @param \ArrayAccess|array $array
         * @param string|array $keys
         * @return bool
         */
        public static function has($array, $keys)
        {
            $keys = (array)$keys;
            if (!$array || $keys === []) {
                return false;
            }
            foreach ($keys as $key) {
                $subKeyArray = $array;
                if (static::exists($array, $key)) {
                    continue;
                }
                foreach (explode('.', $key) as $segment) {
                    if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                        $subKeyArray = $subKeyArray[$segment];
                    } else {
                        return false;
                    }
                }
            }
            return true;
        }

        /**
         * Determines if an array is associative.
         *
         * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
         *
         * @param array $array
         * @return bool
         */
        public static function isAssoc(array $array)
        {
            $keys = array_keys($array);
            return array_keys($keys) !== $keys;
        }

        /**
         * Get a subset of the items from the given array.
         *
         * @param array $array
         * @param array|string $keys
         * @return array
         */
        public static function only($array, $keys)
        {
            return array_intersect_key($array, array_flip((array)$keys));
        }

        /**
         * Pluck an array of values from an array.
         *
         * @param array $array
         * @param string|array $value
         * @param string|array|null $key
         * @return array
         */
        public static function pluck($array, $value, $key = null)
        {
            $results = [];
            list($value, $key) = static::explodePluckParameters($value, $key);
            foreach ($array as $item) {
                $itemValue = data_get($item, $value);
                // If the key is "null", we will just append the value to the array and keep
                // looping. Otherwise we will key the array using the value of the key we
                // received from the developer. Then we'll return the final array form.
                if (is_null($key)) {
                    $results[] = $itemValue;
                } else {
                    $itemKey = data_get($item, $key);
                    if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                        $itemKey = (string)$itemKey;
                    }
                    $results[$itemKey] = $itemValue;
                }
            }
            return $results;
        }

        /**
         * Explode the "value" and "key" arguments passed to "pluck".
         *
         * @param string|array $value
         * @param string|array|null $key
         * @return array
         */
        protected static function explodePluckParameters($value, $key)
        {
            $value = is_string($value) ? explode('.', $value) : $value;
            $key = is_null($key) || is_array($key) ? $key : explode('.', $key);
            return [$value, $key];
        }

        /**
         * Push an item onto the beginning of an array.
         *
         * @param array $array
         * @param mixed $value
         * @param mixed $key
         * @return array
         */
        public static function prepend($array, $value, $key = null)
        {
            if (is_null($key)) {
                array_unshift($array, $value);
            } else {
                $array = [$key => $value] + $array;
            }
            return $array;
        }

        /**
         * Get a value from the array, and remove it.
         *
         * @param array $array
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public static function pull(&$array, $key, $default = null)
        {
            $value = static::get($array, $key, $default);
            static::forget($array, $key);
            return $value;
        }

        /**
         * Get one or a specified number of random values from an array.
         *
         * @param array $array
         * @param int|null $number
         * @return mixed
         *
         * @throws \InvalidArgumentException
         */
        public static function random($array, $number = null)
        {
            $requested = is_null($number) ? 1 : $number;
            $count = count($array);
            if ($requested > $count) {
                throw new InvalidArgumentException("You requested {$requested} items, but there are only {$count} items available.");
            }
            if (is_null($number)) {
                return $array[array_rand($array)];
            }
            if ((int)$number === 0) {
                return [];
            }
            $keys = array_rand($array, $number);
            $results = [];
            foreach ((array)$keys as $key) {
                $results[] = $array[$key];
            }
            return $results;
        }

        /**
         * Set an array item to a given value using "dot" notation.
         *
         * If no key is given to the method, the entire array will be replaced.
         *
         * @param array $array
         * @param string $key
         * @param mixed $value
         * @return array
         */
        public static function set(&$array, $key, $value)
        {
            if (is_null($key)) {
                return $array = $value;
            }
            $keys = explode('.', $key);
            while (count($keys) > 1) {
                $key = array_shift($keys);
                // If the key doesn't exist at this depth, we will just create an empty array
                // to hold the next value, allowing us to create the arrays to hold final
                // values at the correct depth. Then we'll keep digging into the array.
                if (!isset($array[$key]) || !is_array($array[$key])) {
                    $array[$key] = [];
                }
                $array =& $array[$key];
            }
            $array[array_shift($keys)] = $value;
            return $array;
        }

        /**
         * Shuffle the given array and return the result.
         *
         * @param array $array
         * @param int|null $seed
         * @return array
         */
        public static function shuffle($array, $seed = null)
        {
            if (is_null($seed)) {
                shuffle($array);
            } else {
                mt_srand($seed);
                shuffle($array);
                mt_srand();
            }
            return $array;
        }

        /**
         * Sort the array using the given callback or "dot" notation.
         *
         * @param array $array
         * @param callable|string|null $callback
         * @return array
         */
        public static function sort($array, $callback = null)
        {
            return Collection::make($array)->sortBy($callback)->all();
        }

        /**
         * Recursively sort an array by keys and values.
         *
         * @param array $array
         * @return array
         */
        public static function sortRecursive($array)
        {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = static::sortRecursive($value);
                }
            }
            if (static::isAssoc($array)) {
                ksort($array);
            } else {
                sort($array);
            }
            return $array;
        }

        /**
         * Convert the array into a query string.
         *
         * @param array $array
         * @return string
         */
        public static function query($array)
        {
            return http_build_query($array, null, '&', PHP_QUERY_RFC3986);
        }

        /**
         * Filter the array using the given callback.
         *
         * @param array $array
         * @param callable $callback
         * @return array
         */
        public static function where($array, callable $callback)
        {
            return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
        }

        /**
         * If the given value is not an array and not null, wrap it in one.
         *
         * @param mixed $value
         * @return array
         */
        public static function wrap($value)
        {
            if (is_null($value)) {
                return [];
            }
            return is_array($value) ? $value : [$value];
        }
    }

    /**
     * Class Collection
     * @package Dissonance\Support
     * @see  https://laravel.com/docs/5.8/collections
     */
    class Collection implements \ArrayAccess, ArrayableInterface, JsonableInterface, \Countable, \IteratorAggregate, \JsonSerializable
    {
        use CollectionTrait;
    }
}

namespace Dissonance {

    use Dissonance\Support\Collection;
    use Dissonance\Bootstrap\BootBootstrap;
    use Dissonance\Container\DIContainerInterface;
    use Dissonance\Container\Traits\ArrayAccessTrait;
    use Dissonance\Container\Traits\SingletonTrait;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Bootstrap\CoreBootstrap;
    use Dissonance\Contracts\RunnerInterface;
    use Dissonance\Bootstrap\ProvidersBootstrap;
    use Closure;
    use Dissonance\Container\Container;
    use Dissonance\Container\ServiceContainerTrait;
    use Psr\SimpleCache\CacheInterface;
    use const _DS\DS;

    /**
     *
     * ПОЛНЫЙ ГОВНО КОД, но работает )) подделка автолоадера композера(made in china)
     * Class Autoloader
     * @package Dissonance
     */
    class Autoloader
    {
        protected static $namespace = 'Dissonance';
        private static $registered = false;
        protected static $packages_dirs = [];
        protected static $strorage_path = null;
        protected static $registered_namespaces = [];
        protected static $files = [];
        protected static $classes = [];

        public static function register($prepend = false, array $scan_dirs = null, $storage_path = null)
        {
            if ($storage_path) {
                self::$strorage_path = rtrim($storage_path, '/\\');
            }
            if (self::$registered === true) {
                return;
            }
            self::$packages_dirs = $scan_dirs ? array_map(function ($v) {
                return rtrim($v, '\\/');
            }, $scan_dirs) : [];
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
            self::registerNamespaces();
            self::$registered = true;
        }

        /**
         * @return bool
         */
        public static function registerNamespaces()
        {
            $file = self::$strorage_path ? self::$strorage_path . '/autoload.dump.php' : null;
            if (file_exists($file)) {
                $data = (include $file);
                static::$registered_namespaces = $data['namespaces'];
                static::$classes = $data['classes'];
                self::requireFiles($data['files']);
                return;
            }
            foreach (self::$packages_dirs as $dirname) {
                if (is_dir($dirname) && @is_readable($dirname)) {
                    static::loadPackages($dirname);
                }
            }
            uksort(static::$registered_namespaces, function ($a, $b) {
                $ex_a = count(explode('\\', trim($a, '/\\')));
                $ex_b = count(explode('\\', trim($b, '/\\')));
                if ($ex_a > $ex_b) {
                    return -1;
                } elseif ($ex_a < $ex_b) {
                    return 1;
                }
                return 0;
            });
            if ($file) {
                if (!is_dir(self::$strorage_path)) {
                    \mkdir(self::$strorage_path, 0777, true);
                }
                file_put_contents($file, '<?php ' . PHP_EOL . 'return ' . var_export(['namespaces' => static::$registered_namespaces, 'classes' => static::$classes, 'files' => static::$files], true) . ';');
            }
        }

        protected static function loadPackages($dir)
        {
            /*foreach(new \DirectoryIterator($dir) as $fileInfo) {*/
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF));
            $iterator->setMaxDepth(2);
            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isDir()) {
                    continue;
                }
                $composer_file = $fileInfo->getRealPath() . '/composer.json';
                if (file_exists($composer_file) && is_readable($composer_file)) {
                    $loader = self::getComposerLoader($fileInfo->getRealPath() . '/composer.json', $fileInfo->getRealPath());
                    if (!empty($loader)) {
                        // files load now!
                        if (!empty($loader['files'])) {
                            static::$files = array_merge(static::$files, $loader['files']);
                            self::requireFiles($loader['files']);
                        }
                        if (!empty($loader['namespaces'])) {
                            foreach ($loader['namespaces'] as $namespace => $data) {
                                $namespace = trim($namespace, '\\') . '\\';
                                if (array_key_exists($namespace, self::$registered_namespaces)) {
                                    $data['root_dir'] = array_merge(self::$registered_namespaces[$namespace]['root_dir'], $data['root_dir']);
                                }
                                self::$registered_namespaces[$namespace] = $data;
                                foreach ($data['root_dir'] as $d) {
                                    $directory = new \RecursiveDirectoryIterator($d);
                                    $iterator = new \RecursiveIteratorIterator($directory);
                                    $regex = new \RegexIterator($iterator, '/\\.php$/i');
                                    foreach ($regex as $file) {
                                        $path = $file->getRealPath();
                                        $class_path = str_replace($d, '', $path);
                                        $key = $namespace . trim(str_replace('.php', '', $class_path), '\\/');
                                        static::$classes[$key] = $path;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        protected static function requireFiles($files)
        {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }

        protected static function getComposerLoader($file, $base_dir)
        {
            $loader = [];
            if (file_exists($file) && is_readable($file)) {
                $data = \json_decode(file_get_contents($file), true);
                if (is_array($data)) {
                    $loader['namespaces'] = [];
                    $loader['files'] = [];
                    $get_autoloads = function ($base_dir, $autoload, array &$loader) {
                        if (isset($autoload['psr-4']) && is_array($autoload['psr-4'])) {
                            foreach ($autoload['psr-4'] as $namespace => $dir) {
                                $namespace = rtrim($namespace, '\\');
                                $loader['namespaces'][$namespace] = ['namespace' => $namespace, 'root_dir' => [$base_dir . DIRECTORY_SEPARATOR . trim($dir, '\\/')]];
                            }
                        }
                        if (isset($autoload['files']) && is_array($autoload['files'])) {
                            foreach ($autoload['files'] as $v) {
                                $loader['files'][] = $base_dir . DIRECTORY_SEPARATOR . ltrim($v, '\\/');
                            }
                        }
                    };
                    if (isset($data['autoload'])) {
                        $get_autoloads($base_dir, $data['autoload'], $loader);
                    }
                    /* if (self::$env === 'dev') {
                           if (isset($data['autoload-dev'])) {
                               $get_autoloads($base_dir, $data['autoload-dev'], $loader);
                           }
                       }*/
                }
            }
            return $loader;
        }

        public static function autoload($class)
        {
            if (isset(static::$classes[$class])) {
                return static::requireFile(static::$classes[$class]);
            }
            static::search($class);
        }

        protected static function search($class)
        {
            foreach (self::$registered_namespaces as $namespace => $data) {
                if (strpos($class, $namespace) === 0) {
                    $name = substr($class, strlen($namespace));
                    foreach ($data['root_dir'] as $root_dir) {
                        if (preg_match('/\\\\Tests\\\\$/i', $namespace)) {
                            $root_dir = rtrim($root_dir . '/' . $namespace, '\\/');
                            // $name =  $class;
                        }
                        $fileName = strtr($root_dir . '/' . ltrim($name, '\\/'), '\\', '/') . '.php';
                        if (static::requireFile($fileName, false)) {
                            return;
                        } else {
                            $data = $fileName;
                        }
                    }
                }
            }
        }

        private static function requireFile($file, $throw = false)
        {
            if (file_exists($file)) {
                return require_once $file;
            } elseif ($throw) {
                debug_print_backtrace(1, 5);
                echo 'File not found' . $file;
                var_dump($file);
            }
            return false;
        }
    }

    abstract class Runner implements RunnerInterface
    {
        /**
         * @var CoreInterface
         */
        protected $app;

        public function __construct(CoreInterface $container)
        {
            $this->app = $container;
        }
    }

    /**
     * Class Core
     * @package Dissonance/Core
     */
    class Core extends Container implements CoreInterface
    {
        use ServiceContainerTrait, ArrayAccessTrait, SingletonTrait;

        /**
         * Class names Runners {@see \Dissonance\Contracts\Runner}
         * @var string[]
         */
        protected $runners = [];
        /**
         * @var string|null
         */
        protected $base_path;
        /**
         * The bootstrap classes for the application.
         *
         * @var array
         */
        protected $bootstraps = [];
        /**
         * The bootstrap classes for the application.
         *
         * @var array
         */
        protected $last_bootstraps = [ProvidersBootstrap::class, BootBootstrap::class];
        /**
         * Массив ключей разрешенных сервисов для кеширования
         * @var array|string[]
         */
        protected $allow_cached = [];

        public function __construct(array $config = [])
        {
            $this->dependencyInjectionContainer = static::$instance = $this;
            $this->instance(DIContainerInterface::class, $this);
            $this->instance(CoreInterface::class, $this);
            $this->instance('bootstrap_config', $config);
            $this->base_path = rtrim(isset($config['base_path']) ? $config['base_path'] : __DIR__, '\\/');
            $this->runBootstrap(CoreBootstrap::class);
        }

        /**
         * @param string| string[] $bootstraps
         */
        public function addBootstraps( $bootstraps): void
        {
            foreach ((array)$bootstraps as $v) {
                $this->bootstraps[] = $v;
            }
        }

        /**
         * Determine if the application has booted.
         *
         * @return bool
         */
        public function isBooted(): bool
        {
            return $this->booted;
        }

        public function bootstrap(): void
        {
            if (!$this->isBooted()) {
                foreach ($this->bootstraps + $this->last_bootstraps as $class) {
                    $this->runBootstrap($class);
                }
            }
            $this->booted = true;
        }

        public function runBootstrap($class): void
        {
            if (class_exists($class)) {
                (new $class())->bootstrap($this);
            }
        }

        public function addRunner(RunnerInterface $runner): void
        {
            $this->runners[] = $runner;
        }

        public function run(): void
        {
            foreach ($this->runners as $runner) {
                /**
                 * @var \Dissonance\Contracts\RunnerInterface $runner
                 */
                $runner = new $runner($this);
                if ($runner->isHandle()) {
                    $runner->run();
                    break;
                }
            }
        }

        /**
         * Get the base path of the Laravel installation.
         *
         * @param string $path Optionally, a path to append to the base path
         * @return string
         *
         * @todo: Метод используется один раз, нужен ли он?
         */
        public function getBasePath($path = '')
        {
            return $this->base_path . ($path ? DS . $path : $path);
        }
    }

    class Config extends Collection
    {
    }
}

namespace Dissonance\CachedContainer {

    use Dissonance\Container\Container;
    use Dissonance\Container\CachedContainerInterface;
    use Psr\SimpleCache\CacheInterface;
    use Dissonance\Core;

    /**
     * Trait CacheContainerTrait
     * @package Dissonance\CachedContainer
     */
    trait CachedContainerTrait
    {
        /**
         * @var null|CacheInterface
         */
        protected $container_cache = null;
        protected $container_key = '';
        /**
         * Массив ключей разрешенных сервисов для кеширования
         * @var array|string[]
         */
        protected $allow_cached = [];

        /**
         * set cache storage
         * @param CacheInterface $cache
         * @param string $key
         */
        public function setCache(CacheInterface $cache, string $key)
        {
            $this->container_cache = $cache;
            $this->container_key = $key;
        }

        /**
         * Разрешает кеширование сервиса в контейнере
         *
         * Если есть сервис кеша {@see \Psr\SimpleCache\CacheInterface} в контейнере , то указанный ключ будет добавлен для кешироваиня
         *
         * @param string $abstract - ключ сервиса для кеширования
         *
         */
        public function cached(string $abstract): void
        {
            $this->allow_cached[$abstract] = 1;
        }

        /**
         * @return string
         *
         * @see \Serializable::serialize()
         */
        public function serialize()
        {
            return \serialize($this->getSerializeData());
        }

        protected function getSerializeData(): array
        {
            /**
             * @var \Dissonance\Container\ContainerTrait|\Dissonance\Container\SubContainerTrait| $this
             */
            $data = ['cache' => $this->container_cache, 'key' => $this->container_key];
            $instances = [];
            foreach ($this->allow_cached as $k => $v) {
                if (isset($this->instances[$k])) {
                    $instances[$k] = $this->instances[$k];
                }
            }
            $data['instances'] = $instances;
            return $data;
        }

        /**
         * @param $serialized
         *
         * @see \Serializable::unserialize()
         */
        public function unserialize($serialized)
        {
            /**
             * @var \Dissonance\Container\ContainerTrait|\Dissonance\Container\SubContainerTrait| $this
             */
            $data = \unserialize($serialized, ['allowed_classes' => true]);
            $this->container_cache = $data['cache'];
            $this->container_key = $data['key'];
            foreach ($data['instances'] as $k => $instance) {
                $this->instances[$k] = $instance;
                $this->resolved[$k] = true;
            }
            $this->unserialized($data);
        }

        protected function unserialized(array $data)
        {
        }

        public function __destruct()
        {
            /**
             * @var \Dissonance\Container\ContainerTrait|\Dissonance\Container\SubContainerTrait| $this
             */
            if ($this->container_cache) {
                // $this->container_cache->has($this->container_key);
                if (!$this->container_cache->has($this->container_key) && !$this->has('cache_cleaned')) {
                    $this->container_cache->set($this->container_key, $this, 60 * 60);
                }
            }
        }
    }

    class ContainerBuilder
    {
        /**
         * @var CacheInterface |null
         */
        protected $cache;

        public function __construct(CacheInterface $cache = null)
        {
            $this->cache = $cache;
        }

        public function buildCore(array $config, string $key = null)
        {
            $key = $key ?: \md5(__FILE__ . CachedCore::class);
            $time = microtime();
            if ($this->cache) {
                $data = $this->cache->get($key, $time);
                if ($data === $time) {
                    $core = new CachedCore($config);
                    $core->setCache($this->cache, $key);
                    return $core;
                }
                return $data;
            }
            return new Core($config);
        }
    }

    class CachedCore extends Core implements \Dissonance\Container\CachedContainerInterface
    {
        use CachedContainerTrait {
            CachedContainerTrait::unserialized as traitUnserialized;
            CachedContainerTrait::getSerializeData as traitGetSerializeData;
        }

        public function getSerializeData(): array
        {
            $data = $this->traitGetSerializeData();
            $data['config'] = $this['bootstrap_config'];
            return $data;
        }

        protected function unserialized(array $data)
        {
            $this->__construct($data['config']);
        }
    }

    class CachedContainer extends Container implements CachedContainerInterface
    {
        use CachedContainerTrait;
    }
}

namespace Dissonance\Contracts\App {

    use Dissonance\Container\ArrayContainerInterface;
    use Dissonance\Container\DIContainerInterface;
    use Dissonance\Container\ServiceContainerInterface;

    /**
     * Interface AppConfigInterface
     *
     * @package Dissonance\Contracts\Apps
     */
    interface AppConfigInterface extends ArrayContainerInterface
    {
    }

    /**
     * Interface Module
     * @package Dissonance\Contracts\Application
     * @property \Dissonance\Core|ApplicationInterface $app
     *
     */
    interface ApplicationInterface extends AppConfigInterface, DIContainerInterface, ServiceContainerInterface
    {
    }
}

namespace Dissonance\Contracts\Http {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    interface HttpKernelInterface extends RequestHandlerInterface
    {
    }
}

namespace Dissonance\Contracts\Session {

    use Dissonance\Container\ArrayContainerInterface;

    interface SessionStorageInterface extends ArrayContainerInterface
    {
    }
}


namespace Dissonance\App {

    use Dissonance\Container\Traits\ArrayAccessTrait;
    use Dissonance\Container\Traits\BaseContainerTrait;
    use Dissonance\Contracts\App\AppConfigInterface;
    use Dissonance\Contracts\App\ResourcesInterface;
    use Dissonance\Contracts\App\ApplicationInterface;
    use Dissonance\Container\DIContainerInterface;
    use Dissonance\Container\SubContainerTrait;
    use Dissonance\Container\ServiceContainerTrait;

    /**
     * Class Application
     * @package Dissonance\App
     * @property AppConfigInterface $config
     * @property $this = [
     *     'config' => new AppConfig()
     * ]
     */
    class Application implements ApplicationInterface
    {
        use ServiceContainerTrait, SubContainerTrait;

        /**
         * @var DIContainerInterface|null
         */
        protected $app = null;

        public function __construct(DIContainerInterface $app, AppConfigInterface $config = null)
        {
            $this->app = $app;
            $this->instance(AppConfigInterface::class, $config, 'config');
            $config_class = get_class($config);
            if ($config_class !== AppConfig::class) {
                $this->alias(AppConfigInterface::class, AppConfig::class);
            }
            $class = get_class($this);
            $this->dependencyInjectionContainer = $this;
            $this->instance($class, $this);
            $this->alias($class, ApplicationInterface::class);
            if ($class !== self::class) {
                $this->alias($class, self::class);
            }
        }

        public function getId(): string
        {
            return $this['config']->getId();
        }

        public function getAppName(): string
        {
            return $this['config']->getAppName();
        }

        public function getAppTitle(): string
        {
            return $this['config']->getAppName();
        }

        public function getRoutingProvider(): ?string
        {
            return $this['config']->getRoutingProvider();
        }

        public function hasParentApp(): bool
        {
            return $this['config']->hasParentApp();
        }

        public function getParentAppId(): ?string
        {
            return $this['config']->getParentAppId();
        }

        protected function getBootstrapCallback()
        {
            return function () {
                $this->registerProviders();
                $this->boot();
            };
        }

        /**
         * @param array|\Closure[]|null $bootstraps Подмодуль может передать свой загрузчик для правильной
         * последовательности загрузки зависимостей
         *
         */
        public function bootstrap(array $bootstraps = null): void
        {
            if (!is_array($bootstraps)) {
                $bootstraps = [];
            }
            if (!$this->booted) {
                $bootstraps[] = $this->getBootstrapCallback();
            }
            // Если есть родительский модуль передаем свой загрузчик
            if (!$this->booted && ($parent_app = $this->getParentApp())) {
                $parent_app->bootstrap($bootstraps);
            } else {
                // Запускаем загрузку , начиная от самого корневого модуля
                foreach (array_reverse($bootstraps) as $boot) {
                    $boot();
                }
            }
            $this->booted = true;
        }

        protected function registerProviders()
        {
            $providers = $this('config::providers', []);
            foreach ($providers as $provider) {
                $this->register($provider);
            }
        }

        /**
         * @param string|null $path
         * @return string|null
         */
        public function getBasePath(string $path = null): ?string
        {
            $base = $this->get('config::base_path');
            return $base ? $path ? $base . \_DS\DS . ltrim($path) : $base : null;
        }

        /**
         * @return string|null
         * @deprecated
         */
        public function getAssetsPath(): ?string
        {
            return $this->getBasePath('assets');
        }

        /**
         * @return string|null
         * @deprecated
         */
        public function getResourcesPath(): ?string
        {
            return $this->getBasePath('resources');
        }

        /**
         * @return ApplicationInterface|null
         */
        protected function getParentApp(): ?ApplicationInterface
        {
            return $this->hasParentApp() ? $this->app['apps']->get($this->getParentAppId()) : null;
        }
    }

    class AppConfig implements AppConfigInterface
    {
        use ArrayAccessTrait, BaseContainerTrait;

        protected $items = [];
        /**
         * @var string
         */
        protected $id = null;

        public function __construct(array $config)
        {
            $this->id = isset($config['id']) ? $config['id'] : null;
            $this->items = $config;
        }

        protected function &getContainerItems()
        {
            return $this->items;
        }

        /**
         * @return string
         */
        public function getId(): string
        {
            if (is_null($this->id)) {
                $alias = $this->getAppName();
                $parent = $this->getParentAppId();
                $this->id = $parent ? $parent . '@' . $alias : $alias;
            }
            return $this->id;
        }

        /**
         * @return string
         */
        public function getAppName(): string
        {
            return $this->get('name');
        }

        /**
         * @return string
         */
        public function getAppTitle(): string
        {
            return $this->get('name');
        }

        /**
         * @return string|null
         */
        public function getRoutingProvider(): ?string
        {
            return $this->get('routing');
        }

        /**
         * @return bool
         */
        public function hasParentApp(): bool
        {
            return $this->has('parent_app');
        }

        /**
         * @return string|null
         */
        public function getParentAppId(): ?string
        {
            return $this->get('parent_app');
        }
    }
}

namespace Dissonance\Bootstrap {

    use Dissonance\Container\CachedContainerInterface;
    use Dissonance\Event\ListenerProvider;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Config;
    use Dissonance\Events\CacheClear;
    use Dissonance\View\View;
    use Dissonance\Event\ListenersInterface;
    use Dissonance\Event\EventDispatcher;
    use Psr\EventDispatcher\ListenerProviderInterface;
    use Dissonance\Contracts\BootstrapInterface;
    use Psr\EventDispatcher\EventDispatcherInterface;
    use Dissonance\Packages\Contracts\PackagesRepositoryInterface;
    use Dissonance\Providers\ProvidersRepository;
    use function _DS\config;

    class ProvidersBootstrap
    {
        /**
         * @param \Dissonance\Core|\Dissonance\Container\ServiceContainerInterface $app
         */
        public function bootstrap($app)
        {
            $app->singleton(ProvidersRepository::class);
            $providers_class = \Dissonance\Providers\ProvidersRepository::class;
            if ($app instanceof CachedContainerInterface) {
                $app->cached($providers_class);
            }
            if (!$app->bound($providers_class)) {
                $app->singleton($providers_class);
            }
            /**
             * @var \Dissonance\Providers\ProvidersRepository $providers_repository
             */
            $providers_repository = $app[$providers_class];
            $providers_repository->load($app, config('providers', []), config('providers_exclude', []));
        }
    }

    class BootBootstrap
    {
        /**
         * @param \Dissonance\Container\ServiceContainerInterface|\Dissonance\Core $app
         */
        public function bootstrap($app)
        {
            $app->boot();
        }
    }

    class EventBootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            // Events listeners
            $listener_interface = ListenerProviderInterface::class;
            $app->singleton($listener_interface, function ($app) {
                return new ListenerProvider(function ($listener) use ($app) {
                    return function (object $event) use ($listener, $app) {
                        if (is_string($listener) && class_exists($listener, false)) {
                            $handler = $app->make($listener);
                            if (method_exists($handler, 'handle') || is_callable($handler)) {
                                return $app->call([$handler, method_exists($handler, 'handle') ? 'handle' : '__invoke'], ['event' => $event]);
                            }
                            return null;
                        } elseif ($listener instanceof \Closure) {
                            return $app->call($listener, ['event' => $event]);
                        }
                    };
                });
            }, 'listeners')->alias($listener_interface, ListenersInterface::class);
            // Events dispatcher
            $app->singleton(EventDispatcherInterface::class, EventDispatcher::class, 'events');
        }
    }

    class CoreBootstrap implements BootstrapInterface
    {
        /**
         * @param CoreInterface $app
         */
        public function bootstrap(CoreInterface $app): void
        {
            // Config
            // $app = $core[CoreInterface::class];
            $app->singleton(Config::class, function ($app) {
                return new Config($app['bootstrap_config']);
            }, 'config');
            // Providers repository
            View::setContainer($app);
            // Env settings
            $console_running_key = 'APP_RUNNING_IN_CONSOLE';
            if (isset($_ENV[$console_running_key]) && $_ENV[$console_running_key] === 'true' || \in_array(\php_sapi_name(), ['cli', 'phpdbg'])) {
                $app['env'] = 'console';
            } else {
                $app['env'] = 'web';
            }
            \date_default_timezone_set($app('config::core.timezone', 'UTC'));
            \mb_internal_encoding('UTF-8');
            $start_bootstrappers = $app->get('config::bootstrappers');
            if (\is_array($start_bootstrappers)) {
                foreach ($start_bootstrappers as $class) {
                    $app->runBootstrap($class);
                }
            }
            $app['listeners']->add(CacheClear::class, function ($event) use ($app) {
                $app['cache_cleaned'] = true;
            });
            if ($app instanceof CachedContainerInterface) {
                $app->cached('bootstrap_config');
            }
        }
    }
}


namespace Dissonance\View {

    use Dissonance\Contracts\Support\RenderableInterface;
    use Dissonance\App\Application;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Contracts\App\ApplicationInterface;
    use Dissonance\Contracts\Routing\RouteInterface;
    use Dissonance\Apps\AppsRepositoryInterface;
    use Dissonance\Packages\Contracts\TemplatesRepositoryInterface;
    use Dissonance\Support\Str;

    /**
     * Без параметров возвращает текущий модуль {@see ApplicationInterface}
     *  или необходимый параметр из контейнера текущего приложения
     *
     * @param null $abstract
     * @param array $parameters
     * @return ApplicationInterface|Application| mixed
     *
     */
    function app($abstract = null, array $parameters = [])
    {
        $container = View::getCurrentContainer();
        if (is_null($abstract)) {
            return $container;
        }
        return $container->make($abstract, $parameters);
    }

    /**
     * @param string $path
     * @param bool $absolute
     * @return string Uri файла приложения
     */
    function asset($path = '', $absolute = true)
    {
        if (!is_array(Str::sc($path))) {
            /* @throws \Exception Если нет текущего пакета в view */
            $path = View::getCurrentPackageId() . '::' . ltrim($path, '\\/');
        }
        return \_DS\app('url')->asset($path, $absolute);
    }

    function route($name, $parameters = [], $absolute = true)
    {
        if (!is_array(Str::sc($name))) {
            /* @throws \Exception Если нет текущего пакета в view */
            $name = View::getCurrentPackageId() . '::' . $name;
        }
        return \_DS\app('url')->route($name, $parameters, $absolute);
    }

    /**
     * @param string $path
     * @param bool $absolute
     * @return string html style
     */
    function css($path = '', $absolute = true)
    {
        return '<link rel="stylesheet" href="' . asset($path, $absolute) . '">';
    }

    function js($path = '', $absolute = true)
    {
        return '<script type="text/javascript" src="' . asset($path, $absolute) . '"></script>';
    }

    class Section
    {
        /**
         * All of the captured sections.
         *
         * @var array
         */
        public $sections = array();
        /**
         * The last section on which injection was started.
         *
         * @var array
         */
        public $last = array();

        /**
         * Start injecting content into a section.
         *
         * <code>
         *        // Start injecting into the "header" section
         *        Section::start('header');
         *
         *        // Inject a raw string into the "header" section without buffering
         *        Section::start('header', '<title>Dissonance php</title>');
         * </code>
         *
         * @param string $section
         * @param string|\Closure $content
         * @return void
         */
        public function start($section, $content = null)
        {
            if ($content === null) {
                ob_start() and $this->last[] = $section;
            } else {
                $this->extend($section, $content);
            }
        }

        /**
         * Inject inline content into a section.
         *
         * This is helpful for injecting simple strings such as page titles.
         *
         * <code>
         *        // Inject inline content into the "header" section
         *        Section::inject('header', '<title>Laravel</title>');
         * </code>
         *
         * @param string $section
         * @param string $content
         * @return void
         */
        public function inject($section, $content)
        {
            $this->start($section, $content);
        }

        /**
         * Stop injecting content into a section and return its contents.
         *
         * @return string
         */
        public function yield_section()
        {
            return $this->yield($this->stop());
        }

        /**
         * Stop injecting content into a section.
         *
         * @return string
         */
        public function stop()
        {
            $this->extend($last = array_pop($this->last), ob_get_clean());
            return $last;
        }

        /**
         * Extend the content in a given section.
         *
         * @param string $section
         * @param string $content
         * @return void
         */
        protected function extend($section, $content)
        {
            if (isset($this->sections[$section])) {
                $this->sections[$section] = $content instanceof View ? function () use ($content) {
                    $content->render();
                } : str_replace('@parent', $content, $this->sections[$section]);
            } else {
                $this->sections[$section] = $content;
            }
        }

        /**
         * Append content to a given section.
         *
         * @param string $section
         * @param string $content
         * @return void
         */
        public function append($section, $content)
        {
            if (isset($this->sections[$section])) {
                $this->sections[$section] .= $content;
            } else {
                $this->sections[$section] = $content;
            }
        }

        /**
         * Get the string contents of a section.
         *
         * @param string $section
         * @return string
         */
        public function yield($section)
        {
            if (isset($this->sections[$section])) {
                $section = $this->sections[$section];
                if (is_callable($section)) {
                    $section();
                } elseif ($section instanceof RenderableInterface) {
                    $section->render();
                } else {
                    echo $section;
                }
            }
        }
    }

    /**
     * Class View
     *
     * @package Dissonance\View
     *
     */
    class View implements RenderableInterface
    {
        /**
         * @var CoreInterface | array $core = [
         *       'config' => new \Dissonance\Config(),
         *       'router' => new \Dissonance\Contracts\Routing\Router(),
         *       'apps' => new \Dissonance\Contracts\Apps\AppsRepository()
         *
         * ]
         * @used-by View::setContainer()
         * @see     CoreProvider::boot()
         */
        protected static $core;
        /**
         * @var array
         * @used-by setContainer() -  в методе очищается переменная и устанавливается последний
         * @used-by View::render() -  в методе устанавливается последним текущий модуль и потом удаляется
         */
        protected static $current_container = [];
        protected $template = '';
        protected $vars = [];
        /**
         * @see ApplicationInterface::getId()
         * @var null|string
         */
        protected $app_id;
        /**
         * All of the captured sections.
         *
         * @var array
         */
        public $sections = [];
        /**
         * The last section on which injection was started.
         *
         * @var array
         */
        public $last = [];

        public function __construct(string $path, array $vars = [], $app_id = null)
        {
            $this->vars = $vars;
            // Template find
            $id = null;
            if (is_string($app_id)) {
                $id = $app_id;
            } else {
                if (is_array($sc = Str::sc($path))) {
                    $id = $sc[0];
                    $path = $sc[1];
                } else {
                    /**
                     * @var RouteInterface | null $route
                     */
                    $route = static::$core->get('route');
                    if ($route && $route->getApp() !== null) {
                        $id = $route->getApp();
                    }
                }
            }
            $this->app_id = $id;
            $this->template = static::$core->get(TemplatesRepositoryInterface::class)->getTemplate($id, $path);
        }

        public function url($path = '', $absolute = true)
        {
            //todo: yf aeyrwbb gthtdtcnb
            return static::$core['url']->to($this->prepareModulePath($path), $absolute);
        }

        public function asset($path = '', $absolute = true)
        {
            return static::$core['url']->asset($this->prepareModulePath($path), $absolute);
        }

        public function route($path = '', $absolute = true)
        {
            return static::$core['url']->asset($this->prepareModulePath($path), $absolute);
        }

        protected function prepareModulePath($path)
        {
            if (!is_array(Str::sc($path))) {
                $path = $this->app_id . '::' . $path;
            }
            return $path;
        }

        public static function make($template, array $vars = [], $app_id = null)
        {
            return new static($template, $vars, $app_id);
        }

        /**
         * Start injecting content into a section.
         *
         * <code>
         *        // Start injecting into the "header" section
         *        Section::start('header');
         *
         *        // Inject a raw string into the "header" section without buffering
         *        Section::start('header', '<title>Laravel</title>');
         * </code>
         *
         * @param string $section
         * @param string|\Closure $content
         * @return void
         */
        public function start($section, $content = null)
        {
            if ($content === null) {
                ob_start() and $this->last[] = $section;
            } else {
                $this->extend($section, $content);
            }
        }

        /**
         * Inject inline content into a section.
         *
         * This is helpful for injecting simple strings such as page titles.
         *
         * <code>
         *        // Inject inline content into the "header" section
         *        Section::inject('header', '<title>Laravel</title>');
         * </code>
         *
         * @param string $section
         * @param string $content
         * @return void
         */
        public function inject($section, $content)
        {
            $this->start($section, $content);
        }

        /**
         * Stop injecting content into a section and return its contents.
         *
         * @return string
         */
        public function yield_section()
        {
            return $this->fetch($this->stop());
        }

        /**
         * Stop injecting content into a section.
         *
         * @return string
         */
        public function stop()
        {
            $this->extend($last = array_pop($this->last), ob_get_clean());
            return $last;
        }

        /**
         * Extend the content in a given section.
         *
         * @param string $section
         * @param string $content
         * @return void
         */
        protected function extend($section, $content)
        {
            if (isset($this->sections[$section])) {
                $this->sections[$section] = $content instanceof View ? function () use ($content) {
                    $content->render();
                } : str_replace('@parent', $content, $this->sections[$section]);
            } else {
                $this->sections[$section] = $content;
            }
        }

        /**
         * Append content to a given section.
         *
         * @param string $section
         * @param string $content
         * @return void
         */
        public function append($section, $content)
        {
            if (isset($this->sections[$section])) {
                $this->sections[$section] .= $content;
            } else {
                $this->sections[$section] = $content;
            }
        }

        /**
         * Get the string contents of a section.
         *
         * @param string $section
         * @return string
         */
        public function yield($section)
        {
            if (isset($this->sections[$section])) {
                $section = $this->sections[$section];
                if (is_callable($section)) {
                    $section();
                } elseif ($section instanceof View) {
                    $section->setSections($this->sections);
                    $section->render();
                } elseif ($section instanceof RenderableInterface) {
                    $section->render();
                } else {
                    echo (string)$section;
                }
            }
        }

        public function setSections($sections)
        {
            $this->sections = $sections;
        }

        /**
         * Специальный метод для передачи шаблона в слой
         *
         * @param string $template
         * @param $content_template
         * @param array $vars
         * @return static
         */
        public function layout(string $template, $content_template, $vars = [], $before = false)
        {
            $app_id = $this->app_id;
            if (is_array($sc = Str::sc($template))) {
                $app_id = $sc[0];
                $template = $sc[1];
            }
            $this->template = $content_template;
            $view = new static($template, $vars, $app_id);
            $content = $this;
            if ($before) {
                $content = $this->fetch($this);
                $view->setSections($this->sections);
            }
            $view->inject('content', $content);
            return $view;
        }


        /**
         * @param CoreInterface $app
         * @uses    View::$core
         * @used-by CoreProvider::boot()
         */
        public static function setContainer(CoreInterface $app)
        {
            static::$core = $app;
            static::$current_container = [$app];
        }

        public static function getCurrentPackageId()
        {
            $app = end(static::$current_container);
            if (is_string($app)) {
                return $app;
            }
            if ($app instanceof ApplicationInterface) {
                return $app->getId();
            }
            throw new \Exception('Container is not app!');
        }

        public static function getCurrentContainer()
        {
            $app = end(static::$current_container);
            // Загружаем контейнер приложения только по запросу (приложения поставляющие только шаблоны не имеют контейнера)
            if (is_string($app)) {
                if ($app === 'app' && static::$core->has('app')) {
                    $app = static::$core['app'];
                    $app->bootstrap();
                } elseif ($apps = static::$core['apps']) {
                    /**
                     * @var AppsRepositoryInterface $apps
                     */
                    if (!$apps->has($app)) {
                        throw new \Exception('Not exists App [' . $app . ']');
                    }
                    $app = $apps->getBootedApp($app);
                } else {
                    throw new \Exception('Not exists App [' . $app . ']');
                }
                array_pop(static::$current_container);
                static::$current_container[] = $app;
            }
            return $app;
        }

        public function with(array $vars)
        {
            $this->vars = $vars;
            return $this;
        }

        public function render()
        {
            static::$current_container[] = $this->app_id;
            if (!empty($this->template)) {
                ///echo $this->template;
                extract($this->vars);
                $__view = $this;
                try {
                    eval($this->getTemplate());
                } catch (\ParseError $e) {
                    throw new \Exception($e->getMessage() . PHP_EOL . $this->template, $e->getCode(), $e);
                }
            }
            array_pop(static::$current_container);
        }

        public function fetch($content)
        {
            ob_start();
            if (is_callable($content)) {
                $content();
            } elseif ($content instanceof RenderableInterface) {
                $content->render();
            } else {
                echo $content;
            }
            return ob_get_clean();
        }

        protected function getTemplate()
        {
            return 'use function ' . __NAMESPACE__ . '\\app; 
        use function ' . __NAMESPACE__ . '\\asset; 
        use function ' . __NAMESPACE__ . '\\route; 
        use function ' . __NAMESPACE__ . '\\css; 
        use function ' . __NAMESPACE__ . '\\js; 
        
        ?>' . $this->template;
        }

        public function __toString()
        {
            return $this->fetch($this);
        }
    }
}

namespace Dissonance\Event {

    use Psr\EventDispatcher\EventDispatcherInterface;
    use Psr\EventDispatcher\ListenerProviderInterface;
    use Psr\EventDispatcher\StoppableEventInterface;

    interface DispatcherInterface extends EventDispatcherInterface
    {
    }

    interface ListenersInterface extends ListenerProviderInterface
    {
    }

    class ListenerProvider implements ListenersInterface
    {
        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @var \Closure|null
         * @see \Dissonance\Bootstrap\EventBootstrap::bootstrap()
         */
        protected $listenerWrapper;
        protected $listeners = [];

        public function __construct(\Closure $listenerWrapper = null)
        {
            $this->listenerWrapper = $listenerWrapper;
        }

        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @param string $event class or interface name
         * @param string |\Closure $handler callback or string class name (if the wrapper is attached {@see ListenerProvider::$listenerWrapper})
         *
         * @return void
         */
        public function add(string $event, $handler): void
        {
            $this->listeners[$event][] = $handler;
        }

        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @param object $event
         *
         * @return iterable|\Closure[]
         */
        public function getListenersForEvent(object $event): iterable
        {
            $parents = \class_parents($event);
            $implements = \class_implements($event);
            $classes = array_merge([\get_class($event)], $parents ?: [], $implements ?: []);
            $listeners = [];
            foreach ($classes as $v) {
                $listeners = array_merge($listeners, isset($this->listeners[$v]) ? $this->listeners[$v] : []);
            }
            $wrapper = $this->listenerWrapper;
            return $wrapper ? array_map(function ($v) use ($wrapper) {
                return $wrapper($v);
            }, $listeners) : $listeners;
        }
    }

    class EventDispatcher implements DispatcherInterface
    {
        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @var ListenerProviderInterface
         */
        protected $listenerProvider;

        public function __construct(ListenerProviderInterface $listenerProvider)
        {
            $this->listenerProvider = $listenerProvider;
        }

        /**
         * @param object $event
         * @return object event object
         */
        public function dispatch(object $event): object
        {
            /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
             * @var \Closure|string $listener - if the listener is a string, you need to wrap it in a function {@see $listener_wrapper}
             * @var \Closure $wrapper {@see ListenerProvider::prepareListener()}
             */
            foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
                $listener($event);
                if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                    return $event;
                }
            }
            return $event;
        }
    }
}

namespace Dissonance\PackagesLoaderFilesystem {

    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Contracts\BootstrapInterface;
    use Dissonance\Packages\Contracts\PackagesRepositoryInterface;
    use Dissonance\Packages\Contracts\PackagesLoaderInterface;
    use Dissonance\Support\Arr;
    use Psr\SimpleCache\CacheInterface;

    class PackagesLoader implements PackagesLoaderInterface
    {
        /**
         * @var array
         */
        protected $scan_dirs = [];
        /**
         * @var int
         */
        protected $max_depth = 3;
        /**
         * @var null |CacheInterface
         */
        protected $cache = null;

        /**
         * PackagesLoader constructor.
         * @param array $scan_dirs
         * @param null|CacheInterface $cache
         * @param int $max_depth
         */
        public function __construct(array $scan_dirs = [], CacheInterface $cache = null, int $max_depth = 3)
        {
            $this->scan_dirs = $scan_dirs;
            $this->max_depth = $max_depth;
            $this->cache = $cache;
        }

        public function load(PackagesRepositoryInterface $repository)
        {
            /**
             * @var null|CacheInterface $cache
             */
            $cache = $this->cache;
            $key = 'packages_filesystem';
            if ($cache && ($packages = $cache->get($key)) && is_array($packages)) {
            } else {
                $packages = [];
                if (!empty($this->scan_dirs)) {
                    foreach ($this->scan_dirs as $dir) {
                        if (is_dir($dir) && is_readable($dir)) {
                            $packages = array_merge($packages, $this->getDirPackages($dir));
                        } else {
                            throw new \Exception('Directory [' . $dir . '] is not readable or not exists!');
                        }
                    }
                }
                if ($cache) {
                    $cache->set($key, $packages);
                }
            }
            foreach ($packages as $v) {
                $repository->addPackage($v);
            }
        }

        protected function getDirPackages($dir)
        {
            $packages = [];
            $files = glob($dir . '/*/composer.json', GLOB_NOSORT);
            $files = array_merge($files, glob($dir . '/*/*/composer.json', GLOB_NOSORT));
            foreach ($files as $file) {
                if (file_exists($file) && is_readable($file)) {
                    $config = Arr::get(@\json_decode(file_get_contents($file), true), 'extra.dissonance');
                    if (is_array($config)) {
                        $app = Arr::get($config, 'app');
                        $config['base_path'] = dirname($file);
                        if (is_array($app)) {
                            $app['base_path'] = $config['base_path'];
                            $config['app'] = $app;
                        }
                        $packages[] = $config;
                    }
                }
            }
            return $packages;
        }
    }

    class Bootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $app->afterResolving(PackagesRepositoryInterface::class, function (PackagesRepositoryInterface $repository) use ($app) {
                $repository->addPackagesLoader(new PackagesLoader($app->get('config::packages_paths'), $app('cache', null)));
            });
        }
    }
}

namespace Dissonance\Http {

    use Dissonance\Contracts\CoreInterface;
    use Psr\Http\Message\UriFactoryInterface;
    use Psr\Http\Message\StreamFactoryInterface;
    use Psr\Http\Message\StreamInterface;
    use Psr\Http\Message\ResponseFactoryInterface;
    use Psr\Http\Message\ServerRequestFactoryInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\RequestFactoryInterface;
    use Psr\Http\Message\UriInterface;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\UploadedFileInterface;
    use Dissonance\Contracts\BootstrapInterface;
    use Dissonance\Contracts\Support\RenderableInterface;

    class UploadedFile extends \Nyholm\Psr7\UploadedFile
    {
    }

    class Stream extends \Nyholm\Psr7\Stream
    {
    }

    /**
     * Class ServerRequest
     */
    class ServerRequest extends \Nyholm\Psr7\ServerRequest
    {
        public function isXMLHttpRequest()
        {
            return $this->getServerParam('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
        }

        public function getUserAgent()
        {
            return $this->getServerParam('HTTP_USER_AGENT');
        }

        public function getServerParam($name, $default = null)
        {
            $server = $this->getServerParams();
            return isset($server[$name]) ? $server[$name] : $default;
        }

        /**
         * @param $name
         * @param null| string $default
         */
        public function getInput($name, $default = null)
        {
            $params = $this->getParsedBody();
            return $params[$name] ?? $default;
        }

        public function getQuery($name, $default = null)
        {
            $params = $this->getQueryParams();
            return $params[$name] ?? $default;
        }
    }

    /**
     * Class Response
     */
    class Response extends \Nyholm\Psr7\Response
    {
    }

    class Request extends \Nyholm\Psr7\Request
    {
    }

    class Uri extends \Nyholm\Psr7\Uri
    {
    }

    class UriHelper
    {
        public function deletePrefix(string $prefix, UriInterface $uri): UriInterface
        {
            $prefix = $this->normalizePrefix($prefix);
            if (!empty($prefix)) {
                $path = $uri->getPath();
                $path = preg_replace('~^' . preg_quote($prefix, '~') . '~', '', $path);
                $uri = $uri->withPath($path);
            }
            return $uri;
        }

        public function normalizePrefix(string $prefix): string
        {
            $prefix = trim($prefix, ' \\/');
            return $prefix == '' ? '' : '/' . $prefix;
        }
    }

    class ResponseSender implements RenderableInterface
    {
        /**
         * @var ResponseInterface
         */
        protected $response;

        public function __construct(ResponseInterface $response)
        {
            $this->response = $response;
        }

        public function render()
        {
            $response = $this->response;
            $http_line = sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase());
            header($http_line, true, $response->getStatusCode());
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header("{$name}: {$value}", false);
                }
            }
            $stream = $response->getBody();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            while (!$stream->eof()) {
                echo $stream->read(1024 * 8);
            }
        }

        public function __toString()
        {
            ob_start();
            $this->render();
            return ob_get_clean();
        }
    }

    /**
     * Class Response
     */
    class DownloadResponse extends Response
    {
        public function __construct(StreamInterface $body, string $filename, int $status = 200, array $headers = [], string $version = '1.1', string $reason = null)
        {
            parent::__construct($status, array_merge($headers, ['Content-Description' => 'File Transfer', 'Content-Type' => 'application/octet-stream', 'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"', 'Content-Transfer-Encoding' => 'binary', 'Expires' => '0', 'Cache-Control' => 'must-revalidate', 'Pragma' => 'public', 'Content-Length' => $body->getSize()]), $body, $version, $reason);
        }
    }

    class Bootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $concrete = PsrHttpFactory::class;
            $app->singleton($concrete);
            $app->alias($concrete, 'http_factory');
            $app->alias($concrete, UriFactoryInterface::class);
            $app->alias($concrete, StreamFactoryInterface::class);
            $app->alias($concrete, ResponseFactoryInterface::class);
            $app->alias($concrete, ServerRequestFactoryInterface::class);
            $app->alias($concrete, RequestFactoryInterface::class);
        }
    }

    class PsrHttpFactory implements UriFactoryInterface, StreamFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, RequestFactoryInterface
    {
        /**
         * @return ServerRequestInterface
         */
        public function createServerRequestFromGlobals()
        {
            $server = $_SERVER;
            $method = $server['REQUEST_METHOD'];
            $serverRequest = $this->createServerRequest($method, $this->createUriFromGlobals(), $server);
            foreach ($server as $key => $value) {
                if ($value) {
                    if (0 === \strpos($key, 'HTTP_')) {
                        $name = \strtr(\strtolower(\substr($key, 5)), '_', '-');
                        if (\is_int($name)) {
                            $name = (string)$name;
                        }
                        $serverRequest->withAddedHeader((string)$name, $value);
                    } elseif (0 === \strpos($key, 'CONTENT_')) {
                        $name = 'content-' . \strtolower(\substr($key, 8));
                        $serverRequest->withAddedHeader($name, $value);
                    }
                }
            }
            $serverRequest = $serverRequest->withProtocolVersion(isset($server['SERVER_PROTOCOL']) ? \str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1')->withCookieParams($_COOKIE)->withQueryParams($_GET)->withUploadedFiles($this->normalizeFiles($_FILES));
            if ($method === 'POST') {
                $serverRequest = $serverRequest->withParsedBody($_POST);
            }
            $body = \fopen('php://input', 'r');
            if (!$body) {
                return $serverRequest;
            }
            if (\is_resource($body)) {
                $body = $this->createStreamFromResource($body);
            } elseif (\is_string($body)) {
                $body = $this->createStream($body);
            } elseif (!$body instanceof StreamInterface) {
                throw new \InvalidArgumentException('The $body parameter to ServerRequestCreator::fromArrays must be string, resource or StreamInterface');
            }
            return $serverRequest->withBody($body);
        }

        /**
         * Return an UploadedFile instance array.
         *
         * @param array $files A array which respect $_FILES structure
         *
         * @return UploadedFileInterface[]
         *
         * @throws \InvalidArgumentException for unrecognized values
         */
        private function normalizeFiles(array $files): array
        {
            $normalized = [];
            foreach ($files as $key => $value) {
                if ($value instanceof UploadedFileInterface) {
                    $normalized[$key] = $value;
                } elseif (\is_array($value) && isset($value['tmp_name'])) {
                    $normalized[$key] = $this->createUploadedFileFromSpec($value);
                } elseif (\is_array($value)) {
                    $normalized[$key] = $this->normalizeFiles($value);
                } else {
                    throw new \InvalidArgumentException('Invalid value in files specification');
                }
            }
            return $normalized;
        }

        /**
         * Create and return an UploadedFile instance from a $_FILES specification.
         *
         * If the specification represents an array of values, this method will
         * delegate to normalizeNestedFileSpec() and return that return value.
         *
         * @param array $value $_FILES struct
         *
         * @return array|UploadedFileInterface
         */
        private function createUploadedFileFromSpec(array $value)
        {
            if (\is_array($value['tmp_name'])) {
                return $this->normalizeNestedFileSpec($value);
            }
            try {
                $stream = $this->createStreamFromFile($value['tmp_name']);
            } catch (\RuntimeException $e) {
                $stream = $this->createStream();
            }
            return $this->createUploadedFile($stream, (int)$value['size'], (int)$value['error'], $value['name'], $value['type']);
        }

        /**
         * Normalize an array of file specifications.
         *
         * Loops through all nested files and returns a normalized array of
         * UploadedFileInterface instances.
         *
         * @return UploadedFileInterface[]
         */
        private function normalizeNestedFileSpec(array $files = []): array
        {
            $normalizedFiles = [];
            foreach (\array_keys($files['tmp_name']) as $key) {
                $spec = ['tmp_name' => $files['tmp_name'][$key], 'size' => $files['size'][$key], 'error' => $files['error'][$key], 'name' => $files['name'][$key], 'type' => $files['type'][$key]];
                $normalizedFiles[$key] = $this->createUploadedFileFromSpec($spec);
            }
            return $normalizedFiles;
        }

        public function isSecure()
        {
            $server = $_SERVER;
            foreach (['HTTPS' => ['on', '1'], 'HTTP_SSL' => ['1'], 'HTTP_X_SSL' => ['yes', '1'], 'HTTP_X_FORWARDED_PROTO' => ['https'], 'HTTP_X_SCHEME' => ['https']] as $key => $values) {
                if (!empty($server[$key])) {
                    foreach ($values as $value) {
                        if (strtolower($server[$key]) == $value) {
                            return true;
                        }
                    }
                }
            }
            return !empty($server['HTTP_X_HTTPS']) && strtolower($server['HTTP_X_HTTPS']) != 'off';
        }

        /**
         * Implemented Nyholm/psr7-server
         *
         * @return UriInterface
         */
        public function createUriFromGlobals()
        {
            $uri = $this->createUri('');
            $server = $_SERVER;
            $uri = $uri->withScheme($this->isSecure() ? 'https' : 'http');
            if (isset($server['REQUEST_SCHEME']) && isset($server['SERVER_PORT'])) {
                $uri = $uri->withPort($server['SERVER_PORT']);
            }
            if (isset($server['HTTP_HOST'])) {
                if (1 === \preg_match('/^(.+)\\:(\\d+)$/', $server['HTTP_HOST'], $matches)) {
                    $uri = $uri->withHost($matches[1])->withPort($matches[2]);
                } else {
                    $uri = $uri->withHost($server['HTTP_HOST']);
                }
            } elseif (isset($server['SERVER_NAME'])) {
                $uri = $uri->withHost($server['SERVER_NAME']);
            }
            if (isset($server['REQUEST_URI'])) {
                $uri = $uri->withPath(\current(\explode('?', $server['REQUEST_URI'])));
            }
            if (isset($server['QUERY_STRING'])) {
                $uri = $uri->withQuery($server['QUERY_STRING']);
            }
            return $uri;
        }

        public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
        {
            return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
        }

        public function createRequest(string $method, $uri): RequestInterface
        {
            return new Request($method, $uri);
        }

        public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
        {
            if (2 > \func_num_args()) {
                // This will make the Response class to use a custom reasonPhrase
                $reasonPhrase = null;
            }
            return new Response($code, [], null, '1.1', $reasonPhrase);
        }

        public function createStream(string $content = ''): StreamInterface
        {
            return Stream::create($content);
        }

        public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
        {
            $resource = @\fopen($filename, $mode);
            if (false === $resource) {
                if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'])) {
                    throw new \InvalidArgumentException('The mode ' . $mode . ' is invalid.');
                }
                throw new \RuntimeException('The file ' . $filename . ' cannot be opened.');
            }
            return Stream::create($resource);
        }

        public function createStreamFromResource($resource): StreamInterface
        {
            return Stream::create($resource);
        }

        public function createUploadedFile(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): UploadedFileInterface
        {
            if (null === $size) {
                $size = $stream->getSize();
            }
            return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
        }

        public function createUri(string $uri = ''): UriInterface
        {
            return new Uri($uri);
        }
    }
}

namespace Dissonance\Http\Cookie {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresDispatcher;
    use Dissonance\Container\ServiceProvider;

    interface CookiesInterface extends \ArrayAccess
    {
    }

    class CookiesProvider extends ServiceProvider
    {
        public function register(): void
        {
            $app = $this->app;
            $app->singleton(CookiesInterface::class, function (CoreInterface $app) {
                $request = $app['request'];
                $expires = $app('config::cookie_expires', 3600 * 24 * 365);
                $cookies = $this->factoryCookiesClass();
                if ($request instanceof ServerRequestInterface) {
                    $cookies->setDefaults($request->getUri()->getHost(), '/', $expires, $request->getUri()->getScheme() === 'https');
                } else {
                    $cookies->setDefaults($app['config::default_host'], '/', $expires);
                }
                return $cookies;
            }, 'cookie');
            $app['listeners']->add(MiddlewaresDispatcher::class, function ($event) use ($app) {
                /** @var MiddlewaresDispatcher $event */
                $event->prependToGroup(MiddlewaresDispatcher::GROUP_GLOBAL, CookiesMiddleware::class);
            });
        }

        protected function factoryCookiesClass(): CookiesInterface
        {
            return new Cookies();
        }
    }

    class CookiesMiddleware implements MiddlewareInterface
    {
        /**
         * @var CookiesInterface
         */
        protected $cookies;

        public function __construct(CookiesInterface $cookies)
        {
            $this->cookies = $cookies;
        }

        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            $this->cookies->setRequestCookies($request->getCookieParams());
            $response = $handler->handle($request);
            return $this->cookies->toResponse($response);
        }
    }

    class Cookies implements CookiesInterface
    {
        /**
         * @var array[]
         */
        protected $items = [];
        /**
         * @var string|null
         */
        protected $domain;
        /**
         * @var string if empty path, browser set request request path
         */
        protected $path = '';
        /**
         * @var bool
         */
        protected $secure = false;
        /**
         * @var int
         */
        protected $expires = 0;
        /**
         * @var string|null
         * @uses \Dissonance\Http\Cookie\CookiesInterface::SAMESITE_VALUES
         */
        protected $same_site;
        /**
         * @var array [ name => value...]
         */
        protected $request_cookies = [];

        /**
         * @param string|null $domain
         * @param string|null $path
         * @param int|null $expires
         * @param bool|null $secure
         * @param string|null $same_site
         * @return mixed|void
         * @throws \Exception
         */
        public function setDefaults(string $domain = null, string $path = null, int $expires = null, bool $secure = null, string $same_site = null)
        {
            if ($domain) {
                $this->domain = $domain;
            }
            if (!is_null($secure)) {
                $this->secure = $secure;
            }
            if (is_int($expires)) {
                $this->expires = $expires;
            }
            if ($path) {
                $this->path = $path;
            }
            if ($same_site) {
                if (!in_array($same_site, static::SAMESITE_VALUES)) {
                    throw new \Exception('Incorrect sameSite value(' . $same_site . ')');
                }
                $this->same_site = $same_site;
            }
        }

        /**
         * Set cookie to response
         *
         * @notice Please do not install serialized objects, this violates security!!! use json_encode
         * @param string $name
         * @param string $value
         * @param int|null $expires
         * @param bool|null $httponly
         * @param string|null $path
         * @param string|null $domain
         * @param bool|null $secure
         * @param array $options
         * @return array|\ArrayAccess
         *
         */
        public function setCookie(string $name, string $value = '', int $expires = null, bool $httponly = null, string $path = null, string $domain = null, bool $secure = null, array $options = [])
        {
            $data = ['expires' => is_int($expires) ? $expires : $this->expires, 'httponly' => !empty($httponly), 'domain' => !is_null($domain) ? $domain : $this->domain, 'path' => is_null($path) ? $this->path : $path, 'secure' => !is_null($secure) ? $secure : $this->secure];
            if (!isset($options['same_site']) && isset($this->same_site)) {
                $options['same_site'] = $this->same_site;
            }
            $data = array_merge($data, $options);
            $cookie = $this->create($name, $value);
            foreach ($data as $k => $v) {
                $cookie[$k] = $v;
            }
            return $this->items[] = $cookie;
        }

        /**
         * @param $name
         * @param string $value
         * @return array
         */
        protected function create($name, $value = '')
        {
            return ['name' => $name, 'value' => $value];
        }

        /**
         * @param array $cookies [ name => value...]
         */
        public function setRequestCookies(array $cookies)
        {
            $this->request_cookies = $cookies;
        }

        /**
         * @inheritDoc
         * @return array|\ArrayAccess[]
         */
        public function getResponseCookies(): array
        {
            return $this->items;
        }

        /**
         * @param string $name
         * @param string $value
         */
        public function set(string $name, string $value = ''): void
        {
            $this->setCookie($name, $value);
        }

        /**
         * @inheritDoc
         */
        public function has(string $name): bool
        {
            return isset($this->request_cookies[$name]);
        }

        /**
         * @inheritDoc
         */
        public function get(string $name, string $default = null)
        {
            $cookies = $this->request_cookies;
            return isset($cookies[$name]) ? $cookies[$name] : $default;
        }

        /**
         * Delete cookie
         *
         * @param string[]|string $names
         */
        public function remove($names): void
        {
            foreach ((array)$names as $v) {
                $this->setCookie($v, '', time() - 3600 * 48, true, $this->path, $this->domain);
            }
        }

        /**
         * Send cookies to response
         *
         * @param ResponseInterface $response
         * @return ResponseInterface
         */
        public function toResponse(ResponseInterface $response): ResponseInterface
        {
            foreach ($this->items as $cookie) {
                $response = $response->withAddedHeader(static::SET_COOKIE_HEADER, $this->cookieToResponse($cookie));
            }
            return $response;
        }

        /**
         * Get cookie header value from array
         *
         * @param array | \ArrayAccess $cookie
         * @return string
         */
        public function cookieToResponse($cookie)
        {
            return sprintf('%s=%s; ', $cookie['name'], urlencode($cookie['value'])) . (!empty($cookie['domain']) ? 'Domain=' . $cookie['domain'] . '; ' : '') . (!empty($cookie['path']) ? 'Path=' . $cookie['path'] . '; ' : '') . (isset($cookie['expires']) && $cookie['expires'] !== 0 ? sprintf('Expires=%s; ', gmdate('D, d M Y H:i:s T', $cookie['expires'])) : '') . (isset($cookie['max_age']) && is_int($cookie['max_age']) ? sprintf('Max-Age=%d; ', $cookie['max_age']) : '') . (!empty($cookie['secure']) ? 'Secure; ' : '') . (!empty($cookie['httponly']) ? 'HttpOnly; ' : '') . (!empty($cookie['same_site']) && in_array($cookie['same_site'], CookiesInterface::SAMESITE_VALUES) ? 'SameSite=' . $cookie['same_site'] . '; ' : '');
        }

        /**
         * Get an item at a given offset.
         *
         * @param mixed $key
         * @return mixed
         */
        public function offsetExists($key)
        {
            return $this->has($key);
        }

        /**
         * Get an item at a given offset.
         *
         * @param mixed $key
         * @return string or null if not exists
         * @uses get()
         */
        public function offsetGet($key)
        {
            return $this->get($key);
        }

        /**
         * Set the item at a given offset.
         *
         * @param mixed $key
         * @param mixed $value
         * @return void
         */
        public function offsetSet($key, $value)
        {
            $this->set($key, $value);
        }

        /**
         * Unset the item at a given offset.
         *
         * @param string|array $key
         * @return void
         */
        public function offsetUnset($key)
        {
            $this->remove($key);
        }
    }
}

namespace Dissonance\HttpKernel {

    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Runner;
    use Dissonance\Packages\AssetFileRequestMiddleware;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresHandler;
    use Dissonance\Http\MiddlewaresSupport\MiddlewareCallback;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseFactoryInterface;
    use Dissonance\Http\UriHelper;
    use Dissonance\RequestPrefixMiddleware\Middleware;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresDispatcher;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresCollection;
    use Dissonance\Contracts\Http\HttpKernelInterface;
    use Dissonance\Http\ResponseSender;
    use Dissonance\View\View;
    use Dissonance\Http\PsrHttpFactory;
    use Dissonance\Http\ResponseMutable\ResponseMutable;
    use function _DS\response;
    use function _DS\config;
    use Psr\Http\Message\StreamInterface;
    use Dissonance\Contracts\BootstrapInterface;
    use Dissonance\Support\Collection;
    use Dissonance\Apps\AppsRepositoryInterface;
    use Dissonance\Contracts\App\ApplicationInterface;
    use Dissonance\Contracts\Support\RenderableInterface;
    use Dissonance\Contracts\Support\ArrayableInterface;
    use Dissonance\Contracts\Routing\RouteInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    class RoutingHandler implements RequestHandlerInterface
    {
        protected $app;
        protected $kernel;

        public function __construct(CoreInterface $app)
        {
            $this->app = $app;
        }

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $app = $this->app;
            /**
             * @var \Dissonance\Contracts\Routing\RouteInterface|null $route
             */
            $path = $request->getUri()->getPath();
            $route = $app['router']->match($request->getMethod(), $path);
            if ($route) {
                $middlewares = $route->getMiddlewares();
                if (!empty($middlewares)) {
                    $app[MiddlewaresDispatcher::class]->factoryCollection($middlewares);
                }
                return (new MiddlewaresCollection($middlewares))->process($request, new RouteHandler($app, $route));
            } else {
                if (\_DS\config('symbiosis')) {
                    $app['destroy_response'] = true;
                }
                return response(404, new \Exception('Route not found for path [' . $path . ']', 7623));
            }
        }
    }

    class RouteHandler implements RequestHandlerInterface
    {
        /**
         * @var CoreInterface
         */
        protected $app;
        /**
         * @var RouteInterface
         */
        protected $route;

        public function __construct(CoreInterface $app, RouteInterface $route)
        {
            $this->app = $app;
            $this->route = $route;
        }

        /**
         * @param ServerRequestInterface $request
         * @return ResponseInterface
         * @throws
         */
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $app = $this->app;
            /**
             * @var RouteInterface $route
             * @var CoreInterface|ApplicationInterface $container
             * @var AppsRepositoryInterface|null $apps
             * @var callable|string|null $handler
             */
            $route = $app[RouteInterface::class] = $this->route;
            $app->alias(RouteInterface::class, 'route');
            $apps = $app[AppsRepositoryInterface::class];
            $action = $route->getAction();
            $container = isset($action['app']) && $apps instanceof AppsRepositoryInterface ? $apps->getBootedApp($action['app']) : $this->app;
            $handler = $route->getHandler();
            if (!is_string($handler) && !is_callable($handler)) {
                throw new \Exception('Incorrect route handler for route ' . $route->getPath() . '!');
            }
            // Раздаем запрос
            $request_interface = ServerRequestInterface::class;
            $app->instance($request_interface, $request, 'request');
            $app->alias($request_interface, \get_class($request));
            // Ставим мутабельный объект ответа для контроллеров и экшенов
            $response = new ResponseMutable($app[ResponseFactoryInterface::class]->createResponse());
            $app->instance(ResponseInterface::class, $response, 'response');
            return $this->prepareResponse($container->call($handler, $route->getParams()), $response);
        }

        protected function prepareResponse($data, ResponseMutable $response): ResponseInterface
        {
            if ($data instanceof ResponseInterface) {
                return $data;
            } elseif ($data instanceof StreamInterface) {
                return $response->withBody($data)->getRealInstance();
            }
            if (is_array($data) || $data instanceof \Traversable || $data instanceof ArrayableInterface || $data instanceof \JsonSerializable) {
                $response->withHeader('content-type', 'application/json');
                $data = \_DS\collect($data)->__toString();
            } elseif ($data instanceof RenderableInterface || $data instanceof \Stringable) {
                $data = $data->__toString();
            }
            $response->getBody()->write((string)$data);
            return $response->getRealInstance();
        }
    }

    class HttpRunner extends Runner
    {
        public function isHandle(): bool
        {
            return $this->app['env'] === 'web';
        }

        public function run(): void
        {
            $app = $this->app;
            try {
                $request_interface = ServerRequestInterface::class;
                $request = $app[PsrHttpFactory::class]->createServerRequestFromGlobals();
                $app->instance($request_interface, $request, 'request');
                $app->alias($request_interface, get_class($request));
                $base_uri = $this->prepareBaseUrl($request);
                $app['base_uri'] = $base_uri;
                $request = $request->withUri((new UriHelper())->deletePrefix($base_uri, $request->getUri()));
                $Middlewares = [new Middleware($app('config::uri_prefix', null)), new AssetFileRequestMiddleware($app('config::assets_prefix', 'assets'), $app['resources'], $app[ResponseFactoryInterface::class]), new MiddlewareCallback(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                    if ($handler instanceof MiddlewaresHandler) {
                        $real = $handler->getRealHandler();
                        if ($real instanceof HttpKernelInterface) {
                            $real->bootstrap();
                        }
                    }
                    return $handler->handle($request);
                })];
                $handler = new MiddlewaresHandler($app->make(HttpKernelInterface::class), $Middlewares);
                $response = $handler->handle($request);
                if (!$app('destroy_response', false)) {
                    $this->sendResponse($response);
                    if (\_DS\config('symbiosis')) {
                        exit;
                        // завершаем работу
                    }
                }
            } catch (\Throwable $e) {
                if (!\_DS\config('symbiosis')) {
                    $this->sendResponse($app[HttpKernelInterface::class]->response(500, $e));
                }
            }
        }

        protected function prepareBaseUrl(ServerRequestInterface $request): string
        {
            $server = $request->getServerParams();
            $baseUrl = '/';
            if (PHP_SAPI !== 'cli') {
                foreach (['PHP_SELF', 'SCRIPT_NAME', 'ORIG_SCRIPT_NAME'] as $v) {
                    $value = $server[$v];
                    if (!empty($value) && basename($value) == basename($server['SCRIPT_FILENAME'])) {
                        $this->file = basename($value);
                        $request_uri = $request->getUri()->getPath();
                        $value = '/' . ltrim($value, '/');
                        if ($request_uri === preg_replace('~^' . preg_quote($value, '~') . '~i', '', $request_uri)) {
                            $app = $this->app;
                            if (is_null($app('mod_rewrite'))) {
                                $this->app['mod_rewrite'] = true;
                            }
                            $value = dirname($value);
                        }
                        $baseUrl = $value;
                        break;
                    }
                }
            }
            return rtrim($baseUrl, '/' . \DIRECTORY_SEPARATOR);
        }

        public function sendResponse(ResponseInterface $response)
        {
            $sender = new ResponseSender($response);
            $sender->render();
        }
    }

    class HttpKernel implements HttpKernelInterface
    {
        /**
         * @var CoreInterface
         */
        protected $app;
        /**
         * @var string[]  Names of classes implements from {@uses \Dissonance\Contracts\BootstrapInterface}
         */
        protected $bootstrappers = [];
        protected $mod_rewrite = null;

        public function __construct(CoreInterface $container)
        {
            $this->app = $container;
            $this->mod_rewrite = $container('config::mod_rewrite', null);
        }

        /**
         * Запускает инициализацию ядра
         */
        public function bootstrap(): void
        {
            if (!$this->app->isBooted()) {
                $this->app->addBootstraps($this->bootstrappers);
                $this->app->bootstrap();
            }
        }

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $app = $this->app;
            $dispatcher = $app->instance(MiddlewaresDispatcher::class, new MiddlewaresDispatcher());
            $dispatcher->setDefaultCallback(function ($class) use ($app) {
                return $app->make($class);
            });
            \_DS\event($dispatcher);
            $middlewares_collection = new MiddlewaresCollection($dispatcher->factoryGroup(MiddlewaresDispatcher::GROUP_GLOBAL));
            $routing_handler = $app->make(RoutingHandler::class);
            return $middlewares_collection->process($request, $routing_handler);
        }

        /**
         * @param int $code
         * @param \Throwable |null $exception
         * @return ResponseInterface
         */
        public function response(int $code = 200, \Throwable $exception = null): ResponseInterface
        {
            $app = $this->app;
            /**
             * @var ResponseInterface $response
             */
            $response = $app[ResponseFactoryInterface::class]->createResponse($code);
            if ($code >= 400) {
                $path = $app('templates_package', 'ui_http_kernel') . '::';
                if ($exception && config('debug')) {
                    $view = View::make($path . "exception", ['error' => $exception]);
                } else {
                    $view = View::make($path . "error", ['response' => $response]);
                }
                $response->getBody()->write($view->__toString());
            }
            return $response;
        }
    }

    class Bootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $app->bind(HttpKernelInterface::class, HttpKernel::class);
            $app->addRunner(new HttpRunner($app));
        }
    }
}

namespace Dissonance\Http\MiddlewaresSupport {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    /**
     * Class MiddlewaresDispatcher
     * @package Dissonance\Http\MiddlewaresSupport
     * @category Dissonance\Http
     *
     * @notice The use of functions as middleware is made exclusively for Micro assembly
     * @example function(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface {}
     */
    class MiddlewaresDispatcher
    {
        const GROUP_GLOBAL = 'global';
        /**
         * @var string[][]|\Closure[][]|array = [
         *     'name' => [Middlewares ...],
         *     ...
         * ]
         */
        protected $middlewares_groups = [self::GROUP_GLOBAL => []];
        /**
         * @var array |\Closure[]
         */
        protected $binds = [];
        /**
         * @uses
         * @used-by factory()
         * @var null|\Closure
         */
        protected $default_callback;

        public function addMiddlewareGroup(string $name, array $middlewares)
        {
            $this->middlewares_groups[$name] = $middlewares;
        }

        public function appendToGroup($name, $middleware, \Closure $bind = null): self
        {
            if (!isset($this->middlewares_groups[$name])) {
                $this->middlewares_groups[$name] = [];
            }
            $this->middlewares_groups[$name][] = $middleware;
            if ($bind) {
                $this->bind($middleware, $bind);
            }
            return $this;
        }

        public function prependToGroup($name, $middleware, \Closure $bind = null): self
        {
            if (!isset($this->middlewares_groups[$name])) {
                $this->middlewares_groups[$name] = [];
            }
            array_unshift($this->middlewares_groups[$name], $middleware);
            if ($bind) {
                $this->bind($middleware, $bind);
            }
            return $this;
        }

        /**
         * @param string $name
         * @return \Closure[]|string[]
         * @throws \Exception
         */
        public function getMiddlewareGroup(string $name): array
        {
            if (!isset($this->middlewares_groups[$name])) {
                throw new \Exception('Middleware group [' . htmlspecialchars($name) . '] not found');
                //Группа промежуточного программного обеспечения не найдена
            }
            return $this->middlewares_groups[$name];
        }

        /**
         * @param array $middlewares
         * @return array|MiddlewareInterface[]
         */
        public function factoryCollection(array $middlewares)
        {
            return array_map(function ($v) {
                return $this->factory($v);
            }, $middlewares);
        }

        public function factoryGroup($name)
        {
            $middlewares = $this->getMiddlewareGroup($name);
            return $this->factoryCollection($middlewares);
        }

        /**
         * @param string|\Closure $middleware
         * @return MiddlewareInterface
         */
        public function factory( $middleware): MiddlewareInterface
        {
            if ($middleware instanceof \Closure) {
                return new MiddlewareCallback($middleware);
            }
            if (isset($this->middlewares_groups[$middleware])) {
                $middlewares = $this->factoryCollection($this->middlewares_groups[$middleware]);
                return new MiddlewaresCollection($middlewares);
            }
            if (!class_exists($middleware)) {
                throw new \Exception('Middleware group or class [' . $middleware . '] not found!');
            }
            $callback = isset($this->binds[$middleware]) ? $this->binds[$middleware] : ($this->default_callback ?: function ($class) {
                return new $class();
            });
            return $callback($middleware);
        }

        /**
         * @param \Closure $callback
         */
        public function setDefaultCallback(\Closure $callback)
        {
            $this->default_callback = $callback;
        }

        /**
         * @param string $middleware_classname
         * @param \Closure $callback
         */
        public function bind(string $middleware_classname, \Closure $callback)
        {
            $this->binds[$middleware_classname] = $callback;
        }
    }

    /**
     * Class MiddlewareHandler
     * @package Dissonance\Http\MiddlewaresSupport
     * @category Dissonance\Http
     *
     * @author shadowhand https://github.com/shadowhand
     * @link https://github.com/jbboehr/dispatch - base source
     */
    class MiddlewaresHandler implements RequestHandlerInterface
    {
        /**
         * @var MiddlewareInterface[]
         */
        protected $middleware = [];
        /**
         * @var RequestHandlerInterface
         */
        protected $handler;
        /**
         * @var int
         */
        protected $index = 0;

        /**
         * @param MiddlewareInterface[] $middleware
         * @param RequestHandlerInterface $handler
         */
        public function __construct(RequestHandlerInterface $handler, array $middleware = [])
        {
            $this->middleware = $middleware;
            $this->handler = $handler;
        }

        public function getRealHandler()
        {
            return $this->handler;
        }

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            if (empty($this->middleware)) {
                return $this->handler->handle($request);
            }
            $middleware = \array_shift($this->middleware);
            return $middleware->process($request, clone $this);
        }
    }

    /**
     * Class MiddlewaresCollection
     * @package Dissonance\Http\MiddlewareHandler
     *
     * @author shadowhand https://github.com/shadowhand
     * @link https://github.com/jbboehr/dispatch - base source
     */
    class MiddlewaresCollection implements MiddlewareInterface
    {
        /**
         * @var MiddlewareInterface[]
         */
        protected $middleware = [];

        /**
         * @param MiddlewareInterface[] $middleware
         */
        public function __construct(array $middleware = [])
        {
            array_map(function ($v) {
                $this->append($v);
            }, $middleware);
        }

        /**
         * Add a middleware to the end of the stack.
         *
         * @param MiddlewareInterface $middleware
         *
         * @return void
         */
        public function append(MiddlewareInterface $middleware)
        {
            array_push($this->middleware, $middleware);
        }

        /**
         * Add a middleware to the beginning of the stack.
         *
         * @param MiddlewareInterface $middleware
         *
         * @return void
         */
        public function prepend(MiddlewareInterface $middleware)
        {
            array_unshift($this->middleware, $middleware);
        }

        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return (new MiddlewaresHandler($handler, $this->middleware))->handle($request);
        }
    }

    /**
     * Class MiddlewareHandler
     * @package Dissonance\Http\MiddlewaresSupport
     * @category Dissonance\Http
     *
     * @author shadowhand https://github.com/shadowhand
     * @link https://github.com/jbboehr/dispatch - base source
     */
    class MiddlewareCallback implements MiddlewareInterface
    {
        /**
         * @var \Closure function()
         */
        protected $middleware;

        /**
         * MiddlewareCallback constructor.
         * @param \Closure $middleware
         */
        public function __construct(\Closure $middleware)
        {
            $this->middleware = $middleware;
        }

        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            $closure = $this->middleware;
            return $closure($request, $handler);
        }
    }
}

namespace Dissonance\Http\ResponseMutable {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\StreamInterface;

    class ResponseMutable implements ResponseInterface
    {
        /**
         * @var ResponseInterface
         */
        protected $response;

        public function __construct(ResponseInterface $response)
        {
            $this->response = $response;
        }

        /**
         * @return ResponseInterface
         */
        public function getRealInstance()
        {
            return $this->response;
        }

        /**
         * @inheritDoc
         */
        public function getProtocolVersion()
        {
            return $this->response->getProtocolVersion();
        }

        /**
         * @inheritDoc
         */
        public function withProtocolVersion($version)
        {
            $this->response = $this->response->withProtocolVersion($version);
            return $this;
        }

        /**
         * @inheritDoc
         */
        public function getHeaders()
        {
            return $this->response->getHeaders();
        }

        /**
         * @inheritDoc
         */
        public function hasHeader($name)
        {
            return $this->response->hasHeader($name);
        }

        /**
         * @inheritDoc
         */
        public function getHeader($name)
        {
            return $this->response->getHeader($name);
        }

        /**
         * @inheritDoc
         */
        public function getHeaderLine($name)
        {
            return $this->response->getHeaderLine($name);
        }

        /**
         * @inheritDoc
         */
        public function withHeader($name, $value)
        {
            $this->response = $this->response->withHeader($name, $value);
            return $this;
        }

        /**
         * @inheritDoc
         */
        public function withAddedHeader($name, $value)
        {
            $this->response = $this->response->withAddedHeader($name, $value);
            return $this;
        }

        /**
         * @inheritDoc
         */
        public function withoutHeader($name)
        {
            $this->response = $this->response->withoutHeader($name);
            return $this;
        }

        /**
         * @inheritDoc
         */
        public function getBody()
        {
            return $this->response->getBody();
        }

        /**
         * @inheritDoc
         */
        public function withBody(StreamInterface $body)
        {
            $this->response = $this->response->withBody($body);
            return $this;
        }

        /**
         * @inheritDoc
         */
        public function getStatusCode()
        {
            return $this->response->getStatusCode();
        }

        /**
         * @inheritDoc
         */
        public function withStatus($code, $reasonPhrase = '')
        {
            $this->response = $this->response->withStatus($code, $reasonPhrase);
            return $this;
        }

        /**
         * @inheritDoc
         */
        public function getReasonPhrase()
        {
            return $this->response->getReasonPhrase();
        }
    }
}

namespace Dissonance\RequestPrefixMiddleware {

    use Dissonance\Http\UriHelper;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresDispatcher;
    use Dissonance\Container\ServiceProvider;


    class Middleware implements MiddlewareInterface
    {
        /**
         * @var null|string
         */
        protected $uri_prefix;

        /**
         * RequestPrefixMiddleware constructor.
         * @param $uri_prefix - set in the Core container constructor config $app['config::uri_prefix'] {@see /config.php}
         */
        public function __construct(string $uri_prefix = null)
        {
            $this->uri_prefix = $uri_prefix;
        }

        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return $handler->handle(empty($this->uri_prefix) ? $request : $request->withUri((new UriHelper())->deletePrefix($this->uri_prefix, $request->getUri())));
        }
    }
}

namespace Dissonance\Packages {

    use Dissonance\Mimetypes\MimeTypesMini;
    use Psr\Http\Message\StreamInterface;
    use Dissonance\Packages\Contracts\TemplatesRepositoryInterface;
    use Dissonance\Packages\Contracts\PackagesRepositoryInterface;
    use Dissonance\Packages\Contracts\AssetsRepositoryInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\StreamFactoryInterface;
    use Dissonance\Container\CachedContainerInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Message\ResponseFactoryInterface;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Http\MiddlewaresSupport\MiddlewaresDispatcher;
    use function _DS\app;
    use Dissonance\Contracts\BootstrapInterface;
    use Psr\SimpleCache\CacheInterface;
    use Dissonance\Packages\Contracts\TemplateCompilerInterface;
    use Dissonance\Packages\Contracts\PackagesLoaderInterface;
    use Dissonance\Container\Traits\BaseContainerTrait;
    use Dissonance\Container\Traits\ArrayAccessTrait;
    use Dissonance\Packages\Contracts\ResourcesRepositoryInterface;

    class TemplateCompiler
    {
        protected $extensions = [];

        /**
         * @param TemplateCompilerInterface $compiler
         */
        public function addCompiler(TemplateCompilerInterface $compiler)
        {
            // todo: нужно сделать по именам
            foreach ($compiler->getExtensions() as $v) {
                $this->extensions[$v] = $compiler;
            }
        }

        /**
         * @param string $path путь к файлу или его название для определения компилера
         * @param string $template контент файла для преобразования
         *
         * @return string  html / php валидный код для выполнения через include {@link https://www.php.net/manual/ru/function.include.php)
         */
        public function compile(string $path, string $template): string
        {
            $ext = (new MimeTypesMini())->findExtension($path, array_keys($this->extensions));
            return $ext !== false ? $this->extensions[$ext]->compile($template) : $template;
        }
    }

    class ResourcesBootstrap
    {
        protected $cache_key = 'core.resources';

        /**
         * @param \Dissonance\Container\ServiceContainerInterface|\Dissonance\Core $app
         */
        public function bootstrap($app)
        {
            $app->singleton(TemplateCompiler::class);
            $res_interface = ResourcesRepositoryInterface::class;
            if ($app instanceof CachedContainerInterface) {
                //$app->cached($res_interface);
            }
            $app->singleton($res_interface, function () use ($app, $res_interface) {
                $cache = $app('cache');
                if ($cache instanceof CacheInterface && ($object = $cache->get($this->cache_key)) && $object instanceof ResourcesRepositoryInterface) {
                    // return $object;
                }
                /**
                 * @var ResourcesRepositoryInterface $repository
                 * @var PackagesRepositoryInterface $packages_repository
                 */
                $repository = new ResourcesRepository($app[TemplateCompiler::class], $app[StreamFactoryInterface::class], $app[PackagesRepositoryInterface::class]);
                //$packages_repository = $app[PackagesRepositoryInterface::class];
                /* foreach ($packages_repository->getPackages() as $k => $v) {
                       if (isset($v['id']) || isset($v['app'])) {
                           $repository->add(isset($v['id'])?$v['id']:$v['app']['id'], $v);
                       }
                   }
                   if ($cache instanceof CacheInterface) {
                       $cache->set($this->cache_key, $repository);
                   }*/
                return $repository;
            }, 'resources');
            $app->alias($res_interface, TemplatesRepositoryInterface::class);
            /*Перенесено в httpRunner костылем! Нет смысла бутить провайдеры при запросе файла*/
            /* $app['listeners']->add(MiddlewaresDispatcher::class, function (MiddlewaresDispatcher $event) use ($app) {
                        $event->appendToGroup('global',
                            AssetFileRequestMiddleware::class,
                            function () use ($app) {
                                return new AssetFileRequestMiddleware(
                                    $app('config::assets_prefix', 'assets'),
                                    $app['resources'],
                                    $app[ResponseFactoryInterface::class]);

                            }
                        );
                    });*/
        }
    }

    /**
     * Class AssetsRepository
     * @package Dissonance\Apps
     *
     */
    class ResourcesRepository implements ResourcesRepositoryInterface, AssetsRepositoryInterface, TemplatesRepositoryInterface
    {
        protected $packages = [];
        /**
         * @var TemplateCompiler
         */
        protected $compiler;
        /**
         * @var StreamFactoryInterface
         */
        protected $factory;
        /**
         * @var PackagesRepositoryInterface
         */
        protected $packages_repository;

        /**
         * ResourcesRepository constructor.
         *
         * @param TemplateCompiler $compiler
         * @param StreamFactoryInterface $factory
         */
        public function __construct(TemplateCompiler $compiler, StreamFactoryInterface $factory, PackagesRepositoryInterface $packages)
        {
            $this->compiler = $compiler;
            $this->factory = $factory;
            $this->packages_repository = $packages;
        }

        public function packageExists($id): bool
        {
            return $this->packages_repository->has($id);
        }

        /**
         * @param string $package_id
         * @param array $package_config = [
         *     'public_path' => 'static_files_path'
         *     'resources_path' => 'templates_path'
         * ];
         */
        public function add(string $package_id, array $package_config)
        {
            $assets = [];
            foreach (['public_path' => 'assets', 'resources_path' => 'resources'] as $k => $v) {
                if (!empty($package_config[$k]) || isset($package_config['app'])) {
                    $assets[$k] = rtrim($package_config['base_path'], '\\/') . \_DS\DS . (isset($package_config[$k]) ? trim($package_config[$k], '\\/') : $v);
                }
            }
            if (!empty($assets)) {
                $this->packages[$package_id] = $assets;
            }
        }

        /**
         * @param string $package_id
         * @param string $path
         * @return StreamInterface
         * @throws \Exception
         */
        public function getAssetFileStream(string $package_id, string $path): StreamInterface
        {
            return $this->getPathTypeFileStream($package_id, $path, 'public_path');
        }

        /**
         * @param string $package_id
         * @param string $path
         * @return StreamInterface
         * @throws \Exception
         */
        public function getResourceFileStream(string $package_id, string $path): StreamInterface
        {
            return $this->getPathTypeFileStream($package_id, $path, 'resources_path');
        }

        /**
         * @param string $package_id
         * @param string $path layouts/base/index or /layouts/base/index  - real path(module_root/resources/views/layouts/base/index)
         * if use config resources  storage as strings
         * layouts/base/index or /layouts/base/index  - $config['resources']['views']['layouts/base/index']
         *
         * @return string
         *
         * @throws \Exception
         */
        public function getTemplate(string $package_id, string $path): string
        {
            $base_name = basename($path);
            if (strpos($base_name, '.') === false) {
                $path .= '.blade.php';
            }
            $file = $this->getResourceFileStream($package_id, 'views/' . ltrim($this->cleanPath($path), '\\/'));
            return $this->compiler->compile($path, $file->getContents());
        }

        protected function cleanPath(string $path)
        {
            return preg_replace('!\\.\\.[/\\\\]!', '', $path);
        }

        /**
         * @param string $package_id
         * @param string $path
         * @param string $path_type resources array key 'public_path' or 'resources_path'
         * @return StreamInterface|null
         */
        protected function getPathTypeFileStream(string $package_id, string $path, string $path_type): ?StreamInterface
        {
            $path = $this->cleanPath($path);
            if ($this->packageExists($package_id)) {
                $assets = [];
                $package_config = $this->packages_repository->get($package_id);
                foreach (['public_path' => 'assets', 'resources_path' => 'resources'] as $k => $v) {
                    if (!empty($package_config[$k]) || isset($package_config['app'])) {
                        $assets[$k] = rtrim($package_config['base_path'], '\\/') . \_DS\DS . (isset($package_config[$k]) ? trim($package_config[$k], '\\/') : $v);
                    }
                }
                if (isset($assets[$path_type])) {
                    $full_path = $assets[$path_type] . '/' . ltrim($path, '/\\');
                    if (!file_exists($full_path) || !\is_readable($full_path)) {
                        throw new \Exception('File is not exists or not readable [' . $full_path . ']!');
                    }
                    return $this->factory->createStreamFromResource(\fopen($full_path, 'r'));
                }
            }
            throw new \Exception('Package not found [' . $package_id . ']!');
        }
    }

    /**
     * Class PackagesRepository
     * @package Dissonance\Apps
     * @property  CoreInterface|array $app  = [
     *       'config' => new \Dissonance\Config(),
     *       'router' => new \Dissonance\Contracts\Routing\Router(),
     *       'apps' => new \Dissonance\Contracts\Apps\AppsRepository(),
     *       'events' => new \Dissonance\Event\DispatcherInterface(),
     *       'listeners' => new \Dissonance\Event\ListenersInterface(),
     * ]
     */
    class PackagesRepository implements PackagesRepositoryInterface
    {
        /**
         * @var PackagesLoaderInterface[]
         */
        protected $loaders = [];
        protected $items = [];
        protected $loaded = false;

        public function addPackagesLoader(PackagesLoaderInterface $loader): void
        {
            $this->loaders[] = $loader;
        }

        /**
         * @param array $config
         * @return void
         */
        public function addPackage(array $config): void
        {
            $app = isset($config['app']) ? $config['app'] : null;
            // if modules are supported
            if (is_array($app)) {
                if (!isset($app['id']) && isset($config['id'])) {
                    $app['id'] = $config['id'];
                } else {
                    $config['id'] = $app['id'] = self::getAppId($app);
                }
                $config['app'] = $app;
            }
            $this->items[isset($config['id']) ? $config['id'] : \count($this->items)] = $config;
        }

        protected function bootPackage(array $config, CoreInterface $app)
        {
            if (!empty($config['bootstrappers'])) {
                foreach ((array)$config['bootstrappers'] as $v) {
                    if ($v === PackagesBootstrap::class) {
                        continue;
                    }
                    $app->runBootstrap($v);
                }
            }
        }

        public function has($id): bool
        {
            return isset($this->items[$id]);
        }

        public function get($key): array
        {
            return $this->items[$key];
        }

        /**
         * @return array
         */
        public function getPackages(): array
        {
            return $this->items;
        }

        public static function normalizeId(string $id)
        {
            return str_replace(['/', '-', '.'], ['_', '_', ''], \strtolower($id));
        }

        public static function getAppId(array $config)
        {
            if (!isset($config['id'])) {
                throw new \Exception('App id is required [' . \serialize($config) . ']!');
            }
            $name = $config['id'] = self::normalizeId($config['id']);
            $parent_app = $config['parent_app'] ?? null;
            if ($parent_app) {
                $config['parent_app'] = $parent_app = self::normalizeId($parent_app);
            }
            return $parent_app ? $parent_app . '.' . $name : $name;
        }

        public function load(CoreInterface $app): void
        {
            /* странно, но сериализация тоже быстро работает.
               if(!$this->loaded && file_exists('./pack_cache.php')){
                    $this->items = include './pack_cache.php';
                    $this->loaded = true;
                }*/
            if (!$this->loaded) {
                foreach ($this->loaders as $loader) {
                    $loader->load($this);
                }
                $this->loaded = true;
            }
            /*  file_put_contents('./pack_cache.php','<?php '.PHP_EOL.'return '.var_export($this->items,true).';');*/
            foreach ($this->getPackages() as $config) {
                $this->bootPackage($config, $app);
            }
        }

        public function __wakeup()
        {
            $this->loaded = true;
        }
    }

    class PackagesBootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $packages_class = PackagesRepositoryInterface::class;
            if ($app instanceof CachedContainerInterface) {
                $app->cached($packages_class);
            }
            if (!$app->bound($packages_class)) {
                $app->singleton($packages_class, PackagesRepository::class);
            }
            $p = $app[$packages_class];
            $p->load($app);
            $app['events']->dispatch($p);
        }
    }

    class AssetFileRequestMiddleware implements MiddlewareInterface
    {
        protected $path;
        /**
         * @var ResourcesRepositoryInterface
         */
        protected $resources;
        /**
         * @var ResponseFactoryInterface
         */
        protected $response_factory;

        /**
         * AssetFileRequestMiddleware constructor.
         * @param string $path Базовая директория для перехвата запросов
         * @param ResourcesRepositoryInterface $resources Репозиторий Файлов пакетов
         * @param ResponseFactoryInterface $factory Фабрика ответа
         */
        public function __construct(string $path, ResourcesRepositoryInterface $resources, ResponseFactoryInterface $factory)
        {
            $this->path = $path;
            $this->resources = $resources;
            $this->response_factory = $factory;
        }

        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            /**
             * @var  \Dissonance\Http\PsrHttpFactory|ResponseFactoryInterface $response_factory
             */
            $response_factory = $this->response_factory;
            $pattern = '~^' . preg_quote(trim($this->path, '/'), '~') . '/(.[^/]+)(.+)~i';
            $assets_repository = $this->resources;
            if (preg_match($pattern, ltrim($request->getRequestTarget(), '/'), $match)) {
                if ($assets_repository->packageExists($match[1]) && ($file = $assets_repository->getAssetFileStream($match[1], $match[2])) !== null) {
                    /**
                     * @var MimeTypesMini $mime_types
                     */
                    $mime_types = new MimeTypesMini();
                    $response = $response_factory->createResponse(200);
                    return $response->withBody($file)->withHeader('content-type', $mime_types->getMimeType($match[2]))->withHeader('content-length', $file->getSize());
                } else {
                    app()->set('destroy_response', true);
                    return $response_factory->createResponse(404);
                }
            }
            return $handler->handle($request);
        }
    }
}

namespace Dissonance\Routing {

    use Dissonance\Contracts\Routing\RouteInterface;
    use Dissonance\Contracts\Routing\RouterFactoryInterface;
    use Dissonance\Contracts\Routing\RouterInterface;
    use Dissonance\Contracts\Routing\UrlGeneratorInterface;
    use Dissonance\Container\DIContainerInterface;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Contracts\Routing\AppRoutingInterface;
    use Dissonance\Support\Str;
    use Dissonance\Container\ServiceProvider;
    use Dissonance\Support\Arr;
    use Dissonance\Contracts\App\AppConfigInterface;

    /**
     * Trait HttpMethodsTrait
     * @package Dissonance\Routing
     * @method RouteInterface addRoute($httpMethods, string $uri, $action)
     *
     * @uses \Dissonance\Contracts\Routing\RouterInterface::addRoute()
     */
    trait AddRouteTrait
    {
        /**
         * Add GET(HEAD) method route
         *
         * @param string $uri pattern
         * @param array|string|\Closure $action
         *
         * @return Route
         * @see addRoute()
         *
         */
        public function get(string $uri, $action): RouteInterface
        {
            return $this->addRoute(['GET', 'HEAD'], $uri, $action);
        }

        /**
         * Add HEAD method route
         *
         * @param string $uri pattern
         * @param array|string|\Closure $action
         *
         * @return Route
         * @see addRoute()
         *
         */
        public function head(string $uri, $action): RouteInterface
        {
            return $this->addRoute('HEAD', $uri, $action);
        }

        /**
         * Add POST method route
         *
         * @param string $uri pattern
         * @param array|string|\Closure $action
         *
         * @return Route
         * @see addRoute()
         *
         */
        public function post(string $uri, $action): RouteInterface
        {
            return $this->addRoute('POST', $uri, $action);
        }

        /**
         * Add PUT method route
         *
         * @param string $uri pattern
         * @param array|string|\Closure $action
         *
         * @return Route
         * @see addRoute()
         *
         */
        public function put(string $uri, $action): RouteInterface
        {
            return $this->addRoute('PUT', $uri, $action);
        }

        /**
         * Add DELETE method route
         *
         * @param string $uri pattern
         * @param array|string|\Closure $action
         *
         * @return Route
         * @see addRoute()
         *
         */
        public function delete(string $uri, $action): RouteInterface
        {
            return $this->addRoute('DELETE', $uri, $action);
        }

        /**
         * Add OPTIONS method route
         *
         * @param string $uri pattern
         * @param array|string|\Closure $action
         *
         * @return Route
         * @see addRoute()
         *
         */
        public function options(string $uri, $action): RouteInterface
        {
            return $this->addRoute('OPTIONS', $uri, $action);
        }
    }

    class UrlGenerator implements \Dissonance\Contracts\Routing\UrlGeneratorInterface
    {
        /**
         * The named parameter defaults.
         *
         * @var array
         */
        public $defaultParameters = [];
        /**
         * Characters that should not be URL encoded.
         *
         * @var array
         */
        public $dontEncode = ['%2F' => '/', '%40' => '@', '%3A' => ':', '%3B' => ';', '%2C' => ',', '%3D' => '=', '%2B' => '+', '%21' => '!', '%2A' => '*', '%7C' => '|', '%3F' => '?', '%26' => '&', '%23' => '#', '%25' => '%'];
        protected $base_uri;
        protected $assets_path;
        /**
         * @var Router
         */
        protected $router;

        public function __construct(RouterInterface $router, string $base_uri = '', string $assets_path = 'assets')
        {
            $this->router = $router;
            $this->base_uri = rtrim($base_uri, '/');
            $this->assets_path = $assets_path;
        }

        public function to(string $path = '')
        {
            return $this->base_uri . '/' . $this->preparePath($path);
        }

        public function asset($path = '')
        {
            return $this->to($this->assets_path . '/' . $this->preparePath($path));
        }

        public function route($name, $parameters = [], $absolute = true)
        {
            $route = $this->router->getRoute($name);
            if (!$route) {
                throw new \Exception('Not find route by name: ' . $name);
            }
            $uri = $this->addQueryString($this->replaceRouteParameters($route->getPath(), $parameters), $parameters);
            if (preg_match('/\\{.*?\\}/', $uri)) {
                throw new \Exception('Required  param not replaced: ' . $uri);
            }
            $uri = strtr(rawurlencode($uri), $this->dontEncode);
            $uri = $this->base_uri . '/' . ltrim($uri, '/');
            if ($absolute) {
                $uri = 'http' . ($route->getSecure() ? 's' : '') . '://' . $route->getDomain() . $uri;
            }
            return $uri;
        }

        protected function preparePath($path)
        {
            if (is_array($sc = Str::sc($path))) {
                $path = $sc[0] . '/' . $sc[1];
            }
            return ltrim($path, '/');
        }

        /**
         * Get the query string for a given route.
         *
         * @param array $parameters
         * @return string
         */
        protected function getRouteQueryString(array $parameters)
        {
            // First we will get all of the string parameters that are remaining after we
            // have replaced the route wildcards. We'll then build a query string from
            // these string parameters then use it as a starting point for the rest.
            if (count($parameters) === 0) {
                return '';
            }
            $query = http_build_query($keyed = $this->getStringParameters($parameters), null, '&', PHP_QUERY_RFC3986);
            // Lastly, if there are still parameters remaining, we will fetch the numeric
            // parameters that are in the array and add them to the query string or we
            // will make the initial query string if it wasn't started with strings.
            if (count($keyed) < count($parameters)) {
                $query .= '&' . implode('&', $this->getNumericParameters($parameters));
            }
            return '?' . trim($query, '&');
        }

        /**
         * Get the string parameters from a given list.
         *
         * @param array $parameters
         * @return array
         */
        protected function getStringParameters(array $parameters)
        {
            return array_filter($parameters, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        /**
         * Get the numeric parameters from a given list.
         *
         * @param array $parameters
         * @return array
         */
        protected function getNumericParameters(array $parameters)
        {
            return array_filter($parameters, 'is_numeric', ARRAY_FILTER_USE_KEY);
        }

        /**
         * Replace all of the wildcard parameters for a route path.
         *
         * @param string $path
         * @param array $parameters
         * @return string
         */
        protected function replaceRouteParameters($path, array &$parameters)
        {
            $path = $this->replaceNamedParameters($path, $parameters);
            $path = preg_replace_callback('/\\{.*?\\}/', function ($match) use (&$parameters) {
                return empty($parameters) && !Str::endsWith($match[0], '?}') ? $match[0] : array_shift($parameters);
            }, $path);
            return preg_replace('/\\{.*?\\?\\}/', '', $path);
        }

        /**
         * Replace all of the named parameters in the path.
         *
         * @param string $path
         * @param array $parameters
         * @return string
         */
        protected function replaceNamedParameters($path, &$parameters)
        {
            return preg_replace_callback('/\\{(.*?)\\??\\}/', function ($m) use (&$parameters) {
                if (isset($parameters[$m[1]])) {
                    return Arr::pull($parameters, $m[1]);
                } elseif (isset($this->defaultParameters[$m[1]])) {
                    return $this->defaultParameters[$m[1]];
                }
                return $m[0];
            }, $path);
        }

        /**
         * Add a query string to the URI.
         *
         * @param string $uri
         * @param array $parameters
         * @return mixed|string
         */
        protected function addQueryString($uri, array $parameters)
        {
            // If the URI has a fragment we will move it to the end of this URI since it will
            // need to come after any query string that may be added to the URL else it is
            // not going to be available. We will remove it then append it back on here.
            if (!is_null($fragment = parse_url($uri, PHP_URL_FRAGMENT))) {
                $uri = preg_replace('/#.*/', '', $uri);
            }
            $uri .= $this->getRouteQueryString($parameters);
            return is_null($fragment) ? $uri : $uri . "#{$fragment}";
        }
    }

    class AppsRoutesRepository
    {
        /**
         * @var array |AppRoutingInterface[]
         */
        protected $providers = [];

        public function append(AppRoutingInterface $routing)
        {
            $this->providers[] = $routing;
        }

        public function prepend(AppRoutingInterface $routing)
        {
            array_unshift($this->providers, $routing);
        }

        /**
         * @return array|AppRoutingInterface[]
         */
        public function getProviders()
        {
            return $this->providers;
        }
    }

    /**
     * Class Router
     * @package Dissonance\Routing
     *
     */
    class RouterFactory implements RouterFactoryInterface
    {
        protected $router_class = null;
        protected $routes_loader_callback = null;
        /**
         * @var string|null
         */
        protected $domain = null;
        /**
         * @var DIContainerInterface
         */
        protected $app;

        public function __construct(DIContainerInterface $app, string $router_class, callable $routes_loader_callback, string $domain = null)
        {
            $this->app = $app;
            $this->router_class = $router_class;
            $this->domain = $domain;
            $this->routes_loader_callback = $routes_loader_callback;
        }

        /**
         * @param array|null $params
         * @return RouterInterface
         */
        public function factoryRouter(array $params = []): RouterInterface
        {
            $router = new $this->router_class();
            $router->setRoutesDomain($this->domain);
            return $router;
        }

        public function loadRoutes(RouterInterface $router)
        {
            $callable = $this->routes_loader_callback;
            $callable($router);
        }
    }

    /**
     * Class Router
     * @package Dissonance\Routing
     *
     */
    class Router implements RouterInterface
    {
        use AddRouteTrait;

        public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        /**
         * @used-by group()
         *
         * @var array
         */
        protected $groupStack = [];
        /**
         * @used-by addRoute()
         *
         * @var array = [
         *     'GET' => [
         *        'pattern/test' => Route(),
         *        'pattern/test1' => Route(),
         *        // ....
         *      ],
         *      'POST' => [],
         *       // ....
         * ]
         */
        protected $routes = [];
        /**
         * @see     addRoute()
         * @used-by getRoute()
         *
         * @var array
         */
        protected $named_routes = [];
        protected $domain = '';

        /**
         * Router constructor.
         */
        public function __construct()
        {
            foreach (static::$verbs as $verb) {
                $this->routes[$verb] = [];
            }
        }

        public function setRoutesDomain(string $domain)
        {
            $this->domain = $domain;
        }

        /**
         * Add route
         *
         * @param array |string $httpMethods
         * @param string $uri Uri pattern
         * @param array|string|\Closure $action = [
         *
         *                    'uses' => '\\Module\\Http\\EntityController@edit',//  \Closure | string
         *                     // optional params
         *                     'as' => 'module.entity.edit',
         *                     'module' => 'module_name',
         *                     'middleware' => ['\\Dissonance\\Http\\Middlewares\Auth', '\\Module\\Http\\Middlewares\Test']
         * ]
         *
         * @return Route
         */
        public function addRoute($httpMethods, string $uri, $action): RouteInterface
        {
            $httpMethods = array_map('strtoupper', (array)$httpMethods);
            $route = $this->createRoute($uri, $action, $httpMethods);
            $this->setRoute($route);
            return $route;
        }

        public function setRoute(RouteInterface $route)
        {
            if ($this->domain && !$route->getDomain()) {
                $route->setDomain($this->domain);
            }
            foreach ($route->getAction()['methods'] as $method) {
                $this->routes[$method][$route->getPath()] = $route;
            }
            $name = $route->getName();
            if ($name) {
                $this->named_routes[$name] = $route;
            }
            return $route;
        }

        public $count_routes = 0;

        /**
         * @param string $uri
         * @param array|string|\Closure $action
         * @param array $httpMethods
         * @return Route
         */
        protected function createRoute(string $uri, $action, array $httpMethods)
        {
            $this->count_routes++;
            if (is_string($action) || $action instanceof \Closure) {
                $action = ['uses' => $action];
            }
            if (is_array($action)) {
                if (!empty($this->groupStack)) {
                    $group = end($this->groupStack);
                    // Merge group namespace with controller name
                    if (isset($action['uses']) && is_string($action['uses'])) {
                        $class = $action['uses'];
                        $action['uses'] = isset($group['namespace']) && strpos($class, '\\') !== 0 ? rtrim($group['namespace'], '\\') . '\\' . $class : $class;
                    }
                    // Merge other params (as, prefix, namespace,module)
                    $action = static::mergeAttributes($action, $group);
                    // Merge Uri with prefix
                    $uri = trim(trim(isset($group['prefix']) ? $group['prefix'] : '', '/') . '/' . trim($uri, '/'), '/') ?: '/';
                }
            }
            $action['methods'] = $httpMethods;
            return new Route($uri, $action);
        }

        /**
         * @param $name
         * @return mixed|null
         */
        public function getRoute(string $name): ?RouteInterface
        {
            return $this->named_routes[$name] ?? null;
        }

        /**
         * Create a route group with shared attributes.
         *
         * @param array $attributes
         * @param \Closure|callable| object $routes if object need __invoke method
         *
         * @return void
         */
        public function group(array $attributes, callable $routes)
        {
            $attributes = static::mergeAttributes($attributes, !empty($this->groupStack) ? end($this->groupStack) : []);
            $this->groupStack[] = $attributes;
            $routes($this);
            array_pop($this->groupStack);
        }

        /**
         * @param  $httpMethod
         * @param  $uri
         * @return Route|null
         */
        public function match(string $httpMethod, string $uri): ?RouteInterface
        {
            $uri = trim($uri, '/');
            $httpMethod = strtoupper($httpMethod);
            $all_routes = $this->getRoutes();
            $routes = isset($all_routes[$httpMethod]) ? $all_routes[$httpMethod] : [];
            /**
             * @var Route $route
             */
            foreach ($routes as $route) {
                $vars = [];
                $pattern = \preg_replace('/(^|[^\\.])\\*/ui', '$1.*?', \str_replace(array(' ', '.', '('), array('\\s', '\\.', '(?:'), $route->getPath()));
                if (\preg_match_all('/\\{([a-z_]+):?([^\\}]*)?\\}/ui', $pattern, $match, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
                    $offset = 0;
                    foreach ($match as $m) {
                        $vars[] = $m[1][0];
                        $p = $m[2][0] ? $m[2][0] : '.*?';
                        $pattern = substr($pattern, 0, $offset + $m[0][1]) . '(' . $p . ')' . substr($pattern, $offset + $m[0][1] + strlen($m[0][0]));
                        $offset = $offset + strlen($p) + 2 - strlen($m[0][0]);
                    }
                }
                if (preg_match('!^' . $pattern . '$!ui', $uri, $match)) {
                    if ($vars) {
                        $route = clone $route;
                        array_shift($match);
                        foreach ($vars as $i => $v) {
                            if (isset($match[$i])) {
                                $route->setParam($v, $match[$i]);
                            }
                        }
                    }
                    return $route;
                }
            }
            return null;
        }

        protected static function mergeAttributes(array $new, array $old)
        {
            $as = 'as';
            if (isset($old[$as])) {
                $is_app = substr($old[$as], -2) === '::';
                $new[$as] = $old[$as] . (isset($new[$as]) ? ($is_app ? '' : '.') . $new[$as] : '');
            }
            $module = 'module';
            if (!isset($new[$module]) && isset($old[$module])) {
                $new[$module] = $old[$module];
            }
            $secure = 'secure';
            if (!isset($new[$secure]) && isset($old[$secure])) {
                $new[$secure] = $old[$secure];
            }
            $namespace = 'namespace';
            if (isset($new[$namespace])) {
                $new[$namespace] = isset($old[$namespace]) && strpos($new[$namespace], '\\') !== 0 ? rtrim($old[$namespace], '\\') . '\\' . trim($new[$namespace], '\\') : '\\' . trim($new[$namespace], '\\');
            } elseif (isset($old[$namespace])) {
                $new[$namespace] = $old[$namespace];
            } else {
                $new[$namespace] = null;
            }
            $prefix = 'prefix';
            $old_p = isset($old[$prefix]) ? $old[$prefix] : null;
            $new[$prefix] = isset($new[$prefix]) ? trim($old_p, '/') . '/' . trim($new[$prefix], '/') : $old_p;
            foreach ([$as, $module, $namespace, $prefix] as $v) {
                if (array_key_exists($v, $old)) {
                    unset($old[$v]);
                }
            }
            return array_merge_recursive($old, $new);
        }

        /**
         * @param null|string $httpMethod
         * @return array
         * @uses $routes - see structure
         *
         */
        public function getRoutes(string $httpMethod = null): array
        {
            if ($httpMethod && in_array(strtoupper($httpMethod), static::$verbs)) {
                return $this->routes[strtoupper($httpMethod)];
            }
            return $this->routes;
        }

        /**
         * @param string $settlement
         * @return array|RouteInterface[]
         */
        public function getBySettlement(string $settlement): array
        {
            $routes = [];
            foreach ($this->named_routes as $v) {
                if (preg_match('/^' . preg_quote($settlement, '/') . '/', $v->getName())) {
                    $routes[$v->getName()] = $v;
                }
            }
            return $routes;
        }
    }

    class Route implements RouteInterface
    {
        protected $action = [];
        protected $pattern = '';
        protected $request_params = [];

        /**
         * Route constructor.
         * @param string $uri
         * @param array| \Closure $action
         */
        public function __construct(string $uri, array $action)
        {
            $this->pattern = trim($uri, '/');
            $this->action = $action;
        }

        public function getName(): string
        {
            return $this->action['as'] ?? $this->pattern;
        }

        public function isStatic(): bool
        {
            return strpos($this->getPath(), '{') === false;
        }

        public function getAction(): array
        {
            return $this->action;
        }

        public function getMiddlewares(): array
        {
            return isset($this->action['middleware']) ? $this->action['middleware'] : [];
        }

        public function setDomain(string $domain)
        {
            $this->action['domain'] = $domain;
            return $this;
        }

        public function getSecure(): bool
        {
            return isset($this->action['secure']) ? (bool)$this->action['secure'] : false;
        }

        public function getDomain(): ?string
        {
            return $this->action['domain'] ?? null;
        }

        public function getApp(): ?string
        {
            return $this->action['app'] ?? null;
        }

        public function getPath(): string
        {
            return $this->pattern;
        }

        public function getHandler()
        {
            return $this->action['uses'];
        }

        /**
         * @param $key
         * @param $value
         */
        public function setParam($key, $value)
        {
            $this->request_params[$key] = $value;
        }

        /**
         * @return array
         */
        public function getParams(): array
        {
            return $this->request_params;
        }

        /**
         * @param $name
         *
         * @return string|null
         */
        public function getParam($name)
        {
            return $this->request_params[$name] ?? null;
        }
    }

    class Provider extends ServiceProvider
    {
        public function register(): void
        {
            $this->registerFactory();
            $this->registerRoutesRepository();
            $this->registerRouter();
            $this->registerUriGenerator();
        }

        protected function registerFactory()
        {
            $this->app->singleton(RouterFactoryInterface::class, function (DIContainerInterface $app) {
                $class = $this->getFactoryClass();
                return new $class($app, $this->getRouterClass(), $this->routesLoaderCallback(), $this->app['request']->getUri()->getHost());
            });
        }

        protected function getFactoryClass()
        {
            return RouterFactory::class;
        }

        protected function getRouterClass()
        {
            return Router::class;
        }

        protected function routesLoaderCallback()
        {
            return function (RouterInterface $router) {
                /**
                 * @var AppRoutingInterface $provider
                 * @var RouterInterface $router
                 */
                /// $router = $this->app['router'];
                $providers = \_DS\event($this->app[AppsRoutesRepository::class])->getProviders();
                // TODO: Нужно сделать и прокинуть Auth Middleware!!!
                foreach ($providers as $provider) {
                    $app_id = $provider->getAppConfig()->getId();
                    $router->group(['prefix' => $app_id . '::'], function ($router) use ($provider) {
                        $provider->loadFrontendRoutes($router);
                    });
                    $router->group(['prefix' => 'api/' . $app_id, 'as' => 'api:' . $app_id . '::', 'app' => $app_id], function ($router) use ($provider) {
                        $provider->loadApiRoutes($router);
                    });
                    $router->group(['prefix' => 'backend/' . $app_id, 'as' => 'backend:' . $app_id . '::', 'app' => $app_id], function ($router) use ($provider) {
                        $provider->loadBackendRoutes($router);
                    });
                    $router->group(['as' => 'default:' . $app_id . '::', 'app' => $app_id], function ($router) use ($provider) {
                        $provider->loadDefaultRoutes($router);
                    });
                }
            };
        }

        protected function registerRoutesRepository()
        {
            $this->app->singleton(AppsRoutesRepository::class)->afterResolving(AppsRoutesRepository::class, function (AppsRoutesRepository $repository) {
                /**
                 * @var AppsRoutesRepository $repository
                 */
                return \_DS\event($repository);
            });
        }

        protected function registerRouter()
        {
            $this->app->singleton(RouterInterface::class, function ($app) {
                /**
                 * @var RouterFactoryInterface $f
                 */
                $f = $app[RouterFactoryInterface::class];
                $router = $f->factoryRouter(['name' => 'default']);
                $f->loadRoutes($router);
                return $router;
            }, 'router');
        }

        protected function registerUriGenerator()
        {
            $this->app->singleton(UrlGeneratorInterface::class, function (CoreInterface $app) {
                $base_uri = $app['base_uri'];
                $prefix = $app['config::uri_prefix'];
                if (!empty($prefix)) {
                    $base_uri = rtrim($base_uri, '\\/') . '/' . trim($prefix, '\\/');
                }
                return new UrlGenerator($app['router'], $base_uri, trim($app('config::assets_prefix', 'assets'), '/'));
            }, 'url');
        }

        public function boot(): void
        {
        }

        /**
         * @return RouterFactoryInterface
         */
        protected function getFactory()
        {
            return $this->app->make(RouterFactoryInterface::class);
        }
    }

    class AppRouting implements AppRoutingInterface
    {
        /**
         * @var AppConfigInterface
         */
        protected $app = null;

        public function __construct(AppConfigInterface $app)
        {
            $this->app = $app;
        }

        public function backendRoutes(RouterInterface $router)
        {
        }

        public function frontendRoutes(RouterInterface $router)
        {
        }

        public function apiRoutes(RouterInterface $router)
        {
        }

        public function defaultRoutes(RouterInterface $router)
        {
        }

        public function loadBackendRoutes(RouterInterface $router)
        {
            $options = $this->getRoutingOptions();
            unset($options['prefix']);
            unset($options['as']);
            $router->group($options, $this->getLoadRoutesCallback('backendRoutes'));
        }

        public function loadApiRoutes(RouterInterface $router)
        {
            $options = $this->getRoutingOptions();
            unset($options['prefix']);
            unset($options['as']);
            $router->group($options, $this->getLoadRoutesCallback('apiRoutes'));
        }

        public function loadFrontendRoutes(RouterInterface $router)
        {
            $options = $this->getRoutingOptions();
            unset($options['prefix']);
            unset($options['as']);
            $router->group($options, $this->getLoadRoutesCallback('frontendRoutes'));
        }

        public function loadDefaultRoutes(RouterInterface $router)
        {
            $options = $this->getRoutingOptions();
            unset($options['prefix']);
            unset($options['as']);
            unset($options['app']);
            $router->group($options, $this->getLoadRoutesCallback('defaultRoutes'));
        }

        protected function getRoutingOptions()
        {
            $id = $this->app->getId();
            return ['prefix' => $id, 'app' => $id, 'as' => $id, 'namespace' => $this->getControllersNamespace()];
        }

        protected function loadPrefixRoutes(RouterInterface $router, $function)
        {
            $router->group($this->getRoutingOptions(), $this->getLoadRoutesCallback($function));
        }

        protected function getLoadRoutesCallback($method)
        {
            // TODO: переделать в Switch!
            return function (RouterInterface $router) use ($method) {
                $this->{$method}($router);
            };
        }

        protected function getControllersNamespace(): string
        {
            return $this->app->get('controllers_namespace');
        }

        /**
         * @return AppConfigInterface
         */
        public function getAppConfig()
        {
            return $this->app;
        }
    }

    /**
     * @covers
     */
    class FakeRoutes extends AppRouting
    {
        public function backendRoutes(RouterInterface $router)
        {
            $this->loadTestRoutes($router);
        }

        public function apiRoutes(RouterInterface $router)
        {
            $this->loadTestRoutes($router);
        }

        public function defaultRoutes(RouterInterface $router)
        {
            $this->loadTestRoutes($router);
        }

        public function frontendRoutes(RouterInterface $router)
        {
            $this->loadTestRoutes($router);
        }

        protected function loadTestRoutes($router)
        {
            /**
             * @var \Dissonance\Contracts\Routing\RouterInterface $router
             */
            //$router = $this->router;
            // base routes /test(n+1)
            $this->generateTestRoutes($router, 'test');
            //  group base  /test_group(n+1)
            $router->group([], function (\Dissonance\Contracts\Routing\RouterInterface $router) {
                $this->generateTestRoutes($router, 'test_group');
            });
            //  group with params
            $router->group(['as' => 'group_prefix', 'prefix' => 'group_prefix', 'namespace' => 'Prefix', 'module' => 'prefix_module', 'middleware' => ['middleware1']], function (RouterInterface $router) {
                $this->generateTestRoutes($router, 'test_groupcc');
                $router->group(['as' => 'subgroup', 'prefix' => 'subgroup', 'namespace' => 'Subgroup\\', 'module' => 'subgroup', 'middleware' => ['middleware1', 'middleware2']], function (RouterInterface $router) {
                    $this->generateTestRoutes($router, 'test_subgroup');
                });
                $router->group(['as' => 'subgroup_base_namespace', 'prefix' => 'subgroup_base_namespace', 'namespace' => '\\Subgroup\\', 'module' => 'subgroup_base_namespace'], function (RouterInterface $router) {
                    $this->generateTestRoutes($router, 'test_subgroup');
                });
            });
        }

        protected function generateTestRoutes(RouterInterface $router, $name, $module = null)
        {
            for ($i = 1; $i < 30; $i++) {
                $route_uri = $name . $i;
                $router->get($route_uri, $this->prepareTestRouteParams(['uses' => $route_uri, 'as' => $route_uri]));
            }
        }

        protected function prepareTestRouteParams($params)
        {
            foreach (['path', 'as', 'uses', 'module'] as $v) {
                if (!array_key_exists($v, $params)) {
                    $params[$v] = null;
                } elseif ($v == 'uses') {
                    $params[$v] = 'Controller@' . $params[$v];
                } elseif ($v == 'as') {
                    $params[$v] = 'name_' . $params[$v];
                }
            }
            return $params;
        }

        protected function fillRouteTestParams($params)
        {
            foreach (['path', 'as', 'uses', 'module'] as $v) {
                if (!array_key_exists($v, $params)) {
                    $params[$v] = null;
                }
            }
            return $params;
        }
    }
}

namespace Dissonance\SettlementsRouting {

    use Dissonance\Container\CachedContainerInterface;
    use Dissonance\Routing\Router;
    use Dissonance\Contracts\App\AppConfigInterface;
    use Dissonance\Contracts\Routing\AppRoutingInterface;
    use Dissonance\Contracts\Routing\RouterFactoryInterface;
    use Dissonance\Contracts\Routing\RouterInterface;
    use Dissonance\Routing\RouterFactory;
    use Dissonance\Contracts\Routing\RouteInterface;
    use Dissonance\Routing\AppsRoutesRepository;
    use Dissonance\Support\Collection;
    use Dissonance\Support\Arr;
    use Dissonance\Container\DIContainerInterface;

    interface SettlementsInterface
    {
    }

    /**
     * Trait NamedRouterTrait
     * @package Dissonance\Routing
     *
     */
    trait NamedRouterTrait
    {
        protected $name = '';

        public function setName(string $name)
        {
            $this->name = $name;
        }

        public function getName(): string
        {
            return $this->name;
        }
    }

    /**
     * Interface NamedRouterInterface
     * @package Dissonance\Routing
     */
    interface NamedRouterInterface
    {
    }

    /**
     * Interface NamedRouterInterface
     * @package Dissonance\Routing
     */
    interface LazyRouterInterface extends NamedRouterInterface
    {
    }

    class Settlement
    {
        protected $config = [];
        protected $base_prefix = null;
        protected $path = '/';

        /**
         * Settlement constructor.
         *
         * @param array $config = [
         *      'prefix' => '/backend/', // Require parameter
         *      'router' => 'backend', // Require parameter
         *       // optional params
         *      'settings' => [],
         *      'locale' => ''...
         *    ];
         *
         * @param null|string $base_prefix
         *
         */
        public function __construct(array $config, $base_prefix = null)
        {
            $this->config = $config;
            $this->path = '/' . trim($config['prefix']);
            // $this->setBasePrefix($base_prefix);
        }

        public function getPath(): string
        {
            return $this->path;
        }

        public function getBasePrefix()
        {
            return $this->base_prefix;
        }

        /*  public function setBasePrefix($prefix)
            {
                $this->base_prefix = $prefix;
                $base_prefix = $this->base_prefix ? Settlements::normalizePrefix($this->base_prefix):'/';
                $this->path = $base_prefix.ltrim(Settlements::normalizePrefix($this->get('prefix','/')),'\\/');
            }*/
        public function getUriWithoutSettlement(string $uri): string
        {
            return preg_replace('/^' . preg_quote($this->getPath(), '/') . '/uDs', '/', $uri);
        }

        public function getRouter(): string
        {
            return $this->get('router');
        }

        /**
         * Проверяет соответствие пути к префиксу поселения
         * Например:
         * prefix = '/test/'
         * валидные пути
         * /test/
         * /test/data
         * /test/data/data....
         *
         * @param string $path
         *
         * @return bool
         */
        public function validatePath(string $path)
        {
            return (bool)preg_match('/^' . preg_quote($this->getPath(), '/') . '.*/uDs', $path, $r);
        }

        /**
         * @param string|null $name
         * @param null $default
         *
         * @return array|mixed
         */
        public function get(string $name = null, $default = null)
        {
            return !$name ? $this->config : (isset($this->config[$name]) ? $this->config[$name] : $default);
        }
    }

    class Provider extends \Dissonance\Routing\Provider
    {
        public function boot(): void
        {
        }

        public function register(): void
        {
            parent::register();
            if ($this->app instanceof CachedContainerInterface) {
                // $this->app->cached(SettlementsInterface::class);
            }
            if (!$this->app->bound(SettlementsInterface::class)) {
                $this->app->singleton(SettlementsInterface::class, function ($app) {
                    $settlements = $app('config::settlements');
                    if (empty($settlements)) {
                        $settlements = [];
                        /**
                         * @var AppConfigInterface $item
                         */
                        foreach ($app['apps']->enabled() as $item) {
                            $id = $item->getId();
                            $settlements[] = ['prefix' => $id, 'router' => $id];
                            $settlements[] = ['prefix' => 'backend/' . $id, 'router' => 'backend:' . $id];
                            $settlements[] = ['prefix' => 'api/' . $id, 'router' => 'api:' . $id];
                        }
                    }
                    return new Settlements($settlements);
                }, 'settlements');
            }
            $this->app->alias(SettlementsInterface::class, 'settlements');
        }

        protected function registerRouter()
        {
            $this->app->singleton(RouterInterface::class, function (DIContainerInterface $app) {
                return new SettlementsRouter($this->getFactory(), $app['settlements']);
            }, 'router');
        }

        protected function getFactoryClass()
        {
            return RouterNamedFactory::class;
        }

        protected function getRouterClass()
        {
            return RouterLazy::class;
        }

        protected function routesLoaderCallback()
        {
            $app = $this->app;
            return function (RouterInterface $router) use ($app) {
                /**
                 * @var SettlementsRouter $routing
                 * @var RouterInterface|NamedRouterInterface $router
                 */
                $router_name = $router->getName();
                // $routing = $app['router'];
                //$routing->selectRouter($router_name);
                //if (!$router->isLoadedRoutes()) {
                /**
                 * @var AppRoutingInterface $provider
                 */
                foreach ($app[AppsRoutesRepository::class]->getProviders() as $provider) {
                    $app_id = $provider->getAppConfig()->getId();
                    if ($router_name === 'backend:' . $app_id) {
                        // TODO: Нужно сделать и прокинуть Auth Middleware!!!
                        $provider->loadBackendRoutes($router);
                    } elseif ($router_name === 'api:' . $app_id) {
                        $provider->loadApiRoutes($router);
                    } elseif ($router_name === 'default') {
                        $router->group(['as' => $app_id, 'app' => $app_id], function ($router) use ($provider) {
                            $provider->loadDefaultRoutes($router);
                        });
                    } elseif ($router_name === strtolower($provider->getAppConfig()->getId())) {
                        $provider->loadFrontendRoutes($router);
                    }
                }
                //   }
                // $routing->selectPreviousRouter();
            };
        }
    }

    class SettlementsRouter extends Router implements RouterInterface
    {
        const DELIMITER = '::';
        const DEFAULT_ROUTER = 'default';
        /**
         * @var \Closure
         */
        protected $dispatch_callback = null;
        /**
         * @var RouterLazy[]|\Dissonance\Contracts\Routing\RouterInterface[]
         */
        protected $routers = [];
        /**
         * @var Router|\Dissonance\Contracts\Routing\RouterInterface|null
         */
        protected $current_router = null;
        /**
         * @var string
         */
        protected $current_router_name = null;
        protected $previous_collections_names = [];
        /**
         * @var Settlements|Settlement[]
         */
        protected $settlements = null;
        protected $router_factory = null;

        public function __construct(RouterFactoryInterface $routerFactory, Settlements $settlements)
        {
            $this->router_factory = $routerFactory;
            /**
             * @var Settlement[]|Settlements $settlements
             */
            $this->settlements = $settlements;
            // $this->router(static::DEFAULT_ROUTER);
            foreach ($settlements as $settlement) {
                // $this->router($settlement->getRouter());
            }
            // $this->selectRouter(static::DEFAULT_ROUTER);
        }

        public function group(array $attributes, callable $routes)
        {
            $this->current_router->group($attributes, $routes);
        }

        public function addRoute($httpMethods, string $uri, $action): RouteInterface
        {
            return $this->current_router->addRoute($httpMethods, $uri, $action);
        }

        public function getRoute(string $name): ?RouteInterface
        {
            $delimiter = static::DELIMITER;
            $router = static::DEFAULT_ROUTER;
            $settlement = null;
            if (false !== strpos($name, $delimiter)) {
                $router = strstr($name, $delimiter, true);
                $name = substr(strstr($name, $delimiter), 2);
                $settlement = $this->settlements->getByRouter($router);
            }
            $route = $this->router($router)->getRoute($name);
            if ($route && $settlement) {
                return new SettlementRouteDecorator($route, $settlement);
            }
            return $route;
        }

        public function getRoutes(string $httpMethod = null): array
        {
            $all_routes = [];
            foreach ($this->settlements as $settlement) {
                $routes = $this->router($settlement->getRouter())->getRoutes($httpMethod);
                foreach ($routes as $method => $collection) {
                    $collection = new Collection($collection);
                    $settlement_collection = $collection->map(function (RouteInterface $item, $key) use ($settlement) {
                        return [new SettlementRouteDecorator($item, $settlement), $settlement->getPath() . $key];
                    });
                    if (!isset($all_routes[$method])) {
                        $all_routes[$method] = new Collection();
                    }
                    $all_routes[$method]->merge($settlement_collection);
                }
            }
            return $all_routes;
        }

        /**
         * @param string $settlement
         * @return array|RouteInterface[]
         */
        public function getBySettlement(string $settlement): array
        {
            $routes = [];
            if ($sett = $this->settlements->getByRouter($settlement)) {
                /**
                 * @var RouteInterface $v
                 */
                foreach ($this->router($settlement)->getRoutes('get') as $v) {
                    $routes[$v->getName()] = new SettlementRouteDecorator($v, $sett);
                }
            }
            return $routes;
        }

        public function selectRouter(string $name = null): \Dissonance\Contracts\Routing\RouterInterface
        {
            $name = $this->castRouterName($name);
            if ($name === $this->current_router_name) {
                return $this;
            }
            if (!empty($this->previous_collections_names) && $name === $this->getLastPreviousRouterName()) {
                return $this->selectPreviousRouter();
            }
            if ($this->current_router_name !== null) {
                $this->previous_collections_names[] = $this->current_router_name;
            }
            $this->current_router = $this->router($name);
            $this->current_router_name = $name;
            return $this;
        }

        public function collection(string $name, callable $callback)
        {
            $current_router = $this->getCurrentRouterName();
            $this->selectRouter($name);
            if (is_callable($callback)) {
                $callback($this);
            }
            $this->selectRouter($current_router);
        }

        public function getCurrentRouterName(): string
        {
            return $this->current_router_name;
        }

        protected function getLastPreviousRouterName()
        {
            return !empty($this->previous_collections_names) ? end($this->previous_collections_names) : null;
        }

        public function selectPreviousRouter()
        {
            $name = $this->castRouterName(array_pop($this->previous_collections_names) ?? '');
            $this->selectRouter($name);
            array_pop($this->previous_collections_names);
            return $this;
        }

        protected function castRouterName(string $name = null)
        {
            if (in_array($name, ['', null])) {
                $name = static::DEFAULT_ROUTER;
            }
            return strtolower($name);
        }

        public function hasRouter($name)
        {
            return !is_null($this->settlements->getByRouter(\strtolower($name)));
        }

        public function getRouters()
        {
            return $this->routers;
        }

        /**
         * @param $name
         * @return Router
         */
        public function router($name = null): \Dissonance\Contracts\Routing\RouterInterface
        {
            $name = $this->castRouterName($name);
            if (!isset($this->routers[$name])) {
                $this->routers[$name] = $router = $this->router_factory->factoryRouter(['name' => $name]);
                if (method_exists($router, 'setName')) {
                    $router->setName($name);
                }
            }
            return $this->routers[$name];
        }

        /**
         * @param $httpMethod
         * @param $uri
         * @return bool | RouteInterface
         */
        public function match(string $httpMethod, string $uri): ?RouteInterface
        {
            $uri = '/' . ltrim($uri, '\\/');
            $route = null;
            $settlement = $this->settlements->getByUrl($uri);
            if ($settlement) {
                $route = $this->router($settlement->getRouter())->match($httpMethod, $settlement->getUriWithoutSettlement($uri));
            }
            if (!$route) {
                $route = $this->router()->match($httpMethod, $uri);
            }
            return $route;
        }
    }

    /**
     * Class Settlements
     * @package Dissonance\Services
     *
     * @property Settlement[] $items
     */
    class Settlements extends Collection implements SettlementsInterface
    {
        /**
         * Settlements constructor.
         *
         * @param array $items = [
         *    [
         *      'prefix' => '/backend/',
         *      'router' => 'backend',
         *       // optional params
         *      'settings' => [],
         *      'locale' => ''...
         *    ],
         *    [
         *      'prefix' => '/api/',
         *      'router' => 'api',
         *       // .....
         *    ],
         *    [
         *      'prefix' => '/module1_baseurl/',
         *      'router' => 'module1',
         *       // .....
         *    ]
         * ];
         *
         * @param string|null $base_prefix base url prefix before settlements
         *
         *
         */
        public function __construct($items = [], $base_prefix = null)
        {
            foreach ($items as &$v) {
                $v = is_array($v) ? new Settlement($v, $base_prefix) : $v;
            }
            unset($v);
            // php 8 delete
            parent::__construct($items);
        }

        public function addSettlement(array $data)
        {
            parent::add($settlement = new Settlement($data));
            return $settlement;
        }

        public function getByRouter($router)
        {
            return $this->getByKey('router', $router);
        }

        public function getByUrl($url): ?Settlement
        {
            $path = $this->getPathByUrl($url);
            return $this->first(function ($settlement) use ($path) {
                /**
                 * @var Settlement $settlement
                 */
                return $settlement->validatePath($path);
            });
        }

        /**
         * @param string $key
         * @param $value
         * @param bool $all
         * @return Collection|Settlement[]|Settlement|null
         */
        public function getByKey(string $key, $value, $all = false)
        {
            $callback = function ($settlement) use ($key, $value) {
                /**
                 * @var Settlement $settlement
                 */
                return $settlement->get($key) === $value;
            };
            return $all ? $this->filter($callback) : $this->first($callback);
        }

        public static function normalizePrefix(string $prefix): string
        {
            $prefix = trim($prefix, ' \\/');
            return $prefix == '' ? '/' : '/' . $prefix . '/';
        }

        public function getPathByUrl(string $url): string
        {
            return preg_replace('~(^((.+?\\..+?)[/])|(^(https?://)?localhost(:\\d+)?[/]))(.*)~i', '/', $url);
        }
    }

    class SettlementRouteDecorator implements RouteInterface
    {
        /**
         * @var RouteInterface
         */
        protected $route = null;
        /**
         * @var Settlement|null
         */
        protected $settlement = null;
        /**
         * @var string
         */
        protected $path;

        public function __construct(RouteInterface $route, Settlement $settlement)
        {
            $this->route = $route;
            $this->settlement = $settlement;
            $this->path = $this->settlement->getPath() . '/' . ltrim($this->route->getPath(), '\\/');
        }

        public function getName(): string
        {
            return $this->route->getName();
        }

        public function isStatic(): bool
        {
            return $this->route->isStatic();
        }

        public function getAction(): array
        {
            return $this->route->getAction();
        }

        public function getMiddlewares(): array
        {
            return $this->route->getMiddlewares();
        }

        public function getPath(): string
        {
            return $this->path;
        }

        public function getHandler()
        {
            return $this->route->getHandler();
        }

        public function setParam($key, $value)
        {
            return $this->route->setParam($key, $value);
        }

        public function getParam($key)
        {
            return $this->route->getParam($key);
        }

        public function getParams(): array
        {
            return $this->route->getParams();
        }

        public function getSecure(): bool
        {
            return $this->route->getSecure();
        }

        public function getDomain(): ?string
        {
            return $this->route->getDomain();
        }

        public function setDomain(string $domain)
        {
            return $this->route->setDomain($domain);
        }
    }

    /**
     * Class Router
     * @package Dissonance\Routing
     *
     */
    class RouterNamedFactory extends RouterFactory
    {
        public function factoryRouter(array $params = []): RouterInterface
        {
            $factory = $this->app[RouterFactoryInterface::class];
            /// Олучаем реальную файбрику через контейнер
            $router = new $this->router_class($factory);
            $router->setRoutesDomain($this->domain);
            if (isset($params['name']) && $router instanceof NamedRouterInterface) {
                $router->setName($params['name']);
            }
            return $router;
        }
    }

    /**
     * Class RouterLazy
     * @package Dissonance\Routing
     * @property  RouterNamedFactory $router_factory
     */
    class RouterLazy extends Router implements NamedRouterInterface, LazyRouterInterface
    {
        use NamedRouterTrait;

        /**
         * @var bool
         */
        protected $loaded_routes = false;
        protected $router_factory = null;

        public function __construct(RouterFactoryInterface $routerFactory)
        {
            $this->router_factory = $routerFactory;
            parent::__construct();
        }

        /**
         * @param $name
         * @return mixed|null
         */
        public function getRoute(string $name): ?RouteInterface
        {
            $this->loadRoutes();
            return parent::getRoute($name);
        }

        public function getBySettlement(string $settlement): array
        {
            $this->loadRoutes();
            return parent::getBySettlement($settlement);
        }

        /**
         * @param string|null $httpMethod
         * @return array
         */
        public function getRoutes(string $httpMethod = null): array
        {
            $this->loadRoutes();
            return parent::getRoutes($httpMethod);
        }

        public function isLoadedRoutes(): bool
        {
            return $this->loaded_routes;
        }

        /**
         *
         */
        public function loadRoutes()
        {
            if (!$this->loaded_routes) {
                $this->loaded_routes = true;
                $this->router_factory->loadRoutes($this);
            }
        }

        public function __sleep()
        {
            return ['named_routes', 'routes', 'loaded_routes', 'name', 'domain'];
        }

        public function __wakeup()
        {
            $this->loaded_routes = true;
        }
    }
}

namespace Dissonance\CacheRouting {

    use Dissonance\Contracts\BootstrapInterface;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Contracts\Routing\RouterFactoryInterface;
    use Dissonance\Contracts\Routing\RouteInterface;
    use Dissonance\Contracts\Routing\RouterInterface;
    use Dissonance\SettlementsRouting\LazyRouterInterface;
    use Dissonance\SettlementsRouting\NamedRouterInterface;
    use Closure;
    use Dissonance\Routing\AddRouteTrait;
    use Dissonance\SimpleCacheFilesystem\SimpleCacheInterface;

    class RouterCacheFactory implements RouterFactoryInterface
    {
        /**
         * @var RouterFactoryInterface
         */
        protected $factory = null;
        /**
         * @var SimpleCacheInterface|null
         */
        protected $cache = null;

        public function __construct(RouterFactoryInterface $factory, SimpleCacheInterface $cache = null)
        {
            $this->factory = $factory;
            $this->cache = $cache;
        }

        public function factoryRouter(array $params = []): RouterInterface
        {
            if ($this->cache) {
                $cache_key = 'router_' . \md5(\serialize($params));
                $data = $this->cache->get($cache_key, $t = \uniqid());
                if ($data === $t) {
                    $router = $this->factory->factoryRouter($params);
                    $class = $router instanceof LazyRouterInterface ? CacheLazyRouterDecorator::class : CacheRouterDecorator::class;
                    $data = new $class($this, $this->factory->factoryRouter($params), $cache_key);
                }
                return $data;
            }
            return $this->factory->factoryRouter($params);
        }

        public function loadRoutes(RouterInterface $router)
        {
            $this->factory->loadRoutes($router);
            if ($this->cache && $router instanceof CacheRouterDecorator && $router->isAllowedCache()) {
                $this->cache->set($router->getCacheKey(), $router->getRealInstance());
            }
        }
    }

    class CacheRouterDecorator implements RouterInterface
    {
        use AddRouteTrait;

        /**
         * @var RouterFactoryInterface
         */
        protected $factory = null;
        /**
         * @var RouterInterface
         */
        protected $router = null;
        protected $cache_key = '';
        protected $allowed_cache = true;

        public function __construct(RouterFactoryInterface $factory, RouterInterface $router, string $cache_key)
        {
            $this->factory = $factory;
            $this->router = $router;
            $this->cache_key = $cache_key;
        }

        public function getCacheKey(): string
        {
            return $this->cache_key;
        }

        public function isAllowedCache()
        {
            return $this->allowed_cache;
        }

        public function getRealInstance(): RouterInterface
        {
            return $this->router;
        }

        public function setRoutesDomain(string $domain)
        {
            $this->call(__FUNCTION__, func_get_args());
        }

        public function addRoute($httpMethods, string $uri, $action): RouteInterface
        {
            $this->checkCallbacks($action);
            return $this->call(__FUNCTION__, func_get_args());
        }

        private function checkCallbacks($data)
        {
            if (is_array($data)) {
                if (isset($data['middleware'])) {
                    foreach ((array)$data['middleware'] as $v) {
                        if ($v instanceof Closure) {
                            $this->allowed_cache = false;
                        }
                    }
                }
                if (isset($data['uses']) && $data['uses'] instanceof Closure) {
                    $this->allowed_cache = false;
                }
            } elseif ($data instanceof Closure) {
                $this->allowed_cache = false;
            }
        }

        public function group(array $attributes, callable $routes)
        {
            $this->checkCallbacks($attributes);
            $this->router->group($attributes, function ($real_router) use ($routes) {
                $routes($this);
            });
        }

        public function getRoute(string $name): ?RouteInterface
        {
            return $this->call(__FUNCTION__, func_get_args());
        }

        public function getBySettlement(string $settlement): array
        {
            return $this->call(__FUNCTION__, func_get_args());
        }

        public function getRoutes(string $httpMethod = null): array
        {
            return $this->call(__FUNCTION__, func_get_args());
        }

        public function match(string $httpMethod, string $uri): ?RouteInterface
        {
            return $this->call(__FUNCTION__, func_get_args());
        }

        protected function call($method, $parameters)
        {
            return call_user_func_array([$this->router, $method], $parameters);
        }
    }

    class CacheLazyRouterDecorator extends CacheRouterDecorator implements RouterInterface, NamedRouterInterface, LazyRouterInterface
    {
        protected $loaded = false;

        public function isLoadedRoutes(): bool
        {
            return $this->loaded;
        }

        public function loadRoutes()
        {
            if (!$this->loaded) {
                $this->factory->loadRoutes($this);
                $this->loaded = true;
            }
        }

        public function getRoute(string $name): ?RouteInterface
        {
            $this->loadRoutes();
            return parent::getRoute($name);
        }

        public function getRoutes(string $httpMethod = null): array
        {
            $this->loadRoutes();
            return parent::getRoutes($httpMethod);
        }

        public function getBySettlement(string $settlement): array
        {
            $this->loadRoutes();
            return parent::getBySettlement($settlement);
        }

        public function match(string $httpMethod, string $uri): ?RouteInterface
        {
            $this->loadRoutes();
            return parent::match($httpMethod, $uri);
            // TODO: Change the autogenerated stub
        }

        public function setName(string $name)
        {
            $this->call(__FUNCTION__, func_get_args());
        }

        public function getName(): string
        {
            return $this->call(__FUNCTION__, func_get_args());
        }
    }

    class Bootstrap implements BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $app->extend(RouterFactoryInterface::class, function ($factory) use ($app) {
                // return $factory;
                return new RouterCacheFactory($factory, $app('cache'));
            });
        }
    }
}

namespace Dissonance\Session {

    use Dissonance\Container\ServiceProvider;
    use Dissonance\Contracts\Session\SessionStorageInterface;
    use Dissonance\Container\Traits\ArrayAccessTrait;

    class SessionStorageNative implements SessionStorageInterface
    {
        use ArrayAccessTrait;

        protected $items = [];
        protected $started = false;
        /**
         * @var string|null
         */
        protected $session_namespace;

        public function __construct(string $session_namespace = null)
        {
            $this->session_namespace = $session_namespace;
        }

        /**
         * Start the session, reading the data from a handler.
         *
         * @return bool
         */
        public function start()
        {
            if ($this->started) {
                return true;
            }
            if (\PHP_SESSION_ACTIVE !== \session_status()) {
                // ok to try and start the session
                if (!\session_start()) {
                    throw new \RuntimeException('Failed to start the session');
                }
            }
            $this->loadSession();
            $this->started = true;
            if (!$this->has('_token')) {
                $this->regenerateToken();
            }
            return true;
        }

        /**
         * Load the session data from the handler.
         *
         * @return void
         */
        protected function loadSession()
        {
            $session_namespace = $this->session_namespace;
            $session =& $_SESSION;
            if ($session_namespace) {
                if (!isset($_SESSION[$session_namespace])) {
                    $_SESSION[$session_namespace] = [];
                }
                $this->items =& $_SESSION[$session_namespace];
            } else {
                $this->items =& $_SESSION;
            }
        }

        public function has(string $key): bool
        {
            $this->start();
            return isset($this->items[$key]);
            // todo: may be array_key_exists???
        }

        public function get(string $key)
        {
            $this->start();
            return $this->items[$key] ?? null;
        }

        public function set($key, $value): void
        {
            $this->start();
            $this->items[$key] = $value;
        }

        public function delete(string $key): bool
        {
            $this->start();
            unset($this->items[$key]);
            return true;
        }

        public function clear()
        {
            $this->items = [];
        }

        public function destroy()
        {
            return \session_destroy();
        }

        /**
         * Save the session data to storage.
         *
         * @return bool
         */
        public function save()
        {
            // native save
            return true;
        }

        /**
         * Determine if the session has been started.
         *
         * @return bool
         */
        public function isStarted()
        {
            return $this->started;
        }

        /**
         * Get the name of the session.
         *
         * @return string
         */
        public function getName()
        {
            return \session_name();
        }

        /**
         * Get the current session ID.
         *
         * @return string
         */
        public function getId()
        {
            return \session_id();
        }

        /**
         * Set the session ID.
         *
         * @param string $id
         * @return void
         */
        public function setId(string $id)
        {
            if (\session_status() === \PHP_SESSION_ACTIVE || !\ctype_alnum($id) || !\strlen($id) === 40) {
                throw new \Exception('Session active or invalid id');
            }
            \session_id($id);
        }

        /**
         * Get the CSRF token value.
         *
         * @return string
         */
        public function token()
        {
            return !$this->has('_token') ? $this->regenerateToken() : $this->get('_token');
        }

        /**
         * Regenerate the CSRF token value.
         *
         * @return string
         */
        public function regenerateToken()
        {
            $this->set('_token', $token = \md5(\uniqid('', true)));
            return $token;
        }
    }

    class NativeProvider extends ServiceProvider
    {
        public function register(): void
        {
            $this->app->singleton(SessionStorageInterface::class, function ($app) {
                return new SessionStorageNative();
            }, 'session');
        }
    }
}

namespace Dissonance\SimpleCacheFilesystem {

    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Events\CacheClear;
    use Psr\SimpleCache\CacheInterface;
    use Psr\SimpleCache\CacheException;

    interface SimpleCacheInterface extends \Psr\SimpleCache\CacheInterface
    {
    }

    class InvalidArgumentException extends \Exception implements \Psr\SimpleCache\InvalidArgumentException
    {
    }

    class Bootstrap implements \Dissonance\Contracts\BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $storage_path = $app('config::storage_path');
            if (is_dir($storage_path)) {
                $app['cache_path'] = $storage_path . '/cache';
                $app->singleton(CacheInterface::class, function (CoreInterface $app) {
                    return new SimpleCache(rtrim($app->get('cache_path'), '\\/') . '/core', $app('config::cache_time', 3600));
                }, 'cache');
                $app['listeners']->add(CacheClear::class, function ($event) use ($app) {
                    $app[CacheInterface::class]->clear();
                });
                $app->alias(CacheInterface::class, \Dissonance\SimpleCacheFilesystem\SimpleCacheInterface::class);
            }
        }
    }

    class SimpleCache implements SimpleCacheInterface
    {
        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @var string
         */
        protected $cache_directory;
        protected $ttl = 600;

        /**
         * Cache constructor.
         * @param string $cache_directory
         * @param int $default_ttl
         * @throws \Dissonance\SimpleCacheFilesystem\Exception
         */
        public function __construct(string $cache_directory, int $default_ttl = 600)
        {
            if (!is_dir($cache_directory)) {
                $uMask = umask(0);
                @mkdir($cache_directory, 0755, true);
                umask($uMask);
            }
            if (!is_dir($cache_directory) || !is_writable($cache_directory)) {
                throw new Exception("The cache path ({$cache_directory}) is not writeable.");
            }
            $this->cache_directory = \rtrim($cache_directory, '\\/');
            $this->ttl = $default_ttl;
        }

        /**
         * @inheritdoc
         *
         * @throws Exception
         * @throws InvalidArgumentException
         */
        public function remember($key, \Closure $value, $ttl = null)
        {
            $data = $this->get($key, $u = \uniqid());
            if ($data === $u) {
                $data = $value();
                $this->set($key, $data, $ttl);
            }
            return $data;
        }

        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @param string $key
         * @param null $default
         * @return mixed|null
         * @throws Exception
         * @throws \Dissonance\SimpleCacheFilesystem\InvalidArgumentException
         */
        public function get($key, $default = null)
        {
            $file = $this->getKeyFilePath($key);
            if (file_exists($file) && \is_readable($file) && ($data = @\unserialize(file_get_contents($file)))) {
                if (!empty($data) && isset($data['ttl']) && $data['ttl'] >= time() + 1) {
                    return $data['data'];
                } else {
                    $this->delete($key);
                }
            }
            return $default;
        }

        /**
         * @param string $key
         * @param mixed $value
         * @param int $ttl
         * @return bool
         * @throws \Dissonance\SimpleCacheFilesystem\InvalidArgumentException
         */
        public function set($key, $value, $ttl = null)
        {
            $file = $this->getKeyFilePath($key);
            if ($data = \serialize(['ttl' => time() + (is_int($ttl) ? $ttl : $this->ttl), 'data' => $value])) {
                return \file_put_contents($file, $data) !== false;
            }
            return false;
        }

        /**
         * @param string $key
         * @return bool
         * @throws \Dissonance\SimpleCacheFilesystem\Exception|\Dissonance\SimpleCacheFilesystem\InvalidArgumentException
         */
        public function delete($key)
        {
            $file = $this->getKeyFilePath($key);
            if (file_exists($file)) {
                if (is_file($file) && !@unlink($file)) {
                    throw new Exception("Can't delete the cache file ({$file}).");
                }
                clearstatcache(true, $file);
            }
            return true;
        }

        /**
         * @return bool
         */
        public function clear()
        {
            // todo: может сделать через glob? что быстрее? foreach(glob($dir . '/*', GLOB_NOSORT | GLOB_BRACE) as $File)
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->cache_directory, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
            $result = true;
            /**
             * @var \SplFileInfo $file
             */
            foreach ($files as $file) {
                $file_path = $file->getRealPath();
                $func = $file->isDir() ? 'rmdir' : 'unlink';
                if (!$func($file_path)) {
                    $result = false;
                }
                clearstatcache(true, $file_path);
            }
            return $result;
        }

        /**
         * @param iterable $keys
         * @param null $default
         * @return array|iterable
         * @throws \Dissonance\SimpleCacheFilesystem\Exception
         * @throws \Dissonance\SimpleCacheFilesystem\InvalidArgumentException
         */
        public function getMultiple($keys, $default = null)
        {
            $result = [];
            foreach ($this->getValidatedIterable($keys) as $v) {
                $result[$v] = $this->get($v, $default);
            }
            return $result;
        }

        /**
         * @param iterable $values
         * @param null $ttl
         * @return bool
         * @throws \Dissonance\SimpleCacheFilesystem\InvalidArgumentException|\Dissonance\SimpleCacheFilesystem\Exception
         */
        public function setMultiple($values, $ttl = null)
        {
            $result = true;
            foreach ($this->getValidatedIterable($values) as $k => $v) {
                if (!$this->set($k, $v, $ttl)) {
                    $result = false;
                }
            }
            return $result;
        }

        /**
         * @param iterable $keys
         * @return bool
         * @throws Exception
         * @throws InvalidArgumentException
         */
        public function deleteMultiple($keys)
        {
            $result = true;
            foreach ($this->getValidatedIterable($keys) as $v) {
                if (!$this->delete($v)) {
                    $result = false;
                }
            }
            return $result;
        }

        /**
         * @param string $key
         * @return bool
         */
        public function has($key)
        {
            $file = $this->getKeyFilePath($key);
            return \file_exists($file) && \is_readable($file);
        }

        /**
         * @param string $key
         * @return string
         * @throws InvalidArgumentException
         */
        protected function getKeyFilePath(string $key)
        {
            $this->validateKey($key);
            return $this->cache_directory . DIRECTORY_SEPARATOR . \md5($key) . '.cache';
        }

        /**
         * @param string $key
         * @throws InvalidArgumentException
         */
        protected function validateKey(string $key)
        {
            if (false === preg_match('/[^A-Za-z_\\.0-9]/i', $key)) {
                throw new InvalidArgumentException('Key is not valid string!');
            }
        }

        /**
         * @param $keys
         * @return mixed
         * @throws InvalidArgumentException
         */
        protected function getValidatedIterable($keys)
        {
            if (!\is_iterable($keys)) {
                throw new InvalidArgumentException('Keys is not Iterable!');
            }
            return $keys;
        }
    }

    /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
     * Class Exception
     * @package Dissonance\SimpleCacheFilesystem
     */
    class Exception extends \Exception implements CacheException
    {
    }
}

namespace Dissonance\ViewBlade {
    # Use: Blade::compile( $string);
    # visit: http://araujo.cc
    # original class from http://cutlasswp.com
    use Dissonance\Packages\Contracts\TemplateCompilerInterface;
    use Dissonance\Contracts\CoreInterface;
    use Dissonance\Packages\TemplateCompiler;

    class Bootstrap implements \Dissonance\Contracts\BootstrapInterface
    {
        public function bootstrap(CoreInterface $app): void
        {
            $app->afterResolving(TemplateCompiler::class, function (TemplateCompiler $compiler) {
                $compiler->addCompiler(new Blade());
            });
        }
    }

    class Blade implements TemplateCompilerInterface
    {
        /**
         * All of the compiler functions used by Blade.
         *
         * @var array
         */
        protected static $compilers = array('extensions', 'comments', 'php', 'define', 'echos', 'forelse', 'empty', 'endforelse', 'structure_openings', 'structure_closings', 'else', 'unless', 'endunless', 'includes', 'render_each', 'render', 'yields', 'yield_sections', 'section_start', 'section_end', 'url', 'asset', 'route', 'show');
        /**
         * An array of user defined compilers.
         *
         * @var array
         */
        protected static $extensions = array();

        public function getExtensions(): array
        {
            return ['blade', 'blade.php'];
        }

        /**
         * Register a custom Blade compiler.
         *
         * <code>
         *        Blade::extend(function($view)
         *        {
         *            return str_replace('foo', 'bar', $view);
         *        });
         * </code>
         *
         * @param \Closure $compiler
         * @return void
         */
        public static function extend(\Closure $compiler)
        {
            static::$extensions[] = $compiler;
        }

        /**
         * Compiles the given string containing Blade pseudo-code into valid PHP.
         *
         * @param string $template
         * @return string
         */
        public function compile(string $template): string
        {
            foreach (static::$compilers as $compiler) {
                $method = "compile_{$compiler}";
                $template = call_user_func([$this, $method], $template);
            }
            $template = $this->compile_layouts($template);
            return $template;
        }

        /**
         * Rewrites Blade "@layout" expressions into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_layouts($value)
        {
            // If the Blade template is not using "layouts", we'll just return it
            // unchanged since there is nothing to do with layouts and we will
            // just let the other Blade compilers handle the rest.
            if (strpos($value, '@layout') !== false) {
                $key = 'layout';
            } elseif (strpos($value, '@extends') !== false) {
                $key = 'extends';
            } else {
                return $value;
            }
            //Strip end of file
            $value = rtrim($value);
            preg_match('/@' . $key . '(\\s*\\(.*\\))(\\s*)/', $value, $matches);
            $layout = str_replace(array("('", "')", '("', ')"'), '', $matches[1]);
            // First we'll split out the lines of the template so we can get the
            // layout from the top of the template. By convention it must be
            // located on the first line of the template contents.
            $lines = preg_split("/(\r?\n)/", $value);
            $code = implode(PHP_EOL, array_slice($lines, 1));
            // We will add a "render" statement to the end of the templates and
            // then slice off the "@layout" shortcut from the start so the
            // sections register before the parent template renders.
            return '<?php echo $__view->layout("' . $layout . '", \'' . str_replace("'", "\\'", $code) . '\', get_defined_vars(), ' . ($key == 'extends' ? 'true' : '') . ')->render(); ?>';
        }

        protected function compile_php($value)
        {
            return preg_replace('/\\@php(.+?)@endphp/is', '<?php ${1}; ?>', $value);
        }

        /**
         * Rewrites Blade comments into PHP comments.
         *
         * @param string $value
         * @return string
         */
        protected function compile_comments($value)
        {
            return preg_replace('/\\{\\{--((.|\\s)*?)--\\}\\}/', "<?php /** \$1 **/ ?>\n", $value);
        }

        /**
         * Rewrites Blade echo statements into PHP echo statements.
         *
         * @param string $value
         * @return string
         */
        protected function compile_echos($value)
        {
            $value = preg_replace('/\\{!!(.+?)!!\\}/', '<?php echo $1; ?>', $value);
            return preg_replace('/\\{\\{(.+?)\\}\\}/', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\', false); ?>', $value);
        }

        /**
         * Rewrites Blade echo statements into PHP echo statements.
         *
         * @param string $value
         * @return string
         */
        protected function compile_define($value)
        {
            $value = preg_replace('/\\{\\{\\{(.+?)\\}\\}\\}/', '<?php  $1;  ?>', $value);
            return $value;
        }

        /**
         * Rewrites Blade "for else" statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_forelse($value)
        {
            preg_match_all('/(\\s*)@forelse(\\s*\\(.*\\))(\\s*)/', $value, $matches);
            foreach ($matches[0] as $forelse) {
                preg_match('/\\s*\\(\\s*(\\S*)\\s/', $forelse, $variable);
                // Once we have extracted the variable being looped against, we can add
                // an if statement to the start of the loop that checks if the count
                // of the variable being looped against is greater than zero.
                $if = "<?php if (count({$variable[1]}) > 0): ?>";
                $search = '/(\\s*)@forelse(\\s*\\(.*\\))/';
                $replace = '$1' . $if . '<?php foreach$2: ?>';
                $blade = preg_replace($search, $replace, $forelse);
                // Finally, once we have the check prepended to the loop we'll replace
                // all instances of this forelse syntax in the view content of the
                // view being compiled to Blade syntax with real PHP syntax.
                $value = str_replace($forelse, $blade, $value);
            }
            return $value;
        }

        /**
         * Rewrites Blade "empty" statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_empty($value)
        {
            $value = str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value);
            return str_replace('@continue', '<?php continue; ?>', $value);
        }

        /**
         * Rewrites Blade "forelse" endings into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_endforelse($value)
        {
            return str_replace('@endforelse', '<?php endif; ?>', $value);
        }

        /**
         * Rewrites Blade structure openings into PHP structure openings.
         *
         * @param string $value
         * @return string
         */
        protected function compile_structure_openings($value)
        {
            $pattern = '/(\\s*)@(if|elseif|foreach|for|while)(\\s*\\(.*\\))/';
            return preg_replace($pattern, '$1<?php $2$3: ?>', $value);
        }

        /**
         * Rewrites Blade structure closings into PHP structure closings.
         *
         * @param string $value
         * @return string
         */
        protected function compile_structure_closings($value)
        {
            $pattern = '/(\\s*)@(endif|endforeach|endfor|endwhile)(\\s*)/';
            return preg_replace($pattern, '$1<?php $2; ?>$3', $value);
        }

        /**
         * Rewrites Blade else statements into PHP else statements.
         *
         * @param string $value
         * @return string
         */
        protected function compile_else($value)
        {
            return preg_replace('/(\\s*)@(else)(\\s*)/', '$1<?php $2: ?>$3', $value);
        }

        /**
         * Rewrites Blade "unless" statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_unless($value)
        {
            $pattern = '/(\\s*)@unless(\\s*\\(.*\\))/';
            return preg_replace($pattern, '$1<?php if ( ! ($2)): ?>', $value);
        }

        /**
         * Rewrites Blade "unless" endings into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_endunless($value)
        {
            return str_replace('@endunless', '<?php endif; ?>', $value);
        }

        /**
         * Rewrites Blade @include statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_includes($value)
        {
            $pattern = $this->matcher('include');
            return preg_replace($pattern, '$1<?php echo \\Dissonance\\View\\View::make$2->with(get_defined_vars())->render(); ?>', $value);
        }

        /**
         * Rewrites Blade @render statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_render($value)
        {
            $pattern = $this->matcher('render');
            return preg_replace($pattern, '$1<?php echo render$2; ?>', $value);
        }

        /**
         * Rewrites Blade @render statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_url($value)
        {
            $pattern = $this->matcher('url');
            return preg_replace($pattern, '$1<?php echo  $__view->url$2; ?>', $value);
        }

        /**
         * Rewrites Blade @render statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_asset($value)
        {
            $pattern = $this->matcher('asset');
            return preg_replace($pattern, '$1<?php echo  $__view->asset$2; ?>', $value);
        }

        /**
         * Rewrites Blade @render statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_route($value)
        {
            $pattern = $this->matcher('route');
            return preg_replace($pattern, '$1<?php echo  $__view->route$2; ?>', $value);
        }

        /**
         * Rewrites Blade @render_each statements into valid PHP.
         *
         * @param string $value
         * @return string
         */
        protected function compile_render_each($value)
        {
            $pattern = $this->matcher('render_each');
            return preg_replace($pattern, '$1<?php echo render_each$2; ?>', $value);
        }

        /**
         * Rewrites Blade @yield statements into Section statements.
         *
         * The Blade @yield statement is a shortcut to the Section::yield method.
         *
         * @param string $value
         * @return string
         */
        protected function compile_yields($value)
        {
            $pattern = $this->matcher('yield');
            return preg_replace($pattern, '$1<?php echo  $__view->yield$2; ?>', $value);
        }

        /**
         * Rewrites Blade yield section statements into valid PHP.
         *
         * @return string
         */
        protected function compile_yield_sections($value)
        {
            $replace = '<?php echo  $__view->sectionYieldSection(); ?>';
            return str_replace('@yield_section', $replace, $value);
        }

        /**
         * Rewrites Blade @section statements into Section statements.
         *
         * The Blade @section statement is a shortcut to the Section::start method.
         *
         * @param string $value
         * @return string
         */
        protected function compile_section_start($value)
        {
            $pattern = $this->matcher('section');
            return preg_replace($pattern, '$1<?php $__view->start$2; ?>', $value);
        }

        /**
         * Rewrites Blade @endsection statements into Section statements.
         *
         * The Blade @endsection statement is a shortcut to the Section::stop method.
         *
         * @param string $value
         * @return string
         */
        protected function compile_section_end($value)
        {
            return preg_replace('/@endsection|@stop/', '<?php $__view->stop(); ?>', $value);
        }

        /**
         * Rewrites Blade @endsection statements into Section statements.
         *
         * The Blade @endsection statement is a shortcut to the Section::stop method.
         *
         * @param string $value
         * @return string
         */
        protected function compile_show($value)
        {
            return preg_replace('/@show/', '<?php $__view->yield($__view->stop()); ?>', $value);
        }

        /**
         * Execute user defined compilers.
         *
         * @param string $value
         * @return string
         */
        protected function compile_extensions($value)
        {
            foreach (static::$extensions as $compiler) {
                $value = $compiler($value);
            }
            return $value;
        }

        /**
         * Get the regular expression for a generic Blade function.
         *
         * @param string $function
         * @return string
         */
        public function matcher($function)
        {
            return '/(\\s*)@' . $function . '(\\s*\\(.*\\))/';
        }
    }
}