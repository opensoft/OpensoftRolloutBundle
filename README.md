OpensoftRolloutBundle
=====================

A Symfony2 Bundle for [opensoft/rollout](http://github.com/opensoft/rollout)

[![Build Status](https://travis-ci.org/opensoft/OpensoftRolloutBundle.svg?branch=master)](https://travis-ci.org/opensoft/OpensoftRolloutBundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/badges/quality-score.png?s=4980d55f8407070251ca97ba3b92f855cfce67ce)](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/) [![Code Coverage](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/badges/coverage.png?s=2a11bb9fe02adb950f1b446311c6044a70a2e1fd)](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/) [![Total Downloads](https://poser.pugx.org/opensoft/rollout-bundle/downloads.png)](https://packagist.org/packages/opensoft/rollout-bundle) [![Latest Stable Version](https://poser.pugx.org/opensoft/rollout-bundle/v/stable.png)](https://packagist.org/packages/opensoft/rollout-bundle)

### Obligatory Screenshot

![Screenshot](https://github.com/opensoft/OpensoftRolloutBundle/raw/master/Resources/doc/screenshot-extended.png)

Installation
------------

### 1) Install via composer

Add the bundle via composer

    composer require opensoft/rollout-bundle

And activate it inside your `app\AppKernel.php`

```php
new Opensoft\RolloutBundle\OpensoftRolloutBundle(),
```

### 2) Configuration

Add the following to your configuration

```yaml
opensoft_rollout:
    user_provider_service: acme.user_provider_service
    storage_service: acme.storage_service
```

### 3) Implement Interfaces

#### RolloutUserInterface

Any rollout user _must_ implement the `RolloutUserInterface`.  Often, this will be your main user object in the application.

```php
<?php

use Opensoft/Rollout/RolloutUserInterface;

class User implements RolloutUserInterface
{
    /**
     * @return string
     */
    public function getRolloutIdentifier()
    {
        return $this->email;
    }
}

```

#### UserProviderInterface

Expose individual users to the rollout interface by implementing the `UserProviderInterface`

```php
<?php

use Doctrine\ORM\EntityRepository;
use Opensoft\RolloutBundle\Rollout\UserProviderInterface;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * @param  mixed $id
     * @return RolloutUserInterface|null
     */
    public function findByRolloutIdentifier($id)
    {
        return $this->findOneBy(array('email' => $id));
    }
}
```

#### GroupDefinitionInterface

Provide different groups of users to your rollout.

Tag all of your group definitions with the DIC tag `rollout.group` to expose them to the rollout interface

```php
<?php

use Opensoft\RolloutBundle\Rollout\GroupDefinitionInterface;

class AcmeEmployeeGroupDefinition implements GroupDefinitionInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'acme_employee';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'This group contains acme company employees';
    }

    /**
     * @return \Closure
     */
    public function getCallback()
    {
        return function(RolloutUserInterface $user) {
            // Is this user an employee of acme?
            return strpos($user->getEmail(), 'acme.com') !== false;
        };
    }
}
```

#### StorageInterface

Implement a custom storage solution.

**Note:** The rollout `StorageInterface` [changed](https://github.com/opensoft/rollout/releases/tag/2.0.0) in version `2.0.0`.

```php
<?php

use Opensoft\Rollout\Storage\StorageInterface;

class MyStorage implements StorageInterface
{
    /**
     * @param  string     $key
     * @return mixed|null Null if the value is not found
     */
    public function get($key)
    {
        // implement get
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        // implement set
    }

    /**
     * @param string $key
     * @since 2.0.0
     */
    public function remove($key)
    {
        // implement remove
    }
}
```

### 4) Activate Routing

Add the following to your `app/Resources/config/routing.yml` file:

    opensoft_rollout:
        resource: "@OpensoftRolloutBundle/Resources/config/routing.yml"
        prefix:   /admin/rollout

## Usage

Check if a feature is enabled in a controller

```php
if ($this->get('rollout')->isActive('chat', $this->getUser())) {
    // do some chat related feature work
}
```

Twig example:

```
{% if rollout_is_active('chat', app.user) %}
   <!-- show a chat interface -->
{% endif %}
```


## Further Reading

* https://github.com/FetLife/rollout
* http://blog.travis-ci.com/2014-03-04-use-feature-flags-to-ship-changes-with-confidence/
* http://code.flickr.net/2009/12/02/flipping-out/
* http://en.wikipedia.org/wiki/Feature_toggle
