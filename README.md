# zzzfile
File-based cache storage implementation for ZzzCache

# Requirement
- [PHP >= 5.4](https://php.net)
- [Composer](https://getcomposer.org)
- [ZzzCache](https://github.com/zamronypj/zzzcache)

# Installation
Run through composer

    $ composer require juhara/zzzfile

# How to use

    <?php

    use Juhara\ZzzCache\Cache;
    use Juhara\ZzzCache\Storages\File;
    use Juhara\ZzzCache\Helpers\TimeUtility;
    use Juhara\ZzzCache\Helpers\Md5Hash;

    // create a file-based cache where all cache
    // files is stored in directory name
    // app/storages/cache with
    // filename prefixed with string 'cache'
    $cache = new Cache(
        new File(
            new Md5Hash(),
            'app/storages/cache/',
            'cache'
        ),
        new TimeUtility()
    );

# Contributing

If you have any improvement or issues please submit PR.

Thank you.
