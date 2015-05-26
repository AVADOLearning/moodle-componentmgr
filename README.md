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

Moodle's modular design not a bad thing, but it can be difficult to manage large
scale deployments when your platform is comprised of countless separate modules.

### Component Manager aims to fix this problem.

By defining your requirements in one single file, you remove a great deal of the
complexity involved in managing your deployment environments. Just drop a single
manifest file into the top of your Moodle installation, execute one command and
watch the deployment happen.

### This is not a new concept.

Linux distributions have been managing system libraries, applications and configuration files within package managers for years. Perl has had CPAN for a
very long time. Magento developers even use a tool called Modman to manage
Magento modules (and Magento's module system is a great deal more difficultEnt than
Moodle's!).

## Installation

We haven't figured out distribution yet -- just drop this directory somewhere in
your home directory and add its ```bin``` directory to your ```PATH```.

## Troubleshooting

* "cURL error 60: SSL certificate problem: unable to get local issuer certificate"
  Ensure that ```curl.cainfo``` in ```php.ini``` is set to a valid certificate bundle.
