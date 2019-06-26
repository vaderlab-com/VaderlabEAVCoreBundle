# Working with entities

Create, Update, Delete and get/set values

# Create new Entity

```php
<?php

$em = $this->get('doctrine.orm.entity_manager');

// Find existing schema
$userSchema = $em
    ->getRepository(Schema::class)
    ->findOneByName('User');

// Get entity service for working with entities
$entityService = $this->get('vaderlab.eav.entity_resolver');

// Create new Entity
$entity = $entityService->createEntity($userSchema);

// Exiting attributes
$attributes = [
    'email'     => 'username@email.com',
    'username'  => 'Asisyas',
];

// Fill attributes
foreach($attributes as $attribute => $value) {
    $entityService->setValue($entity, $attribute, $value);    
}

// Persist and flush
$em->persist($entity);
$em->flush();

```