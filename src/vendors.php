<?php
/**
 * Все свое ношу с собой, для работы без композера
 * Методы очищены чтобы не было конфликтов 
 */
namespace Psr\Container {
    if (!interface_exists(ContainerExceptionInterface::class)) {
        interface ContainerExceptionInterface {}
    }
    if (!interface_exists(ContainerInterface::class)) {
        interface ContainerInterface {}
    }
    if (!interface_exists(NotFoundExceptionInterface::class)) {
        interface NotFoundExceptionInterface extends ContainerExceptionInterface {}
    }
}
namespace Psr\EventDispatcher {
    if (!interface_exists(EventDispatcherInterface::class)) {
        interface EventDispatcherInterface {}
    }
    if (!interface_exists(ListenerProviderInterface::class)) {
        interface ListenerProviderInterface {}
    }
    if (!interface_exists(StoppableEventInterface::class)) {
        interface StoppableEventInterface {}
    }
}
namespace Psr\Http\Message {
    if (!interface_exists(RequestFactoryInterface::class)) {
        interface RequestFactoryInterface {}
    }
    if (!interface_exists(ResponseFactoryInterface::class)) {
        interface ResponseFactoryInterface {}
    }
    if (!interface_exists(ServerRequestFactoryInterface::class)) {
        interface ServerRequestFactoryInterface {}
    }
    if (!interface_exists(StreamFactoryInterface::class)) {
        interface StreamFactoryInterface {}
    }
    if (!interface_exists(UploadedFileFactoryInterface::class)) {
        interface UploadedFileFactoryInterface {}
    }
    if (!interface_exists(UriFactoryInterface::class)) {
        interface UriFactoryInterface {}
    }
    if (!interface_exists(MessageInterface::class)) {
        interface MessageInterface {}
    }
    if (!interface_exists(RequestInterface::class)) {
        interface RequestInterface extends MessageInterface {}
    }
    if (!interface_exists(ResponseInterface::class)) {
        interface ResponseInterface extends MessageInterface {}
    }
    if (!interface_exists(ServerRequestInterface::class)) {
        interface ServerRequestInterface extends RequestInterface {}
    }
    if (!interface_exists(StreamInterface::class)) {
        interface StreamInterface {}
    }
    if (!interface_exists(UploadedFileInterface::class)) {
        interface UploadedFileInterface {}
    }
    if (!interface_exists(UriInterface::class)) {
        interface UriInterface {}
    }
}
namespace Psr\Http\Server {
    if (!interface_exists(RequestHandlerInterface::class)) {
        interface RequestHandlerInterface {}
    }
    if (!interface_exists(MiddlewareInterface::class)) {
        interface MiddlewareInterface {}
    }
}

namespace Psr\SimpleCache {
    if (!interface_exists(CacheException::class)) {
        interface CacheException {}
    }
    if (!interface_exists(CacheInterface::class)) {
        interface CacheInterface {}
    }
    if (!interface_exists(InvalidArgumentException::class)) {
        interface InvalidArgumentException extends CacheException {}
    }
}

/**
 * Nyholm  v1.3.2
 * @todo Может быть конфликт в теории, хотя либа стабильная!
 */
namespace Nyholm\Psr7 {

    use Psr\Http\Message\StreamInterface;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\UriInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\UploadedFileInterface;
    use Symfony\Component\Debug\ErrorHandler as SymfonyLegacyErrorHandler;
    use Symfony\Component\ErrorHandler\ErrorHandler as SymfonyErrorHandler;

