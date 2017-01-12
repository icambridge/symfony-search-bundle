# Symfony Search Bundle

This bundle provides a suitable search for Symfony. Only a few steps are necessary.
 
## Requirements
 
 * Symfony 3.1+
 * SQLite
 
## Documentation

### Bundle installation

#### Step 1: Add github repository to composer.json

Add the following to your composer.json:

```
"repositories": [
    {
        "type": "git",
        "url":  "git@github.com:Interlutions/symfony-search-bundle.git"
    }
],
```

Define a personal token in your [github](https://github.com/settings/tokens) settings and save it to your composer 
config:

```
composer config --global github-oauth.github.com <TOKEN>
```

#### Step 2: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require itl/search-bundle dev-master
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

#### Step 3: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Itl\ItlSearchBundle\ItlSearchBundle()

        );

        // ...
    }

    // ...
}
```

### Usage

#### Build search index

Set the configuration and build the search index:

```
$storage = '%kernel.root_dir%/../var/search';

$config = [
    'driver'   => 'db_driver', // e. g. mysql
    'database' => 'database_name',
    'host'     => 'database_host',
    'username' => 'database_user',
    'password' => 'database_password',
    'storage'  => 'storage'
];

$query = 'SELECT id, title FROM products';
$this->container->get('search_service')->startIndex('INDEX_NAME', $query, $config, $storage);
```

#### Get search results

Pass the configuration and search query, set the limit and the index name. You should get the corresponding ID's.
 
 ```
$query = $request->query->get('q', null);
 
$storage = '%kernel.root_dir%/../var/search';
 
$config = [
    'driver'   => 'db_driver', // e. g. mysql
    'database' => 'database_name',
    'host'     => 'database_host',
    'username' => 'database_user',
    'password' => 'database_password',
    'storage'  => $storage
];
 
$resultIds = $this->get('search_service')->getSearchResults($query, 'INDEX_NAME', $config, 1000);
```

### Warning

MySQL returns the result ordered by ID, not the best match of the search query you get from this search bundle. To
archive the correct order follow these steps:

#### Step 1: Get DoctrineExtensions

The DoctrineExtensions let you use the MySQL order by instruction "FIELD". So require them:

```
composer require beberlei/DoctrineExtensions
```

#### Step 2: Usage of MySQL's "FIELD"

Make the DoctrineExtensions available:

```
$doctrineConfig = $this->_em->getConfiguration();
$doctrineConfig->addCustomStringFunction('FIELD', 'DoctrineExtensions\Query\Mysql\Field');
```

Now build the MySQL query:

```
$queryBuilder = $this->createQueryBuilder('p')
        ->select('p')
        ->where('p.id IN (:ids) ORDER BY FIELD(p.id, :ids)')
        ->setParameter('ids', $ids);
```