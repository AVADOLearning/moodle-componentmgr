# Change log

## 0.2.0: Doin' it Right

Unreleased

* Refactored installation and packaging operations
* New `Directory` package source
* New `Filesystem` package repository

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
