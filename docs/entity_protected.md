# Use protected schema and attributes

Protected attributes can not update and delete.

When user try update or delete this attributes from database,
it is throw exceptions: 
  * [ProtectedAttributeUpdateDeniedException](Exception/Attribute/ProtectedAttributeUpdateDeniedException.php)
  * [ProtectedAttributeRemoveDeniedException](Exception/Attribute/ProtectedAttributeRemoveDeniedException.php)


## Basic usage

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