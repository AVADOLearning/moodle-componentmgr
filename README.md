# Component Manager

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

## Requirements

* PHP >= 5.4
* Moodle >= 2.7

## Installation

We haven't quite settled on a means of distribution yet, but it'll look along
the lines of the following:

1. Clone this directory somewhere on your disk.
2. Ensure our ```/src-componentmgr/bin``` directory is on your ```PATH```.
3. Copy the ```src-local_componentmgr``` directory's contents to
   ```/local/componentmgr``` within your Moodle installation.
4. Execute ```composer install``` within the ```src-componentmgr``` directory
   to obtain the dependencies.

## Usage

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
    * ```"zip"``` sources components via zip archives from remote locations.

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

## To do

* Ditch JSON for storage of package repository cache and use SQLite instead.
  This should offer a sizeable reduction in the amount of time we spend locating
  package metadata.
* Define services in a YAML file rather than a PHP file for clearer expression
  of dependencies. This is starting to become a problem.
* Add init and validate commands to help new users get their project files set
  up correctly.
* Stop using the crappy JSON library and use Symfony's serialisers instead.
  This should simplify error handling considerably.
* Consider adding an alternative syntax to specify arbitrary version formats
  that might be exposed by the underlying component source.
* Check response statuses from HTTP requests and handle failures; we probably
  need a new exception type for transient failures.
* Handle cases where packages don't exist within repositories.
* Use the Symfony process builder to ensure we correctly sanitise shell
  commands.
* Clean up temporary directories used during installation operations.

## Troubleshooting

* ```"cURL error 60: SSL certificate problem: unable to get local issuer
  certificate"```
  Ensure that ```curl.cainfo``` in ```php.ini``` is set to a valid certificate
  bundle. A certificate bundle is provided for Windows users.
