DoctrineCacheBundle
===================

This Bundle provides integration into Symfony2 with the Doctrine Common Cache layer

Installation
============

### 1. Add this bundle to your project:

**Using submodules**

```bash
$ git submodule add git://github.com/liip/LiipDoctrineCacheBundle.git vendor/bundles/Liip/DoctrineCacheBundle
```

**Using the vendors script**

Add the following lines in your `deps` file:

```
[LiipDoctrineCacheBundle]
    git=git://github.com/liip/LiipDoctrineCacheBundle.git
    target=/bundles/Liip/DoctrineCacheBundle
```

Now, run the vendors script to download the bundle:

```bash
$ php bin/vendors install
```

### 2. Add the Liip namespace to your autoloader:

```php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    'Liip' => __DIR__.'/../vendor/bundles',
    // your other namespaces
));
```

### 3. Add this bundle to your application's kernel:

```php
<?php
// application/ApplicationKernel.php
public function registerBundles()
{
  return array(
      // ...
      new Liip\DoctrineCacheBundle\LiipDoctrineCacheBundle(),
      // ...
  );
}
```

Configuration
=============

Simply configure any number of cache services:

    # app/config.yml
    liip_doctrine_cache:
        namespaces:
            # name of the service (aka liip_doctrine_cache.ns.foo)
            foo:
                # cache namespace is "ding", this is optional
                namespace: ding
                # cache type is "apc"
                type: apc
            # name of the service (aka liip_doctrine_cache.ns.lala) and namespace
            lala:
                # cache type is "apc"
                type: apc
            # name of the service (aka liip_doctrine_cache.ns.bar)
            bar:
                # cache namespace is "dong"
                namespace: dong
                # cache type is "memcached"
                type: memcached
                # name of a service of class Memcached that is fully configured (optional)
                id: my_memcached_service
                # port to use for memcache(d) (default is 11211)
                port: 11211
                # host to use for memcache(d) (default is localhost)
                host: localhost

Usage
=====

Simply use `liip_doctrine_cache.ns.[your_name]` in dependency injection config files or using `$container->get('liip_doctrine_cache.ns.[your_name]')` in your code.

Custom cache types
==================

Simply define a new type by defining a service named `liip_doctrine_cache.[type name]`.
Note the service needs to implement ``Doctrine\Common\Cache\Cache`` interface.
