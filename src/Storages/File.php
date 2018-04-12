<?php

namespace Juhara\ZzzCache\Storages;

use Juhara\ZzzCache\Contracts\CacheStorageInterface;
use Juhara\ZzzCache\Contracts\HashInterface;

/**
* cache implementation using File as storage
* ----------------------------------------------
* Note:
* This storage implementation use file access time and file modified time
* to store time to live of cache.
* ttl = (file access time - file modified time)
*
* When we create the file, we will make 'file modified time' set to current time
* and and file access time a time in future (current time + time to live)
*
* Everytime we check file cache existence, we will update file modified time to
* current time but maintain file access time to its original value
*
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
        $filename = $this->path($cacheId);

        if (! file_exists($filename)) {
            return false;
        }

        //ok file is exist, test if it is not expired
        //we considered file is expired if modified time >= access time
        $fstat = stat($filename);
        if ($fstat['mtime'] >= $fstat['atime']) {
            //if we get here then cache is expired,
            //delete file and tell cache manager that cache is missed
            unlink($filename);
            return false;
        }

        //update file time info and tell cache manager that cache is hit
        touch($filename, time(), $fstat['atime']);
        return true;
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
     * @param  int $ttl time to live
     * @return int number of bytes written
     */
    public function write($cacheId, $data, $ttl)
    {
        $filename = $this->path($cacheId);
        $bytesWritten = file_put_contents($filename, $data);
        $currTime = time();
        touch($filename, $currTime, $currTime + $ttl);
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
