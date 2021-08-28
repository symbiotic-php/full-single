<?php
namespace Dissonance\Core\Support { use ArrayAccess, InvalidArgumentException, Traversable, ArrayIterator; use Dissonance\Container\{ArrayAccessTrait, MagicAccessTrait}; use function _DS\{value, data_get}; trait CollectionTrait { use ArrayAccessTrait, MagicAccessTrait; protected $items = []; function __construct($items = []){
 $this->items = $this->getArrayableItems($items); } static function create($items = []){
 return new static($items); } static function wrap($value){
 return new static($value instanceof self ? $value : Arr::wrap($value)); } static function unwrap($value){
 return $value instanceof self ? $value->all() : $value; } function all(){
 return $this->items; } function filter(callable $callback = null){
 return new static($callback ? Arr::where($this->items, $callback) : array_filter($this->items)); } function when($value, callable $callback, callable $default = null){
 if ($value){
 return $callback($this, $value); } elseif ($default){
 return $default($this, $value); } return $this; } protected function operatorForWhere($key, $operator = null, $value = null){
 $args = func_num_args(); if ($args < 3){
 $value = $args < 2 ? true : $operator; $operator = '='; } return function ($item) use ($key, $operator, $value){
 $retrieved = \_DS\data_get($item, $key); $strings = array_filter([$retrieved, $value], function ($value){
 return is_string($value) || is_object($value) && method_exists($value, '__toString'); }); if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1){
 return in_array($operator, ['!=', '<>', '!==']); } switch ($operator){
 default: case '=': case '==': return $retrieved == $value; case '!=': case '<>': return $retrieved != $value; case '<': return $retrieved < $value; case '>': return $retrieved > $value; case '<=': return $retrieved <= $value; case '>=': return $retrieved >= $value; case '===': return $retrieved === $value; case '!==': return $retrieved !== $value; } }; } function get(string $key, $default = null){
 return Arr::get($this->items, $key, $default); } function first(callable $callback = null, $default = null){
 return Arr::first($this->items, $callback, $default); } function last(callable $callback = null, $default = null){
 return Arr::last($this->items, $callback, $default); } function has(string $key): bool { return Arr::has($this->items, $key); } function set($key, $value): void { if (is_null($key)){
 $this->items[] = $value; } else { Arr::set($this->items, $key, $value);}}
 function remove($keys): void { Arr::forget($this->items, $keys); } function isEmpty(){
 return empty($this->items); } protected function useAsCallable($value){
 return !is_string($value) && is_callable($value); } function keys(){
 return new static(array_keys($this->items)); } function map(callable $callback, $replace_keys = false){
 $keys = array_keys($this->items); $items = array_map($callback, $this->items, $keys); if ($replace_keys){
 $tmp = []; foreach ($items as $item){
 $tmp[$item[1]] = $item[0]; } return new static($tmp); } return new static(array_combine($keys, $items)); } function transform(callable $callback){
 $this->items = $this->map($callback)->all(); return $this; } function mapInto($class){
 return $this->map(function ($value, $key) use ($class){
 return new $class($value, $key); }); } function merge($items){
 return new static(array_merge($this->items, $this->getArrayableItems($items))); } function combine($values){
 return new static(array_combine($this->all(), $this->getArrayableItems($values))); } function union($items){
 return new static($this->items + $this->getArrayableItems($items)); } function pop(){
 return array_pop($this->items); } function prepend($value, $key = null){
 $this->items = Arr::prepend($this->items, $value, $key); return $this; } function add($item){
 $this->set(null, $item); return $this; } function push($value){
 return $this->add($value); } function pull($key, $default = null){
 return Arr::pull($this->items, $key, $default); } function put($key, $value){
 $this->set($key, $value); return $this; } function search($value, $strict = false){
 if (!$this->useAsCallable($value)){
 return array_search($value, $this->items, $strict); } foreach ($this->items as $key => $item){
 if (call_user_func($value, $item, $key)){
 return $key;}}
 return false; } function reject($callback){
 if ($this->useAsCallable($callback)){
 return $this->filter(function ($value, $key) use ($callback){
 return !$callback($value, $key); }); } return $this->filter(function ($item) use ($callback){
 return $item != $callback; }); } function shift(){
 return array_shift($this->items); } function split($numberOfGroups){
 if ($this->isEmpty()){
 return new static(); } $groups = new static(); $groupSize = floor($this->count() / $numberOfGroups); $remain = $this->count() % $numberOfGroups; $start = 0; for ($i = 0; $i < $numberOfGroups; $i++){
 $size = $groupSize; if ($i < $remain){
 $size++; } if ($size){
 $groups->push(new static(array_slice($this->items, $start, $size))); $start += $size;}}
 return $groups; } function chunk($size){
 if ($size <= 0){
 return new static(); } $chunks = []; foreach (array_chunk($this->items, $size, true) as $chunk){
 $chunks[] = new static($chunk); } return new static($chunks); } function slice($offset, $length = null){
 return new static(array_slice($this->items, $offset, $length, true)); } function sort(callable $callback = null){
 $items = $this->items; $callback ? uasort($items, $callback) : asort($items); return new static($items); } function unique($key = null, $strict = false){
 $callback = $this->valueRetriever($key); $exists = []; return $this->reject(function ($item, $key) use ($callback, $strict, &$exists){
 if (in_array($id = $callback($item, $key), $exists, $strict)){
 return true; } $exists[] = $id; }); } function sortKeys($options = SORT_REGULAR, $descending = false){
 $items = $this->items; $descending ? krsort($items, $options) : ksort($items, $options); return new static($items); } protected function valueRetriever($value){
 if ($this->useAsCallable($value)){
 return $value; } return function ($item) use ($value){
 return data_get($item, $value); }; } function pad($size, $value){
 return new static(array_pad($this->items, $size, $value)); } function reverse(){
 return new static(array_reverse($this->items, true)); } function toArray(){
 return array_map(function ($value){
 return $value instanceof ArrayableInterface ? $value->toArray() : $value; }, $this->items); } function jsonSerialize(){
 return array_map(function ($value){
 if ($value instanceof \JsonSerializable){
 return $value->jsonSerialize(); } elseif ($value instanceof JsonableInterface){
 return \json_decode($value->toJson(), true); } elseif ($value instanceof ArrayableInterface){
 return $value->toArray(); } return $value; }, $this->items); } function toJson($options = 0){
 return \json_encode($this->jsonSerialize(), $options); } function getIterator(){
 return new ArrayIterator($this->items); } function count(){
 return count($this->items); } function __toString(){
 return $this->toJson(); } protected function getArrayableItems($items){
 if (is_array($items)){
 return $items; } elseif ($items instanceof self){
 return $items->all(); } elseif ($items instanceof ArrayableInterface){
 return $items->toArray(); } elseif ($items instanceof JsonableInterface){
 return \json_decode($items->toJson(), true); } elseif ($items instanceof \JsonSerializable){
 return $items->jsonSerialize(); } elseif ($items instanceof Traversable){
 return iterator_to_array($items); } return (array)$items;}}
 interface ArrayableInterface { function toArray(); } interface JsonableInterface { function toJson($options = 0); } interface RenderableInterface { function render(); function __toString(); } class Arr { static function accessible($value){
 return is_array($value) || $value instanceof ArrayAccess; } static function add($array, $key, $value){
 if (is_null(static::get($array, $key))){
 static::set($array, $key, $value); } return $array; } static function collapse($array){
 $results = []; foreach ($array as $values){
 if ($values instanceof Collection){
 $values = $values->all(); } elseif (!is_array($values)){
 continue; } $results = array_merge($results, $values); } return $results; } static function divide($array){
 return [array_keys($array), array_values($array)]; } static function dot($array, $prepend = ''){
 $results = []; foreach ($array as $key => $value){
 if (is_array($value) && !empty($value)){
 $results = array_merge($results, static::dot($value, $prepend . $key . '.')); } else { $results[$prepend . $key] = $value;}}
 return $results; } static function except($array, $keys){
 static::forget($array, $keys); return $array; } static function exists($array, $key){
 if ($array instanceof ArrayAccess){
 return $array->offsetExists($key); } return array_key_exists($key, $array); } static function first($array, callable $callback = null, $default = null){
 foreach ($array as $key => $value){
 if (!is_callable($callback) || call_user_func($callback, $value, $key)){
 return $value;}}
 return value($default); } static function last($array, callable $callback = null, $default = null){
 return is_null($callback) ? empty($array) ? value($default) : end($array) : static::first(array_reverse($array, true), $callback, $default); } static function flatten($array, $depth = INF){
 $result = []; foreach ($array as $item){
 $item = $item instanceof Collection ? $item->all() : $item; if (!is_array($item)){
 $result[] = $item; } elseif ($depth === 1){
 $result = array_merge($result, array_values($item)); } else { $result = array_merge($result, static::flatten($item, $depth - 1));}}
 return $result; } static function forget(&$array, $keys){
 $original =& $array; $keys = (array)$keys; if (count($keys) === 0){
 return; } foreach ($keys as $key){
 if (static::exists($array, $key)){
 unset($array[$key]); continue; } $parts = explode('.', $key); $array =& $original; while (count($parts) > 1){
 $part = array_shift($parts); if (isset($array[$part]) && is_array($array[$part])){
 $array =& $array[$part]; } else { continue 2;}}
 unset($array[array_shift($parts)]);}}
 static function get($array, $key, $default = null){
 if (!static::accessible($array)){
 return value($default); } if (is_null($key)){
 return $array; } if (static::exists($array, $key)){
 return $array[$key]; } if (strpos($key, '.') === false){
 return isset($array[$key]) ? $array[$key] : value($default); } foreach (explode('.', $key) as $segment){
 if (static::accessible($array) && static::exists($array, $segment)){
 $array = $array[$segment]; } else { return value($default);}}
 return $array; } static function has($array, $keys){
 $keys = (array)$keys; if (!$array || $keys === []){
 return false; } foreach ($keys as $key){
 $subKeyArray = $array; if (static::exists($array, $key)){
 continue; } foreach (explode('.', $key) as $segment){
 if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)){
 $subKeyArray = $subKeyArray[$segment]; } else { return false;}}
 } return true; } static function isAssoc(array $array){
 $keys = array_keys($array); return array_keys($keys) !== $keys; } static function only($array, $keys){
 return array_intersect_key($array, array_flip((array)$keys)); } static function pluck($array, $value, $key = null){
 $results = []; list($value, $key) = static::explodePluckParameters($value, $key); foreach ($array as $item){
 $itemValue = data_get($item, $value); if (is_null($key)){
 $results[] = $itemValue; } else { $itemKey = data_get($item, $key); if (is_object($itemKey) && method_exists($itemKey, '__toString')){
 $itemKey = (string)$itemKey; } $results[$itemKey] = $itemValue;}}
 return $results; } protected static function explodePluckParameters($value, $key){
 $value = is_string($value) ? explode('.', $value) : $value; $key = is_null($key) || is_array($key) ? $key : explode('.', $key); return [$value, $key]; } static function prepend($array, $value, $key = null){
 if (is_null($key)){
 array_unshift($array, $value); } else { $array = [$key => $value] + $array; } return $array; } static function pull(&$array, $key, $default = null){
 $value = static::get($array, $key, $default); static::forget($array, $key); return $value; } static function random($array, $number = null){
 $requested = is_null($number) ? 1 : $number; $count = count($array); if ($requested > $count){
 throw new InvalidArgumentException("You requested {$requested} items, but there are only {$count} items available."); } if (is_null($number)){
 return $array[array_rand($array)]; } if ((int)$number === 0){
 return []; } $keys = array_rand($array, $number); $results = []; foreach ((array)$keys as $key){
 $results[] = $array[$key]; } return $results; } static function set(&$array, $key, $value){
 if (is_null($key)){
 return $array = $value; } $keys = explode('.', $key); while (count($keys) > 1){
 $key = array_shift($keys); if (!isset($array[$key]) || !is_array($array[$key])){
 $array[$key] = []; } $array =& $array[$key]; } $array[array_shift($keys)] = $value; return $array; } static function shuffle($array, $seed = null){
 if (is_null($seed)){
 shuffle($array); } else { mt_srand($seed); shuffle($array); mt_srand(); } return $array; } static function sortRecursive($array){
 foreach ($array as &$value){
 if (is_array($value)){
 $value = static::sortRecursive($value);}}
 if (static::isAssoc($array)){
 ksort($array); } else { sort($array); } return $array; } static function query($array){
 return http_build_query($array, null, '&', PHP_QUERY_RFC3986); } static function where($array, callable $callback){
 return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH); } static function wrap($value){
 if (is_null($value)){
 return []; } return is_array($value) ? $value : [$value];}}
 class Str { protected static $snakeCache = [], $camelCache = [], $studlyCache = []; static function after($subject, $search){
 return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0]; } static function before($subject, $search){
 return $search === '' ? $subject : explode($search, $subject)[0]; } static function camel($value){
 if (isset(static::$camelCache[$value])){
 return static::$camelCache[$value]; } return static::$camelCache[$value] = lcfirst(static::studly($value)); } static function contains($haystack, $needles){
 foreach ((array)$needles as $needle){
 if ($needle !== '' && mb_strpos($haystack, $needle) !== false){
 return true;}}
 return false; } static function sc($value, $delimiter = '::'){
 return static::splitClass($value, $delimiter); } static function splitClass($value, $delimiter = '::'){
 return false !== strpos($value, $delimiter) ? explode('::', $value) : $value; } static function endsWith($haystack, $needles){
 foreach ((array)$needles as $needle){
 if (substr($haystack, -strlen($needle)) === (string)$needle){
 return true;}}
 return false; } static function finish($value, $cap){
 return preg_replace('/(?:' . preg_quote($cap, '/') . ')+$/u', '', $value) . $cap; } static function is($pattern, $value){
 $patterns = Arr::wrap($pattern); if (empty($patterns)){
 return false; } foreach ($patterns as $pattern){
 if ($pattern == $value){
 return true; } $pattern = preg_quote($pattern, '#'); $pattern = str_replace('\\*', '.*', $pattern); if (preg_match('#^' . $pattern . '\\z#u', $value) === 1){
 return true;}}
 return false; } static function length($value, $encoding = null){
 return mb_strlen($value, $encoding); } static function limit($value, $limit = 100, $end = '...'){
 if (mb_strwidth($value, 'UTF-8') <= $limit){
 return $value; } return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end; } static function lower($value){
 return mb_strtolower($value, 'UTF-8'); } static function random($length = 16){
 $string = ''; while (($len = strlen($string)) < $length){
 $size = $length - $len; $bytes = random_bytes($size); $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size); } return $string; } static function start($value, $prefix){
 return $prefix . preg_replace('/^(?:' . preg_quote($prefix, '/') . ')+/u', '', $value); } static function upper($value){
 return mb_strtoupper($value, 'UTF-8'); } static function title($value){
 return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'); } static function snake($value, $delimiter = '_'){
 $key = $value; if (isset(static::$snakeCache[$key][$delimiter])){
 return static::$snakeCache[$key][$delimiter]; } if (!ctype_lower($value)){
 $value = preg_replace('/\\s+/u', '', ucwords($value)); $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value)); } return static::$snakeCache[$key][$delimiter] = $value; } static function studly($value){
 $key = $value; if (isset(static::$studlyCache[$key])){
 return static::$studlyCache[$key]; } $value = ucwords(str_replace(['-', '_'], ' ', $value)); return static::$studlyCache[$key] = str_replace(' ', '', $value); } static function substr($string, $start, $length = null){
 return mb_substr($string, $start, $length, 'UTF-8'); } static function ucfirst($string){
 return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);}}
 class Collection implements ArrayAccess, ArrayableInterface, JsonableInterface, \Countable, \IteratorAggregate, \JsonSerializable { use CollectionTrait;}}
 namespace Dissonance\Http\Middleware { use Psr\Http\Message\{ResponseInterface, ServerRequestInterface}; use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface}; use Dissonance\Http\UriHelper; trait MiddlewaresCollectionTrait { protected $middleware = []; function append(MiddlewareInterface $middleware){
 array_push($this->middleware, $middleware); } function prepend(MiddlewareInterface $middleware){
 array_unshift($this->middleware, $middleware);}}
 class MiddlewaresDispatcher { const GROUP_GLOBAL = 'global'; protected $middlewares_groups = [self::GROUP_GLOBAL => []], $binds = [], $default_callback; function addMiddlewareGroup(string $name, array $middlewares){
 $this->middlewares_groups[$name] = $middlewares; } function appendToGroup($name, $middleware, \Closure $bind = null): self { if (!isset($this->middlewares_groups[$name])){
 $this->middlewares_groups[$name] = []; } $this->middlewares_groups[$name][] = $middleware; if ($bind){
 $this->bind($middleware, $bind); } return $this; } function prependToGroup($name, $middleware, \Closure $bind = null): self { if (!isset($this->middlewares_groups[$name])){
 $this->middlewares_groups[$name] = []; } array_unshift($this->middlewares_groups[$name], $middleware); if ($bind){
 $this->bind($middleware, $bind); } return $this; } function getMiddlewareGroup(string $name): array { if (!isset($this->middlewares_groups[$name])){
 throw new \Exception('Middleware group [' . htmlspecialchars($name) . '] not found'); } return $this->middlewares_groups[$name]; } function factoryCollection(array $middlewares){
 return array_map(function ($v){
 return $this->factory($v); }, $middlewares); } function factoryGroup($name){
 $middlewares = $this->getMiddlewareGroup($name); return $this->factoryCollection($middlewares); } function factory($middleware): MiddlewareInterface { if ($middleware instanceof \Closure){
 return new MiddlewareCallback($middleware); } if (isset($this->middlewares_groups[$middleware])){
 $middlewares = $this->factoryCollection($this->middlewares_groups[$middleware]); return new MiddlewaresCollection($middlewares); } if (!class_exists($middleware)){
 throw new \Exception('Middleware group or class [' . $middleware . '] not found!'); } $callback = isset($this->binds[$middleware]) ? $this->binds[$middleware] : ($this->default_callback ?: function ($class){
 return new $class(); }); return $callback($middleware); } function setDefaultCallback(\Closure $callback){
 $this->default_callback = $callback; } function bind(string $middleware_classname, \Closure $callback){
 $this->binds[$middleware_classname] = $callback;}}
 class MiddlewareCallback implements MiddlewareInterface { protected $middleware; function __construct(\Closure $middleware){
 $this->middleware = $middleware; } function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface { $closure = $this->middleware; return $closure($request, $handler);}}
 class MiddlewaresCollection implements MiddlewareInterface { use MiddlewaresCollectionTrait; function __construct(array $middleware = []){
 array_map(function ($v){
 $this->append($v); }, $middleware); } function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface { return (new MiddlewaresHandler($handler, $this->middleware))->handle($request);}}
 class MiddlewaresHandler implements RequestHandlerInterface { use MiddlewaresCollectionTrait; protected $middleware = [], $handler, $index = 0; function __construct(RequestHandlerInterface $handler, array $middleware = []){
 $this->middleware = $middleware; $this->handler = $handler; } function getRealHandler(){
 return $this->handler; } function handle(ServerRequestInterface $request): ResponseInterface { if (empty($this->middleware)){
 return $this->handler->handle($request); } $middleware = \array_shift($this->middleware); return $middleware->process($request, clone $this);}}
 class RequestPrefixMiddleware implements MiddlewareInterface { protected $uri_prefix; function __construct(string $uri_prefix = null){
 $this->uri_prefix = $uri_prefix; } function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface { return $handler->handle(empty($this->uri_prefix) ? $request : $request->withUri((new UriHelper())->deletePrefix($this->uri_prefix, $request->getUri())));}}
 } namespace Dissonance\Storage { interface RememberingInterface { function remember(string $key, callable $value);}}
 namespace Dissonance\Container { use ReflectionMethod, ReflectionFunction, InvalidArgumentException, Closure, Psr\SimpleCache\CacheInterface, Throwable; use Psr\Container\{ContainerExceptionInterface, ContainerInterface, NotFoundExceptionInterface}; trait ArrayAccessTrait { function offsetExists($key){
 return $this->has($key); } function offsetGet($key){
 return $this->get($key); } function offsetSet($key, $value){
 $this->set($key, $value); } function offsetUnset($key){
 $this->delete($key);}}
 trait BaseContainerTrait { protected abstract function &getContainerItems(); function get($key){
 $items =& $this->getContainerItems(); return $this->hasBy($key, $items) ? $items[$key] : (is_callable($default = \func_num_args() === 2 ? \func_get_arg(1) : null) ? $default() : $default); } function has($key): bool { $items =& $this->getContainerItems(); return $this->hasBy($key, $items); } private function hasBy($key, &$items): bool { return isset($items[$key]) || is_array($items) && array_key_exists($key, $items) || $items instanceof \ArrayAccess && $items->offsetExists($key); } function set($key, $value): void { $items =& $this->getContainerItems(); $items[$key] = $value; } function delete($key): bool { $items =& $this->getContainerItems(); unset($items[$key]); return true;}}
 trait CachedContainerTrait { protected $container_cache = null; protected $container_key = ''; protected $allow_cached = []; function setCache(CacheInterface $cache, string $key){
 $this->container_cache = $cache; $this->container_key = $key; } function addToCache(string $abstract){
 $this->allow_cached[$abstract] = 1; } function cached(string $abstract, $concrete = null, string $alias = null){
 if (!$this->bound($abstract)){
 $this->singleton($abstract, $concrete); } else { if (!$concrete instanceof \Closure){
 $concrete = $this->getClosure($abstract, is_null($concrete) ? $abstract : $concrete); } $this->bindings[$abstract] = ['concrete' => $concrete, 'shared' => true]; } if ($alias){
 $this->alias($abstract, $alias); } $this->allow_cached[$abstract] = 1; } function serialize(){
 return \serialize($this->getSerializeData()); } protected function getSerializeData(): array { $data = ['cache' => $this->container_cache, 'key' => $this->container_key]; $instances = []; foreach ($this->allow_cached as $k => $v){
 if (isset($this->instances[$k])){
 $instances[$k] = $this->instances[$k];}}
 $data['instances'] = $instances; return $data; } function unserialize($serialized){
 $data = \unserialize($serialized, ['allowed_classes' => true]); $this->container_cache = $data['cache']; $this->container_key = $data['key']; foreach ($data['instances'] as $k => $instance){
 $this->instances[$k] = $instance; $this->resolved[$k] = true; } $this->unserialized($data); } protected function unserialized(array $data){
 } function __destruct(){
 if ($this->container_cache){
 if (!$this->container_cache->has($this->container_key) && !$this->has('cache_cleaned')){
 $this->container_cache->set($this->container_key, $this, 60 * 60);}}
 } } trait ContainerTrait { use ArrayAccessTrait, DeepGetterTrait, MultipleAccessTrait; protected $resolved = []; protected $bindings = []; protected $instances = []; protected $aliases = []; protected $abstractAliases = []; protected $extenders = []; protected $tags = []; protected $buildStack = []; protected $with = []; public $contextual = []; protected $current_build = null; protected $reboundCallbacks = []; protected $globalResolvingCallbacks = []; protected $globalAfterResolvingCallbacks = []; protected $resolvingCallbacks = []; protected $afterResolvingCallbacks = []; protected $containersStack = []; function has(string $key): bool { return $this->bound($key); } function set(string $key, $value): void { $this->bind($key, $value instanceof \Closure ? $value : function () use ($value){
 return $value; }); } function delete(string $key): bool { unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]); } function bound(string $abstract){
 return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || $this->isAlias($abstract); } function resolved(string $abstract){
 $abstract = $this->getAlias($abstract); return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]); } function isShared(string $abstract){
 return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared'] === true; } function isAlias(string $name){
 return isset($this->aliases[$name]); } function bind(string $abstract, $concrete = null, bool $shared = false): void { unset($this->instances[$abstract], $this->aliases[$abstract]); if (is_null($concrete)){
 $concrete = $abstract; } if (!$concrete instanceof \Closure){
 $concrete = $this->getClosure($abstract, $concrete); } $this->bindings[$abstract] = ['concrete' => $concrete, 'shared' => $shared]; if ($this->resolved($abstract)){
 $this->rebound($abstract);}}
 function getClosure(string $abstract, string $concrete){
 return function ($container, $parameters = []) use ($abstract, $concrete){
 if ($abstract === $concrete){
 return $container->build($concrete); } return $container->resolve($concrete, $parameters, $raiseEvents = false); }; } function bindIf(string $abstract, $concrete = null, bool $shared = false){
 if (!$this->bound($abstract)){
 $this->bind($abstract, $concrete, $shared); } return $this; } function singleton(string $abstract, $concrete = null, string $alias = null){
 $this->bind($abstract, $concrete, true); if (is_string($alias)){
 $this->alias($abstract, $alias); } return $this; } function extend(string $abstract, Closure $closure): void { $abstract = $this->getAlias($abstract); if (isset($this->instances[$abstract])){
 $this->instances[$abstract] = $closure($this->instances[$abstract], $this); $this->rebound($abstract); } else { $this->extenders[$abstract][] = $closure; if ($this->resolved($abstract)){
 $this->rebound($abstract);}}
 } function instance(string $abstract, $instance, string $alias = null){
 if (isset($this->aliases[$abstract])){
 foreach ($this->abstractAliases as $abstr => $aliases){
 foreach ($aliases as $index => $alias){
 if ($alias == $abstract){
 unset($this->abstractAliases[$abstr][$index]);}}
 } } $isBound = $this->bound($abstract); unset($this->aliases[$abstract]); $this->instances[$abstract] = $instance; if ($isBound){
 $this->rebound($abstract); } if ($alias){
 $this->alias($abstract, $alias); } return $instance; } function alias(string $abstract, string $alias){
 if ($alias === $abstract){
 throw new \LogicException("[{$abstract}] is aliased to itself."); } $this->aliases[$alias] = $abstract; $this->abstractAliases[$abstract][] = $alias; } function getAbstractAliases(string $abstract): ?array { return $this->abstractAliases[$abstract] ?? null; } function rebinding(string $abstract, Closure $callback){
 $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback; if ($this->bound($abstract)){
 return $this->make($abstract);}}
 function refresh($abstract, $target, $method){
 return $this->rebinding($abstract, function ($app, $instance) use ($target, $method){
 $target->{$method}($instance); }); } protected function rebound(string $abstract){
 $instance = $this->make($abstract); foreach (isset($this->reboundCallbacks[$abstract]) ? $this->reboundCallbacks[$abstract] : [] as $callback){
 call_user_func($callback, $this, $instance);}}
 function wrap(\Closure $callback, array $parameters = [], $defaultMethod = null){
 return function () use ($callback, $parameters, $defaultMethod){
 return $this->call($callback, $parameters, $defaultMethod); }; } function call($callback, array $parameters = [], string $defaultMethod = null){
 return BoundMethod::call($this, $callback, $parameters, $defaultMethod); } function factory(string $abstract): Closure { return function () use ($abstract){
 return $this->make($abstract); }; } function make(string $abstract, array $parameters = []){
 return $this->resolve($abstract, $parameters); } function setContainersStack(DIContainerInterface $container){
 $this->containersStack[] = $container; } function popCurrentContainer(){
 array_pop($this->containersStack); } function resolve(string $abstract, array $parameters = [], bool $raiseEvents = true){
 if (empty($parameters)){
 $container = !empty($this->containersStack) ? end($this->containersStack) : null; if ($container && $container instanceof $abstract){
 return $container;}}
 $abstract = $this->getAlias($abstract); $interface = DIContainerInterface::class; if (isset($parameters[$interface])){
 if ($abstract === $interface){
 return $parameters[$interface]; } $this->containersStack[] = $parameters[$interface]; unset($parameters[$interface]); } else { $this->containersStack[] = $this; } $conceptual_concrete = $this->current_build ? $this->getContextualConcrete($this->current_build, $abstract) : null; $needsContextualBuild = !empty($parameters) || null !== $conceptual_concrete; if (!$needsContextualBuild){
 if (isset($this->instances[$abstract])){
 return $this->instances[$abstract];}}
 $this->with[] = $parameters; $concrete = !empty($conceptual_concrete) ? $conceptual_concrete : (isset($this->bindings[$abstract]) ? $this->bindings[$abstract]['concrete'] : ($this instanceof ServiceContainerInterface && $this->loadDefer($abstract) && isset($this->bindings[$this->getAlias($abstract)]) ? $this->bindings[$this->getAlias($abstract)]['concrete'] : $abstract)); if ($this->isBuildable($concrete, $abstract)){
 if (\is_string($concrete) && \strpos($concrete, '\\') === false){
 throw new NotFoundException($concrete, $this); } $object = $this->build($concrete); } else { $object = $this->make($concrete); } foreach ($this->getExtenders($abstract) as $extender){
 $object = $extender($object, $this); } if ($this->isShared($abstract) && !$needsContextualBuild){
 $this->instances[$abstract] = $object; } if ($raiseEvents){
 $this->fireResolvingCallbacks($abstract, $object); } $this->resolved[$abstract] = true; array_pop($this->with); return $object; } protected function getContextualConcrete(string $for_building, string $need){
 $current_container = end($this->containersStack); return $current_container instanceof ContextualBindingsInterface ? $current_container->getContextualConcrete($for_building, $need) : null; } protected function isBuildable($concrete, string $abstract){
 return $concrete === $abstract || $concrete instanceof \Closure; } function build($concrete){
 if ($concrete instanceof \Closure){
 return $concrete($this, $this->getLastParameterOverride()); } try { $reflector = new \ReflectionClass($concrete); } catch (\Exception $e){
 throw new ContainerException("Target [{$concrete}] is not instantiable and key not exists in container data!"); } if (!$reflector->isInstantiable()){
 if (!empty($this->buildStack)){
 $previous = implode(', ', $this->buildStack); $message = "Target [{$concrete}] is not instantiable while building [{$previous}]."; } else { $message = "Target [{$concrete}] is not instantiable."; } throw new ContainerException($message); } $this->buildStack[] = $concrete; $this->current_build = $concrete; $constructor = $reflector->getConstructor(); if (null === $constructor){
 array_pop($this->buildStack); $this->current_build = end($this->buildStack); return new $concrete(); } $dependencies = $constructor->getParameters(); $instances = $this->resolveDependencies($dependencies); array_pop($this->buildStack); $this->current_build = end($this->buildStack); return $reflector->newInstanceArgs($instances); } protected function resolveDependencies(array $dependencies){
 $results = []; foreach ($dependencies as $k => $dependency){
 if ($this->hasParameterOverride($dependency, $k)){
 $results[] = $this->getParameterOverride($dependency, $k); continue; } $results[] = is_null(Reflection::getParameterClassName($dependency)) ? $this->resolvePrimitive($dependency) : $this->resolveClass($dependency); } return $results; } protected function hasParameterOverride(\ReflectionParameter $dependency, int $param_number = null){
 $params = $this->getLastParameterOverride(); return array_key_exists($dependency->name, $params) || null !== $param_number && array_key_exists($param_number, $params); } protected function getParameterOverride(\ReflectionParameter $dependency, $param_number = null){
 $params = $this->getLastParameterOverride(); if (array_key_exists($dependency->name, $params)){
 return $params[$dependency->name]; } elseif (null !== $param_number && array_key_exists($param_number, $params)){
 return $params[$param_number]; } elseif (($class = Reflection::getParameterClassName($dependency)) && array_key_exists($class, $params)){
 return $params[$class]; } return null; } protected function getLastParameterOverride(){
 return !empty($this->with) ? end($this->with) : []; } protected function resolvePrimitive(\ReflectionParameter $parameter){
 if ($this->current_build && !is_null($concrete = $this->getContextualConcrete($this->current_build, '$' . $parameter->name))){
 return $concrete instanceof \Closure ? $concrete($this) : $concrete; } if ($parameter->isDefaultValueAvailable()){
 return $parameter->getDefaultValue(); } throw new \ArgumentCountError("Unresolvable dependency resolving [{$parameter}] in class {$parameter->getDeclaringClass()->getName()}::{$parameter->getDeclaringFunction()->getName()}"); } protected function resolveClass(\ReflectionParameter $parameter){
 try { $container = end($this->containersStack); $class = Reflection::getParameterClassName($parameter); return $container ? $container->make($class) : $this->make($class); } catch (BindingResolutionException $e){
 if ($parameter->isOptional()){
 return $parameter->getDefaultValue(); } throw $e;}}
 function resolving($abstract, callable $callback = null){
 if (is_string($abstract)){
 $abstract = $this->getAlias($abstract); } if (is_null($callback) && is_callable($abstract)){
 $this->globalResolvingCallbacks[] = $abstract; } else { $this->resolvingCallbacks[$abstract][] = $callback;}}
 function afterResolving($abstract, callable $callback = null){
 if (is_string($abstract)){
 $abstract = $this->getAlias($abstract); } if (is_callable($abstract) && is_null($callback)){
 $this->globalAfterResolvingCallbacks[] = $abstract; } else { $this->afterResolvingCallbacks[$abstract][] = $callback;}}
 protected function fireResolvingCallbacks(string $abstract, $object){
 $this->fireResolvingByData($abstract, $object, $this->globalResolvingCallbacks, $this->resolvingCallbacks); $this->fireResolvingByData($abstract, $object, $this->globalAfterResolvingCallbacks, $this->afterResolvingCallbacks); } protected function fireResolvingByData(string $abstract, $object, array $global_callbacks = [], array $types_callbacks = []){
 if (!empty($global_callbacks)){
 $this->fireCallbackArray($object, $global_callbacks); } $callbacks = $this->getCallbacksForType($abstract, $object, $types_callbacks); if (!empty($callbacks)){
 $this->fireCallbackArray($object, $callbacks);}}
 protected function getCallbacksForType(string $abstract, $value, array $callbacksPerType){
 $results = []; foreach ($callbacksPerType as $type => $callbacks){
 if ($type === $abstract || is_object($value) && $value instanceof $type){
 $results = array_merge($results, $callbacks);}}
 return $results; } protected function fireCallbackArray($object, array $callbacks){
 foreach ($callbacks as $callback){
 $callback($object, $this);}}
 function getBindings(){
 return $this->bindings; } function getAlias(string $abstract): string { while (isset($this->aliases[$abstract])){
 $abstract = $this->aliases[$abstract]; } return $abstract; } function getExtenders(string $abstract){
 $container = !empty($this->containersStack) ? end($this->containersStack) : null; return $container instanceof $this ? $this->extenders[$this->getAlias($abstract)] ?? [] : $container->getExtenders($abstract); } function forgetExtenders(string $abstract){
 unset($this->extenders[$this->getAlias($abstract)]); } protected function dropStaleInstances($abstract){
 unset($this->instances[$abstract], $this->aliases[$abstract]); } function forgetInstance($abstract){
 unset($this->instances[$abstract]); } function forgetInstances(){
 $this->instances = []; } function clear(): void { $this->aliases = []; $this->resolved = []; $this->bindings = []; $this->instances = []; $this->abstractAliases = [];}}
 trait ContextualBindingsTrait { public $contextual = []; function when($concrete): ContextualBindingBuilder { $aliases = []; $concrete = is_array($concrete) ? $concrete : [$concrete]; foreach ($concrete as $c){
 $aliases[] = $this->getAlias($c); } return new ContextualBindingBuilder($this, $aliases); } function addContextualBinding(string $concrete, string $abstract, $implementation): void { $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation; } function getContextualConcrete(string $for_building, string $need){
 if (isset($this->contextual[$for_building][$need])){
 return $this->contextual[$for_building][$need]; } $aliases = $this->getAbstractAliases($need); if (empty($aliases)){
 return null; } foreach ($aliases as $alias){
 if (isset($this->contextual[$for_building][$alias])){
 return $this->contextual[$for_building][$alias];}}
 } } trait DeepGetterTrait { private $deep_delimiter = '::'; function get(string $key){
 $key = false === \strpos($key, $this->deep_delimiter) ? $key : \explode($this->deep_delimiter, $key); try { if (\is_array($key)){
 $c = $key[0]; $k = $key[1]; $service = $this->make($c); if (\is_array($service)){
 if (isset($service[$k]) || \array_key_exists($k, $service)){
 return $service[$k];}}
 elseif ($service instanceof ContainerInterface || $service instanceof BaseContainerInterface){
 if ($service->has($k)){
 return $service->get($k);}}
 elseif ($service instanceof \ArrayAccess && $service->offsetExists($k)){
 return $service->offsetGet($k); } throw new NotFoundException($k, $service); } try { return $this->make($key); } catch (\Exception $e){
 if (!$this->has($key)){
 throw new NotFoundException($key, $this);}}
 } catch (ContainerException $e){
 if ($e instanceof NotFoundExceptionInterface && \func_num_args() === 2){
 return \func_get_arg(1); } throw $e;}}
 function __invoke($key, $default = null){
 return $this->get($key, $default);}}
 trait ItemsContainerTrait { protected $items = []; function get($key){
 $items =& $this->items; return $this->hasBy($key, $items) ? $items[$key] : (is_callable($default = \func_num_args() === 2 ? \func_get_arg(1) : null) ? $default() : $default); } function has($key): bool { return $this->hasBy($key, $this->items); } private function hasBy($key, &$items): bool { return isset($items[$key]) || is_array($items) && array_key_exists($key, $items) || $items instanceof \ArrayAccess && $items->offsetExists($key); } function set($key, $value): void { $this->items[$key] = $value; } function delete($key): bool { unset($this->items[$key]); return true;}}
 trait MagicAccessTrait { function __get($key){
 return $this->get($key); } function __set(string $key, $value): void { $this->set($key, $value); } function __unset(string $key): void { $this->delete($key); } function __isset($key): bool { return $this->has($key); } function __invoke($key, $default = null){
 return $this->has($key) ? $this->get($key) : (\is_callable($default) ? $default() : $default);}}
 trait MethodBindingsTrait { protected $methodBindings = []; function hasMethodBinding($method){
 return isset($this->methodBindings[$method]); } function bindMethod($method, $callback){
 $this->methodBindings[$this->parseBindMethod($method)] = $callback; } protected function parseBindMethod($method){
 if (is_array($method)){
 return $method[0] . '@' . $method[1]; } return $method; } function callMethodBinding($method, $instance){
 return call_user_func($this->methodBindings[$method], $instance, $this);}}
 trait MultipleAccessTrait { function getMultiple(iterable $keys){
 $result = []; foreach ($keys as $key){
 $result[$key] = $this->get($key); } return $result; } function setMultiple(iterable $values): void { foreach ($values as $key => $value){
 $this->set($key, $value);}}
 function deleteMultiple(iterable $keys): bool { $result = true; foreach ($keys as $key){
 if (!$this->delete($key)){
 $result = false;}}
 return $result;}}
 trait ServiceContainerTrait { protected $dependencyInjectionContainer; protected $serviceProviders = []; protected $loadedProviders = []; protected $defer_services = []; protected $booted = false; function isDeferService(string $service): bool { return isset($this->defer_services[\ltrim($service)]); } function loadDefer(string $service): bool { $class = \ltrim($service); if (isset($this->defer_services[$class])){
 $this->register($this->defer_services[$class]); return true; } return false; } function setDeferred(array $services){
 $this->defer_services = $services; } function register($provider, $force = false){
 if (($registered = $this->getProvider($provider)) && !$force){
 return $registered; } if (is_string($provider)){
 $provider = $this->resolveProvider($provider); } if (method_exists($provider, 'register')){
 $provider->register(); } if (property_exists($provider, 'bindings')){
 foreach ($provider->bindings() as $key => $value){
 $this->bind($key, $value);}}
 if (property_exists($provider, 'singletons')){
 foreach ($provider->singletons() as $key => $value){
 $this->singleton($key, $value);}}
 if (property_exists($provider, 'aliases')){
 foreach ($provider->aliases() as $key => $value){
 $this->singleton($key, $value);}}
 $this->markAsRegistered($provider); if ($this->booted){
 $this->bootProvider($provider); } return $provider; } function boot(){
 if ($this->booted){
 return; } array_walk($this->serviceProviders, function ($p){
 $this->bootProvider($p); }); $this->booted = true; } protected function bootProvider($provider){
 if (method_exists($provider, 'boot')){
 return $this->dependencyInjectionContainer->call([$provider, 'boot']);}}
 function resolveProvider($provider){
 return new $provider($this->dependencyInjectionContainer); } protected function markAsRegistered($provider){
 $class = $this->getClass($provider); $this->serviceProviders[$class] = $provider; $this->loadedProviders[$class] = true; } function getProvider($provider){
 $providers =& $this->serviceProviders; $name = $this->getClass($provider); return isset($providers[$name]) ? $providers[$name] : null; } protected function getClass($provider){
 return \is_string($provider) ? \ltrim($provider, '\\') : \get_class($provider); } function getProviders($provider){
 $name = $this->getClass($provider); return \array_filter($this->serviceProviders, function ($value) use ($name){
 return $value instanceof $name; }, ARRAY_FILTER_USE_BOTH);}}
 trait SingletonTrait { protected static $instance; static function getInstance(){
 return null === static::$instance ? static::$instance = new static() : static::$instance;}}
 trait SubContainerTrait { use DeepGetterTrait, ArrayAccessTrait, MethodBindingsTrait, ContextualBindingsTrait; protected $app = null; protected $aliases = []; protected $instances = []; protected $abstractAliases = []; protected $reboundCallbacks = []; protected $bindings = []; protected $resolved = []; protected $extenders = []; function call($callback, array $parameters = [], string $defaultMethod = null){
 return BoundMethod::call($this, $callback, $this->bindParameters($parameters), $defaultMethod); } function bindParameters(&$parameters){
 $di = DIContainerInterface::class; if (!isset($parameters[$di])){
 $parameters[$di] = $this; } return $parameters; } function make(string $abstract, array $parameters = []){
 return $this->resolve($abstract, $parameters); } function has(string $key): bool { return isset($this->bindings[$key]) || isset($this->instances[$key]) || isset($this->aliases[$key]) || $this->app->has($this->getAlias($key)); } function set($key, $value): void { $this->bind($key, $value instanceof \Closure ? $value : function () use ($value){
 return $value; }); } function delete(string $key): bool { unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key], $this->aliases[$key], $this->abstractAliases[$key]); return true; } function bound($abstract){
 return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || isset($this->aliases[$abstract]) || $this->app->bound($abstract); } function alias(string $abstract, string $alias){
 if ($alias === $abstract){
 throw new \LogicException("[{$abstract}] is aliased to itself."); } $this->aliases[$alias] = $abstract; $this->abstractAliases[$abstract][] = $alias; } protected function rebound($abstract){
 $instance = $this->make($abstract); foreach (isset($this->reboundCallbacks[$abstract]) ? $this->reboundCallbacks[$abstract] : [] as $callback){
 call_user_func($callback, $this, $instance);}}
 function bind(string $abstract, $concrete = null, bool $shared = false): void { unset($this->instances[$abstract], $this->aliases[$abstract]); if (!$concrete){
 $concrete = $abstract; } $this->bindings[$abstract] = ['concrete' => function ($container, $parameters = []) use ($abstract, $concrete, $shared){
 if ($concrete instanceof \Closure){
 $instance = $concrete($this, $parameters); foreach ($this->getExtenders($abstract) as $v){
 $instance = $v($instance);}}
 else { if ($abstract == $concrete){
 $container->setContainersStack($this); $instance = $container->build($concrete); $container->popCurrentContainer(); } else { $instance = $this->app->resolve($concrete, $parameters, $raiseEvents = false);}}
 $this->resolved[$abstract] = true; if ($shared){
 $this->instances[$abstract] = $instance; } return $instance; }, 'shared' => $shared]; $alias = $this->getAlias($abstract); if (isset($this->resolved[$alias]) || isset($this->instances[$alias])){
 $this->rebound($abstract);}}
 function rebinding(string $abstract, Closure $callback){
 $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback; if ($this->bound($abstract)){
 return $this->make($abstract);}}
 function resolve(string $abstract, array $parameters = [], bool $raiseEvents = true){
 $alias = $this->getAlias($abstract); if (!$parameters && isset($this->instances[$abstract])){
 return $this->instances[$abstract]; } if (isset($this->bindings[$abstract])){
 return $this->app->build($this->bindings[$abstract]['concrete']); } if (!$parameters && isset($this->instances[$alias])){
 return $this->instances[$alias]; } if (isset($this->bindings[$alias])){
 return $this->app->build($this->bindings[$alias]['concrete']); } return $this->app->resolve($alias, $this->bindParameters($parameters), $raiseEvents); } function build($concrete){
 return $this->app->build($concrete); } function bindIf(string $abstract, $concrete = null, bool $shared = false){
 if (!$this->bound($abstract)){
 $this->bind($abstract, $concrete, $shared);}}
 function singleton(string $abstract, $concrete = null, string $alias = null){
 $this->bind($abstract, $concrete, true); if (is_string($alias)){
 $this->alias($abstract, $alias); } return $this; } function extend(string $abstract, Closure $closure): void { $abstract = $this->getAlias($abstract); if (isset($this->instances[$abstract])){
 $this->instances[$abstract] = $closure($this->instances[$abstract], $this); $this->rebound($abstract); } else { $this->extenders[$abstract][] = $closure; if ($this->resolved($abstract)){
 $this->rebound($abstract);}}
 } function getExtenders(string $abstract){
 return $this->extenders[$this->getAlias($abstract)] ?? []; } function forgetExtenders(string $abstract){
 unset($this->extenders[$this->getAlias($abstract)]); } function getAlias(string $abstract): string { if (!isset($this->aliases[$abstract])){
 return $this->app->getAlias($abstract); } return $this->getAlias($this->aliases[$abstract]); } function instance(string $abstract, $instance, string $alias = null){
 if (isset($this->aliases[$abstract])){
 foreach ($this->abstractAliases as $abstr => $aliases){
 foreach ($aliases as $index => $als){
 if ($als == $abstract){
 unset($this->abstractAliases[$abstr][$index]);}}
 } } $isBound = $this->bound($abstract); unset($this->aliases[$abstract]); $this->instances[$abstract] = $instance; if ($isBound){
 $this->rebound($abstract); } if ($alias){
 $this->alias($abstract, $alias); } return $instance; } function addContextualBinding(string $concrete, string $abstract, $implementation): void { $this->app->addContextualBinding($concrete, $abstract, $implementation); } function when($concrete): ContextualBindingBuilder { return $this->app->when($concrete); } function factory(string $abstract): Closure { return function () use ($abstract){
 return $this->make($abstract); }; } function clear(): void { $this->aliases = []; $this->abstractAliases = []; } function resolved(string $abstract){
 if ($this->isAlias($abstract)){
 $abstract = $this->getAlias($abstract); } return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]) || $this->app->resolved($abstract); } function resolving($abstract, callable $callback = null){
 if (is_string($abstract)){
 $abstract = $this->getAlias($abstract); } $this->app->resolving($abstract, function (object $object, $app) use ($callback){
 return $callback($object, $this); }); } function afterResolving($abstract, callable $callback = null){
 if (is_string($abstract)){
 $abstract = $this->getAlias($abstract); } $this->app->afterResolving($abstract, function (object $object, $app) use ($callback){
 return $callback($object, $this); }); } function isAlias(string $name): bool { return isset($this->aliases[$name]) || $this->app->isAlias($name); } function getAbstractAliases(string $abstract): ?array { return $this->abstractAliases[$abstract] ?? null;}}
 interface BaseContainerInterface { function get(string $key); function has(string $key): bool; function set(string $key, $value): void; function delete(string $key): bool; } interface ContextualBindingsInterface { function when($concrete): ContextualBindingBuilder; function addContextualBinding(string $concrete, string $abstract, $implementation): void; function getContextualConcrete(string $for_building, string $need); } interface FactoryInterface { function make(string $name, array $parameters = []); } interface MagicAccessInterface { function __set(string $key, $value): void; function __get(string $key); function __isset(string $key): bool; function __unset(string $key): void; function __invoke($key, $default = null); } interface MultipleAccessInterface { function getMultiple(iterable $keys); function setMultiple(iterable $values): void; function deleteMultiple(iterable $keys): bool; } interface ServiceContainerInterface { function register($provider, $force = false); function boot(); function getProvider($provider); function getProviders($provider); function setDeferred(array $services); function isDeferService(string $service): bool; function loadDefer(string $service): bool; } interface ArrayContainerInterface extends BaseContainerInterface, \ArrayAccess { } interface DIContainerInterface extends ArrayContainerInterface, ContainerInterface, FactoryInterface { function bound(string $abstract); function alias(string $abstract, string $alias); function bind(string $abstract, $concrete = null, bool $shared = false): void; function rebinding(string $abstract, Closure $callback); function bindIf(string $abstract, $concrete = null, bool $shared = false); function singleton(string $abstract, $concrete = null, string $alias = null); function extend(string $abstract, Closure $closure): void; function instance(string $abstract, $instance, string $alias = null); function resolve(string $abstract, array $parameters = [], bool $raiseEvents = true); function build($concrete); function factory(string $abstract): Closure; function clear(): void; function call($callback, array $parameters = [], string $defaultMethod = null); function resolved(string $abstract); function resolving($abstract, callable $callback = null); function afterResolving($abstract, callable $callback = null); function isAlias(string $name); function getAbstractAliases(string $abstract): ?array; function getAlias(string $abstract): string; function __invoke($key, $default = null); } interface CachedContainerInterface extends DIContainerInterface, \Serializable { function cached(string $abstract, $concrete = null, string $alias = null); function addToCache(string $abstract); } class BoundMethod { static function call($container, $callback, array $parameters = [], $defaultMethod = null){
 if (static::isCallableWithAtSign($callback) || $defaultMethod){
 return static::callClass($container, $callback, $parameters, $defaultMethod); } return static::callBoundMethod($container, $callback, function () use ($container, $callback, $parameters){
 return call_user_func_array($callback, static::getMethodDependencies($container, $callback, $parameters)); }); } protected static function callClass($container, $target, array $parameters = [], $defaultMethod = null){
 $segments = explode('@', $target); $method = count($segments) === 2 ? $segments[1] : $defaultMethod; if (null === $method){
 throw new InvalidArgumentException('Method not provided.'); } return static::call($container, [$container->make($segments[0]), $method], $parameters); } protected static function callBoundMethod($container, $callback, $default){
 if (!is_array($callback)){
 return $default instanceof \Closure ? $default() : $default; } $method = static::normalizeMethod($callback); if ($container->hasMethodBinding($method)){
 return $container->callMethodBinding($method, $callback[0]); } return $default instanceof \Closure ? $default() : $default; } protected static function normalizeMethod($callback){
 $class = is_string($callback[0]) ? $callback[0] : get_class($callback[0]); return "{$class}@{$callback[1]}"; } protected static function getMethodDependencies($container, $callback, array $parameters = []){
 $dependencies = []; foreach (static::getCallReflector($callback)->getParameters() as $parameter){
 static::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies); } return array_merge(array_values($dependencies), array_values($parameters)); } protected static function getCallReflector($callback){
 if (is_string($callback) && strpos($callback, '::') !== false){
 $callback = explode('::', $callback); } return is_array($callback) ? new ReflectionMethod($callback[0], $callback[1]) : new ReflectionFunction($callback); } protected static function addDependencyForCallParameter(DIContainerInterface $container, \ReflectionParameter $parameter, array &$parameters, &$dependencies){
 if (array_key_exists($parameter->name, $parameters)){
 $dependencies[] = $parameters[$parameter->name]; unset($parameters[$parameter->name]); } elseif ($class = Reflection::getParameterClassName($parameter)){
 if (array_key_exists($class, $parameters)){
 $dependencies[] = $parameters[$class]; unset($parameters[$class]); } else { $dependencies[] = $container->make($class);}}
 elseif ($parameter->isDefaultValueAvailable()){
 $dependencies[] = $parameter->getDefaultValue(); } else { throw new BindingResolutionException('Parameter [' . $parameter->getName() . '] is not find!');}}
 static function isCallableWithAtSign($callback){
 return is_string($callback) && strpos($callback, '@') !== false;}}
 class ContextualBindingBuilder { protected $container; protected $concrete; protected $needs; function __construct(DIContainerInterface $container, $concrete){
 $this->concrete = $concrete; $this->container = $container; } function needs(string $abstract){
 $this->needs = $abstract; return $this; } function give($implementation){
 $concretes = $this->concrete; foreach (!empty($concretes) ? (array)$concretes : [] as $concrete){
 $this->container->addContextualBinding($concrete, $this->needs, $implementation);}}
 } class Reflection { static function getParameterClassName(\ReflectionParameter $parameter): ?string { if (\PHP_VERSION_ID >= 70000){
 $type = $parameter->getType(); if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()){
 return null; } $name = $type->getName(); if (!is_null($class = $parameter->getDeclaringClass())){
 if ($name === 'self'){
 return $class->getName(); } if ($name === 'parent' && ($parent = $class->getParentClass())){
 return $parent->getName();}}
 return $name; } else { return $parameter->getClass() ? $parameter->getClass()->getName() : null;}}
 } class ContainerException extends \Exception implements ContainerExceptionInterface { } class Container implements DIContainerInterface { use MethodBindingsTrait, ContainerTrait; } class BindingResolutionException extends ContainerException { } class CachedContainer extends Container implements CachedContainerInterface { use CachedContainerTrait; } class NotFoundException extends ContainerException implements NotFoundExceptionInterface { function __construct($key, $container, $code = 1384, Throwable $previous = null){
 $message = 'Not found key [' . $key . '] in (' . (is_object($container) ? get_class($container) : gettype($container)) . ')!'; parent::__construct($message, $code, $previous);}}
 class SubContainer implements DIContainerInterface, ContextualBindingsInterface { use SubContainerTrait; function __construct(ContainerInterface $container){
 $this->app = $container;}}
 } namespace Dissonance\Core { use Dissonance\Container\{ CachedContainerInterface, CachedContainerTrait, DIContainerInterface, ArrayAccessTrait, SingletonTrait, Container, ServiceContainerTrait, ServiceContainerInterface }; use Dissonance\Core\Bootstrap\{ BootBootstrap, CoreBootstrap, ProvidersBootstrap, LazyPackagesBootstrap, }; use Dissonance\Core\Support\Collection, Psr\SimpleCache\CacheInterface, Psr\Http\Message\ResponseInterface, Psr\Http\Server\RequestHandlerInterface, Dissonance\Packages\PackagesRepositoryInterface; interface BootstrapInterface { function bootstrap(CoreInterface $app): void; } interface RunnerInterface { function isHandle(): bool; function run(): void; } interface ServiceProviderInterface { function register(): void; function boot(): void; function bindings(): array; function singletons(): array; function aliases(): array; } interface HttpKernelInterface extends RequestHandlerInterface { function bootstrap(): void; function response(int $code = 200, \Throwable $exception = null): ResponseInterface; } interface CoreInterface extends DIContainerInterface, ServiceContainerInterface { function addBootstraps($bootstraps); function isBooted(): bool; function bootstrap(): void; function runBootstrap($class): void; function addRunner(RunnerInterface $runner): void; function run(): void; } class Autoloader { protected static $namespace = 'Dissonance'; private static $registered = false; protected static $packages_dirs = []; protected static $strorage_path = null; protected static $registered_namespaces = []; protected static $files = []; protected static $classes = []; static function register($prepend = false, array $scan_dirs = null, $storage_path = null){
 if ($storage_path){
 self::$strorage_path = rtrim($storage_path, '/\\'); } if (self::$registered === true){
 return; } self::$packages_dirs = $scan_dirs ? array_map(function ($v){
 return rtrim($v, '\\/'); }, $scan_dirs) : []; spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend); self::registerNamespaces(); self::$registered = true; } static function registerNamespaces(){
 $file = self::$strorage_path ? self::$strorage_path . '/autoload.dump.php' : null; if (file_exists($file)){
 $data = (include $file); static::$registered_namespaces = $data['namespaces']; static::$classes = $data['classes']; self::requireFiles($data['files']); return; } foreach (self::$packages_dirs as $dirname){
 if (is_dir($dirname)){
 static::loadPackages($dirname);}}
 uksort(static::$registered_namespaces, function ($a, $b){
 $ex_a = count(explode('\\', trim($a, '/\\'))); $ex_b = count(explode('\\', trim($b, '/\\'))); if ($ex_a > $ex_b){
 return -1; } elseif ($ex_a < $ex_b){
 return 1; } return 0; }); if ($file){
 if (!is_dir(self::$strorage_path)){
 \mkdir(self::$strorage_path, 0777, true); } file_put_contents($file, '<?php ' . PHP_EOL . 'return ' . var_export(['namespaces' => static::$registered_namespaces, 'classes' => static::$classes, 'files' => static::$files], true) . ';');}}
 protected static function loadPackages($dir){
 $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF)); $iterator->setMaxDepth(2); foreach ($iterator as $fileInfo){
 if (!$fileInfo->isDir()){
 continue; } $composer_file = $fileInfo->getRealPath() . '/composer.json'; if (\is_readable($composer_file)){
 $loader = self::getComposerLoader($fileInfo->getRealPath() . '/composer.json', $fileInfo->getRealPath()); if (!empty($loader)){
 if (!empty($loader['files'])){
 static::$files = array_merge(static::$files, $loader['files']); self::requireFiles($loader['files']); } if (!empty($loader['namespaces'])){
 foreach ($loader['namespaces'] as $namespace => $data){
 $namespace = trim($namespace, '\\') . '\\'; if (array_key_exists($namespace, self::$registered_namespaces)){
 $data['root_dir'] = array_merge(self::$registered_namespaces[$namespace]['root_dir'], $data['root_dir']); } self::$registered_namespaces[$namespace] = $data; foreach ($data['root_dir'] as $d){
 $directory = new \RecursiveDirectoryIterator($d); $iterator = new \RecursiveIteratorIterator($directory); $regex = new \RegexIterator($iterator, '/\\.php$/i'); foreach ($regex as $file){
 $path = $file->getRealPath(); $r = preg_replace('@/@', DIRECTORY_SEPARATOR, $d); $class_path = str_replace($r, '', $path); $key = $namespace . trim(str_replace('.php', '', $class_path), '\\/'); static::$classes[$key] = $path;}}
 } } } } } } protected static function requireFiles($files){
 foreach ($files as $file){
 if (file_exists($file)){
 require_once $file;}}
 } protected static function getComposerLoader($file, $base_dir){
 $loader = []; if (\is_readable($file)){
 $data = \json_decode(file_get_contents($file), true); if (is_array($data)){
 $loader['namespaces'] = []; $loader['files'] = []; $get_autoloads = function ($base_dir, $autoload, array &$loader){
 if (isset($autoload['psr-4']) && is_array($autoload['psr-4'])){
 foreach ($autoload['psr-4'] as $namespace => $dir){
 $namespace = rtrim($namespace, '\\'); $loader['namespaces'][$namespace] = ['namespace' => $namespace, 'root_dir' => [$base_dir . DIRECTORY_SEPARATOR . trim($dir, '\\/')]];}}
 if (isset($autoload['files']) && is_array($autoload['files'])){
 foreach ($autoload['files'] as $v){
 $loader['files'][] = $base_dir . DIRECTORY_SEPARATOR . ltrim($v, '\\/');}}
 }; if (isset($data['autoload'])){
 $get_autoloads($base_dir, $data['autoload'], $loader);}}
 } return $loader; } static function autoload($class){
 if (isset(static::$classes[$class])){
 return static::requireFile(static::$classes[$class]); } static::search($class); } protected static function search($class){
 foreach (self::$registered_namespaces as $namespace => $data){
 if (strpos($class, $namespace) === 0){
 $name = substr($class, strlen($namespace)); foreach ($data['root_dir'] as $root_dir){
 if (preg_match('/\\\\Tests\\\\$/i', $namespace)){
 $root_dir = rtrim($root_dir . '/' . $namespace, '\\/'); } $fileName = strtr($root_dir . '/' . ltrim($name, '\\/'), '\\', '/') . '.php'; if (static::requireFile($fileName, false)){
 return; } else { $data = $fileName;}}
 } } } private static function requireFile($file, $throw = false){
 if (file_exists($file)){
 return require_once $file; } elseif ($throw){
 debug_print_backtrace(1, 5); echo 'File not found' . $file; var_dump($file); } return false;}}
 class ContainerBuilder { protected $cache; function __construct(CacheInterface $cache = null){
 $this->cache = $cache; } function buildCore(array $config, string $key = null){
 $key = $key ?: \md5(__FILE__ . CachedCore::class); if (isset($config['bootstrappers'])){
 array_unshift($config['bootstrappers'], LazyPackagesBootstrap::class); } $time = microtime(); if ($this->cache){
 $core = $this->cache->get($key, $time); if ($core === $time){
 $core = new CachedCore($config); $core->setCache($this->cache, $key);}}
 else { $core = new Core($config); } return $core;}}
 class ProvidersRepository { const EXCLUDE = 0; const ACTIVE = 1; const DEFER = 2; protected $providers = []; protected $defer_services = []; protected $loaded = false; function add(array $items, $flag = self::ACTIVE){
 $providers =& $this->providers; foreach ($items as $v){
 $v = ltrim($v, '\\'); $providers[$v] = isset($providers[$v]) ? $providers[$v] | $flag : $flag;}}
 function exclude(array $items){
 $this->add($items, self::EXCLUDE); } protected function defer(array $items){
 $providers = []; foreach ($items as $provider => $services){
 $providers[] = $provider; foreach ($services as $v){
 $this->defer_services[\ltrim($v)] = $provider;}}
 $this->add($providers, self::DEFER); } function all(){
 return $this->providers; } function isDefer($service){
 return isset($this->defer_services[\ltrim($service)]); } function load(ServiceContainerInterface $app, array $force_providers = [], array $force_exclude = []){
 if (!$this->loaded){
 foreach ($app[PackagesRepositoryInterface::class]->getPackages() as $config){
 $this->add(isset($config['providers']) ? (array)$config['providers'] : []); $this->defer(isset($config['defer']) ? (array)$config['defer'] : []); $this->exclude(isset($config['providers_exclude']) ? (array)$config['providers_exclude'] : []);}}
 $this->exclude($force_exclude); foreach ($force_providers as $v){
 $this->providers[ltrim($v, '\\')] = self::ACTIVE; } foreach ($this->providers as $provider => $mask){
 if (!($mask & (self::DEFER | self::EXCLUDE))){
 $app->register($provider);}}
 $app->setDeferred($this->defer_services); } function __wakeup(){
 $this->loaded = true;}}
 abstract class AbstractBootstrap implements BootstrapInterface { protected function cached($app, string $abstract, $concrete = null, string $alias = null){
 $app instanceof CachedContainerInterface ? $app->cached($abstract, $concrete, $alias) : $app->singleton($abstract, $concrete, $alias);}}
 class Config extends Collection { } abstract class Runner implements RunnerInterface { protected $app; function __construct(CoreInterface $container){
 $this->app = $container;}}
 class ServiceProvider implements ServiceProviderInterface { protected $app = null; function __construct(DIContainerInterface $app){
 $this->app = $app; } function register(): void { } function boot(): void { } function bindings(): array { return []; } function singletons(): array { return []; } function aliases(): array { return [];}}
 class Core extends Container implements CoreInterface { use ServiceContainerTrait, ArrayAccessTrait, SingletonTrait; protected $runners = []; protected $base_path = null; protected $bootstraps = []; protected $last_bootstraps = [ProvidersBootstrap::class, BootBootstrap::class]; protected $allow_cached = []; function __construct(array $config = []){
 $this->dependencyInjectionContainer = static::$instance = $this; $this->instance(DIContainerInterface::class, $this); $this->instance(CoreInterface::class, $this); $this->instance('bootstrap_config', $config); $this->base_path = rtrim(isset($config['base_path']) ? $config['base_path'] : __DIR__, '\\/'); $this->runBootstrap(CoreBootstrap::class); } function addBootstraps($bootstraps): void { foreach ((array)$bootstraps as $v){
 $this->bootstraps[] = $v;}}
 function isBooted(): bool { return $this->booted; } function bootstrap(): void { if (!$this->isBooted()){
 foreach ($this->bootstraps + $this->last_bootstraps as $class){
 $this->runBootstrap($class);}}
 $this->booted = true; } function runBootstrap($class): void { if (class_exists($class)){
 (new $class())->bootstrap($this);}}
 function addRunner(RunnerInterface $runner): void { $this->runners[] = $runner; } function run(): void { foreach ($this->runners as $runner){
 $runner = new $runner($this); if ($runner->isHandle()){
 $runner->run(); break;}}
 } function getBasePath($path = ''){
 return $this->base_path . ($path ? \_DS\DS . $path : $path);}}
 class CachedCore extends Core implements CachedContainerInterface { use CachedContainerTrait { CachedContainerTrait::unserialized as traitUnserialized; CachedContainerTrait::getSerializeData as traitGetSerializeData; } function getSerializeData(): array { $data = $this->traitGetSerializeData(); $data['config'] = $this['bootstrap_config']; return $data; } protected function unserialized(array $data){
 $this->__construct($data['config']);}}
 } namespace Dissonance\Filesystem { use Dissonance\Container\DIContainerInterface, Dissonance\Filesystem\Adapter\Local; trait ArrayStorageTrait { private $storage_path = null; protected function setStoragePath(string $path){
 $path = \rtrim($path); if (!file_exists($path)){
 mkdir($path, 0700, true); } if (!is_dir($path)){
 throw new NotExistsException("Не удалось создать папку[{$path}]!"); } $this->storage_path = $path; } function remember(string $fileName, callable $callback){
 if (!$this->storage_path){
 return $callback(); } $path = $this->storage_path . \_DS\DS . $fileName; if (\is_readable($path)){
 return include $path; } $data = $callback(); if (!\is_array($data)){
 throw new \TypeError('Данные должны быть массивом [' . gettype($data) . ']!'); } if (!\file_put_contents($path, '<?php ' . PHP_EOL . 'return ' . var_export($data, true) . ';')){
 throw new \Exception('Не удалось записать в файл[' . $path . ']!'); } return $data;}}
 interface AdapterInterface { function has($path); function read($path); function listContents($directory = '', $recursive = false); function getMetadata($path); function getSize($path); function getMimetype($path); function getTimestamp($path); function write(string $path, $contents, array $options = []); function rename($path, $newpath); function copy($path, $newpath); function delete($path); function deleteDir($path); function createDir($dirname, array $options = []); function setVisibility($path, $visibility); function getVisibility($path); } interface PathPrefixInterface { function setPathPrefix($path); function getPathPrefix(); function applyPathPrefix($path); function removePathPrefix($path); } interface FilesystemInterface extends AdapterInterface { } class FilesystemManager { protected $app; protected $disks = []; protected $customCreators = []; function __construct(DIContainerInterface $app){
 $this->app = $app; } function disk($name = null){
 $name = $name ?: $this->getDefaultDriver(); return $this->disks[$name] = $this->get($name); } protected function get($name){
 return $this->disks[$name] ?? $this->resolve($name); } protected function resolve($name){
 $config = $this->getConfig($name); if (isset($this->customCreators[$config['driver']])){
 return $this->callCustomCreator($config); } $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver'; if (method_exists($this, $driverMethod)){
 return $this->{$driverMethod}($config); } else { throw new \InvalidArgumentException("Driver [{$config['driver']}] is not supported.");}}
 protected function callCustomCreator(array $config){
 return $this->customCreators[$config['driver']]($this->app, $config); } function createLocalDriver(array $config){
 return new Local($config['root'] ?? $this->app->getBasePath(), LOCK_EX, $config['permissions'] ?? []); } function set($name, $disk){
 $this->disks[$name] = $disk; return $this; } protected function getConfig($name){
 return $this->app['config']["filesystems.disks.{$name}"]; } function getDefaultDriver(){
 return $this->app['config']['filesystems.default']; } function getDefaultCloudDriver(){
 return $this->app['config']['filesystems.cloud']; } function forgetDisk($disk){
 foreach ((array)$disk as $diskName){
 unset($this->disks[$diskName]); } return $this; } function extend($driver, \Closure $callback){
 $this->customCreators[$driver] = $callback; return $this; } function __call($method, $parameters){
 return call_user_func_array([$this->disk(), $method], $parameters);}}
 class NotExistsException extends \Exception { } class Filesystem implements FilesystemInterface { protected $adapter; function __construct(AdapterInterface $adapter){
 $this->adapter = $adapter; } function getAdapter(){
 return $this->adapter; } function has($path){
 $path = self::normalizePath($path); return strlen($path) === 0 ? false : (bool)$this->getAdapter()->has($path); } function write(string $path, $contents, array $options = []){
 return (bool)$this->getAdapter()->write(self::normalizePath($path), $contents, $options); } function readAndDelete($path){
 $path = self::normalizePath($path); $contents = $this->read($path); if ($contents === false){
 return false; } $this->delete($path); return $contents; } function read($path){
 return $this->getAdapter()->read(self::normalizePath($path)); } function rename($path, $newpath){
 return (bool)$this->getAdapter()->rename(self::normalizePath($path), self::normalizePath($newpath)); } function copy($path, $newpath){
 return $this->getAdapter()->copy(self::normalizePath($path), self::normalizePath($newpath)); } function delete($path){
 return $this->getAdapter()->delete(self::normalizePath($path)); } function deleteDir($dirname){
 $dirname = self::normalizePath($dirname); if ($dirname === ''){
 throw new \Exception('Root directories can not be deleted.'); } return (bool)$this->getAdapter()->deleteDir($dirname); } static function normalizePath($path){
 $path = rtrim(str_replace("\\", "/", trim($path)), '/'); $unx = strlen($path) > 0 && $path[0] == '/'; $parts = array_filter(explode('/', $path)); $absolutes = []; foreach ($parts as $part){
 if ('.' == $part){
 continue; } if ('..' == $part){
 array_pop($absolutes); } else { $absolutes[] = $part;}}
 $path = implode('/', $absolutes); $path = $unx ? '/' . $path : $path; return $path; } function createDir($dirname, array $config = []){
 return (bool)$this->getAdapter()->createDir(self::normalizePath($dirname), $config); } function listContents($directory = '', $recursive = false){
 return $this->getAdapter()->listContents(self::normalizePath($directory), $recursive); } function getMimetype($path){
 return $this->getAdapter()->getMimetype(self::normalizePath($path)); } function getTimestamp($path){
 return $this->getAdapter()->getTimestamp(self::normalizePath($path)); } function getVisibility($path){
 $path = Util::normalizePath($path); $this->assertPresent($path); if (!($object = $this->getAdapter()->getVisibility($path)) || !array_key_exists('visibility', $object)){
 return false; } return $object['visibility']; } function getSize($path){
 $path = Util::normalizePath($path); $this->assertPresent($path); if (!($object = $this->getAdapter()->getSize($path)) || !array_key_exists('size', $object)){
 return false; } return (int)$object['size']; } function setVisibility($path, $visibility){
 $path = Util::normalizePath($path); $this->assertPresent($path); return (bool)$this->getAdapter()->setVisibility($path, $visibility); } function getMetadata($path){
 $path = Util::normalizePath($path); return $this->getAdapter()->getMetadata($path);}}
 } namespace Dissonance\Http { use Dissonance\Core\{CoreInterface, BootstrapInterface, Support\RenderableInterface}; use Psr\Http\Message\{ StreamFactoryInterface, RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, UriFactoryInterface, StreamInterface, RequestInterface, ResponseInterface, ServerRequestInterface, UploadedFileInterface, UriInterface }; class Request extends \Nyholm\Psr7\Request { } class Response extends \Nyholm\Psr7\Response { } class ServerRequest extends \Nyholm\Psr7\ServerRequest { function isXMLHttpRequest(){
 return $this->getServerParam('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'; } function getUserAgent(){
 return $this->getServerParam('HTTP_USER_AGENT'); } function getServerParam($name, $default = null){
 $server = $this->getServerParams(); return isset($server[$name]) ? $server[$name] : $default; } function getInput($name, $default = null){
 $params = $this->getParsedBody(); return $params[$name] ?? $default; } function getQuery($name, $default = null){
 $params = $this->getQueryParams(); return $params[$name] ?? $default;}}
 class Stream extends \Nyholm\Psr7\Stream { } class UploadedFile extends \Nyholm\Psr7\UploadedFile { } class Uri extends \Nyholm\Psr7\Uri { } class UriHelper { function deletePrefix(string $prefix, UriInterface $uri): UriInterface { $prefix = $this->normalizePrefix($prefix); if (!empty($prefix)){
 $path = $uri->getPath(); $path = preg_replace('~^' . preg_quote($prefix, '~') . '~', '', $path); $uri = $uri->withPath($path); } return $uri; } function normalizePrefix(string $prefix): string { $prefix = trim($prefix, ' \\/'); return $prefix == '' ? '' : '/' . $prefix;}}
 class Bootstrap implements BootstrapInterface { function bootstrap(CoreInterface $app): void { $concrete = PsrHttpFactory::class; $app->singleton($concrete); $app->alias($concrete, 'http_factory'); $app->alias($concrete, UriFactoryInterface::class); $app->alias($concrete, StreamFactoryInterface::class); $app->alias($concrete, ResponseFactoryInterface::class); $app->alias($concrete, ServerRequestFactoryInterface::class); $app->alias($concrete, RequestFactoryInterface::class);}}
 class DownloadResponse extends Response { function __construct(StreamInterface $body, string $filename, int $status = 200, array $headers = [], string $version = '1.1', string $reason = null){
 parent::__construct($status, array_merge($headers, ['Content-Description' => 'File Transfer', 'Content-Type' => 'application/octet-stream', 'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"', 'Content-Transfer-Encoding' => 'binary', 'Expires' => '0', 'Cache-Control' => 'must-revalidate', 'Pragma' => 'public', 'Content-Length' => $body->getSize()]), $body, $version, $reason);}}
 class ResponseMutable implements ResponseInterface { protected $response; function __construct(ResponseInterface $response){
 $this->response = $response; } function getRealInstance(){
 return $this->response; } function getProtocolVersion(){
 return $this->response->getProtocolVersion(); } function withProtocolVersion($version){
 $this->response = $this->response->withProtocolVersion($version); return $this; } function getHeaders(){
 return $this->response->getHeaders(); } function hasHeader($name){
 return $this->response->hasHeader($name); } function getHeader($name){
 return $this->response->getHeader($name); } function getHeaderLine($name){
 return $this->response->getHeaderLine($name); } function withHeader($name, $value){
 $this->response = $this->response->withHeader($name, $value); return $this; } function withAddedHeader($name, $value){
 $this->response = $this->response->withAddedHeader($name, $value); return $this; } function withoutHeader($name){
 $this->response = $this->response->withoutHeader($name); return $this; } function getBody(){
 return $this->response->getBody(); } function withBody(StreamInterface $body){
 $this->response = $this->response->withBody($body); return $this; } function getStatusCode(){
 return $this->response->getStatusCode(); } function withStatus($code, $reasonPhrase = ''){
 $this->response = $this->response->withStatus($code, $reasonPhrase); return $this; } function getReasonPhrase(){
 return $this->response->getReasonPhrase();}}
 class ResponseSender implements RenderableInterface { protected $response; function __construct(ResponseInterface $response){
 $this->response = $response; } function render(){
 $response = $this->response; $http_line = sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()); header($http_line, true, $response->getStatusCode()); foreach ($response->getHeaders() as $name => $values){
 foreach ($values as $value){
 header("{$name}: {$value}", false);}}
 $stream = $response->getBody(); if ($stream->isSeekable()){
 $stream->rewind(); } while (!$stream->eof()){
 echo $stream->read(1024 * 8);}}
 function __toString(){
 ob_start(); $this->render(); return ob_get_clean();}}
 class PsrHttpFactory implements UriFactoryInterface, StreamFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, RequestFactoryInterface { function createServerRequestFromGlobals(){
 $server = $_SERVER; $method = $server['REQUEST_METHOD']; $serverRequest = $this->createServerRequest($method, $this->createUriFromGlobals(), $server); foreach ($server as $key => $value){
 if ($value){
 if (0 === \strpos($key, 'HTTP_')){
 $name = \strtr(\strtolower(\substr($key, 5)), '_', '-'); if (\is_int($name)){
 $name = (string)$name; } $serverRequest->withAddedHeader((string)$name, $value); } elseif (0 === \strpos($key, 'CONTENT_')){
 $name = 'content-' . \strtolower(\substr($key, 8)); $serverRequest->withAddedHeader($name, $value);}}
 } $serverRequest = $serverRequest->withProtocolVersion(isset($server['SERVER_PROTOCOL']) ? \str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1')->withCookieParams($_COOKIE)->withQueryParams($_GET)->withUploadedFiles($this->normalizeFiles($_FILES)); if ($method === 'POST'){
 $serverRequest = $serverRequest->withParsedBody($_POST); } $body = \fopen('php://input', 'r'); if (!$body){
 return $serverRequest; } if (\is_resource($body)){
 $body = $this->createStreamFromResource($body); } elseif (\is_string($body)){
 $body = $this->createStream($body); } elseif (!$body instanceof StreamInterface){
 throw new \InvalidArgumentException('The $body parameter to ServerRequestCreator::fromArrays must be string, resource or StreamInterface'); } return $serverRequest->withBody($body); } private function normalizeFiles(array $files): array { $normalized = []; foreach ($files as $key => $value){
 if ($value instanceof UploadedFileInterface){
 $normalized[$key] = $value; } elseif (\is_array($value) && isset($value['tmp_name'])){
 $normalized[$key] = $this->createUploadedFileFromSpec($value); } elseif (\is_array($value)){
 $normalized[$key] = $this->normalizeFiles($value); } else { throw new \InvalidArgumentException('Invalid value in files specification');}}
 return $normalized; } private function createUploadedFileFromSpec(array $value){
 if (\is_array($value['tmp_name'])){
 return $this->normalizeNestedFileSpec($value); } try { $stream = $this->createStreamFromFile($value['tmp_name']); } catch (\RuntimeException $e){
 $stream = $this->createStream(); } return $this->createUploadedFile($stream, (int)$value['size'], (int)$value['error'], $value['name'], $value['type']); } private function normalizeNestedFileSpec(array $files = []): array { $normalizedFiles = []; foreach (\array_keys($files['tmp_name']) as $key){
 $spec = ['tmp_name' => $files['tmp_name'][$key], 'size' => $files['size'][$key], 'error' => $files['error'][$key], 'name' => $files['name'][$key], 'type' => $files['type'][$key]]; $normalizedFiles[$key] = $this->createUploadedFileFromSpec($spec); } return $normalizedFiles; } function isSecure(){
 $server = $_SERVER; foreach (['HTTPS' => ['on', '1'], 'HTTP_SSL' => ['1'], 'HTTP_X_SSL' => ['yes', '1'], 'HTTP_X_FORWARDED_PROTO' => ['https'], 'HTTP_X_SCHEME' => ['https']] as $key => $values){
 if (!empty($server[$key])){
 foreach ($values as $value){
 if (strtolower($server[$key]) == $value){
 return true;}}
 } } return !empty($server['HTTP_X_HTTPS']) && strtolower($server['HTTP_X_HTTPS']) != 'off'; } function createUriFromGlobals(){
 $uri = $this->createUri(''); $server = $_SERVER; $uri = $uri->withScheme($this->isSecure() ? 'https' : 'http'); if (isset($server['REQUEST_SCHEME']) && isset($server['SERVER_PORT'])){
 $uri = $uri->withPort($server['SERVER_PORT']); } if (isset($server['HTTP_HOST'])){
 if (1 === \preg_match('/^(.+)\\:(\\d+)$/', $server['HTTP_HOST'], $matches)){
 $uri = $uri->withHost($matches[1])->withPort($matches[2]); } else { $uri = $uri->withHost($server['HTTP_HOST']);}}
 elseif (isset($server['SERVER_NAME'])){
 $uri = $uri->withHost($server['SERVER_NAME']); } if (isset($server['REQUEST_URI'])){
 $uri = $uri->withPath(\current(\explode('?', $server['REQUEST_URI']))); } if (isset($server['QUERY_STRING'])){
 $uri = $uri->withQuery($server['QUERY_STRING']); } return $uri; } function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface { return new ServerRequest($method, $uri, [], null, '1.1', $serverParams); } function createRequest(string $method, $uri): RequestInterface { return new Request($method, $uri); } function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface { if (2 > \func_num_args()){
 $reasonPhrase = null; } return new Response($code, [], null, '1.1', $reasonPhrase); } function createStream(string $content = ''): StreamInterface { return Stream::create($content); } function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface { $resource = @\fopen($filename, $mode); if (false === $resource){
 if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'])){
 throw new \InvalidArgumentException('The mode ' . $mode . ' is invalid.'); } throw new \RuntimeException('The file ' . $filename . ' cannot be opened.'); } return Stream::create($resource); } function createStreamFromResource($resource): StreamInterface { return Stream::create($resource); } function createUploadedFile(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): UploadedFileInterface { if (null === $size){
 $size = $stream->getSize(); } return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType); } function createUri(string $uri = ''): UriInterface { return new Uri($uri);}}
 } namespace Dissonance\Core\View { use Dissonance\{ Core\Support\RenderableInterface, Core\CoreInterface, Core\Support\Str, Apps\ApplicationInterface, Packages\TemplatesRepositoryInterface }; class Section { public $sections = array(); public $last = array(); function start($section, $content = null){
 if ($content === null){
 ob_start() and $this->last[] = $section; } else { $this->extend($section, $content);}}
 function inject($section, $content){
 $this->start($section, $content); } function yield_section(){
 $this->yield($this->stop()); } function stop(){
 $this->extend($last = array_pop($this->last), ob_get_clean()); return $last; } protected function extend($section, $content){
 if (isset($this->sections[$section])){
 $this->sections[$section] = $content instanceof View ? function () use ($content){
 $content->render(); } : str_replace('@parent', $content, $this->sections[$section]); } else { $this->sections[$section] = $content;}}
 function append($section, $content){
 if (isset($this->sections[$section])){
 $this->sections[$section] .= $content; } else { $this->sections[$section] = $content;}}
 function yield($section){
 if (isset($this->sections[$section])){
 $section = $this->sections[$section]; if (is_callable($section)){
 $section(); } elseif ($section instanceof RenderableInterface){
 $section->render(); } else { echo $section;}}
 } } class View implements RenderableInterface { protected static $core; protected static $current_container = []; protected $template = ''; protected $vars = []; protected $app_id; public $sections = []; public $last = []; function __construct(string $path, array $vars = [], $app_id = null){
 $this->vars = $vars; $id = null; if (is_string($app_id)){
 $id = $app_id; } else { if (is_array($sc = Str::sc($path))){
 $id = $sc[0]; $path = $sc[1]; } else { $route = static::$core->get('route'); if ($route && $route->getApp() !== null){
 $id = $route->getApp();}}
 } $this->app_id = $id; $this->template = static::$core->get(TemplatesRepositoryInterface::class)->getTemplate($id, $path); } function url($path = '', $absolute = true){
 return static::$core['url']->to($this->prepareModulePath($path), $absolute); } function asset($path = '', $absolute = true){
 return static::$core['url']->asset($this->prepareModulePath($path), $absolute); } function route($path = '', $absolute = true){
 return static::$core['url']->asset($this->prepareModulePath($path), $absolute); } protected function prepareModulePath($path){
 if (!is_array(Str::sc($path))){
 $path = $this->app_id . '::' . $path; } return $path; } static function make($template, array $vars = [], $app_id = null){
 return new static($template, $vars, $app_id); } function start($section, $content = null){
 if ($content === null){
 ob_start() and $this->last[] = $section; } else { $this->extend($section, $content);}}
 function inject($section, $content){
 $this->start($section, $content); } function yield_section(){
 return $this->fetch($this->stop()); } function stop(){
 $this->extend($last = array_pop($this->last), ob_get_clean()); return $last; } protected function extend($section, $content){
 if (isset($this->sections[$section])){
 $this->sections[$section] = $content instanceof View ? function () use ($content){
 $content->render(); } : str_replace('@parent', $content, $this->sections[$section]); } else { $this->sections[$section] = $content;}}
 function append($section, $content){
 if (isset($this->sections[$section])){
 $this->sections[$section] .= $content; } else { $this->sections[$section] = $content;}}
 function yield($section){
 if (isset($this->sections[$section])){
 $section = $this->sections[$section]; if (is_callable($section)){
 $section(); } elseif ($section instanceof View){
 $section->setSections($this->sections); $section->render(); } elseif ($section instanceof RenderableInterface){
 $section->render(); } else { echo (string)$section;}}
 } function setSections($sections){
 $this->sections = $sections; } function layout(string $template, $content_template, $vars = [], $before = false){
 $app_id = $this->app_id; if (is_array($sc = Str::sc($template))){
 $app_id = $sc[0]; $template = $sc[1]; } $this->template = $content_template; $view = new static($template, $vars, $app_id); if ($before){
 $content = $this->fetch($this); $sections = $this->sections; $sections['content'] = $content; $view->setSections($sections); } else { $view->inject('content', $this); } return $view; } static function setContainer(CoreInterface $app){
 static::$core = $app; static::$current_container = [$app]; } static function getCurrentPackageId(){
 $app = end(static::$current_container); if (is_string($app)){
 return $app; } if ($app instanceof ApplicationInterface){
 return $app->getId(); } throw new \Exception('Container is not app!'); } static function getCurrentContainer(){
 $app = end(static::$current_container); if (is_string($app)){
 if ($app === 'app' && static::$core->has('app')){
 $app = static::$core['app']; $app->bootstrap(); } elseif ($apps = static::$core['apps']){
 if (!$apps->has($app)){
 throw new \Exception('Not exists App [' . $app . ']'); } $app = $apps->getBootedApp($app); } else { throw new \Exception('Not exists App [' . $app . ']'); } array_pop(static::$current_container); static::$current_container[] = $app; } return $app; } function with(array $vars){
 $this->vars = $vars; return $this; } function render(){
 static::$current_container[] = $this->app_id; if (!empty($this->template)){
 extract($this->vars); $__view = $this; try { eval($this->getTemplate()); } catch (\ParseError $e){
 throw new \Exception($e->getMessage() . PHP_EOL . $this->template, $e->getCode(), $e);}}
 array_pop(static::$current_container); } function fetch($content){
 ob_start(); if (is_callable($content)){
 $content(); } elseif ($content instanceof RenderableInterface){
 $content->render(); } else { echo $content; } return ob_get_clean(); } protected function getTemplate(){
 return 'use function ' . __NAMESPACE__ . '\\{app,asset,route,css,js,adminRoute,apiRoute}; '.PHP_EOL.' ?>' . $this->template; } function __toString(){
 return $this->fetch($this);}}
 function app($abstract = null, array $parameters = []){
 $container = View::getCurrentContainer(); if (is_null($abstract)){
 return $container; } return $container->make($abstract, $parameters); } function asset($path = '', $absolute = true){
 if (!is_array(Str::sc($path))){
 $path = View::getCurrentPackageId() . '::' . ltrim($path, '\\/'); } return \_DS\app('url')->asset($path, $absolute); } function route($name, $parameters = [], $absolute = true){
 if (!is_array(Str::sc($name))){
 $name = View::getCurrentPackageId() . '::' . $name; } return \_DS\app('url')->route($name, $parameters, $absolute); } function settlementRoute($settlement, $name, $parameters = [], $absolute = true){
 if (!is_array(Str::sc($name))){
 $name = View::getCurrentPackageId() . '::' . $name; } return \_DS\app('url')->route($settlement . ':' . $name, $parameters, $absolute); } function adminRoute($name, $parameters = [], $absolute = true){
 return settlementRoute('backend', $name, $parameters, $absolute); } function apiRoute($name, $parameters = [], $absolute = true){
 return settlementRoute('api', $name, $parameters, $absolute); } function css($path = '', $absolute = true){
 return '<link rel="stylesheet" href="' . asset($path, $absolute) . '">'; } function js($path = '', $absolute = true){
 return '<script type="text/javascript" src="' . asset($path, $absolute) . '"></script>';}}
 namespace Dissonance\SimpleCacheFilesystem { use Dissonance\Core\{ AbstractBootstrap, CoreInterface, Events\CacheClear }; use Psr\SimpleCache\{CacheInterface, CacheException}; interface SimpleCacheInterface extends \Psr\SimpleCache\CacheInterface { function remember($key, \Closure $value, $ttl = null); } class InvalidArgumentException extends \Exception implements \Psr\SimpleCache\InvalidArgumentException { } class Bootstrap extends AbstractBootstrap { function bootstrap(CoreInterface $app): void { $cache_path = $app('cache_path'); if ($cache_path){
 $app->singleton(CacheInterface::class, function (CoreInterface $app){
 return new SimpleCache($app['cache_path_core'], $app('config::cache_time', 3600)); }, 'cache'); $app['listeners']->add(CacheClear::class, function ($event) use ($app){
 $app[CacheInterface::class]->clear(); }); $app->alias(CacheInterface::class, \Dissonance\SimpleCacheFilesystem\SimpleCacheInterface::class);}}
 } class Exception extends \Exception implements CacheException { } class SimpleCache implements SimpleCacheInterface { protected $cache_directory; protected $ttl = 600; function __construct(string $cache_directory, int $default_ttl = 600){
 if (!is_dir($cache_directory)){
 $uMask = umask(0); mkdir($cache_directory, 0755, true); umask($uMask); } if (!is_dir($cache_directory) || !is_writable($cache_directory)){
 throw new Exception("The cache path ({$cache_directory}) is not writeable."); } $this->cache_directory = \rtrim($cache_directory, '\\/'); $this->ttl = $default_ttl; } function remember($key, \Closure $value, $ttl = null){
 $data = $this->get($key, $u = \uniqid()); if ($data === $u){
 $data = $value(); $this->set($key, $data, $ttl); } return $data; } function get($key, $default = null){
 $file = $this->getKeyFilePath($key); if (\is_readable($file) && ($data = @\unserialize(file_get_contents($file)))){
 if (!empty($data) && isset($data['ttl']) && $data['ttl'] >= time() + 1){
 return $data['data']; } else { $this->delete($key);}}
 return $default; } function set($key, $value, $ttl = null){
 $file = $this->getKeyFilePath($key); if ($data = \serialize(['ttl' => time() + (is_int($ttl) ? $ttl : $this->ttl), 'data' => $value])){
 return \file_put_contents($file, $data) !== false; } return false; } function delete($key){
 $file = $this->getKeyFilePath($key); if (file_exists($file)){
 if (is_file($file) && !@unlink($file)){
 throw new Exception("Can't delete the cache file ({$file})."); } clearstatcache(true, $file); } return true; } function clear(){
 $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->cache_directory, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST); $result = true; foreach ($files as $file){
 $file_path = $file->getRealPath(); $res = $file->isDir() ? rmdir($file_path) : unlink($file_path); if (!$res){
 $result = false; } clearstatcache(true, $file_path); } return $result; } function getMultiple($keys, $default = null){
 $result = []; foreach ($this->getValidatedIterable($keys) as $v){
 $result[$v] = $this->get($v, $default); } return $result; } function setMultiple($values, $ttl = null){
 $result = true; foreach ($this->getValidatedIterable($values) as $k => $v){
 if (!$this->set($k, $v, $ttl)){
 $result = false;}}
 return $result; } function deleteMultiple($keys){
 $result = true; foreach ($this->getValidatedIterable($keys) as $v){
 if (!$this->delete($v)){
 $result = false;}}
 return $result; } function has($key){
 $file = $this->getKeyFilePath($key); return \is_readable($file); } protected function getKeyFilePath(string $key){
 $this->validateKey($key); return $this->cache_directory . DIRECTORY_SEPARATOR . \md5($key) . '.cache'; } protected function validateKey(string $key){
 if (false === preg_match('/[^A-Za-z_\\.0-9]/i', $key)){
 throw new InvalidArgumentException('Key is not valid string!');}}
 protected function getValidatedIterable($keys){
 if (!\is_iterable($keys)){
 throw new InvalidArgumentException('Keys is not Iterable!'); } return $keys;}}
 } namespace Dissonance\Session { use Dissonance\Core\ServiceProvider; use Dissonance\Container\{ArrayContainerInterface, ArrayAccessTrait}; interface SessionStorageInterface extends ArrayContainerInterface { function start(); function getId(); function setId(string $id); function getName(); function save(); function clear(); function isStarted(); } class NativeProvider extends ServiceProvider { function register(): void { $this->app->singleton(SessionStorageInterface::class, function ($app){
 return new SessionStorageNative(); }, 'session');}}
 class SessionStorageNative implements SessionStorageInterface { use ArrayAccessTrait; protected $items = []; protected $started = false; protected $session_namespace; function __construct(string $session_namespace = null){
 $this->session_namespace = $session_namespace; } function start(){
 if ($this->started){
 return true; } if (\PHP_SESSION_ACTIVE !== \session_status()){
 if (!\session_start()){
 throw new \RuntimeException('Failed to start the session');}}
 $this->loadSession(); $this->started = true; if (!$this->has('_token')){
 $this->regenerateToken(); } return true; } protected function loadSession(){
 $session_namespace = $this->session_namespace; if ($session_namespace){
 if (!isset($_SESSION[$session_namespace])){
 $_SESSION[$session_namespace] = []; } $this->items =& $_SESSION[$session_namespace]; } else { $this->items =& $_SESSION;}}
 function has(string $key): bool { $this->start(); return isset($this->items[$key]); } function get(string $key){
 $this->start(); return $this->items[$key] ?? null; } function set($key, $value): void { $this->start(); $this->items[$key] = $value; } function delete(string $key): bool { $this->start(); unset($this->items[$key]); return true; } function clear(){
 $this->items = []; } function destroy(){
 return \session_destroy(); } function save(){
 return true; } function isStarted(){
 return $this->started; } function getName(){
 return \session_name(); } function getId(){
 return \session_id(); } function setId(string $id){
 if (\session_status() === \PHP_SESSION_ACTIVE || !\ctype_alnum($id) || !\strlen($id) === 40){
 throw new \Exception('Session active or invalid id'); } \session_id($id); } function token(){
 return !$this->has('_token') ? $this->regenerateToken() : $this->get('_token'); } function regenerateToken(){
 $this->set('_token', $token = \md5(\uniqid('', true))); return $token;}}
 } namespace Dissonance\Http\Kernel { use Dissonance\Core\{ CoreInterface, BootstrapInterface, HttpKernelInterface, View\View, Runner, Support\ArrayableInterface, Support\RenderableInterface }; use Dissonance\Http\Middleware\{ MiddlewaresCollection, MiddlewaresDispatcher, MiddlewareCallback, MiddlewaresHandler, RequestPrefixMiddleware }; use Dissonance\Http\{ ResponseSender, PsrHttpFactory, UriHelper, ResponseMutable }; use Dissonance\{ Packages\AssetFileMiddleware, Apps\ApplicationInterface, Apps\AppsRepositoryInterface, Routing\RouteInterface }; use Psr\Http\Message\{ ResponseFactoryInterface, ServerRequestInterface, ResponseInterface, StreamInterface }; use Psr\Http\Server\RequestHandlerInterface; use function _DS\config; class Bootstrap implements BootstrapInterface { function bootstrap(CoreInterface $app): void { $app->singleton(HttpKernelInterface::class, HttpKernel::class); $app->addRunner(new HttpRunner($app));}}
 class HttpKernel implements HttpKernelInterface { protected $app; protected $bootstrappers = []; protected $mod_rewrite = null; function __construct(CoreInterface $container){
 $this->app = $container; if ($container->has('config')){
 $config = $container->get('config'); $this->mod_rewrite = $config->has('mod_rewrite') ? $config->get('mod_rewrite') : null;}}
 function bootstrap(): void { if (!$this->app->isBooted()){
 $this->app->addBootstraps($this->bootstrappers); $this->app->bootstrap();}}
 function handle(ServerRequestInterface $request): ResponseInterface { $app = $this->app; $dispatcher = $app->instance(MiddlewaresDispatcher::class, new MiddlewaresDispatcher()); $dispatcher->setDefaultCallback(function ($class) use ($app){
 return $app->make($class); }); return (new MiddlewaresCollection(\_DS\event($dispatcher)->factoryGroup(MiddlewaresDispatcher::GROUP_GLOBAL)))->process($request, $app->make(RoutingHandler::class)); } function response(int $code = 200, \Throwable $exception = null): ResponseInterface { $app = $this->app; $response = $app[ResponseFactoryInterface::class]->createResponse($code); if ($code >= 400){
 $path = $app('templates_package', 'ui_http_kernel') . '::'; if ($exception && config('debug')){
 $view = View::make($path . "exception", ['error' => $exception]); } else { $view = View::make($path . "error", ['response' => $response]); } $response->getBody()->write($view->__toString()); } return $response;}}
 class HttpRunner extends Runner { function isHandle(): bool { return $this->app['env'] === 'web'; } function run(): void { $app = $this->app; $symbiosis = \_DS\config('symbiosis', false); try { $request_interface = ServerRequestInterface::class; $request = $app[PsrHttpFactory::class]->createServerRequestFromGlobals(); $app->instance($request_interface, $request, 'request'); $app->alias($request_interface, get_class($request)); $base_uri = $this->prepareBaseUrl($request); $app['base_uri'] = $base_uri; $app['original_request'] = $request; $request = $request->withUri((new UriHelper())->deletePrefix($base_uri, $request->getUri())); $handler = $app['events']->dispatch(new PreloadKernelHandler($app->make(HttpKernelInterface::class))); $handler->prepend(new RequestPrefixMiddleware($app('config::uri_prefix', null))); $handler->append(new MiddlewareCallback(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface { if ($handler instanceof MiddlewaresHandler){
 $real = $handler->getRealHandler(); if ($real instanceof HttpKernelInterface){
 $real->bootstrap();}}
 return $handler->handle($request); })); $response = $handler->handle($request); if (!$app('destroy_response', false) || !$symbiosis){
 $this->sendResponse($response); if ($symbiosis){
 exit;}}
 } catch (\Throwable $e){
 if (!$symbiosis){
 $this->sendResponse($app[HttpKernelInterface::class]->response(500, $e));}}
 } static function closeOutputBuffers(int $targetLevel, bool $flush): void { $status = ob_get_status(true); $level = \count($status); $flags = \PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? \PHP_OUTPUT_HANDLER_FLUSHABLE : \PHP_OUTPUT_HANDLER_CLEANABLE); while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])){
 if ($flush){
 ob_end_flush(); } else { ob_end_clean();}}
 } protected function prepareBaseUrl(ServerRequestInterface $request): string { $server = $request->getServerParams(); $baseUrl = '/'; if (PHP_SAPI !== 'cli'){
 foreach (['PHP_SELF', 'SCRIPT_NAME', 'ORIG_SCRIPT_NAME'] as $v){
 $value = $server[$v]; if (!empty($value) && basename($value) == basename($server['SCRIPT_FILENAME'])){
 $this->file = basename($value); $request_uri = $request->getUri()->getPath(); $value = '/' . ltrim($value, '/'); if ($request_uri === preg_replace('~^' . preg_quote($value, '~') . '~i', '', $request_uri)){
 $app = $this->app; if (is_null($app('mod_rewrite'))){
 $this->app['mod_rewrite'] = true; } $value = dirname($value); } $baseUrl = $value; break;}}
 } return rtrim($baseUrl, '/' . \DIRECTORY_SEPARATOR); } function sendResponse(ResponseInterface $response){
 $sender = new ResponseSender($response); $sender->render(); if (\function_exists('fastcgi_finish_request')){
 \fastcgi_finish_request(); } elseif (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true)){
 static::closeOutputBuffers(0, true);}}
 } class PreloadKernelHandler extends MiddlewaresHandler { } class RouteHandler implements RequestHandlerInterface { protected $app; protected $route; function __construct(CoreInterface $app, RouteInterface $route){
 $this->app = $app; $this->route = $route; } function handle(ServerRequestInterface $request): ResponseInterface { $app = $this->app; $route = $app[RouteInterface::class] = $this->route; $app->alias(RouteInterface::class, 'route'); $apps = $app[AppsRepositoryInterface::class]; $action = $route->getAction(); $container = isset($action['app']) && $apps instanceof AppsRepositoryInterface ? $apps->getBootedApp($action['app']) : $this->app; $handler = $route->getHandler(); if (!is_string($handler) && !is_callable($handler)){
 throw new \Exception('Incorrect route handler for route ' . $route->getPath() . '!'); } $request_interface = ServerRequestInterface::class; $app->instance($request_interface, $request, 'request'); $app->alias($request_interface, \get_class($request)); $response = new ResponseMutable($app[ResponseFactoryInterface::class]->createResponse()); $app->instance(ResponseInterface::class, $response, 'response'); return $this->prepareResponse($container->call($handler, $route->getParams()), $response); } protected function prepareResponse($data, ResponseMutable $response): ResponseInterface { if ($data instanceof ResponseInterface){
 return $data; } elseif ($data instanceof StreamInterface){
 return $response->withBody($data)->getRealInstance(); } if (is_array($data) || $data instanceof \Traversable || $data instanceof ArrayableInterface || $data instanceof \JsonSerializable){
 $response->withHeader('content-type', 'application/json'); $data = \_DS\collect($data)->__toString(); } elseif ($data instanceof RenderableInterface || $data instanceof \Stringable){
 $data = $data->__toString(); } $response->getBody()->write((string)$data); return $response->getRealInstance();}}
 class RoutingHandler implements RequestHandlerInterface { protected $app; function __construct(CoreInterface $app){
 $this->app = $app; } function handle(ServerRequestInterface $request): ResponseInterface { $app = $this->app; $path = $request->getUri()->getPath(); $route = $app['router']->match($request->getMethod(), $path); if ($route){
 $middlewares = $route->getMiddlewares(); if (!empty($middlewares)){
 $app[MiddlewaresDispatcher::class]->factoryCollection($middlewares); } return (new MiddlewaresCollection($middlewares))->process($request, new RouteHandler($app, $route)); } else { $app['destroy_response'] = true; return \_DS\response(404, new \Exception('Route not found for path [' . $path . ']', 7623));}}
 } } namespace Dissonance\Http\Cookie { use Psr\Http\Message\{ResponseInterface, ServerRequestInterface}; use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface}; use Dissonance\{ Core\CoreInterface, Http\Middleware\MiddlewaresDispatcher, Core\ServiceProvider }; interface CookiesInterface extends \ArrayAccess { const COOKIE_HEADER = 'Cookie'; const SET_COOKIE_HEADER = 'Set-Cookie'; const SAMESITE_NONE = 'None'; const SAMESITE_LAX = 'Lax'; const SAMESITE_STRICT = 'Strict'; const SAMESITE_VALUES = [self::SAMESITE_NONE, self::SAMESITE_LAX, self::SAMESITE_STRICT]; function setDefaults(string $domain = null, string $path = null, int $expires = null, bool $secure = null, string $same_site = null); function setRequestCookies(array $cookies); function getResponseCookies(): array; function setCookie(string $name, string $value = '', int $expires = null, bool $httponly = null, string $path = null, string $domain = null, bool $secure = null, array $options = []); function toResponse(ResponseInterface $response): ResponseInterface; function has(string $name): bool; function get(string $name, string $default = null); function set(string $name, string $value = ''): void; function remove($names): void; } class Cookies implements CookiesInterface { protected $items = []; protected $domain; protected $path = ''; protected $secure = false; protected $expires = 0; protected $same_site; protected $request_cookies = []; function setDefaults(string $domain = null, string $path = null, int $expires = null, bool $secure = null, string $same_site = null){
 if ($domain){
 $this->domain = $domain; } if (!is_null($secure)){
 $this->secure = $secure; } if (is_int($expires)){
 $this->expires = $expires; } if ($path){
 $this->path = $path; } if ($same_site){
 if (!in_array($same_site, static::SAMESITE_VALUES)){
 throw new \Exception('Incorrect sameSite value(' . $same_site . ')'); } $this->same_site = $same_site;}}
 function setCookie(string $name, string $value = '', int $expires = null, bool $httponly = null, string $path = null, string $domain = null, bool $secure = null, array $options = []){
 $data = ['expires' => is_int($expires) ? $expires : $this->expires, 'httponly' => !empty($httponly), 'domain' => !is_null($domain) ? $domain : $this->domain, 'path' => is_null($path) ? $this->path : $path, 'secure' => !is_null($secure) ? $secure : $this->secure]; if (!isset($options['same_site']) && isset($this->same_site)){
 $options['same_site'] = $this->same_site; } $data = array_merge($data, $options); $cookie = $this->create($name, $value); foreach ($data as $k => $v){
 $cookie[$k] = $v; } return $this->items[] = $cookie; } protected function create($name, $value = ''){
 return ['name' => $name, 'value' => $value]; } function setRequestCookies(array $cookies){
 $this->request_cookies = $cookies; } function getResponseCookies(): array { return $this->items; } function set(string $name, string $value = ''): void { $this->setCookie($name, $value); } function has(string $name): bool { return isset($this->request_cookies[$name]); } function get(string $name, string $default = null){
 $cookies = $this->request_cookies; return isset($cookies[$name]) ? $cookies[$name] : $default; } function remove($names): void { foreach ((array)$names as $v){
 $this->setCookie($v, '', time() - 3600 * 48, true, $this->path, $this->domain);}}
 function toResponse(ResponseInterface $response): ResponseInterface { foreach ($this->items as $cookie){
 $response = $response->withAddedHeader(static::SET_COOKIE_HEADER, $this->cookieToResponse($cookie)); } return $response; } function cookieToResponse($cookie){
 return sprintf('%s=%s; ', $cookie['name'], urlencode($cookie['value'])) . (!empty($cookie['domain']) ? 'Domain=' . $cookie['domain'] . '; ' : '') . (!empty($cookie['path']) ? 'Path=' . $cookie['path'] . '; ' : '') . (isset($cookie['expires']) && $cookie['expires'] !== 0 ? sprintf('Expires=%s; ', gmdate('D, d M Y H:i:s T', $cookie['expires'])) : '') . (isset($cookie['max_age']) && is_int($cookie['max_age']) ? sprintf('Max-Age=%d; ', $cookie['max_age']) : '') . (!empty($cookie['secure']) ? 'Secure; ' : '') . (!empty($cookie['httponly']) ? 'HttpOnly; ' : '') . (!empty($cookie['same_site']) && in_array($cookie['same_site'], CookiesInterface::SAMESITE_VALUES) ? 'SameSite=' . $cookie['same_site'] . '; ' : ''); } function offsetExists($key){
 return $this->has($key); } function offsetGet($key){
 return $this->get($key); } function offsetSet($key, $value){
 $this->set($key, $value); } function offsetUnset($key){
 $this->remove($key);}}
 class CookiesMiddleware implements MiddlewareInterface { protected $cookies; function __construct(CookiesInterface $cookies){
 $this->cookies = $cookies; } function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface { $this->cookies->setRequestCookies($request->getCookieParams()); $response = $handler->handle($request); return $this->cookies->toResponse($response);}}
 class CookiesProvider extends ServiceProvider { function register(): void { $app = $this->app; $app->singleton(CookiesInterface::class, function (CoreInterface $app){
 $request = $app['request']; $expires = $app('config::cookie_expires', 3600 * 24 * 365); $cookies = $this->factoryCookiesClass(); if ($request instanceof ServerRequestInterface){
 $cookies->setDefaults($request->getUri()->getHost(), '/', $expires, $request->getUri()->getScheme() === 'https'); } else { $cookies->setDefaults($app['config::default_host'], '/', $expires); } return $cookies; }, 'cookie'); $app['listeners']->add(MiddlewaresDispatcher::class, function ($event) use ($app){
 $event->prependToGroup(MiddlewaresDispatcher::GROUP_GLOBAL, CookiesMiddleware::class); }); } protected function factoryCookiesClass(): CookiesInterface { return new Cookies();}}
 } namespace Dissonance\Event { use Psr\EventDispatcher\{EventDispatcherInterface, ListenerProviderInterface, StoppableEventInterface }; interface DispatcherInterface extends EventDispatcherInterface { } interface ListenersInterface extends ListenerProviderInterface { function add(string $event, $handler): void; } class EventDispatcher implements DispatcherInterface { protected $listenerProvider; function __construct(ListenerProviderInterface $listenerProvider){
 $this->listenerProvider = $listenerProvider; } function dispatch(object $event): object { foreach ($this->listenerProvider->getListenersForEvent($event) as $listener){
 $listener($event); if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()){
 return $event;}}
 return $event;}}
 class ListenerProvider implements ListenersInterface { protected $listenerWrapper; protected $listeners = []; function __construct(\Closure $listenerWrapper = null){
 $this->listenerWrapper = $listenerWrapper; } function add(string $event, $handler): void { $this->listeners[$event][] = $handler; } function getListenersForEvent(object $event): iterable { $parents = \class_parents($event); $implements = \class_implements($event); $classes = array_merge([\get_class($event)], $parents ?: [], $implements ?: []); $listeners = []; foreach ($classes as $v){
 $listeners = array_merge($listeners, isset($this->listeners[$v]) ? $this->listeners[$v] : []); } $wrapper = $this->listenerWrapper; return $wrapper ? array_map(function ($v) use ($wrapper){
 return $wrapper($v); }, $listeners) : $listeners;}}
 } namespace Dissonance\Core\Bootstrap { use Dissonance\Core\{ BootstrapInterface, CoreInterface, Config, Events\CacheClear, View\View, AbstractBootstrap, ProvidersRepository }; use Dissonance\Event\{ EventDispatcher, ListenerProvider, ListenersInterface }; use Psr\EventDispatcher\{ListenerProviderInterface, EventDispatcherInterface}; use Dissonance\Packages\{LazyPackagesDecorator, PackagesRepositoryInterface}; use function _DS\config; class BootBootstrap { function bootstrap($app){
 $app->boot();}}
 class CoreBootstrap implements BootstrapInterface { function bootstrap(CoreInterface $app): void { $app->singleton(Config::class, function ($app){
 return new Config($app['bootstrap_config']); }, 'config'); View::setContainer($app); $console_running_key = 'APP_RUNNING_IN_CONSOLE'; if (isset($_ENV[$console_running_key]) && $_ENV[$console_running_key] === 'true' || \in_array(\php_sapi_name(), ['cli', 'phpdbg'])){
 $app['env'] = 'console'; } else { $app['env'] = 'web'; } \date_default_timezone_set($app('config::core.timezone', 'UTC')); \mb_internal_encoding('UTF-8'); $storage_path = $app('config::storage_path'); if ($storage_path){
 $app['storage_path'] = $storage_path = \rtrim($storage_path, '\\/'); $app['cache_path'] = $storage_path . '/cache/'; $app['cache_path_core'] = $storage_path . '/cache/core'; } $start_bootstrappers = $app->get('config::bootstrappers'); if (\is_array($start_bootstrappers)){
 foreach ($start_bootstrappers as $class){
 $app->runBootstrap($class);}}
 $app['listeners']->add(CacheClear::class, function (CacheClear $event) use ($app){
 if ($event->getPath() === 'all' || $event->getPath() === 'core'){
 $app['cache_cleaned'] = true; } });}}
 class EventBootstrap implements BootstrapInterface { function bootstrap(CoreInterface $app): void { $listener_interface = ListenerProviderInterface::class; $app->singleton($listener_interface, function ($app){
 return new ListenerProvider(function ($listener) use ($app){
 return function (object $event) use ($listener, $app){
 if (is_string($listener) && class_exists($listener)){
 $handler = $app->make($listener); if (method_exists($handler, 'handle') || is_callable($handler)){
 return $app->call([$handler, method_exists($handler, 'handle') ? 'handle' : '__invoke'], ['event' => $event]); } return null; } elseif ($listener instanceof \Closure){
 return $app->call($listener, ['event' => $event]); } }; }); }, 'listeners')->alias($listener_interface, ListenersInterface::class); $app->singleton(EventDispatcherInterface::class, EventDispatcher::class, 'events');}}
 class LazyPackagesBootstrap extends AbstractBootstrap { function bootstrap(CoreInterface $app): void { $app->extend(PackagesRepositoryInterface::class, function (PackagesRepositoryInterface $repo, $app){
 return new LazyPackagesDecorator($repo, $app('cache_path_core')); });}}
 class ProvidersBootstrap extends AbstractBootstrap { function bootstrap($app): void { $providers_class = ProvidersRepository::class; $this->cached($app, $providers_class); $providers_repository = $app[$providers_class]; $providers_repository->load($app, config('providers', []), config('providers_exclude', []));}}
 } namespace Dissonance\View\Blade { use Dissonance\Packages\{TemplateCompilerInterface, TemplateCompiler}; use Dissonance\Core\CoreInterface; class Bootstrap implements \Dissonance\Core\BootstrapInterface { function bootstrap(CoreInterface $app): void { $app->afterResolving(TemplateCompiler::class, function (TemplateCompiler $compiler){
 $compiler->addCompiler(new Blade()); });}}
 class Blade implements TemplateCompilerInterface { protected static $compilers = array('extensions', 'comments', 'php', 'define', 'echos', 'forelse', 'empty', 'endforelse', 'structure_openings', 'structure_closings', 'else', 'unless', 'endunless', 'includes', 'render_each', 'render', 'yields', 'yield_sections', 'section_start', 'section_end', 'url', 'asset', 'route', 'show'); protected static $extensions = array(); function getExtensions(): array { return ['blade', 'blade.php']; } static function extend(\Closure $compiler){
 static::$extensions[] = $compiler; } function compile(string $template): string { foreach (static::$compilers as $compiler){
 $method = "compile_{$compiler}"; $template = call_user_func([$this, $method], $template); } $template = $this->compile_layouts($template); return $template; } protected function compile_layouts($value){
 if (strpos($value, '@layout') !== false){
 $key = 'layout'; } elseif (strpos($value, '@extends') !== false){
 $key = 'extends'; } else { return $value; } $value = rtrim($value); preg_match('/@' . $key . '(\\s*\\(.*\\))(\\s*)/', $value, $matches); $layout = str_replace(array("('", "')", '("', ')"'), '', $matches[1]); $lines = preg_split("/(\r?\n)/", $value); $code = implode(PHP_EOL, array_slice($lines, 1)); return '<?php echo $__view->layout("' . $layout . '", \'' . str_replace("'", "\\'", $code) . '\', get_defined_vars(), ' . ($key == 'extends' ? 'true' : '') . ')->render(); ?>'; } protected function compile_php($value){
 return preg_replace('/\\@php(.+?)@endphp/is', '<?php ${1}; ?>', $value); } protected function compile_comments($value){
 return preg_replace('/\\{\\{--((.|\\s)*?)--\\}\\}/', "<?php /** \$1 **/ ?>\n", $value); } protected function compile_echos($value){
 $value = preg_replace('/\\{!!(.+?)!!\\}/', '<?php echo $1; ?>', $value); return preg_replace('/\\{\\{(.+?)\\}\\}/', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\', false); ?>', $value); } protected function compile_define($value){
 $value = preg_replace('/\\{\\{\\{(.+?)\\}\\}\\}/', '<?php  $1;  ?>', $value); return $value; } protected function compile_forelse($value){
 preg_match_all('/(\\s*)@forelse(\\s*\\(.*\\))(\\s*)/', $value, $matches); foreach ($matches[0] as $forelse){
 preg_match('/\\s*\\(\\s*(\\S*)\\s/', $forelse, $variable); $if = "<?php if (count({$variable[1]}) > 0): ?>"; $search = '/(\\s*)@forelse(\\s*\\(.*\\))/'; $replace = '$1' . $if . '<?php foreach$2: ?>'; $blade = preg_replace($search, $replace, $forelse); $value = str_replace($forelse, $blade, $value); } return $value; } protected function compile_empty($value){
 $value = str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value); return str_replace('@continue', '<?php continue; ?>', $value); } protected function compile_endforelse($value){
 return str_replace('@endforelse', '<?php endif; ?>', $value); } protected function compile_structure_openings($value){
 $pattern = '/(\\s*)@(if|elseif|foreach|for|while)(\\s*\\(.*\\))/'; return preg_replace($pattern, '$1<?php $2$3: ?>', $value); } protected function compile_structure_closings($value){
 $pattern = '/(\\s*)@(endif|endforeach|endfor|endwhile)(\\s*)/'; return preg_replace($pattern, '$1<?php $2; ?>$3', $value); } protected function compile_else($value){
 return preg_replace('/(\\s*)@(else)(\\s*)/', '$1<?php $2: ?>$3', $value); } protected function compile_unless($value){
 $pattern = '/(\\s*)@unless(\\s*\\(.*\\))/'; return preg_replace($pattern, '$1<?php if ( ! ($2)): ?>', $value); } protected function compile_endunless($value){
 return str_replace('@endunless', '<?php endif; ?>', $value); } protected function compile_includes($value){
 $pattern = $this->matcher('include'); return preg_replace($pattern, '$1<?php echo \\Dissonance\\Core\\View\\View::make$2->with(get_defined_vars())->render(); ?>', $value); } protected function compile_render($value){
 $pattern = $this->matcher('render'); return preg_replace($pattern, '$1<?php echo render$2; ?>', $value); } protected function compile_url($value){
 $pattern = $this->matcher('url'); return preg_replace($pattern, '$1<?php echo  $__view->url$2; ?>', $value); } protected function compile_asset($value){
 $pattern = $this->matcher('asset'); return preg_replace($pattern, '$1<?php echo  $__view->asset$2; ?>', $value); } protected function compile_route($value){
 $pattern = $this->matcher('route'); return preg_replace($pattern, '$1<?php echo  $__view->route$2; ?>', $value); } protected function compile_render_each($value){
 $pattern = $this->matcher('render_each'); return preg_replace($pattern, '$1<?php echo render_each$2; ?>', $value); } protected function compile_yields($value){
 $pattern = $this->matcher('yield'); return preg_replace($pattern, '$1<?php echo  $__view->yield$2; ?>', $value); } protected function compile_yield_sections($value){
 $replace = '<?php echo  $__view->sectionYieldSection(); ?>'; return str_replace('@yield_section', $replace, $value); } protected function compile_section_start($value){
 $pattern = $this->matcher('section'); return preg_replace($pattern, '$1<?php $__view->start$2; ?>', $value); } protected function compile_section_end($value){
 return preg_replace('/@endsection|@stop/', '<?php $__view->stop(); ?>', $value); } protected function compile_show($value){
 return preg_replace('/@show/', '<?php $__view->yield($__view->stop()); ?>', $value); } protected function compile_extensions($value){
 foreach (static::$extensions as $compiler){
 $value = $compiler($value); } return $value; } function matcher($function){
 return '/(\\s*)@' . $function . '(\\s*\\(.*\\))/';}}
 } namespace Dissonance\Routing { use Dissonance\Core\{ ServiceProvider, Support\Collection, CoreInterface, Support\Str, Support\Arr }; use Dissonance\{ Packages\PackagesRepositoryInterface, Container\DIContainerInterface, SimpleCacheFilesystem\SimpleCacheInterface, Container\CachedContainerInterface, Apps\AppConfigInterface }; use Closure; trait AddRouteTrait { function get(string $uri, $action): RouteInterface { return $this->addRoute(['GET', 'HEAD'], $uri, $action); } function head(string $uri, $action): RouteInterface { return $this->addRoute('HEAD', $uri, $action); } function post(string $uri, $action): RouteInterface { return $this->addRoute('POST', $uri, $action); } function put(string $uri, $action): RouteInterface { return $this->addRoute('PUT', $uri, $action); } function delete(string $uri, $action): RouteInterface { return $this->addRoute('DELETE', $uri, $action); } function options(string $uri, $action): RouteInterface { return $this->addRoute('OPTIONS', $uri, $action);}}
 trait NamedRouterTrait { protected $name = ''; function setName(string $name){
 $this->name = $name; } function getName(): string { return $this->name;}}
 interface AppRoutingInterface { function loadBackendRoutes(RouterInterface $router); function loadApiRoutes(RouterInterface $router); function loadFrontendRoutes(RouterInterface $router); function loadDefaultRoutes(RouterInterface $router); function getAppId(): string; } interface NamedRouterInterface { function setName(string $name); function getName(): string; } interface RouteInterface { function getPath(): string; function getName(): ?string; function getAction(): array; function isStatic(): bool; function getMiddlewares(): array; function getSecure(): bool; function getDomain(): ?string; function setDomain(string $domain); function getHandler(); function setParam($key, $value); function getParam($key); function getParams(): array; } interface RouterFactoryInterface { function factoryRouter(array $params = []): RouterInterface; function loadRoutes(RouterInterface $router); } interface RouterInterface { function setRoutesDomain(string $domain); function addRoute($httpMethods, string $uri, $action): RouteInterface; function get(string $uri, $action): RouteInterface; function post(string $uri, $action): RouteInterface; function head(string $uri, $action): RouteInterface; function put(string $uri, $action): RouteInterface; function delete(string $uri, $action): RouteInterface; function options(string $uri, $action): RouteInterface; function group(array $attributes, callable $routes); function getRoute(string $name): ?RouteInterface; function getBySettlement(string $settlement): array; function getRoutes(string $httpMethod = null): array; function match(string $httpMethod, string $uri): ?RouteInterface; } interface SettlementsInterface { function getByRouter(string $router); function getByUrl(string $url): ?Settlement; function getByKey(string $key, $value, $all = false); } interface UrlGeneratorInterface { } interface LazyRouterInterface extends NamedRouterInterface { function isLoadedRoutes(): bool; function loadRoutes(); } class AppsRoutesRepository { protected $providers = []; function append(AppRoutingInterface $routing){
 $this->providers[$routing->getAppId()] = $routing; } function getByAppId($app_id){
 return $this->providers[$app_id] ?? null; } function getProviders(){
 return $this->providers;}}
 class Settlement { protected $config = []; protected $path = '/'; function __construct(array $config){
 $this->config = $config; $this->path = '/' . trim($config['prefix'], '\\/ '); } function getPath(): string { return $this->path; } function getUriWithoutSettlement(string $uri): string { return preg_replace('/^' . preg_quote($this->getPath(), '/') . '/uDs', '/', $uri); } function getRouter(): string { return $this->get('router'); } function validatePath(string $path){
 return (bool)preg_match('/^' . preg_quote($this->getPath(), '/') . '.*/uDs', $path, $r); } function get(string $name = null, $default = null){
 return !$name ? $this->config : (isset($this->config[$name]) ? $this->config[$name] : $default);}}
 class SettlementFactory { function make(array $parameters = []){
 return new Settlement($parameters);}}
 class AppRouting implements AppRoutingInterface { protected $app_id; protected $controllers_namespace = null; function __construct(string $app_id, string $controllers_namespace = null){
 $this->app_id = $app_id; $this->controllers_namespace = $controllers_namespace; } function backendRoutes(RouterInterface $router){
 } function frontendRoutes(RouterInterface $router){
 } function apiRoutes(RouterInterface $router){
 } function defaultRoutes(RouterInterface $router){
 } function loadBackendRoutes(RouterInterface $router){
 $options = $this->getRoutingOptions(); unset($options['prefix']); unset($options['as']); $router->group($options, $this->getLoadRoutesCallback('backendRoutes')); } function loadApiRoutes(RouterInterface $router){
 $options = $this->getRoutingOptions(); unset($options['prefix']); unset($options['as']); $router->group($options, $this->getLoadRoutesCallback('apiRoutes')); } function loadFrontendRoutes(RouterInterface $router){
 $options = $this->getRoutingOptions(); unset($options['prefix']); unset($options['as']); $router->group($options, $this->getLoadRoutesCallback('frontendRoutes')); } function loadDefaultRoutes(RouterInterface $router){
 $router->group(['namespace' => $this->controllers_namespace], $this->getLoadRoutesCallback('defaultRoutes')); } protected function getRoutingOptions(){
 $id = $this->app_id; return ['prefix' => $id, 'app' => $id, 'as' => $id, 'namespace' => $this->controllers_namespace]; } protected function loadPrefixRoutes(RouterInterface $router, $function){
 $router->group($this->getRoutingOptions(), $this->getLoadRoutesCallback($function)); } protected function getLoadRoutesCallback($method){
 return function (RouterInterface $router) use ($method){
 $this->{$method}($router); }; } function getAppId(): string { return $this->app_id;}}
 class CacheRouterDecorator implements RouterInterface { use AddRouteTrait; protected $factory = null; protected $router = null; protected $cache_key = ''; protected $allowed_cache = true; function __construct(RouterFactoryInterface $factory, RouterInterface $router, string $cache_key){
 $this->factory = $factory; $this->router = $router; $this->cache_key = $cache_key; } function getCacheKey(): string { return $this->cache_key; } function isAllowedCache(){
 return $this->allowed_cache; } function getRealInstance(): RouterInterface { return $this->router; } function setRoutesDomain(string $domain){
 $this->call(__FUNCTION__, func_get_args()); } function addRoute($httpMethods, string $uri, $action): RouteInterface { $this->checkCallbacks($action); return $this->call(__FUNCTION__, func_get_args()); } private function checkCallbacks($data){
 if (is_array($data)){
 if (isset($data['middleware'])){
 foreach ((array)$data['middleware'] as $v){
 if ($v instanceof Closure){
 $this->allowed_cache = false;}}
 } if (isset($data['uses']) && $data['uses'] instanceof Closure){
 $this->allowed_cache = false;}}
 elseif ($data instanceof Closure){
 $this->allowed_cache = false;}}
 function group(array $attributes, callable $routes){
 $this->checkCallbacks($attributes); $this->router->group($attributes, function ($real_router) use ($routes){
 $routes($this); }); } function getRoute(string $name): ?RouteInterface { return $this->call(__FUNCTION__, func_get_args()); } function getBySettlement(string $settlement): array { return $this->call(__FUNCTION__, func_get_args()); } function getRoutes(string $httpMethod = null): array { return $this->call(__FUNCTION__, func_get_args()); } function match(string $httpMethod, string $uri): ?RouteInterface { return $this->call(__FUNCTION__, func_get_args()); } protected function call($method, $parameters){
 return call_user_func_array([$this->router, $method], $parameters);}}
 class CacheRoutingProvider extends ServiceProvider { function register(): void { $app = $this->app; $app->extend(RouterFactoryInterface::class, function ($factory) use ($app){
 return new RouterCacheFactory($factory, $app('cache')); });}}
 class PackagesSettlements implements SettlementsInterface { protected $settlements; protected $factory; protected $packages; function __construct(SettlementsInterface $settlements, PackagesRepositoryInterface $packages, SettlementFactory $factory){
 $this->settlements = $settlements; $this->factory = $factory; $this->packages = $packages; } function getByRouter(string $router){
 return $this->getSettlementByString($router); } function getByUrl(string $url): ?Settlement { $path = $this->getPathByUrl($url); return $this->getSettlementByString($path); } protected function getSettlementByString(string $string): ?Settlement { if (preg_match('~^(backend|api|default):([0-9a-z_\\-\\.]+)|/(backend|api|default)/(.[^/]+)~', $string, $m)){
 if (!empty($m[1]) && !empty($m[2])){
 $router = $m[1]; $app_id = $m[2]; } else { $router = $m[3]; $app_id = $m[4]; } if ($this->packages->has($app_id)){
 return $this->factory->make(['prefix' => '/' . $router . '/' . $app_id . '/', 'router' => $router . ':' . $app_id]);}}
 $app_id = explode('/', ltrim($string, '\\/'))[0]; if (!empty($app_id) && $this->packages->has($app_id)){
 return $this->factory->make(['prefix' => $app_id, 'router' => $app_id]); } return null; } function getByKey(string $key, $value, $all = false){
 $callback = function ($settlement) use ($key, $value){
 return $settlement->get($key) === $value; }; $result = []; foreach ($this->items as $v){
 if ($v->get($key) === $value){
 if ($all){
 $result[] = $v; } else { return $v;}}
 } return $result; } static function normalizePrefix(string $prefix): string { $prefix = trim($prefix, ' \\/'); return $prefix == '' ? '/' : '/' . $prefix . '/'; } function getPathByUrl(string $url): string { return preg_replace('~(^((.+?\\..+?)[/])|(^(https?://)?localhost(:\\d+)?[/]))(.*)~i', '/', $url);}}
 class Provider extends ServiceProvider { function register(): void { $this->registerFactory(); $this->registerRoutesRepository(); $this->registerRouter(); $this->registerUriGenerator(); } protected function registerFactory(){
 $this->app->singleton(RouterFactoryInterface::class, function (DIContainerInterface $app){
 $class = $this->getFactoryClass(); return new $class($app, $this->getRouterClass(), $this->routesLoaderCallback(), $this->app['request']->getUri()->getHost()); }); } protected function getFactoryClass(){
 return RouterFactory::class; } protected function getRouterClass(){
 return Router::class; } protected function routesLoaderCallback(){
 return function (RouterInterface $router){
 foreach ($this->app[AppsRoutesRepository::class]->getProviders() as $provider){
 $app_id = $provider->getAppId(); $router->group(['prefix' => $app_id, 'as' => $app_id . '::', 'app' => $app_id], function ($router) use ($provider){
 $provider->loadFrontendRoutes($router); }); $router->group(['prefix' => 'api/' . $app_id, 'as' => 'api:' . $app_id . '::', 'app' => $app_id], function ($router) use ($provider){
 $provider->loadApiRoutes($router); }); $router->group(['prefix' => 'backend/' . $app_id, 'as' => 'backend:' . $app_id . '::', 'app' => $app_id], function ($router) use ($provider){
 $provider->loadBackendRoutes($router); }); $router->group(['as' => 'default:' . $app_id . '::', 'app' => $app_id], function ($router) use ($provider){
 $provider->loadDefaultRoutes($router); }); } }; } protected function registerRoutesRepository(){
 $this->app->singleton(AppsRoutesRepository::class)->afterResolving(AppsRoutesRepository::class, function (AppsRoutesRepository $repository){
 return \_DS\event($repository); }); } protected function registerRouter(){
 $this->app->singleton(RouterInterface::class, function ($app){
 $f = $app[RouterFactoryInterface::class]; $router = $f->factoryRouter(['name' => 'default']); $f->loadRoutes($router); return $router; }, 'router'); } protected function registerUriGenerator(){
 $this->app->singleton(UrlGeneratorInterface::class, function (CoreInterface $app){
 $base_uri = $app['base_uri']; $prefix = $app['config::uri_prefix']; if (!empty($prefix)){
 $base_uri = rtrim($base_uri, '\\/') . '/' . trim($prefix, '\\/'); } return new UrlGenerator($app['router'], $base_uri, trim($app('config::assets_prefix', 'assets'), '/')); }, 'url'); } function boot(): void { } protected function getFactory(){
 return $this->app->make(RouterFactoryInterface::class);}}
 class Route implements RouteInterface { protected $action = []; protected $pattern = ''; protected $request_params = []; function __construct(string $uri, array $action){
 $this->pattern = trim($uri, '/'); $this->action = $action; } function getName(): string { return $this->action['as'] ?? $this->pattern; } function isStatic(): bool { return strpos($this->getPath(), '{') === false; } function getAction(): array { return $this->action; } function getMiddlewares(): array { return isset($this->action['middleware']) ? $this->action['middleware'] : []; } function setDomain(string $domain){
 $this->action['domain'] = $domain; return $this; } function getSecure(): bool { return isset($this->action['secure']) ? (bool)$this->action['secure'] : false; } function getDomain(): ?string { return $this->action['domain'] ?? null; } function getApp(): ?string { return $this->action['app'] ?? null; } function getPath(): string { return $this->pattern; } function getHandler(){
 return $this->action['uses']; } function setParam($key, $value){
 $this->request_params[$key] = $value; } function getParams(): array { return $this->request_params; } function getParam($name){
 return $this->request_params[$name] ?? null;}}
 class Router implements RouterInterface { use AddRouteTrait; public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']; protected $groupStack = []; protected $routes = []; protected $named_routes = []; protected $domain = ''; function __construct(){
 foreach (static::$verbs as $verb){
 $this->routes[$verb] = [];}}
 function setRoutesDomain(string $domain){
 $this->domain = $domain; } function addRoute($httpMethods, string $uri, $action): RouteInterface { $httpMethods = array_map('strtoupper', (array)$httpMethods); $route = $this->createRoute($uri, $action, $httpMethods); $this->setRoute($route); return $route; } function setRoute(RouteInterface $route){
 if ($this->domain && !$route->getDomain()){
 $route->setDomain($this->domain); } foreach ($route->getAction()['methods'] as $method){
 $this->routes[$method][$route->getPath()] = $route; } $name = $route->getName(); if ($name){
 $this->named_routes[$name] = $route; } return $route; } public $count_routes = 0; protected function createRoute(string $uri, $action, array $httpMethods){
 $this->count_routes++; if (is_string($action) || $action instanceof \Closure){
 $action = ['uses' => $action]; } if (is_array($action)){
 if (!empty($this->groupStack)){
 $group = end($this->groupStack); if (isset($action['uses']) && is_string($action['uses'])){
 $class = $action['uses']; $action['uses'] = isset($group['namespace']) && strpos($class, '\\') !== 0 ? rtrim($group['namespace'], '\\') . '\\' . $class : $class; } $action = static::mergeAttributes($action, $group); $uri = trim(trim(isset($group['prefix']) ? $group['prefix'] : '', '/') . '/' . trim($uri, '/'), '/') ?: '/';}}
 $action['methods'] = $httpMethods; return new Route($uri, $action); } function getRoute(string $name): ?RouteInterface { return $this->named_routes[$name] ?? null; } function group(array $attributes, callable $routes){
 $attributes = static::mergeAttributes($attributes, !empty($this->groupStack) ? end($this->groupStack) : []); $this->groupStack[] = $attributes; $routes($this); array_pop($this->groupStack); } function match(string $httpMethod, string $uri): ?RouteInterface { $uri = trim($uri, '/'); $httpMethod = strtoupper($httpMethod); $all_routes = $this->getRoutes(); $routes = isset($all_routes[$httpMethod]) ? $all_routes[$httpMethod] : []; foreach ($routes as $route){
 $vars = []; $pattern = \preg_replace('/(^|[^\\.])\\*/ui', '$1.*?', \str_replace(array(' ', '.', '('), array('\\s', '\\.', '(?:'), $route->getPath())); if (\preg_match_all('/\\{([a-z_]+):?([^\\}]*)?\\}/ui', $pattern, $match, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)){
 $offset = 0; foreach ($match as $m){
 $vars[] = $m[1][0]; $p = $m[2][0] ? $m[2][0] : '.*?'; $pattern = substr($pattern, 0, $offset + $m[0][1]) . '(' . $p . ')' . substr($pattern, $offset + $m[0][1] + strlen($m[0][0])); $offset = $offset + strlen($p) + 2 - strlen($m[0][0]);}}
 if (preg_match('!^' . $pattern . '$!ui', $uri, $match)){
 if ($vars){
 $route = clone $route; array_shift($match); foreach ($vars as $i => $v){
 if (isset($match[$i])){
 $route->setParam($v, $match[$i]);}}
 } return $route;}}
 return null; } protected static function mergeAttributes(array $new, array $old){
 $as = 'as'; if (isset($old[$as])){
 $is_app = substr($old[$as], -2) === '::'; $new[$as] = $old[$as] . (isset($new[$as]) ? ($is_app ? '' : '.') . $new[$as] : ''); } $module = 'module'; if (!isset($new[$module]) && isset($old[$module])){
 $new[$module] = $old[$module]; } $secure = 'secure'; if (!isset($new[$secure]) && isset($old[$secure])){
 $new[$secure] = $old[$secure]; } $namespace = 'namespace'; if (isset($new[$namespace])){
 $new[$namespace] = isset($old[$namespace]) && strpos($new[$namespace], '\\') !== 0 ? rtrim($old[$namespace], '\\') . '\\' . trim($new[$namespace], '\\') : '\\' . trim($new[$namespace], '\\'); } elseif (isset($old[$namespace])){
 $new[$namespace] = $old[$namespace]; } else { $new[$namespace] = null; } $prefix = 'prefix'; $old_p = isset($old[$prefix]) ? $old[$prefix] : null; $new[$prefix] = isset($new[$prefix]) ? trim($old_p, '/') . '/' . trim($new[$prefix], '/') : $old_p; foreach ([$as, $module, $namespace, $prefix] as $v){
 if (array_key_exists($v, $old)){
 unset($old[$v]);}}
 return array_merge_recursive($old, $new); } function getRoutes(string $httpMethod = null): array { if ($httpMethod && in_array(strtoupper($httpMethod), static::$verbs)){
 return $this->routes[strtoupper($httpMethod)]; } return $this->routes; } function getBySettlement(string $settlement): array { $routes = []; foreach ($this->named_routes as $v){
 if (preg_match('/^' . preg_quote($settlement, '/') . '/', $v->getName())){
 $routes[$v->getName()] = $v;}}
 return $routes;}}
 class RouterCacheFactory implements RouterFactoryInterface { protected $factory = null; protected $cache = null; function __construct(RouterFactoryInterface $factory, SimpleCacheInterface $cache = null){
 $this->factory = $factory; $this->cache = $cache; } function factoryRouter(array $params = []): RouterInterface { if ($this->cache){
 $cache_key = 'router_' . \md5(\serialize($params)); $data = $this->cache->get($cache_key, $t = \uniqid()); if ($data === $t){
 $router = $this->factory->factoryRouter($params); $class = $router instanceof LazyRouterInterface ? CacheLazyRouterDecorator::class : CacheRouterDecorator::class; $data = new $class($this, $this->factory->factoryRouter($params), $cache_key); } return $data; } return $this->factory->factoryRouter($params); } function loadRoutes(RouterInterface $router){
 $this->factory->loadRoutes($router); if ($this->cache && $router instanceof CacheRouterDecorator && $router->isAllowedCache()){
 $this->cache->set($router->getCacheKey(), $router->getRealInstance());}}
 } class RouterFactory implements RouterFactoryInterface { protected $router_class = null; protected $routes_loader_callback = null; protected $domain = null; protected $app; function __construct(DIContainerInterface $app, string $router_class, callable $routes_loader_callback, string $domain = null){
 $this->app = $app; $this->router_class = $router_class; $this->domain = $domain; $this->routes_loader_callback = $routes_loader_callback; } function factoryRouter(array $params = []): RouterInterface { $router = new $this->router_class(); $router->setRoutesDomain($this->domain); return $router; } function loadRoutes(RouterInterface $router){
 $callable = $this->routes_loader_callback; $callable($router);}}
 class RouterNamedFactory extends RouterFactory { function factoryRouter(array $params = []): RouterInterface { $factory = $this->app[RouterFactoryInterface::class]; $router = new $this->router_class($factory); $router->setRoutesDomain($this->domain); if (isset($params['name']) && $router instanceof NamedRouterInterface){
 $router->setName($params['name']); } return $router;}}
 class SettlementRouteDecorator implements RouteInterface { protected $route = null; protected $settlement = null; protected $path; function __construct(RouteInterface $route, Settlement $settlement){
 $this->route = $route; $this->settlement = $settlement; $this->path = $this->settlement->getPath() . '/' . ltrim($this->route->getPath(), '\\/'); } function getName(): string { return $this->route->getName(); } function isStatic(): bool { return $this->route->isStatic(); } function getAction(): array { return $this->route->getAction(); } function getMiddlewares(): array { return $this->route->getMiddlewares(); } function getPath(): string { return $this->path; } function getHandler(){
 return $this->route->getHandler(); } function setParam($key, $value){
 return $this->route->setParam($key, $value); } function getParam($key){
 return $this->route->getParam($key); } function getParams(): array { return $this->route->getParams(); } function getSecure(): bool { return $this->route->getSecure(); } function getDomain(): ?string { return $this->route->getDomain(); } function setDomain(string $domain){
 return $this->route->setDomain($domain);}}
 class Settlements implements SettlementsInterface { protected $items = []; protected $find_patterns = []; function __construct(\Closure $items, SettlementFactory $factory){
 $pattern = ''; $index = 0; $counter = 0; foreach ($items() as $v){
 $pattern .= '(?<id_' . $index . '>^/' . preg_quote(trim($v['prefix'], '\\/'), '~') . '/.*)|'; $this->items[$index++] = is_array($v) ? new Settlement($v) : $v; if ($counter === 60){
 $this->find_patterns[] = '~' . rtrim($pattern, '|') . '~'; $pattern = ''; $counter = 0; } else { $counter++;}}
 if ($counter > 0){
 $this->find_patterns[] = '~' . rtrim($pattern, '|') . '~';}}
 function addSettlement(array $data){
 } function getByRouter(string $router){
 return $this->getByKey('router', $router); } function getByUrl(string $url): ?Settlement { $path = $this->getPathByUrl($url); foreach ($this->find_patterns as $find_pattern){
 if (preg_match($find_pattern, $path, $m) === 1){
 foreach ($m as $k => $v){
 if (is_int($k) || empty($v)){
 continue; } $id = substr($k, 3); return $this->items[$id]; var_dump($path); var_dump($find_pattern); var_dump($m); exit;}}
 } return $this->first(function ($settlement) use ($path){
 return $settlement->validatePath($path); }); } function validatePath(string $path){
 return (bool)preg_match('/^' . preg_quote($this->getPath(), '/') . '.*/uDs', $path, $r); } function getByKey(string $key, $value, $all = false){
 $callback = function ($settlement) use ($key, $value){
 return $settlement->get($key) === $value; }; $result = []; foreach ($this->items as $v){
 if ($v->get($key) === $value){
 if ($all){
 $result[] = $v; } else { return $v;}}
 } return $result; } static function normalizePrefix(string $prefix): string { $prefix = trim($prefix, ' \\/'); return $prefix == '' ? '/' : '/' . $prefix . '/'; } function getPathByUrl(string $url): string { return preg_replace('~(^((.+?\\..+?)[/])|(^(https?://)?localhost(:\\d+)?[/]))(.*)~i', '/', $url);}}
 class SettlementsRoutingProvider extends Provider { function boot(): void { } function register(): void { parent::register(); if ($this->app instanceof CachedContainerInterface){
 } if (!$this->app->bound(SettlementsInterface::class)){
 $this->app->singleton(SettlementsInterface::class, function ($app){
 $generator = function () use ($app){
 $settlements = $app('config::settlements', []); foreach ($settlements as $v){
 (yield $v); } }; $factory = $app[SettlementFactory::class]; $settlements = new Settlements($generator, $factory); if ($app('config::packages_settlements', true)){
 $settlements = new PackagesSettlements($settlements, $app[PackagesRepositoryInterface::class], $factory); } return $settlements; }, 'settlements'); } $this->app->alias(SettlementsInterface::class, 'settlements'); } protected function registerRouter(){
 $this->app->singleton(RouterInterface::class, function (DIContainerInterface $app){
 return new SettlementsRouter($this->getFactory(), $app['settlements']); }, 'router'); } protected function getFactoryClass(){
 return RouterNamedFactory::class; } protected function getRouterClass(){
 return RouterLazy::class; } protected function routesLoaderCallback(){
 $app = $this->app; return function (RouterInterface $router) use ($app){
 $router_name = $router->getName(); $repo = $app[AppsRoutesRepository::class]; if ($provider = $repo->getByAppId($router_name)){
 $provider->loadFrontendRoutes($router); } else { if (preg_match('~^(backend|api):([0-9a-z_\\-\\.]+)~', $router_name, $m)){
 $action = $m[1]; $app_id = $m[2]; $provider = $repo->getByAppId($app_id); if (!$provider){
 return; } if ($action === 'backend'){
 $provider->loadBackendRoutes($router); } elseif ($action === 'api'){
 $provider->loadApiRoutes($router);}}
 else { if ($router_name === 'default'){
 foreach ($app[AppsRoutesRepository::class]->getProviders() as $provider){
 $app_id = $provider->getAppId(); $router->group(['as' => $app_id, 'app' => $app_id], function ($router) use ($provider){
 $provider->loadDefaultRoutes($router); });}}
 } } };}}
 class UrlGenerator implements UrlGeneratorInterface { public $defaultParameters = []; public $dontEncode = ['%2F' => '/', '%40' => '@', '%3A' => ':', '%3B' => ';', '%2C' => ',', '%3D' => '=', '%2B' => '+', '%21' => '!', '%2A' => '*', '%7C' => '|', '%3F' => '?', '%26' => '&', '%23' => '#', '%25' => '%']; protected $base_uri; protected $assets_path; protected $router; function __construct(RouterInterface $router, string $base_uri = '', string $assets_path = 'assets'){
 $this->router = $router; $this->base_uri = rtrim($base_uri, '/'); $this->assets_path = $assets_path; } function to(string $path = ''){
 return $this->base_uri . '/' . $this->preparePath($path); } function asset($path = ''){
 return $this->to($this->assets_path . '/' . $this->preparePath($path)); } function route($name, $parameters = [], $absolute = true){
 $route = $this->router->getRoute($name); if (!$route){
 throw new \Exception('Not find route by name: ' . $name); } $uri = $this->addQueryString($this->replaceRouteParameters($route->getPath(), $parameters), $parameters); if (preg_match('/\\{.*?\\}/', $uri)){
 throw new \Exception('Required  param not replaced: ' . $uri); } $uri = strtr(rawurlencode($uri), $this->dontEncode); $uri = $this->base_uri . '/' . ltrim($uri, '/'); if ($absolute){
 $uri = 'http' . ($route->getSecure() ? 's' : '') . '://' . $route->getDomain() . $uri; } return $uri; } protected function preparePath($path){
 if (is_array($sc = Str::sc($path))){
 $path = $sc[0] . '/' . $sc[1]; } return ltrim($path, '/'); } protected function getRouteQueryString(array $parameters){
 if (count($parameters) === 0){
 return ''; } $query = http_build_query($keyed = $this->getStringParameters($parameters), null, '&', PHP_QUERY_RFC3986); if (count($keyed) < count($parameters)){
 $query .= '&' . implode('&', $this->getNumericParameters($parameters)); } return '?' . trim($query, '&'); } protected function getStringParameters(array $parameters){
 return array_filter($parameters, 'is_string', ARRAY_FILTER_USE_KEY); } protected function getNumericParameters(array $parameters){
 return array_filter($parameters, 'is_numeric', ARRAY_FILTER_USE_KEY); } protected function replaceRouteParameters($path, array &$parameters){
 $path = $this->replaceNamedParameters($path, $parameters); $path = preg_replace_callback('/\\{.*?\\}/', function ($match) use (&$parameters){
 return empty($parameters) && !Str::endsWith($match[0], '?}') ? $match[0] : array_shift($parameters); }, $path); return preg_replace('/\\{.*?\\?\\}/', '', $path); } protected function replaceNamedParameters($path, &$parameters){
 return preg_replace_callback('/\\{(.*?)\\??\\}/', function ($m) use (&$parameters){
 if (isset($parameters[$m[1]])){
 return Arr::pull($parameters, $m[1]); } elseif (isset($this->defaultParameters[$m[1]])){
 return $this->defaultParameters[$m[1]]; } return $m[0]; }, $path); } protected function addQueryString($uri, array $parameters){
 if (!is_null($fragment = parse_url($uri, PHP_URL_FRAGMENT))){
 $uri = preg_replace('/#.*/', '', $uri); } $uri .= $this->getRouteQueryString($parameters); return is_null($fragment) ? $uri : $uri . "#{$fragment}";}}
 class SettlementsRouter extends Router implements RouterInterface { const DELIMITER = '::'; const DEFAULT_ROUTER = 'default'; protected $routers = []; protected $current_router = null; protected $current_router_name = null; protected $previous_collections_names = []; protected $settlements = null; protected $router_factory = null; function __construct(RouterFactoryInterface $routerFactory, SettlementsInterface $settlements){
 $this->router_factory = $routerFactory; $this->settlements = $settlements; } function group(array $attributes, callable $routes){
 $this->current_router->group($attributes, $routes); } function addRoute($httpMethods, string $uri, $action): RouteInterface { return $this->current_router->addRoute($httpMethods, $uri, $action); } function getRoute(string $name): ?RouteInterface { $delimiter = static::DELIMITER; $router = static::DEFAULT_ROUTER; $settlement = null; if (false !== strpos($name, $delimiter)){
 $router = strstr($name, $delimiter, true); $name = substr(strstr($name, $delimiter), 2); $settlement = $this->settlements->getByRouter($router); } $route = $this->router($router)->getRoute($name); if ($route && $settlement){
 return new SettlementRouteDecorator($route, $settlement); } return $route; } function getRoutes(string $httpMethod = null): array { $all_routes = []; foreach ($this->settlements as $settlement){
 $routes = $this->router($settlement->getRouter())->getRoutes($httpMethod); foreach ($routes as $method => $collection){
 $collection = new Collection($collection); $settlement_collection = $collection->map(function (RouteInterface $item, $key) use ($settlement){
 return [new SettlementRouteDecorator($item, $settlement), $settlement->getPath() . $key]; }); if (!isset($all_routes[$method])){
 $all_routes[$method] = new Collection(); } $all_routes[$method]->merge($settlement_collection);}}
 return $all_routes; } function getBySettlement(string $settlement): array { $routes = []; if (strpos($settlement, 'default:') !== false){
 foreach ($this->router('default')->getBySettlement(substr($settlement, 8)) as $v){
 $routes[$v->getName()] = $v; } return $routes; } if ($sett = $this->settlements->getByRouter($settlement)){
 foreach ($this->router($settlement)->getRoutes('get') as $v){
 $routes[$v->getName()] = new SettlementRouteDecorator($v, $sett);}}
 return $routes; } function selectRouter(string $name = null): RouterInterface { $name = $this->castRouterName($name); if ($name === $this->current_router_name){
 return $this; } if (!empty($this->previous_collections_names) && $name === $this->getLastPreviousRouterName()){
 return $this->selectPreviousRouter(); } if ($this->current_router_name !== null){
 $this->previous_collections_names[] = $this->current_router_name; } $this->current_router = $this->router($name); $this->current_router_name = $name; return $this; } function collection(string $name, callable $callback){
 $current_router = $this->getCurrentRouterName(); $this->selectRouter($name); if (is_callable($callback)){
 $callback($this); } $this->selectRouter($current_router); } function getCurrentRouterName(): string { return $this->current_router_name; } protected function getLastPreviousRouterName(){
 return !empty($this->previous_collections_names) ? end($this->previous_collections_names) : null; } function selectPreviousRouter(){
 $name = $this->castRouterName(array_pop($this->previous_collections_names) ?? ''); $this->selectRouter($name); array_pop($this->previous_collections_names); return $this; } protected function castRouterName(string $name = null){
 if (in_array($name, ['', null])){
 $name = static::DEFAULT_ROUTER; } return strtolower($name); } function hasRouter($name){
 return !is_null($this->settlements->getByRouter(\strtolower($name))); } function getRouters(){
 return $this->routers; } function router($name = null): RouterInterface { $name = $this->castRouterName($name); if (!isset($this->routers[$name])){
 $this->routers[$name] = $router = $this->router_factory->factoryRouter(['name' => $name]); if (method_exists($router, 'setName')){
 $router->setName($name);}}
 return $this->routers[$name]; } function match(string $httpMethod, string $uri): ?RouteInterface { $uri = '/' . ltrim($uri, '\\/'); $route = null; $settlement = $this->settlements->getByUrl($uri); if ($settlement){
 $route = $this->router($settlement->getRouter())->match($httpMethod, $settlement->getUriWithoutSettlement($uri)); } if (!$route){
 $route = $this->router(self::DEFAULT_ROUTER)->match($httpMethod, $uri); } return $route;}}
 class RouterLazy extends Router implements NamedRouterInterface, LazyRouterInterface { use NamedRouterTrait; protected $loaded_routes = false; protected $router_factory = null; function __construct(RouterFactoryInterface $routerFactory){
 $this->router_factory = $routerFactory; parent::__construct(); } function getRoute(string $name): ?RouteInterface { $this->loadRoutes(); return parent::getRoute($name); } function getBySettlement(string $settlement): array { $this->loadRoutes(); return parent::getBySettlement($settlement); } function getRoutes(string $httpMethod = null): array { $this->loadRoutes(); return parent::getRoutes($httpMethod); } function isLoadedRoutes(): bool { return $this->loaded_routes; } function loadRoutes(){
 if (!$this->loaded_routes){
 $this->loaded_routes = true; $this->router_factory->loadRoutes($this);}}
 function __sleep(){
 return ['named_routes', 'routes', 'loaded_routes', 'name', 'domain']; } function __wakeup(){
 $this->loaded_routes = true;}}
 class CacheLazyRouterDecorator extends CacheRouterDecorator implements RouterInterface, NamedRouterInterface, LazyRouterInterface { protected $loaded = false; function isLoadedRoutes(): bool { return $this->loaded; } function loadRoutes(){
 if (!$this->loaded){
 $this->factory->loadRoutes($this); $this->loaded = true;}}
 function getRoute(string $name): ?RouteInterface { $this->loadRoutes(); return parent::getRoute($name); } function getRoutes(string $httpMethod = null): array { $this->loadRoutes(); return parent::getRoutes($httpMethod); } function getBySettlement(string $settlement): array { $this->loadRoutes(); return parent::getBySettlement($settlement); } function match(string $httpMethod, string $uri): ?RouteInterface { $this->loadRoutes(); return parent::match($httpMethod, $uri); } function setName(string $name){
 $this->call(__FUNCTION__, func_get_args()); } function getName(): string { return $this->call(__FUNCTION__, func_get_args());}}
 } namespace Dissonance\Apps { use Dissonance\Container\{ ArrayAccessTrait, ItemsContainerTrait, ArrayContainerInterface, DIContainerInterface, SubContainerTrait, ServiceContainerTrait, ServiceContainerInterface }; use Dissonance\{ Packages\PackagesRepositoryInterface, Core\AbstractBootstrap, Routing\AppsRoutesRepository, Core\CoreInterface }; use function _DS\app; interface AppsRepositoryInterface { function enabled(): array; function all(): array; function getIds(): array; function get(string $id): ?ApplicationInterface; function has(string $id); function getBootedApp(string $id): ?ApplicationInterface; function getPluginsIds(string $app_id); function addApp(array $config); } interface AppConfigInterface extends ArrayContainerInterface { function getId(): string; function getAppName(): string; function getRoutingProvider(): ?string; function hasParentApp(): bool; function getParentAppId(): ?string; } interface ApplicationInterface extends AppConfigInterface, DIContainerInterface, ServiceContainerInterface { function getBasePath(string $path = null); function getAssetsPath(); function getResourcesPath(); function bootstrap(array $bootstraps = null): void; } class AppConfig implements AppConfigInterface { use ArrayAccessTrait, ItemsContainerTrait; protected $id = null; function __construct(array $config){
 $this->id = $config['id'] ?? null; $this->items = $config; } function getId(): string { return $this->id; } function getAppName(): string { return $this->has('name') ? $this->get('name') : ucfirst($this->getId()); } function getRoutingProvider(): ?string { return $this->get('routing'); } function hasParentApp(): bool { return $this->has('parent_app'); } function getParentAppId(): ?string { return $this->get('parent_app');}}
 class Application implements ApplicationInterface { use ServiceContainerTrait, SubContainerTrait; function __construct(DIContainerInterface $app, AppConfigInterface $config = null){
 $this->app = $app; $this->instance(AppConfigInterface::class, $config, 'config'); $config_class = get_class($config); if ($config_class !== AppConfig::class){
 $this->alias(AppConfigInterface::class, AppConfig::class); } $class = get_class($this); $this->dependencyInjectionContainer = $this; $this->instance($class, $this); $this->alias($class, ApplicationInterface::class); if ($class !== self::class){
 $this->alias($class, self::class);}}
 function getId(): string { return $this['config']->getId(); } function getAppName(): string { return $this['config']->getAppName(); } function getAppTitle(): string { return $this['config']->getAppName(); } function getRoutingProvider(): ?string { return $this['config']->getRoutingProvider(); } function hasParentApp(): bool { return $this['config']->hasParentApp(); } function getParentAppId(): ?string { return $this['config']->getParentAppId(); } protected function getBootstrapCallback(){
 return function (){
 $this->registerProviders(); $this->boot(); }; } function bootstrap(array $bootstraps = null): void { if (!is_array($bootstraps)){
 $bootstraps = []; } if (!$this->booted){
 $bootstraps[] = $this->getBootstrapCallback(); } if (!$this->booted && ($parent_app = $this->getParentApp())){
 $parent_app->bootstrap($bootstraps); } else { foreach (array_reverse($bootstraps) as $boot){
 $boot();}}
 $this->booted = true; } protected function registerProviders(){
 foreach ($this('config::providers', []) as $provider){
 $this->register($provider);}}
 function getBasePath(string $path = null){
 $base = $this('config::base_path'); return $base ? $path ? $base . \_DS\DS . ltrim($path) : $base : null; } function getAssetsPath(){
 return $this->getBasePath('assets'); } function getResourcesPath(){
 return $this->getBasePath('resources'); } protected function getParentApp(){
 return $this->hasParentApp() ? $this[AppsRepositoryInterface::class]->get($this->getParentAppId()) : null;}}
 class AppsRepository implements AppsRepositoryInterface { protected $apps = []; protected $apps_plugins = []; protected $apps_config = []; protected $disabled_apps = []; function disableApps(array $ids){
 $this->disabled_apps = array_merge($this->disabled_apps, array_combine($ids, $ids)); } function addApp(array $config){
 if (empty($config['id'])){
 throw new \Exception('Empty app id!'); } $id = $config['id']; $this->apps_config[$id] = $config; $parent_app = $config['parent_app'] ?? null; if ($parent_app){
 $this->apps_plugins[$parent_app][$id] = 1;}}
 function get(string $id): ?ApplicationInterface { if (isset($this->apps[$id])){
 return $this->apps[$id]; } if ($config = $this->getConfig($id)){
 $app = app(isset($config['app_class']) ? $config['app_class'] : Application::class, ['app' => isset($config['parent_app']) ? $this->get($config['parent_app']) : app(), 'config' => $config]); return $this->apps[$id] = $app; } throw new \Exception("Application with id [{$id}] is not exists!"); } function getConfig(string $id): ?AppConfigInterface { return isset($this->apps_config[$id]) ? new AppConfig($this->apps_config[$id]) : null; } function getBootedApp(string $id): ?ApplicationInterface { $app = $this->get($id); if ($app){
 $app->bootstrap(); } return $app; } function has(string $id): bool { return isset($this->apps_config[$id]); } function getIds(): array { return array_keys($this->apps_config); } function enabled(): array { return $this->all(); } function getPluginsIds(string $id): array { return isset($this->apps_plugins[$id]) ? array_keys($this->apps_plugins[$id]) : []; } function all(): array { return $this->apps_config; } function __sleep(){
 return ['apps_config', 'apps_plugins'];}}
 class Bootstrap extends AbstractBootstrap { function bootstrap(CoreInterface $app): void { $this->cached($app, AppsRepositoryInterface::class, function ($app){
 $apps_repository = new AppsRepository(); foreach ($app[PackagesRepositoryInterface::class]->getPackages() as $config){
 $app = isset($config['app']) ? $config['app'] : null; if (is_array($app)){
 $apps_repository->addApp($app);}}
 return $apps_repository; }, 'apps'); $app['listeners']->add(AppsRoutesRepository::class, function (AppsRoutesRepository $event, AppsRepositoryInterface $appsRepository){
 foreach ($appsRepository->enabled() as $v){
 $provider = $v['routing'] ?? null; if ($provider && class_exists($provider)){
 $event->append(new $provider($v['id'], $v['controllers_namespace']));}}
 });}}
 } namespace Dissonance\Packages { use Psr\Http\Message\{ ResponseFactoryInterface, ResponseInterface, ServerRequestInterface, StreamFactoryInterface, StreamInterface }; use Dissonance\Core\{ AbstractBootstrap, CoreInterface, Support\Arr, BootstrapInterface }; use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface}; use Dissonance\{ Filesystem\ArrayStorageTrait, Storage\RememberingInterface, Mimetypes\MimeTypesMini, Http\Kernel\PreloadKernelHandler }; use function _DS\app; interface AssetsRepositoryInterface { function getAssetFileStream(string $package_id, string $path): StreamInterface; } interface PackagesLoaderInterface { function load(PackagesRepositoryInterface $repository); } interface PackagesRepositoryInterface { function getIds(): array; function has(string $id): bool; function get(string $id): array; function getPackages(): array; function addPackagesLoader(PackagesLoaderInterface $loader): void; function addPackage(array $config): void; function load(): void; function getBootstraps(): array; } interface ResourcesRepositoryInterface { function getResourceFileStream(string $package_id, string $path): StreamInterface; } interface TemplateCompilerInterface { function compile(string $template): string; function getExtensions(): array; } interface TemplatesRepositoryInterface { function getTemplate(string $package_id, string $path): string; } class ResourcesBootstrap { protected $cache_key = 'core.resources'; function bootstrap($app){
 $app->singleton(TemplateCompiler::class); $res_interface = ResourcesRepositoryInterface::class; $app->alias($res_interface, TemplatesRepositoryInterface::class); $app->alias($res_interface, AssetsRepositoryInterface::class); $app->singleton($res_interface, function () use ($app){
 $repository = new ResourcesRepository($app[TemplateCompiler::class], $app[StreamFactoryInterface::class], $app[PackagesRepositoryInterface::class]); return $repository; }, 'resources'); $app['listeners']->add(PreloadKernelHandler::class, function (PreloadKernelHandler $event) use ($app){
 $event->prepend(new AssetFileMiddleware($app('config::assets_prefix', 'assets'), $app['resources'], $app[ResponseFactoryInterface::class])); });}}
 class TemplateCompiler { protected $extensions = []; function addCompiler(TemplateCompilerInterface $compiler){
 foreach ($compiler->getExtensions() as $v){
 $this->extensions[$v] = $compiler;}}
 function compile(string $path, string $template): string { $ext = (new MimeTypesMini())->findExtension($path, array_keys($this->extensions)); return $ext !== false ? $this->extensions[$ext]->compile($template) : $template;}}
 class AssetFileMiddleware implements MiddlewareInterface { protected $path; protected $resources; protected $response_factory; function __construct(string $path, AssetsRepositoryInterface $resources, ResponseFactoryInterface $factory){
 $this->path = $path; $this->resources = $resources; $this->response_factory = $factory; } function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface { $pattern = '~^' . preg_quote(trim($this->path, '/'), '~') . '/(.[^/]+)(.+)~i'; $assets_repository = $this->resources; if (preg_match($pattern, ltrim($request->getRequestTarget(), '/'), $match)){
 $response_factory = $this->response_factory; try { $file = $assets_repository->getAssetFileStream($match[1], $match[2]); $mime_types = new MimeTypesMini(); return $response_factory->createResponse(200)->withBody($file)->withHeader('content-type', $mime_types->getMimeType($match[2]))->withHeader('content-length', $file->getSize()); } catch (\Throwable $e){
 app()->set('destroy_response', true); return $response_factory->createResponse(404);}}
 return $handler->handle($request);}}
 class PackagesBootstrap extends AbstractBootstrap { function bootstrap(CoreInterface $app): void { $packages_class = PackagesRepositoryInterface::class; $app->singleton($packages_class, function (){
 return new PackagesRepository(); }); $p = $app[$packages_class]; $p->load(); foreach ($p->getBootstraps() as $v){
 if ($v === get_class($this)){
 continue; } $app->runBootstrap($v);}}
 } class PackagesLoaderFilesystem implements PackagesLoaderInterface { protected $scan_dirs = []; protected $max_depth = 3; protected $cache = null; function __construct(array $scan_dirs = [], int $max_depth = 3){
 $this->scan_dirs = $scan_dirs; $this->max_depth = $max_depth; } function load(PackagesRepositoryInterface $repository){
 $cache = $this->cache; $key = 'packages_filesystem'; if ($cache && ($packages = $cache->get($key)) && is_array($packages)){
 } else { $packages = []; if (!empty($this->scan_dirs)){
 foreach ($this->scan_dirs as $dir){
 if (is_dir($dir) && is_readable($dir)){
 $packages = array_merge($packages, $this->getDirPackages($dir)); } else { throw new \Exception('Directory [' . $dir . '] is not readable or not exists!');}}
 } if ($cache){
 $cache->set($key, $packages);}}
 foreach ($packages as $v){
 $repository->addPackage($v);}}
 protected function getDirPackages($dir){
 $packages = []; $files = array_merge(glob($dir . '/*/composer.json', GLOB_NOSORT), glob($dir . '/*/*/composer.json', GLOB_NOSORT)); foreach ($files as $file){
 if (\is_readable($file)){
 $config = Arr::get(@\json_decode(file_get_contents($file), true), 'extra.dissonance'); if (is_array($config)){
 $app = Arr::get($config, 'app'); $config['base_path'] = dirname($file); if (is_array($app)){
 $app['base_path'] = $config['base_path']; $config['app'] = $app; } $packages[] = $config;}}
 } return $packages;}}
 class PackagesLoaderFilesystemBootstrap implements BootstrapInterface { function bootstrap(CoreInterface $app): void { $app->afterResolving(PackagesRepositoryInterface::class, function (PackagesRepositoryInterface $repository) use ($app){
 $repository->addPackagesLoader(new PackagesLoaderFilesystem($app->get('config::packages_paths'))); });}}
 class PackagesRepository implements PackagesRepositoryInterface { protected $loaders = []; protected $items = []; protected $loaded = false; protected $ids = []; protected $bootstraps = []; function addPackagesLoader(PackagesLoaderInterface $loader): void { $this->loaders[] = $loader; } function addPackage(array $config): void { $app = isset($config['app']) ? $config['app'] : null; if (is_array($app)){
 if (!isset($app['id']) && isset($config['id'])){
 $app['id'] = $config['id']; } else { $config['id'] = $app['id'] = self::getAppId($app); } $config['app'] = $app; } $id = isset($config['id']) ? $config['id'] : \count($this->items); $this->ids[$id] = $id; $this->items[$id] = $config; if (!empty($config['bootstrappers'])){
 $this->bootstraps = array_merge($this->bootstraps, (array)$config['bootstrappers']);}}
 function getBootstraps(): array { return $this->bootstraps; } function has($id): bool { return isset($this->ids[$id]); } function get($key): array { return $this->items[$key]; } function getPackages(): array { $time = microtime(); if (is_null($this->items)){
 $this->items = $this->cache && ($data = $this->cache->get('core.packages', $time)) && $data !== $time ? $data : []; } return $this->items; } static function normalizeId(string $id){
 return str_replace(['/', '-', '.'], ['_', '_', ''], \strtolower($id)); } static function getAppId(array $config){
 if (!isset($config['id'])){
 throw new \Exception('App id is required [' . \serialize($config) . ']!'); } $name = $config['id'] = self::normalizeId($config['id']); $parent_app = $config['parent_app'] ?? null; if ($parent_app){
 $config['parent_app'] = $parent_app = self::normalizeId($parent_app); } return $parent_app ? $parent_app . '.' . $name : $name; } function getIds(): array { return $this->ids; } function load(): void { if (!$this->loaded){
 foreach ($this->loaders as $loader){
 $loader->load($this); } $this->loaded = true;}}
 } class LazyPackagesDecorator implements PackagesRepositoryInterface, RememberingInterface { use ArrayStorageTrait; protected $repository; protected $meta = null; protected $packages = null; function __construct(PackagesRepositoryInterface $repository, string $storage_path = null){
 $this->repository = $repository; if ($storage_path){
 $this->setStoragePath($storage_path);}}
 function getIds(): array { return $this->getMeta()['ids']; } function has($id): bool { return isset($this->getIds()[$id]); } function get(string $id): array { $packages = $this->getPackages(); if (!isset($this->getIds()[$id])){
 throw new \Exception("Package [{$id}] not found!"); } return $packages[$id]; } function getPackages(): array { if (null === $this->packages){
 $this->packages = $this->remember('packages_data.php', function (){
 $this->repository->load(); return $this->repository->getPackages(); }); } return $this->packages; } function addPackagesLoader(PackagesLoaderInterface $loader): void { $this->repository->addPackagesLoader($loader); } function addPackage(array $config): void { $this->repository->addPackage($config); } function getBootstraps(): array { return $this->getMeta()['bootstraps']; } function load(): void { } protected function getMeta(): array { if (null === $this->meta){
 $this->meta = $this->remember('packages_meta.php', function (){
 $this->repository->load(); return ['bootstraps' => $this->repository->getBootstraps(), 'ids' => $this->repository->getIds()]; }); } return $this->meta;}}
 class ResourcesRepository implements ResourcesRepositoryInterface, AssetsRepositoryInterface, TemplatesRepositoryInterface { protected $packages = []; protected $compiler; protected $factory; protected $packages_repository; function __construct(TemplateCompiler $compiler, StreamFactoryInterface $factory, PackagesRepositoryInterface $packages){
 $this->compiler = $compiler; $this->factory = $factory; $this->packages_repository = $packages; } function getAssetFileStream(string $package_id, string $path): StreamInterface { return $this->getPathTypeFileStream($package_id, $path, 'public_path'); } function getResourceFileStream(string $package_id, string $path): StreamInterface { return $this->getPathTypeFileStream($package_id, $path, 'resources_path'); } function getTemplate(string $package_id, string $path): string { $base_name = basename($path); if (strpos($base_name, '.') === false){
 $path .= '.blade.php'; } $file = $this->getResourceFileStream($package_id, 'views/' . ltrim($this->cleanPath($path), '\\/')); return $this->compiler->compile($path, $file->getContents()); } protected function cleanPath(string $path){
 return preg_replace('!\\.\\.[/\\\\]!', '', $path); } protected function getPathTypeFileStream(string $package_id, string $path, string $path_type): ?StreamInterface { $path = $this->cleanPath($path); if ($this->packages_repository->has($package_id)){
 $assets = []; $package_config = $this->packages_repository->get($package_id); foreach (['public_path' => 'assets', 'resources_path' => 'resources'] as $k => $v){
 if (!empty($package_config[$k]) || isset($package_config['app'])){
 $assets[$k] = rtrim($package_config['base_path'], '\\/') . \_DS\DS . (isset($package_config[$k]) ? trim($package_config[$k], '\\/') : $v);}}
 if (isset($assets[$path_type])){
 $full_path = $assets[$path_type] . '/' . ltrim($path, '/\\'); if (!\is_readable($full_path)){
 throw new \Exception('File is not exists or not readable [' . $full_path . ']!'); } return $this->factory->createStreamFromResource(\fopen($full_path, 'r'));}}
 throw new \Exception('Package not found [' . $package_id . ']!');}}
 } namespace Dissonance\Mimetypes { class MimeTypesMini { protected static $mime_types = ['txt' => T . 'plain', 'htm' => T . 'html', 'html' => T . 'html', 'php' => T . 'html', 'css' => T . 'css', 'js' => A . 'javascript', 'json' => A . 'json', 'jsonld' => A . 'ld+json', 'xml' => A . 'xml', 'swf' => A . 'x-shockwave-flash', 'flv' => 'video/x-flv', 'csv' => T . 'csv', 'png' => I . 'png', 'jpe' => I . 'jpeg', 'jpeg' => I . 'jpeg', 'jpg' => I . 'jpeg', 'gif' => I . 'gif', 'bmp' => I . 'bmp', 'ico' => I . 'vnd.microsoft.icon', 'tiff' => I . 'tiff', 'tif' => I . 'tiff', 'svg' => I . 'svg+xml', 'svgz' => I . 'svg+xml', 'zip' => A . 'zip', 'rar' => A . 'x-rar-compressed', 'exe' => A . 'x-msdownload', 'msi' => A . 'x-msdownload', 'cab' => A . 'vnd.ms-cab-compressed', 'tar.gz' => A . 'x-compressed-tar', 'mp3' => 'audio/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'mp4' => 'video/mp4', 'pdf' => A . 'pdf', 'psd' => I . 'vnd.adobe.photoshop', 'ai' => A . 'postscript', 'eps' => A . 'postscript', 'ps' => A . 'postscript', 'doc' => A . 'msword', 'rtf' => A . 'rtf', 'xls' => A . 'vnd.ms-excel', 'ppt' => A . 'vnd.ms-powerpoint', 'odt' => A . 'vnd.oasis.opendocument.text', 'ods' => A . 'vnd.oasis.opendocument.spreadsheet']; function getExtensionsPattern(array $extensions){
 $pattern = ''; foreach ($extensions as $v){
 $pattern .= preg_quote($v, '/') . '|'; } return trim($pattern, '|'); } function findExtension(string $path, array $allowed_extensions = null){
 if (!$allowed_extensions){
 $allowed_extensions = array_keys(static::$mime_types); } usort($allowed_extensions, function ($a, $b){
 return substr_count($a, '.') <=> substr_count($b, '.'); }); return preg_match('/(' . $this->getExtensionsPattern($allowed_extensions) . ')$/i', $path, $m) ? $m[1] : false; } function getMimeType(string $path): ?string { $ext = $this->findExtension($path); return $ext && isset(static::$mime_types[$ext]) ? static::$mime_types[$ext] : null;}}
 const A = 'application/'; const I = 'image/'; const T = 'text/'; } namespace Dissonance\Filesystem\Adapter { use Dissonance\Filesystem\{ AdapterInterface, PathPrefixInterface, FilesystemInterface }; abstract class AbstractAdapter implements AdapterInterface, PathPrefixInterface { protected $path_prefix = '/'; function setPathPrefix($path){
 if (!empty($path)){
 $this->path_prefix = rtrim($path, '\\/') . '/'; } return $this; } function getPathPrefix(){
 return $this->path_prefix; } function applyPathPrefix($path){
 return $this->getPathPrefix() . ltrim($path, '\\/'); } function removePathPrefix($path){
 return str_replace($this->getPathPrefix(), '', $path); } function normalizePath($path){
 $path = rtrim(str_replace("\\", "/", trim($path)), '/'); $unx = strlen($path) > 0 && $path[0] == '/'; $parts = array_filter(explode('/', $path), 'strlen'); $absolutes = []; foreach ($parts as $part){
 if ('.' == $part){
 continue; } if ('..' == $part){
 array_pop($absolutes); } else { $absolutes[] = $part;}}
 $path = implode('/', $absolutes); $path = $unx ? '/' . $path : $path; return $path;}}
 class Local extends AbstractAdapter implements FilesystemInterface { protected $writeFlags = LOCK_EX; protected static $permissions = ['file' => ['public' => 0644, 'private' => 0600], 'dir' => ['public' => 0755, 'private' => 0700]]; protected $permissionMap; function __construct($root = '/', $writeFlags = LOCK_EX, array $permissions = []){
 $root = is_link($root) ? realpath($root) : $root; $this->permissionMap = array_replace_recursive(static::$permissions, $permissions); $this->ensureDirectory($root); if (!is_dir($root) || !is_readable($root)){
 throw new \LogicException('The root path ' . $root . ' is not readable.'); } $this->setPathPrefix($root); $this->writeFlags = $writeFlags; } function listDir($dir_path = ''){
 $files = []; if (!funex("scandir")){
 $h = @opendir($dir_path); while (false !== ($filename = @readdir($h))){
 $files[] = $filename;}}
 else { $files = @scandir($dir_path); } return $files; } function createDir($dirname, array $options = []){
 $return = $dirname = $this->applyPathPrefix($dirname); if (!is_dir($dirname)){
 if (false === @mkdir($dirname, $this->permissionMap['dir'][isset($options['visibility']) ? $options['visibility'] : 'public'], true) || false === is_dir($dirname)){
 $return = false;}}
 return $return; } protected function clearstatcache($path, $flag = false){
 clearstatcache($flag, $path); } protected function ensureDirectory($dirname){
 if (!is_dir($dirname)){
 $error = !@mkdir($dirname, $this->permissionMap['dir']['public'], true) ? error_get_last() : []; if (!@mkdir($dirname, $this->permissionMap['dir']['public'], true)){
 $error = error_get_last(); } $this->clearstatcache($dirname); if (!is_dir($dirname)){
 $errorMessage = isset($error['message']) ? $error['message'] : ''; throw new \Exception(sprintf('Impossible to create the directory "%s". %s', $dirname, $errorMessage));}}
 } function touch($path, $time){
 return @touch($path, $time, $time); } function copy($from, $to, $delete_from = false){
 if (!$this->has($from)){
 throw new \Exception($from . ' File not Found'); } $from = $this->applyPathPrefix($from); $to = $this->applyPathPrefix($to); if (!is_dir($from)){
 $this->ensureDirectory(dirname($to)); $this->copyThrow($from, $to); } else { $from = rtrim($from, '\\/') . '/'; $to = rtrim($to, '\\/') . '/'; foreach ($this->getRecursiveDirectoryIterator($from, \RecursiveIteratorIterator::CHILD_FIRST) as $file){
 $old_path = $file->getType() == 'link' ? $file->getPathname() : $file->getRealPath(); $new_path = str_replace($from, $to, $old_path); if (!$file->isDir()){
 $this->ensureDirectory(dirname($new_path)); $this->copyThrow($old_path, $new_path); } else { $this->ensureDirectory($new_path);}}
 } if ($delete_from){
 return $this->delete($from); } return true; } protected function copyThrow($path, $newpath){
 if ($result = copy($path, $newpath)){
 return $result; } throw new \Exception('File not copied : ' . $path); } function move($dir_from, $dir_to, $filename){
 return $this->copy($dir_from, $dir_to, $filename, true); } function delete($path){
 $path = $this->applyPathPrefix($path); if (is_dir($path)){
 return $this->deleteDir($this->removePathPrefix($path)); } return @unlink($path); } function deleteDir($path){
 $path = $this->applyPathPrefix($path); if (!is_dir($path)){
 return false; } foreach ($this->getRecursiveDirectoryIterator($path, \RecursiveIteratorIterator::CHILD_FIRST) as $file){
 $this->deleteFileInfoObject($file); } return rmdir($path); } protected function deleteFileInfoObject(\SplFileInfo $file){
 switch ($file->getType()){
 case 'dir': return rmdir($file->getRealPath()); break; case 'link': return unlink($file->getPathname()); break; default: unlink($file->getRealPath());}}
 protected function getDirectoryIterator($path){
 return new \DirectoryIterator($path); } function getRecursiveDirectoryIterator($path, $mode = \RecursiveIteratorIterator::SELF_FIRST){
 return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), $mode); } function read($path){
 return @file_get_contents($this->applyPathPrefix($path)); } function write(string $path, $contents, array $options = []){
 $path = $this->applyPathPrefix($path); $time = $this->has($path) ? filemtime($path) : time(); $result = @file_put_contents($path, $contents, isset($options['flags']) ? $options['flags'] : $this->writeFlags); if ($result && !empty($options['no_touch'])){
 @touch($path, $time, $time); } return $result; } function rename($path, $newpath){
 $path = $this->applyPathPrefix($path); $newpath = $this->applyPathPrefix($newpath); $this->ensureDirectory(dirname($newpath)); return rename($path, $newpath); } function has($path){
 $location = $this->applyPathPrefix($path); return file_exists($location); } function getPerms($file_data, $format = 'oct'){
 if (arkex($format . '_perms', $file_data)){
 return $file_data[$format . '_perms']; } if (!isar($file_data)){
 $file_data = $this->fileinfo($file_data); } return $this->permsFormat(ifset($file_data, 'perms', 0), 'base', $format); } function setPerms($filepath, $oct_perms = ''){
 if (!empty($oct_perms)){
 $pm = 0; for ($i = strlen($oct_perms) - 1; $i >= 0; --$i){
 $pm += (int)$oct_perms[$i] * pow(8, strlen($oct_perms) - $i - 1); } return chmod($filepath, $pm); } return false; } function listContents($directory = '', $recursive = false){
 } function getMetadata($path){
 } function getSize($path){
 } function getMimetype($path){
 } function getTimestamp($path){
 } function setVisibility($path, $visibility){
 } function getVisibility($path){
 } } } namespace _DS { use Dissonance\Core\{ Config, Core, Support\Str, Support\Collection, Support\Arr, HttpKernelInterface }; use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface}; const DS = DIRECTORY_SEPARATOR; function app($abstract = null, array $parameters = null){
 $core = Core::getInstance(); if (is_null($abstract)){
 return $core; } return is_null($parameters) ? $core->get($abstract) : $core->make($abstract, $parameters); } if (!function_exists('_DS\\config')){
 function config(string $key = null, $default = null){
 $config = app('config'); return is_null($key) ? $config : ($config->has($key) ? $config->get($key) : $default);}}
 if (!function_exists('_DS\\event')){
 function event(object $event){
 return app('events')->dispatch($event);}}
 if (!function_exists('_DS\\route')){
 function route($name, $parameters = [], $absolute = true){
 return app('url')->route($name, $parameters, $absolute);}}
 if (!function_exists('_DS\\camel_case')){
 function camel_case($value){
 return Str::camel($value);}}
 if (!function_exists('_DS\\class_basename')){
 function class_basename($class){
 $class = is_object($class) ? get_class($class) : $class; return basename(str_replace('\\', '/', $class));}}
 if (!function_exists('_DS\\collect')){
 function collect($value = null){
 return new Collection($value);}}
 if (!function_exists('_DS\\data_fill')){
 function data_fill(&$target, $key, $value){
 return data_set($target, $key, $value, false);}}
 if (!function_exists('_DS\\data_get')){
 function data_get($target, $key, $default = null){
 if (is_null($key)){
 return $target; } $key = is_array($key) ? $key : explode('.', $key); while (!is_null($segment = array_shift($key))){
 if ($segment === '*'){
 if ($target instanceof Collection){
 $target = $target->all(); } elseif (!is_array($target)){
 return value($default); } $result = []; foreach ($target as $item){
 $result[] = data_get($item, $key); } return in_array('*', $key) ? Arr::collapse($result) : $result; } if (Arr::accessible($target) && Arr::exists($target, $segment)){
 $target = $target[$segment]; } elseif (is_object($target) && isset($target->{$segment})){
 $target = $target->{$segment}; } else { return value($default);}}
 return $target;}}
 if (!function_exists('_DS\\data_set')){
 function data_set(&$target, $key, $value, $overwrite = true){
 $segments = is_array($key) ? $key : explode('.', $key); if (($segment = array_shift($segments)) === '*'){
 if (!Arr::accessible($target)){
 $target = []; } if ($segments){
 foreach ($target as &$inner){
 data_set($inner, $segments, $value, $overwrite);}}
 elseif ($overwrite){
 foreach ($target as &$inner){
 $inner = $value;}}
 } elseif (Arr::accessible($target)){
 if ($segments){
 if (!Arr::exists($target, $segment)){
 $target[$segment] = []; } data_set($target[$segment], $segments, $value, $overwrite); } elseif ($overwrite || !Arr::exists($target, $segment)){
 $target[$segment] = $value;}}
 elseif (is_object($target)){
 if ($segments){
 if (!isset($target->{$segment})){
 $target->{$segment} = []; } data_set($target->{$segment}, $segments, $value, $overwrite); } elseif ($overwrite || !isset($target->{$segment})){
 $target->{$segment} = $value;}}
 else { $target = []; if ($segments){
 data_set($target[$segment], $segments, $value, $overwrite); } elseif ($overwrite){
 $target[$segment] = $value;}}
 return $target;}}
 if (!function_exists('_DS\\ends_with')){
 function ends_with($haystack, $needles){
 return Str::endsWith($haystack, $needles);}}
 if (!function_exists('_DS\\blank')){
 function blank($value){
 if (is_null($value)){
 return true; } if (is_string($value)){
 return trim($value) === ''; } if (is_numeric($value) || is_bool($value)){
 return false; } if ($value instanceof \Countable){
 return count($value) === 0; } return empty($value);}}
 if (!function_exists('_DS\\filled')){
 function filled($value){
 return !blank($value);}}
 if (!function_exists('_DS\\preg_replace_array')){
 function preg_replace_array($pattern, array $replacements, $subject){
 return preg_replace_callback($pattern, function () use (&$replacements){
 foreach ($replacements as $key => $value){
 return array_shift($replacements); } }, $subject);}}
 if (!function_exists('_DS\\snake_case')){
 function snake_case($value, $delimiter = '_'){
 return Str::snake($value, $delimiter);}}
 if (!function_exists('_DS\\serialize64')){
 function serialize64($value){
 return \base64_encode(\serialize($value));}}
 if (!function_exists('_DS\\unserialize64')){
 function unserialize64(string $str, array $options = []){
 return \unserialize(\base64_decode($str), $options);}}
 if (!function_exists('_DS\\transform')){
 function transform($value, callable $callback, $default = null){
 if (filled($value)){
 return $callback($value); } if (is_callable($default)){
 return $default($value); } return $default;}}
 if (!function_exists('_DS\\value')){
 function value($value){
 return is_callable($value) ? $value() : $value;}}
 if (!function_exists('_DS\\with')){
 function with($value, callable $callback = null){
 return is_null($callback) ? $value : $callback($value);}}
 if (!function_exists('_DS\\response')){
 function response(int $code = 200, \Throwable $exception = null): ResponseInterface { return app(HttpKernelInterface::class)->response($code, $exception);}}
 if (!function_exists('_DS\\redirect')){
 function redirect(string $uri, int $code = 301): ResponseInterface { $response = app(ResponseFactoryInterface::class)->createResponse($code); return $response->withHeader('Location', $uri);}}
 } namespace Dissonance\Core\Events { class CacheClear { protected $path = null; function __construct(string $path = null){
 $this->path = trim($path); } function getPath(){
 return $this->path;}}
 }