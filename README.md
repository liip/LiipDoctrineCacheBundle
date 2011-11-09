DoctrineCacheBundle
===================

This Bundle provides integration into Symfony2 with the Doctrine Common Cache layer

Installation
============

    1. Add this bundle to your project as a Git submodule:

        $ git submodule add git://github.com/liip/LiipDoctrineCacheBundle.git vendor/bundles/Liip/DoctrineCacheBundle

    2. Add the Liip namespace to your autoloader:

        // app/autoload.php
        $loader->registerNamespaces(array(
            'Liip' => __DIR__.'/../vendor/bundles',
            // your other namespaces
        ));

    3. Add this bundle to your application's kernel:

        // application/ApplicationKernel.php
        public function registerBundles()
        {
          return array(
              // ...
              new Liip\DoctrineCacheBundle\LiipDoctrineCacheBundle(),
              // ...
          );
        }

Configuration
=============

Simply configure any number of cache services:

    # app/config.yml
    liip_doctrine_cache:
        namespaces:
            # name of the service (aka liip_doctrine_cache.ns.foo)
            foo:
                # cache namespace
                namespace: ding
                # cache type
                type: apc
            # name of the service (aka liip_doctrine_cache.ns.bar)
            bar:
                # cache namespace
                namespace: ding
                # cache type
                type: memcached
                # name of a service of class Memcached
                id: my_memcached_service
