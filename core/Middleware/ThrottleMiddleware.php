<?php
// core/Middleware/ThrottleMiddleware.php
namespace Core\Middleware;

use Predis\Client as Redis;

class ThrottleMiddleware
{
    protected Redis $redis;
    protected int   $limit;
    protected int   $window; // seconds

    public function __construct(int $limit, int $windowSeconds)
    {
        $this->redis = new Redis($_ENV['REDIS_URL'] ?? 'tcp://127.0.0.1:6379');
        $this->limit = $limit;
        $this->window = $windowSeconds;
    }

    public function handle(): void
    {
        $ip  = $_SERVER['REMOTE_ADDR'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $key = "throttle:{$uri}:{$ip}";

        $count = $this->redis->incr($key);
        if ($count === 1) {
            // first hit: set expiry
            $this->redis->expire($key, $this->window);
        }

        if ($count > $this->limit) {
            http_response_code(429);
            echo "Too many requests. Try again later.";
            exit;
        }
    }
}
