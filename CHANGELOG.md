# Change log

## 0.4.0: Beings

Unreleased

* Improve error handling
    * Missing plugin type target directory
    * Package source failures
* Package source retry behaviour
* Validate project file before `build` and `package` operations

## 0.3.0: Shuffle

Released 2017-08-12

* New `Git` package repository
* Add missing PHP extension dependencies
    * `curl` for Guzzle HTTP client
    * `dom` for the Monolog bundle
    * `zip` for `ZipArchive` package format

## 0.2.0: Doin' it Right

Released 2016-11-02

* New component build steps support
* New `Directory` package source
* New `Filesystem` package repository
* Internal improvements
    * Refactored installation and packaging operations
    * Update Composer dependencies
    * Rework service definitions
    * Removed PlatformUtil in favour of per-platform classes
    * Clean up temporary directories, like a good citizen

## 0.1.2: packaging improvements

Released 2016-02-24

* New `Directory` package format for pass-through style deployment
* Fixed `ZipArchive` directory separator issues on Windows
* Various improvements to installation and usage documentation

## 0.1.1: Composer clean up

Released 2016-01-28

* Local and global installation with Composer fixed

## 0.1.0: initial release

Released 2016-01-28

* Outlined the basic premise and operation
    * `install` command for installing named plugins from a project file
      into the specified installation, with support for pinning plugin versions
      for reproducible builds
    * `package` command allowing fetching vanilla Moodle source and
      installing plugins named in a project file
* Package repositories
    * `Github` sources plugins from arbitrary GitHub.com repositories
    * `Moodle` sources plugins from the Moodle.org plugins directory
    * `Stash` support for BitBucket.com and on-premist BitBucket Server
      deployments
* Package sources
    * `Git` for checking out a specific reference from a Git repository
    * `Zip` for obtaining `.zip` archives from the Internet and
      extracting the source from within them
* Package formats
    * `WebDeploy` generates Microsoft WebDeploy archives for deployments
      with `msdeploy`
    * `ZipArchive` places all the files within a zip archive
