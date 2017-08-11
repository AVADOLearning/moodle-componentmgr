# Component Manager

[![Travis](https://img.shields.io/travis/LukeCarrier/moodle-componentmgr.svg?style=flat-square)](https://travis-ci.org/LukeCarrier/moodle-componentmgr)
[![Code Climate](https://img.shields.io/codeclimate/github/LukeCarrier/moodle-componentmgr.svg?style=flat-square)](https://codeclimate.com/github/LukeCarrier/moodle-componentmgr)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/LukeCarrier/moodle-componentmgr.svg?style=flat-square)](https://scrutinizer-ci.com/g/LukeCarrier/moodle-componentmgr/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/LukeCarrier/moodle-componentmgr.svg?style=flat-square)](https://scrutinizer-ci.com/g/LukeCarrier/moodle-componentmgr/)
[![Packagist](https://img.shields.io/packagist/v/lukecarrier/moodle-componentmgr.svg?style=flat-square)](https://packagist.org/packages/lukecarrier/moodle-componentmgr)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bhttps%3A%2F%2Fgithub.com%2FLukeCarrier%2Fmoodle-componentmgr.svg?type=shield)](https://app.fossa.io/projects/git%2Bhttps%3A%2F%2Fgithub.com%2FLukeCarrier%2Fmoodle-componentmgr?ref=badge_shield)

Component Manager is a tool to assist with the packaging and management
of Moodle-based learning environments.

* * *

## Moodle is great.

Moodle is the world's most popular learning management system. It's open source,
has a huge developer community and an enormous user base. Its modular design
allows developers to seamlessly extend the platform with new functionality and
integrate it with other business systems.

### But it's hard to manage.

Moodle's modular design empowers developers to create highly bespoke online
learning platforms, but it can become difficult to manage large scale
deployments of platforms which are comprised of countless separate modules.

### Component Manager aims to fix this problem.

By defining your requirements in one single file, you remove a great deal of the
complexity involved in managing your deployment environments. Just drop a single
manifest file into the top of your Moodle installation, and launch a single
command to install all of your desired components.

This has several key advantages over manual deployments:

* If you're deploying to a clustered environment, you can guarantee that each
  individual application server is running the same code as its neighbours.
* When managing multiple environments, e.g. test, UAT and production, you can be
  sure that the code you're deploying to UAT is the same code that passed your
  automated tests. Likewise, production deployments are guaranteed to contain
  only the code that passed your UAT testing.

### This is not a new concept.

Linux distributions have been managing system libraries, applications and
configuration files within package managers for years. Perl has had CPAN for a
very long time. Magento developers even use a tool called Modman to manage
Magento modules (and Magento's module system is a great deal more difficult than
Moodle's!).

## Key concepts

* _Package repositories_ contain metadata about components. This metadata
  describes the components themselves and contains information about available
  versions of the plugin as well as sources to obtain them.
* _Component sources_ describe possible locations to obtain components in either
  source or distribution form, and are assembled based upon data provided by
  package repositories.
* _Package sources_ define strategies that can be used to obtain components from
  specific types of sources (e.g. version control systems, archive files
  downloaded from repositories).
* _Version control_ implementations allow us to download and checkout specific
  versions of components from a range of different sources.

## License

Component Manager is released under the terms of the GPL v3. This is the same
license as the core Moodle distribution.

[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bhttps%3A%2F%2Fgithub.com%2FLukeCarrier%2Fmoodle-componentmgr.svg?type=large)](https://app.fossa.io/projects/git%2Bhttps%3A%2F%2Fgithub.com%2FLukeCarrier%2Fmoodle-componentmgr?ref=badge_large)

## Requirements

* PHP >= 5.5
* Moodle >= 2.7

## Installation

Component Manager can be installed in various different ways, each appropriate
for different use cases.

### Globally, via CGR (recommended)

[CGR](https://github.com/consolidation/cgr) provides a safe alternative to
globally requiring packages by sandboxing individual sandboxing individual
packages and their dependencies. This approach is recommended for most users.

1. Globally require CGR with `composer global require consolidation/cgr`.
2. Install Component Manager with `cgr lukecarrier/moodle-componentmgr`.

### Globally, via Composer

In this configuration, Component Manager is accessible on the shell (via your
preferred `$SHELL` or Command Prompt), and the same installation is used across
all of your projects. This approach is not recommended for Component Manager, as
globally requiring packages with dependencies is likely to lead to dependency
problems.

1. Install PHP for your platform.
2. Install Composer as per their
   [Getting Started](https://getcomposer.org/doc/00-intro.md) guide. We assume
   that you can launch `composer` on your shell.
3. Ensure Composer's global `vendor/bin` directory is on your `PATH`:
  * On Linux/Mac, it's probably `$HOME/.composer/vendor/bin`
  * On Windows, this is usually `%APPDATA%\Roaming\Composer\vendor\bin`
4. Install Component Manager globally with
   `composer global require lukecarrier/moodle-componentmgr`

### Locally, via Composer

In this configuration, Component Manager isn't accessible globally from the
shell, so `package` operations can only be performed by manually adding the
`bin` directory to your `PATH` or specifying the full path to the
`componentmgr` executable.

```
$ composer require lukecarrier/moodle-componentmgr
```

### Manually (ideal for development)

Component Manager can also be run in-place. This is recommended for performing
development within Component Manager itself.

1. Clone this repository somewhere on your disk.
2. Ensure our `bin` directory is on your system `PATH`.
3. Run `composer install` within the repository to obtain the dependencies.

## `install` usage

In this mode, Component Manager is launched from the root directory of a Moodle
site. It reads the project and project lock files from the present working
directory, then deploys the specified components from the specified package
repositories. This mode is designed for use in development environments.

Create a `componentmgr.json` file in the root of your Moodle project. This
file, referred to as the project file or manifest, contains a summary of all of
the plugins required for installation of your platform and associated versions.

In order for Component Manager to source your plugins, you'll need to
explicitly specify which locations to treat as package repositories. These are
declared in the `"packageRepositories"` section of your project file, indexed
by an alias you'll use to refer to them from component entries later. At a
minimum, they'll consist of a `"type"`, but additional options might be
required for other implementations.

To use the [Moodle.org/plugins repository](https://moodle.org/plugins), you'll
need the following stanza in your project file:

```json
{
    "packageRepositories":
    {
        "moodle":
        {
            "type": "Moodle"
        }
    }
}
```

Other package repositories are available, allowing deployment from corporate
version control and distribution systems. At the moment:
* `"Filesystem"` can be used to lookup components on the local disk.
* `"Github"` allows Component Manager to query GitHub.com repositories,
  specified with the `"repository"` property of each component.
* `"Moodle"` allows access to the
  [Moodle.org/plugins](https://moodle.org/plugins/) repository, versioning
  plugins by either their plugin version (`YYYYMMDDXX`) or release name.
* `"Stash"` allows access to individual projects within a Bitbucket Server
  (formerly Stash) deployment. Project names should match component names and
  components are versioned via
  [Git references](https://git-scm.com/book/en/v2/Git-Internals-Git-References).

You're now ready to start declaring components. Components are declared in the
`"components"` section of your project file, indexed by their
[frankenstyle](https://docs.moodle.org/dev/Frankenstyle) component names. Each
component object has three keys:

* The `"version"` key specifies either a plugin version or release name, both
  consistent with Moodle's
  [`version.php`](https://docs.moodle.org/dev/version.php) files.
* The `"packageRepository"` key specifies the package repository, declared in
  the `"packageRepositories"` section of the project file, which should be used
  used as the source of data for this component.
* Finally, the `"packageSource"` key specifies which type of component source
  to obtain. At the moment, the following sources are available:
    * `"Directory"` sources components from the specified filesystem location.
    * `"Git"` sources components from the specified Git reference.
    * `"Zip"` sources components via zip archives from remote locations.

An example to install
[version 0.4.0](https://moodle.org/plugins/pluginversion.php?id=7567) of the
[`local_cpd`](https://moodle.org/plugins/view/local_cpd) plugin from the
zipped distributions on Moodle.org would look like the following:

```json
{
    "components": {
        "local_cpd": {
            "version": "0.4.0",
            "packageRepository": "moodle",
            "packageSource": "Zip"
        }
    }
}
```

Bringing this altogether gives us a `componentmgr.json` file that looks
something like the following:

```json
{
    "components": {
        "local_cpd": {
            "version": "0.4.0",
            "packageRepository": "moodle",
            "packageSource": "Zip"
        }
    },

    "packageRepositories": {
        "moodle": {
            "type": "Moodle"
        }
    }
}
```

We're now ready to install our plugins. First, we'll get Component Manager to
fetch metadata about all of the available components from our configured package
repositories. It'll cache this data to save traffic and time later:

```
$ componentmgr refresh
```

With this data now ready, we can fetch our plugins by switching to the directory
containing our Moodle installation and issuing the install command:

```
$ cd ~/Sites/LukeCarrier-Moodle
$ componentmgr install
```

Now we can choose to perform the plugins' database upgrades via either the
Moodle Notifications page under Site administration, or the handy CLI script:

```
$ php admin/cli/upgrade.php
```

## `package` usage

In this mode, Component Manager can be launched from any arbitrary location, and
it generates a package containing an entire Moodle site. The version of Moodle
and related components to deploy is determined from a property in the project
file. This mode is designed for use in CI and production environments.

To use Component Manager to package Moodle releases, you'll first need to
determine an appropriate expression for your desired Moodle version. You're
advised to use a branch here, as Component Manager will pin the exact Moodle
version in the project lock file during installation.

You'll then need to choose an installation source:
* `"zip"` is the recommended option. Component Manager will obtain the
  specified release archive from `download.moodle.org`. These releases
  will have passed Moodle HQ's testing process.
* `"git"` should be used for more advanced configurations.

The support for the different version formats across the different
installation sources is as follows:

| Version format  | Behaviour                                     | Git | Zip |
| --------------- | --------------------------------------------- | --- | --- |
| `2.7`           | Latest available release in branch            | ✔  | ✔   |
| `2.7+`          | Latest available release in branch with fixes | ✔  | ✔   |
| `2.7.10`        | Specific release version                      | ✔  | ✔   |
| `2.7.10+`       | Specific release version with fixes           | ✔  | ✘   |
| `2014051210`    | Specific release version                      | ✔  | ✘   |
| `2014051210.05` | Specific release version with fixes           | ✔  | ✘   |

Bringing this together, you should place the following stanza into your project
file:

```json
{
    "moodle": {
        "version": "2.7+",
        "source": "zip"
    }
}
```

Packages can be generated in the following formats:

* `"Directory"` simply copies the packaged files to the specified directory.
* `"WebDeploy"` packages are generated using Microsoft's `msdeploy`
  utility and are well suited to deployment on Windows.
* `"ZipArchive"` packages are suited to deployment everywhere.

For example, to generate a generic zip ball containing your Moodle site, you
can run the following command:

```
$ componentmgr package --package-format=ZipArchive \
                       --package-destination=/tmp/moodle.zip \
                       --project-file=moodle.org.json
```

## Component lifecycle

Component Manager allows components to run scripts at specific stages of the
installation and packaging processes. These are:

* `build` -- fired once all components have been installed to the site, intended
  for use to install client-side dependencies via package managers and perform
  any building/minification of assets.

To take advantage of this feature, create a `componentmgr.comnponent.json` in
the top level of your repository with the following content:

```json
{
    "scripts": {
        "build": "your command (e.g. npm install && npm run gulp)"
    }
}
```

You can verify that your build steps function as expected without having to
perform an installation or package operation with the `run-script` command:

```
$ cd local/componentmgrtest/
$ componentmgr run-script build
```

## Development

Milestones are prepared from GitHub issues and maintained using
[HuBoard](https://huboard.com/LukeCarrier/moodle-componentmgr).

To get a change into Component Manager:

1. Fork a new branch off of `develop` if it's a feature for the next major
   release, or off `master` if it's a bug fix.
2. Make your changes, and commit them. Try to be mindful of commit messages,
   and don't be afraid of spreading particularly large or complex changes
   across commits.
3. Submit a pull request.

## Testing

Component Manager is both unit and integration tested.

### Integration tests

Integration tests are written in [ServerSpec](http://serverspec.org/), with
[Test Kitchen](http://kitchen.ci/) configured to run them in a clean environment
using the [Docker](https://www.docker.com/) driver.

To get started, install Kitchen and the necessary dependencies with
[Bundler](http://bundler.io/):

```
$ bundle install
```

Then run the tests:

```
$ bundle exec kitchen test
```

### Unit tests

Unit tests are written with [PHPUnit](https://phpunit.de/). Ensure that
Composer development dependencies are installed, then run the tests:

```
$ vendor/bin/phpunit
```

This will generate various coverage and pass/fail reports in the `tmp`
directory.

Note that a portion of the tests for the platform support components will fail
on platforms they're not designed for. To exclude them, use PHPUnit's
`--exclude-group` switch on the following groups as appropriate:

* `platform-linux`
* `platform-windows`

## Troubleshooting

* `"cURL error 60: SSL certificate problem: unable to get local issuer
  certificate"`
  Ensure that `curl.cainfo` in `php.ini` is set to a valid certificate
  bundle. A certificate bundle is provided for Windows users.
