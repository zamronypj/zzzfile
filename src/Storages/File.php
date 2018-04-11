<?php

namespace Juhara\ZzzCache\Storages;

use Juhara\ZzzCache\Contracts\CacheStorageInterface;
use Juhara\ZzzCache\Contracts\HashInterface;

/**
* cache implementation using File as storage
* @author Zamrony P. Juhara <zamronypj@yahoo.com>
*/
final class File implements CacheStorageInterface
{
    /**
     * cache directory
     * @var string
     */
    private $cacheDirectory;

    /**
     * prefix to append to cache filename
     * @var string
     */
    private $filenamePrefix;

    /**
     * Hash utility class
     * @var HashInterface
     */
    private $hashService;

    /**
     * constructor
     * @param HashInterface $hashService   hash utility
     * @param string        $cacheDirectory cache directory
     * @param string        $filenamePrefix prefix
     */
    public function __construct(
        HashInterface $hashService,
        $cacheDirectory = '',
        $filenamePrefix = ''
    ) {
        $this->hashService = $hashService;
        $this->cacheDirectory = $cacheDirectory;
        $this->filenamePrefix = $filenamePrefix;
    }

    /**
     * get file path based on cache identifier
     * @param  string $cacheId cache identifier
     * @return string filepath
     */
    private function path($cacheId)
    {
        $hashedCacheId = $this->hashService->hash($cacheId);
        return $this->cacheDirectory . $this->filenamePrefix . $hashedCacheId;
    }

    /**
     * test availability of cache
     * @param  string $cacheId cache identifier
     * @return boolean true if available or false otherwise
     */
    public function exists($cacheId)
    {
        return file_exists($this->path($cacheId));
    }

    /**
     * read data from storage by cache name
     * @param  string $cacheId cache identifier
     * @return string data from storage in serialized format
     */
    public function read($cacheId)
    {
        return file_get_contents($this->path($cacheId));
    }

    /**
     * write data to storage by cache name
     * @param  string $cacheId cache identifier
     * @param  string $data item to cache in serialized format
     * @return int number of bytes written
     */
    public function write($cacheId, $data)
    {
        return file_put_contents($this->path($cacheId), $data);
    }

    /**
     * remove data from storage by cache id
     * @param  string $cacheId cache identifier
     * @return boolean true if cache is successfully removed
     */
    public function remove($cacheId)
    {
        return unlink($this->path($cacheId));
    }

    /**
     * remove all data from storage
     */
    public function clear()
    {
        //TODO: implement delete all cache files in cache directory
    }
}
