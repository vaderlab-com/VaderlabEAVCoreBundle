# Entity Attribute Value (EAV)

Based on [DoctrineORM](https://www.doctrine-project.org/) and [Symfony](https://symfony.com/). 

## Requirements

1. php >= 7.3
2. Symfony >= 4.2

## Installation

Use the package manager [composer](https://getcomposer.org/) to install.

```bash
composer require vaderlab/eav-core-bundle
```

## Usage

Create database schema
```bash
  $ php bin/console doctrine:schema:update --force
```

Create new schema class
```php

<?php

use Vaderlab\EAV\Core\Annotation as EAV;

class User
{
    /**
     * @EAV\Id(target="id"),
     */
    private $id;
   
    /**
     * @EAV\Attribute( name="username", type="string", length=256, unique=true)
     */
    private $username;
     
    /**
     * @EAV\Attribute( name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @EAV\Attribute( name="is_enabled", type="boolean")
     */
    private $enabled;

   /* getters and setters */
   /* ....... */
}

```
Create (syncronize) EAV schema with protected attributes
```bash
$ php bin/console vaderlab:eav:schema-update -f
```

Create Entity
```php
$em   = $this->get('doctrine.orm.entity_manager');
$user = new User();

$user->setUsername('Asisyas');
$user->setEnabled(true);

$em->persist($user);
$em->flush();
````

Find entity by Id
```
$em    = $this->get('doctrine.orm.entity_manager');
$user  = $em->find(User::class, 1);

$user->setUsername('Vaderlab');
$em->persist($user);
$em->flush();
```

## TODO:
1. Indexer and QueryBundler
2. Reactive Forms
3. Protected properties (If property contains in the entity class, user can not update this property from admin)
4. Simple admin interface
5. REST interface


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)