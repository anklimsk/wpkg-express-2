# Contributing

Contributions to **Conversio** library are always welcomed and encouraged.

You make our lives easier by sending us your contributions through github pull requests.

* Coding standard for the project is [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

* Any contribution must provide tests for additional introduced conditions

## Team members

The core team members are:

| Name            | Nickname                             |
|:---------------:|:------------------------------------:|
| Leo Di Donato   | [leodido](http://github.com/leodido) |

## Got a question or problem?

If you have questions about how to use Conversio library please write at <leodidonato@gmail.com>.

Other communication channels will be activated soon. In the mean time you can also contact us writing a [new issue](https://github.com/leodido/conversio/issues/new).

Due to time constraints, we are not always able to respond as quickly as we would like. Please do not take delays personal and feel free to remind us.

## New features

You can request a new feature by submitting an issue to our github repository. If you would like to implement a new feature then consider what kind of change it is:

* **Major changes**

    This kind of contribution should be discussed first with us in issues. This way we can better coordinate our efforts, prevent duplication of work, and help you to craft the change so that it is successfully accepted into the project.

* **Small changes**

    Can be crafted and submitted to the github repository as a pull request.

## Bug triage

Bug triaging is managed via github [issues](https://github.com/leodido/conversio/issues).

You can help report bugs by filing them [here](https://github.com/leodido/conversio/issues).

Before submitting new bugs please verify that similar ones do not exists yet. This will help us to reduce the duplicates and the references between issues.

Is desiderable that you provide reproducible behaviours attaching (failing) tests.

## Testing

The PHPUnit version to be used is the one installed as a dev-dependency via [composer](https://getcomposer.org/):

```bash
$ ./vendor/bin/phpunit
```

## Contributing process

What branch to issue the pull request against?

For **new features**, or fixes that introduce **new elements to the public API** (such as new public methods or properties), issue the pull request against the `develop` branch.

For **hotfixes** against the stable release, issue the pull request against the `master` branch.

1. **Fork** the sphinxsearch [repository](https://github.com/leodido/conversio/fork)

2. **Checkout** the forked repository

3. Retrieve **dependencies** using [composer](https://getcomposer.org/)

4. Create your **local branch**, **commit** your code and **push** your local branch to your github fork

5. Send us a **pull request** as descripted for your changes to be included

Please remember that **any contribution must provide tests** for additional introduced conditions. Accepted coverage for new contributions is 75%. Any contribution not satisfying this requirement won't be merged.

Don't get discouraged!