# Component Manager

[![Code Climate](https://img.shields.io/codeclimate/github/LukeCarrier/moodle-componentmgr.svg?style=flat-square)]()
[![Scrutinizer](https://img.shields.io/scrutinizer/g/LukeCarrier/moodle-componentmgr.svg?style=flat-square)](https://scrutinizer-ci.com/g/LukeCarrier/moodle-componentmgr/)
[![Packagist](https://img.shields.io/packagist/v/lukecarrier/moodle-componentmgr.svg?style=flat-square)](https://packagist.org/packages/lukecarrier/moodle-componentmgr)

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
manifest file into the top of your Moodle installation, execute one command and
watch the deployment happen.

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

## Requirements

* PHP >= 5.5
* Moodle >= 2.7

## Installation

Component Manager can be installed in various different ways, each appropriate
for different use cases.

### Globally, via Composer (recommended)

In this configuration, Component Manager is accessible on the shell (via your
preferred ```$SHELL``` or Command Prompt), and the same installation is used
across all of your projects. This approach is recommended for most users.

1. Install PHP for your platform.
2. Install Composer as per their
   [Getting Started](https://getcomposer.org/doc/00-intro.md) guide. We assume
   that you can execute ```composer``` on your shell.
3. Ensure Composer's global ```vendor/bin``` directory is on your ```PATH```:
  * On Linux/Mac, it's probably ```$HOME/.composer/vendor/bin```
  * On Windows, this is usually ```%APPDATA%\Roaming\Composer\vendor\bin```
4. Install Component Manager globally with
   ```composer global require lukecarrier/moodle-componentmgr```

### Locally, via Composer

In this configuration, Component Manager isn't accessible globally from the
shell, so ```package``` operations can only be performed by manually adding the
```bin``` directory to your ```PATH``` or specifying the full path to the
```componentmgr``` executable.

    $ composer require lukecarrier/moodle-componentmgr

### Manually

Component Manager can also be run in-place. This is recommended for performing
development within Component Manager itself.

1. Clone this repository somewhere on your disk.
2. Ensure our ```bin``` directory is on your system ```PATH```.
3. Execute ```composer install``` within the repository to obtain the
   dependencies.

## ```install``` usage

In this mode, Component Manager is executed from the root directory of a Moodle
site. It reads the project and project lock files from the present working
directory, then deploys the specified components from the specified package
repositories. This mode is designed for use in development environments.

Create a ```componentmgr.json``` file in the root of your Moodle project. This
file, referred to as the project file or manifest, contains a summary of all of
the plugins required for installation of your platform and associated versions.

In order for Component Manager to source your plugins, you'll need to
explicitly specify which locations to treat as package repositories. These are
declared in the ```"packageRepositories"``` section of your project file,
indexed by an alias you'll use to refer to them from component entries later. At
a minimum, they'll consist of a ```"type"```, but additional options might be
required for other implementations.

To use the [Moodle.org/plugins repository](https://moodle.org/plugins), you'll
need the following stanza in your project file:

    "packageRepositories":
    {
        "moodle":
        {
            "type": "Moodle"
        }
    }

Other package repositories are available, allowing deployment from corporate
version control and distribution systems. At the moment:
* ```"Github"``` allows Component Manager to query GitHub.com repositories,
  specified with the ```"repository"``` property of each component.
* ```"Moodle"``` allows access to the
  [Moodle.org/plugins](https://moodle.org/plugins/) repository, versioning
  plugins by either their plugin version (```YYYYMMDDXX```) or release
  name.
* ```"Stash"``` allows access to individual projects within a Stash
  deployment. Project names should match component names and components are
  versioned via
  [Git references](https://git-scm.com/book/en/v2/Git-Internals-Git-References).

You're now ready to start declaring components. Components are declared in the
```"components"``` section of your project file, indexed by their
[frankenstyle](https://docs.moodle.org/dev/Frankenstyle) component names. Each
component object has three keys:

* The ```"version"``` key specifies either a plugin version or release name,
  both consistent with Moodle's
  [```version.php```](https://docs.moodle.org/dev/version.php) files.
* The ```"packageRepository"``` key specifies the package repository, declared
  in the ```"packageRepositories"``` section of the project file, which should
  be used as the source of data for this component.
* Finally, the ```"packageSource"``` key specifies which type of component
  source to obtain. At the moment, the following sources are available:
    * ```"Git"``` sources components from the specified Git reference.
    * ```"Zip"``` sources components via zip archives from remote locations.

An example to install
[version 0.4.0](https://moodle.org/plugins/pluginversion.php?id=7567) of the
[```local_cpd```](https://moodle.org/plugins/view/local_cpd) plugin from the
zipped distributions on Moodle.org would look like the following:

    "components":
    {
        "local_cpd":
        {
            "version": "0.4.0",
            "packageRepository": "moodle",
            "packageSource": "Zip"
        }
    }

Bringing this altogether gives us a ```componentmgr.json``` file that looks
something like the following:

    {
        "components":
        {
            "local_cpd":
            {
                "version": "0.4.0",
                "packageRepository": "moodle",
                "packageSource": "Zip"
            }
        },

        "packageRepositories":
        {
            "moodle":
            {
                "type": "Moodle"
            }
        }
    }

We're now ready to install our plugins. First, we'll get Component Manager to
fetch metadata about all of the available components from our configured package
repositories. It'll cache this data to save traffic and time later:

    $ componentmgr refresh

With this data now ready, we can fetch our plugins by switching to the directory
containing our Moodle installation and issuing the install command:

    $ cd ~/Sites/LukeCarrier-Moodle
    $ componentmgr install

Now we can choose to perform the plugins' database upgrades via either the
Moodle Notifications page under Site administration, or the handy CLI script:

    $ php admin/cli/upgrade.php

## ```package``` usage

In this mode, Component Manager can be executed from any arbitrary location, and
it generates a package containing an entire Moodle site. The version of Moodle
and related components to deploy is determined from a property in the project
file. This mode is designed for use in CI and production environments.

To use Component Manager to package Moodle releases, you'll first need to
determine an appropriate expression for your desired Moodle version. You're
advised to use a branch here, as Component Manager will pin the exact Moodle
version in the project lock file during installation.

You'll then need to choose an installation source:
* ```"zip"``` is the recommended option. Component Manager will obtain the
  specified release archive from ```download.moodle.org```. These releases
  will have passed Moodle HQ's testing process.
* ```"git"``` should be used for more advanced configurations.

The support for the different version formats across the different
installation sources is as follows:

| Version format      | Behaviour                                     | Git | Zip |
| ------------------- | --------------------------------------------- | --- | --- |
| ```2.7```           | Latest available release in branch            | ✔  | ✔   |
| ```2.7+```          | Latest available release in branch with fixes | ✔  | ✔   |
| ```2.7.10```        | Specific release version                      | ✔  | ✔   |
| ```2.7.10+```       | Specific release version with fixes           | ✔  | ✘   |
| ```2014051210```    | Specific release version                      | ✔  | ✘   |
| ```2014051210.05``` | Specific release version with fixes           | ✔  | ✘   |

Bringing this together, you should place the following stanza into your project
file:

    "moodle": {
        "version": "2.7+",
        "source": "zip"
    }

Packages can be generated in the following formats:

* ```"Directory"``` simply copies the packaged files to the specified directory.
* ```"WebDeploy"``` packages are generated using Microsoft's ```msdeploy```
  utility and are well suited to deployment on Windows.
* ```"ZipArchive"``` packages are suited to deployment everywhere.

For example, to generate a generic zip ball containing your Moodle site, you
can execute the following command:

    $ componentmgr package --package-format=ZipArchive \
                           --package-destination=/tmp/moodle.zip \
                           --project-file=moodle.org.json

## Troubleshooting

* ```"cURL error 60: SSL certificate problem: unable to get local issuer
  certificate"```
  Ensure that ```curl.cainfo``` in ```php.ini``` is set to a valid certificate
  bundle. A certificate bundle is provided for Windows users.
