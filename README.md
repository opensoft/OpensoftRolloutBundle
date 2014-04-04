OpensoftRolloutBundle
=====================

[![Build Status](https://travis-ci.org/opensoft/OpensoftRolloutBundle.svg?branch=master)](https://travis-ci.org/opensoft/OpensoftRolloutBundle)

Installation
------------

### 4) Activate Routing

Add the following to your `app/Resources/config/routing.yml` file:

    opensoft_rollout:
        resource: "@OpensoftRolloutBundle/Resources/config/routing.yml"
        prefix:   /admin/rollout
