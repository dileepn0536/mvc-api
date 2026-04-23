<?php

class Cache
{
    private string $cacheDir;
    private int $defaultTTL;

    public function __construct(
        string $cacheDir = 'cache/',
        int $defaultTTL = 300
    ) {
        $this->cacheDir = $cacheDir;
        $this->defaultTTL = $defaultTTL;

        // create cache directory if not exists
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get(string $key): mixed
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (!file_exists($cacheFile)) {
            return null; // cache miss
        }

        $content = file_get_contents($cacheFile);
        $data = json_decode($content, true);

        // check if expired
        if (time() > $data['expires_at']) {
            $this->delete($key); // clean up
            return null; // cache expired
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl = null): void
    {
        $cacheFile = $this->getCacheFilePath($key);
        $ttl = $ttl ?? $this->defaultTTL;

        $data = [
            'value'      => $value,
            'expires_at' => time() + $ttl,
            'created_at' => time()
        ];

        file_put_contents($cacheFile, json_encode($data));
    }

    public function delete(string $key): void
    {
        $cacheFile = $this->getCacheFilePath($key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function flush(): void
    {
        // clear ALL cache files
        $files = glob($this->cacheDir . '*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    private function getCacheFilePath(string $key): string
    {
        // md5 to handle special characters in key
        return $this->cacheDir . md5($key) . '.json';
    }
}