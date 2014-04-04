OpensoftRolloutBundle
=====================

[![Build Status](https://travis-ci.org/opensoft/OpensoftRolloutBundle.svg?branch=master)](https://travis-ci.org/opensoft/OpensoftRolloutBundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/badges/quality-score.png?s=4980d55f8407070251ca97ba3b92f855cfce67ce)](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/) [![Code Coverage](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/badges/coverage.png?s=2a11bb9fe02adb950f1b446311c6044a70a2e1fd)](https://scrutinizer-ci.com/g/opensoft/OpensoftRolloutBundle/)

Installation
------------

### 4) Activate Routing

Add the following to your `app/Resources/config/routing.yml` file:

    opensoft_rollout:
        resource: "@OpensoftRolloutBundle/Resources/config/routing.yml"
        prefix:   /admin/rollout