    if (!trait_exists(MessageTrait::class)) {
        trait MessageTrait
        {
            private $headers = [];
            private $headerNames = [];
            private $protocol = '1.1';
            private $stream;

            public function getProtocolVersion(): string
            {
                return $this->protocol;
            }

            public function withProtocolVersion($version): self
            {
                if ($this->protocol === $version) {
                    return $this;
                }
                $new = clone $this;
                $new->protocol = $version;
                return $new;
            }

            public function getHeaders(): array
            {
                return $this->headers;
            }

            public function hasHeader($header): bool
            {
                return isset($this->headerNames[\strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')]);
            }

            public function getHeader($header): array
            {
                $header = \strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
                if (!isset($this->headerNames[$header])) {
                    return [];
                }
                $header = $this->headerNames[$header];
                return $this->headers[$header];
            }

            public function getHeaderLine($header): string
            {
                return \implode(', ', $this->getHeader($header));
            }

            public function withHeader($header, $value): self
            {
                $value = $this->validateAndTrimHeader($header, $value);
                $normalized = \strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
                $new = clone $this;
                if (isset($new->headerNames[$normalized])) {
                    unset($new->headers[$new->headerNames[$normalized]]);
                }
                $new->headerNames[$normalized] = $header;
                $new->headers[$header] = $value;
                return $new;
            }

            public function withAddedHeader($header, $value): self
            {
                if (!\is_string($header) || '' === $header) {
                    throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
                }
                $new = clone $this;
                $new->setHeaders([$header => $value]);
                return $new;
            }

            public function withoutHeader($header): self
            {
                $normalized = \strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
                if (!isset($this->headerNames[$normalized])) {
                    return $this;
                }
                $header = $this->headerNames[$normalized];
                $new = clone $this;
                unset($new->headers[$header], $new->headerNames[$normalized]);
                return $new;
            }

            public function getBody(): StreamInterface
            {
                if (null === $this->stream) {
                    $this->stream = Stream::create('');
                }
                return $this->stream;
            }

            public function withBody(StreamInterface $body): self
            {
                if ($body === $this->stream) {
                    return $this;
                }
                $new = clone $this;
                $new->stream = $body;
                return $new;
            }

            private function setHeaders(array $headers): void
            {
                foreach ($headers as $header => $value) {
                    if (\is_int($header)) {
                        $header = (string)$header;
                    }
                    $value = $this->validateAndTrimHeader($header, $value);
                    $normalized = \strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
                    if (isset($this->headerNames[$normalized])) {
                        $header = $this->headerNames[$normalized];
                        $this->headers[$header] = \array_merge($this->headers[$header], $value);
                    } else {
                        $this->headerNames[$normalized] = $header;
                        $this->headers[$header] = $value;
                    }
                }
            }

            private function validateAndTrimHeader($header, $values): array
            {
                if (!\is_string($header) || 1 !== \preg_match("@^[!#\$%&'*+.^_`|~0-9A-Za-z-]+\$@", $header)) {
                    throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
                }
                if (!\is_array($values)) {
                    if (!\is_numeric($values) && !\is_string($values) || 1 !== \preg_match("@^[ \t!-~\x80-\xff]*\$@", (string)$values)) {
                        throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
                    }
                    return [\trim((string)$values, " \t")];
                }
                if (empty($values)) {
                    throw new \InvalidArgumentException('Header values must be a string or an array of strings, empty array given.');
                }
                $returnValues = [];
                foreach ($values as $v) {
                    if (!\is_numeric($v) && !\is_string($v) || 1 !== \preg_match("@^[ \t!-~\x80-\xff]*\$@", (string)$v)) {
                        throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
                    }
                    $returnValues[] = \trim((string)$v, " \t");
                }
                return $returnValues;
            }
        }
    }
    if (!trait_exists(RequestTrait::class)) {
        trait RequestTrait
        {
            private $method;
            private $requestTarget;
            private $uri;

            public function getRequestTarget(): string
            {
                if (null !== $this->requestTarget) {
                    return $this->requestTarget;
                }
                if ('' === ($target = $this->uri->getPath())) {
                    $target = '/';
                }
                if ('' !== $this->uri->getQuery()) {
                    $target .= '?' . $this->uri->getQuery();
                }
                return $target;
            }

            public function withRequestTarget($requestTarget): self
            {
                if (\preg_match('#\\s#', $requestTarget)) {
                    throw new \InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
                }
                $new = clone $this;
                $new->requestTarget = $requestTarget;
                return $new;
            }

            public function getMethod(): string
            {
                return $this->method;
            }

            public function withMethod($method): self
            {
                if (!\is_string($method)) {
                    throw new \InvalidArgumentException('Method must be a string');
                }
                $new = clone $this;
                $new->method = $method;
                return $new;
            }

            public function getUri(): UriInterface
            {
                return $this->uri;
            }

            public function withUri(UriInterface $uri, $preserveHost = false): self
            {
                if ($uri === $this->uri) {
                    return $this;
                }
                $new = clone $this;
                $new->uri = $uri;
                if (!$preserveHost || !$this->hasHeader('Host')) {
                    $new->updateHostFromUri();
                }
                return $new;
            }

            private function updateHostFromUri(): void
            {
                if ('' === ($host = $this->uri->getHost())) {
                    return;
                }
                if (null !== ($port = $this->uri->getPort())) {
                    $host .= ':' . $port;
                }
                if (isset($this->headerNames['host'])) {
                    $header = $this->headerNames['host'];
                } else {
                    $this->headerNames['host'] = $header = 'Host';
                }
                $this->headers = [$header => [$host]] + $this->headers;
            }
        }
    }
    if (!class_exists(Request::class)) {
        class Request implements RequestInterface
        {
            use MessageTrait;
            use RequestTrait;

            public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
            {
                if (!$uri instanceof UriInterface) {
                    $uri = new Uri($uri);
                }
                $this->method = $method;
                $this->uri = $uri;
                $this->setHeaders($headers);
                $this->protocol = $version;
                if (!$this->hasHeader('Host')) {
                    $this->updateHostFromUri();
                }
                if ('' !== $body && null !== $body) {
                    $this->stream = Stream::create($body);
                }
            }
        }
    }
    if (!class_exists(Response::class)) {
        class Response implements ResponseInterface
        {
            use MessageTrait;

            private const PHRASES = [100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-status', 208 => 'Already Reported', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Switch Proxy', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Unordered Collection', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 451 => 'Unavailable For Legal Reasons', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 511 => 'Network Authentication Required'];
            private $reasonPhrase = '';
            private $statusCode;

            public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = null)
            {
                if ('' !== $body && null !== $body) {
                    $this->stream = Stream::create($body);
                }
                $this->statusCode = $status;
                $this->setHeaders($headers);
                if (null === $reason && isset(self::PHRASES[$this->statusCode])) {
                    $this->reasonPhrase = self::PHRASES[$status];
                } else {
                    $this->reasonPhrase = $reason ?? '';
                }
                $this->protocol = $version;
            }

            public function getStatusCode(): int
            {
                return $this->statusCode;
            }

            public function getReasonPhrase(): string
            {
                return $this->reasonPhrase;
            }

            public function withStatus($code, $reasonPhrase = ''): self
            {
                if (!\is_int($code) && !\is_string($code)) {
                    throw new \InvalidArgumentException('Status code has to be an integer');
                }
                $code = (int)$code;
                if ($code < 100 || $code > 599) {
                    throw new \InvalidArgumentException(\sprintf('Status code has to be an integer between 100 and 599. A status code of %d was given', $code));
                }
                $new = clone $this;
                $new->statusCode = $code;
                if ((null === $reasonPhrase || '' === $reasonPhrase) && isset(self::PHRASES[$new->statusCode])) {
                    $reasonPhrase = self::PHRASES[$new->statusCode];
                }
                $new->reasonPhrase = $reasonPhrase;
                return $new;
            }
        }
    }
    if (!class_exists(ServerRequest::class)) {
        class ServerRequest implements ServerRequestInterface
        {
            use MessageTrait;
            use RequestTrait;

            private $attributes = [];
            private $cookieParams = [];
            private $parsedBody;
            private $queryParams = [];
            private $serverParams;
            private $uploadedFiles = [];

            public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1', array $serverParams = [])
            {
                $this->serverParams = $serverParams;
                if (!$uri instanceof UriInterface) {
                    $uri = new Uri($uri);
                }
                $this->method = $method;
                $this->uri = $uri;
                $this->setHeaders($headers);
                $this->protocol = $version;
                if (!$this->hasHeader('Host')) {
                    $this->updateHostFromUri();
                }
                if ('' !== $body && null !== $body) {
                    $this->stream = Stream::create($body);
                }
            }

            public function getServerParams(): array
            {
                return $this->serverParams;
            }

            public function getUploadedFiles(): array
            {
                return $this->uploadedFiles;
            }

            public function withUploadedFiles(array $uploadedFiles)
            {
                $new = clone $this;
                $new->uploadedFiles = $uploadedFiles;
                return $new;
            }

            public function getCookieParams(): array
            {
                return $this->cookieParams;
            }

            public function withCookieParams(array $cookies)
            {
                $new = clone $this;
                $new->cookieParams = $cookies;
                return $new;
            }

            public function getQueryParams(): array
            {
                return $this->queryParams;
            }

            public function withQueryParams(array $query)
            {
                $new = clone $this;
                $new->queryParams = $query;
                return $new;
            }

            public function getParsedBody()
            {
                return $this->parsedBody;
            }

            public function withParsedBody($data)
            {
                if (!\is_array($data) && !\is_object($data) && null !== $data) {
                    throw new \InvalidArgumentException('First parameter to withParsedBody MUST be object, array or null');
                }
                $new = clone $this;
                $new->parsedBody = $data;
                return $new;
            }

            public function getAttributes(): array
            {
                return $this->attributes;
            }

            public function getAttribute($attribute, $default = null)
            {
                if (false === \array_key_exists($attribute, $this->attributes)) {
                    return $default;
                }
                return $this->attributes[$attribute];
            }

            public function withAttribute($attribute, $value): self
            {
                $new = clone $this;
                $new->attributes[$attribute] = $value;
                return $new;
            }

            public function withoutAttribute($attribute): self
            {
                if (false === \array_key_exists($attribute, $this->attributes)) {
                    return $this;
                }
                $new = clone $this;
                unset($new->attributes[$attribute]);
                return $new;
            }
        }
    }
    if (!class_exists(Stream::class)) {
        class Stream implements StreamInterface
        {
            private $stream;
            private $seekable;
            private $readable;
            private $writable;
            private $uri;
            private $size;
            private const READ_WRITE_HASH = ['read' => ['r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true, 'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true, 'a+' => true], 'write' => ['w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true, 'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true]];

            private function __construct() {}

            public static function create($body = ''): StreamInterface
            {
                if ($body instanceof StreamInterface) {
                    return $body;
                }
                if (\is_string($body)) {
                    $resource = \fopen('php://temp', 'rw+');
                    \fwrite($resource, $body);
                    $body = $resource;
                }
                if (\is_resource($body)) {
                    $new = new self();
                    $new->stream = $body;
                    $meta = \stream_get_meta_data($new->stream);
                    $new->seekable = $meta['seekable'] && 0 === \fseek($new->stream, 0, \SEEK_CUR);
                    $new->readable = isset(self::READ_WRITE_HASH['read'][$meta['mode']]);
                    $new->writable = isset(self::READ_WRITE_HASH['write'][$meta['mode']]);
                    return $new;
                }
                throw new \InvalidArgumentException('First argument to Stream::create() must be a string, resource or StreamInterface.');
            }

            public function __destruct()
            {
                $this->close();
            }

            public function __toString()
            {
                try {
                    if ($this->isSeekable()) {
                        $this->seek(0);
                    }
                    return $this->getContents();
                } catch (\Throwable $e) {
                    if (\PHP_VERSION_ID >= 70400) {
                        throw $e;
                    }
                    if (\is_array($errorHandler = \set_error_handler('var_dump'))) {
                        $errorHandler = $errorHandler[0] ?? null;
                    }
                    \restore_error_handler();
                    if ($e instanceof \Error || $errorHandler instanceof SymfonyErrorHandler || $errorHandler instanceof SymfonyLegacyErrorHandler) {
                        return \trigger_error((string)$e, \E_USER_ERROR);
                    }
                    return '';
                }
            }

            public function close(): void
            {
                if (isset($this->stream)) {
                    if (\is_resource($this->stream)) {
                        \fclose($this->stream);
                    }
                    $this->detach();
                }
            }

            public function detach()
            {
                if (!isset($this->stream)) {
                    return null;
                }
                $result = $this->stream;
                unset($this->stream);
                $this->size = $this->uri = null;
                $this->readable = $this->writable = $this->seekable = false;
                return $result;
            }

            private function getUri()
            {
                if (false !== $this->uri) {
                    $this->uri = $this->getMetadata('uri') ?? false;
                }
                return $this->uri;
            }

            public function getSize(): ?int
            {
                if (null !== $this->size) {
                    return $this->size;
                }
                if (!isset($this->stream)) {
                    return null;
                }
                if ($uri = $this->getUri()) {
                    \clearstatcache(true, $uri);
                }
                $stats = \fstat($this->stream);
                if (isset($stats['size'])) {
                    $this->size = $stats['size'];
                    return $this->size;
                }
                return null;
            }

            public function tell(): int
            {
                if (false === ($result = \ftell($this->stream))) {
                    throw new \RuntimeException('Unable to determine stream position');
                }
                return $result;
            }

            public function eof(): bool
            {
                return !$this->stream || \feof($this->stream);
            }

            public function isSeekable(): bool
            {
                return $this->seekable;
            }

            public function seek($offset, $whence = \SEEK_SET): void
            {
                if (!$this->seekable) {
                    throw new \RuntimeException('Stream is not seekable');
                }
                if (-1 === \fseek($this->stream, $offset, $whence)) {
                    throw new \RuntimeException('Unable to seek to stream position "' . $offset . '" with whence ' . \var_export($whence, true));
                }
            }

            public function rewind(): void
            {
                $this->seek(0);
            }

            public function isWritable(): bool
            {
                return $this->writable;
            }

            public function write($string): int
            {
                if (!$this->writable) {
                    throw new \RuntimeException('Cannot write to a non-writable stream');
                }
                $this->size = null;
                if (false === ($result = \fwrite($this->stream, $string))) {
                    throw new \RuntimeException('Unable to write to stream');
                }
                return $result;
            }

            public function isReadable(): bool
            {
                return $this->readable;
            }

            public function read($length): string
            {
                if (!$this->readable) {
                    throw new \RuntimeException('Cannot read from non-readable stream');
                }
                if (false === ($result = \fread($this->stream, $length))) {
                    throw new \RuntimeException('Unable to read from stream');
                }
                return $result;
            }

            public function getContents(): string
            {
                if (!isset($this->stream)) {
                    throw new \RuntimeException('Unable to read stream contents');
                }
                if (false === ($contents = \stream_get_contents($this->stream))) {
                    throw new \RuntimeException('Unable to read stream contents');
                }
                return $contents;
            }

            public function getMetadata($key = null)
            {
                if (!isset($this->stream)) {
                    return $key ? null : [];
                }
                $meta = \stream_get_meta_data($this->stream);
                if (null === $key) {
                    return $meta;
                }
                return $meta[$key] ?? null;
            }
        }
    }
    if (!class_exists(UploadedFile::class)) {
        class UploadedFile implements UploadedFileInterface
        {
            private const ERRORS = [\UPLOAD_ERR_OK => 1, \UPLOAD_ERR_INI_SIZE => 1, \UPLOAD_ERR_FORM_SIZE => 1, \UPLOAD_ERR_PARTIAL => 1, \UPLOAD_ERR_NO_FILE => 1, \UPLOAD_ERR_NO_TMP_DIR => 1, \UPLOAD_ERR_CANT_WRITE => 1, \UPLOAD_ERR_EXTENSION => 1];
            private $clientFilename;
            private $clientMediaType;
            private $error;
            private $file;
            private $moved = false;
            private $size;
            private $stream;

            public function __construct($streamOrFile, $size, $errorStatus, $clientFilename = null, $clientMediaType = null)
            {
                if (false === \is_int($errorStatus) || !isset(self::ERRORS[$errorStatus])) {
                    throw new \InvalidArgumentException('Upload file error status must be an integer value and one of the "UPLOAD_ERR_*" constants.');
                }
                if (false === \is_int($size)) {
                    throw new \InvalidArgumentException('Upload file size must be an integer');
                }
                if (null !== $clientFilename && !\is_string($clientFilename)) {
                    throw new \InvalidArgumentException('Upload file client filename must be a string or null');
                }
                if (null !== $clientMediaType && !\is_string($clientMediaType)) {
                    throw new \InvalidArgumentException('Upload file client media type must be a string or null');
                }
                $this->error = $errorStatus;
                $this->size = $size;
                $this->clientFilename = $clientFilename;
                $this->clientMediaType = $clientMediaType;
                if (\UPLOAD_ERR_OK === $this->error) {
                    if (\is_string($streamOrFile)) {
                        $this->file = $streamOrFile;
                    } elseif (\is_resource($streamOrFile)) {
                        $this->stream = Stream::create($streamOrFile);
                    } elseif ($streamOrFile instanceof StreamInterface) {
                        $this->stream = $streamOrFile;
                    } else {
                        throw new \InvalidArgumentException('Invalid stream or file provided for UploadedFile');
                    }
                }
            }

            private function validateActive(): void
            {
                if (\UPLOAD_ERR_OK !== $this->error) {
                    throw new \RuntimeException('Cannot retrieve stream due to upload error');
                }
                if ($this->moved) {
                    throw new \RuntimeException('Cannot retrieve stream after it has already been moved');
                }
            }

            public function getStream(): StreamInterface
            {
                $this->validateActive();
                if ($this->stream instanceof StreamInterface) {
                    return $this->stream;
                }
                try {
                    return Stream::create(\fopen($this->file, 'r'));
                } catch (\Throwable $e) {
                    throw new \RuntimeException(\sprintf('The file "%s" cannot be opened.', $this->file));
                }
            }

            public function moveTo($targetPath): void
            {
                $this->validateActive();
                if (!\is_string($targetPath) || '' === $targetPath) {
                    throw new \InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
                }
                if (null !== $this->file) {
                    $this->moved = 'cli' === \PHP_SAPI ? \rename($this->file, $targetPath) : \move_uploaded_file($this->file, $targetPath);
                } else {
                    $stream = $this->getStream();
                    if ($stream->isSeekable()) {
                        $stream->rewind();
                    }
                    try {
                        $dest = Stream::create(\fopen($targetPath, 'w'));
                    } catch (\Throwable $e) {
                        throw new \RuntimeException(\sprintf('The file "%s" cannot be opened.', $targetPath));
                    }
                    while (!$stream->eof()) {
                        if (!$dest->write($stream->read(1048576))) {
                            break;
                        }
                    }
                    $this->moved = true;
                }
                if (false === $this->moved) {
                    throw new \RuntimeException(\sprintf('Uploaded file could not be moved to "%s"', $targetPath));
                }
            }

            public function getSize(): int
            {
                return $this->size;
            }

            public function getError(): int
            {
                return $this->error;
            }

            public function getClientFilename(): ?string
            {
                return $this->clientFilename;
            }

            public function getClientMediaType(): ?string
            {
                return $this->clientMediaType;
            }
        }
    }
    if (!class_exists(Uri::class)) {
        class Uri implements UriInterface
        {
            private const SCHEMES = ['http' => 80, 'https' => 443];
            private const CHAR_UNRESERVED = 'a-zA-Z0-9_\\-\\.~';
            private const CHAR_SUB_DELIMS = '!\\$&\'\\(\\)\\*\\+,;=';
            private $scheme = '';
            private $userInfo = '';
            private $host = '';
            private $port;
            private $path = '';
            private $query = '';
            private $fragment = '';

            public function __construct(string $uri = '')
            {
                if ('' !== $uri) {
                    if (false === ($parts = \parse_url($uri))) {
                        throw new \InvalidArgumentException(\sprintf('Unable to parse URI: "%s"', $uri));
                    }
                    $this->scheme = isset($parts['scheme']) ? \strtr($parts['scheme'], 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') : '';
                    $this->userInfo = $parts['user'] ?? '';
                    $this->host = isset($parts['host']) ? \strtr($parts['host'], 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') : '';
                    $this->port = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
                    $this->path = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
                    $this->query = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
                    $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';
                    if (isset($parts['pass'])) {
                        $this->userInfo .= ':' . $parts['pass'];
                    }
                }
            }

            public function __toString(): string
            {
                return self::createUriString($this->scheme, $this->getAuthority(), $this->path, $this->query, $this->fragment);
            }

            public function getScheme(): string
            {
                return $this->scheme;
            }

            public function getAuthority(): string
            {
                if ('' === $this->host) {
                    return '';
                }
                $authority = $this->host;
                if ('' !== $this->userInfo) {
                    $authority = $this->userInfo . '@' . $authority;
                }
                if (null !== $this->port) {
                    $authority .= ':' . $this->port;
                }
                return $authority;
            }

            public function getUserInfo(): string
            {
                return $this->userInfo;
            }

            public function getHost(): string
            {
                return $this->host;
            }

            public function getPort(): ?int
            {
                return $this->port;
            }

            public function getPath(): string
            {
                return $this->path;
            }

            public function getQuery(): string
            {
                return $this->query;
            }

            public function getFragment(): string
            {
                return $this->fragment;
            }

            public function withScheme($scheme): self
            {
                if (!\is_string($scheme)) {
                    throw new \InvalidArgumentException('Scheme must be a string');
                }
                if ($this->scheme === ($scheme = \strtr($scheme, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'))) {
                    return $this;
                }
                $new = clone $this;
                $new->scheme = $scheme;
                $new->port = $new->filterPort($new->port);
                return $new;
            }

            public function withUserInfo($user, $password = null): self
            {
                $info = $user;
                if (null !== $password && '' !== $password) {
                    $info .= ':' . $password;
                }
                if ($this->userInfo === $info) {
                    return $this;
                }
                $new = clone $this;
                $new->userInfo = $info;
                return $new;
            }

            public function withHost($host): self
            {
                if (!\is_string($host)) {
                    throw new \InvalidArgumentException('Host must be a string');
                }
                if ($this->host === ($host = \strtr($host, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'))) {
                    return $this;
                }
                $new = clone $this;
                $new->host = $host;
                return $new;
            }

            public function withPort($port): self
            {
                if ($this->port === ($port = $this->filterPort($port))) {
                    return $this;
                }
                $new = clone $this;
                $new->port = $port;
                return $new;
            }

            public function withPath($path): self
            {
                if ($this->path === ($path = $this->filterPath($path))) {
                    return $this;
                }
                $new = clone $this;
                $new->path = $path;
                return $new;
            }

            public function withQuery($query): self
            {
                if ($this->query === ($query = $this->filterQueryAndFragment($query))) {
                    return $this;
                }
                $new = clone $this;
                $new->query = $query;
                return $new;
            }

            public function withFragment($fragment): self
            {
                if ($this->fragment === ($fragment = $this->filterQueryAndFragment($fragment))) {
                    return $this;
                }
                $new = clone $this;
                $new->fragment = $fragment;
                return $new;
            }

            private static function createUriString(string $scheme, string $authority, string $path, string $query, string $fragment): string
            {
                $uri = '';
                if ('' !== $scheme) {
                    $uri .= $scheme . ':';
                }
                if ('' !== $authority) {
                    $uri .= '//' . $authority;
                }
                if ('' !== $path) {
                    if ('/' !== $path[0]) {
                        if ('' !== $authority) {
                            $path = '/' . $path;
                        }
                    } elseif (isset($path[1]) && '/' === $path[1]) {
                        if ('' === $authority) {
                            $path = '/' . \ltrim($path, '/');
                        }
                    }
                    $uri .= $path;
                }
                if ('' !== $query) {
                    $uri .= '?' . $query;
                }
                if ('' !== $fragment) {
                    $uri .= '#' . $fragment;
                }
                return $uri;
            }

            private static function isNonStandardPort(string $scheme, int $port): bool
            {
                return !isset(self::SCHEMES[$scheme]) || $port !== self::SCHEMES[$scheme];
            }

            private function filterPort($port): ?int
            {
                if (null === $port) {
                    return null;
                }
                $port = (int)$port;
                if (0 > $port || 0xffff < $port) {
                    throw new \InvalidArgumentException(\sprintf('Invalid port: %d. Must be between 0 and 65535', $port));
                }
                return self::isNonStandardPort($this->scheme, $port) ? $port : null;
            }

            private function filterPath($path): string
            {
                if (!\is_string($path)) {
                    throw new \InvalidArgumentException('Path must be a string');
                }
                return \preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\\/]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $path);
            }

            private function filterQueryAndFragment($str): string
            {
                if (!\is_string($str)) {
                    throw new \InvalidArgumentException('Query and fragment must be a string');
                }
                return \preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\\/\\?]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $str);
            }

            private static function rawurlencodeMatchZero(array $match): string
            {
                return \rawurlencode($match[0]);
            }
        }
    }
}
