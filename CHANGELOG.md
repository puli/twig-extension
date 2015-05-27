Changelog
=========

* 1.0.0-next (@release_date@)

 * upgraded to webmozart/path-util 2.0
 * replaced puli/asset-plugin by puli/url-generator
 * renamed `asset_url()` to `resource_url()`
 * fixed: relative paths in `resource_url()` are always converted into absolute
   paths
 * removed `PathResolver` interface

* 1.0.0-beta3 (2015-04-13)

 * Added support for relative paths in `import` statements
 * Added support for relative paths in `use` statements
 * Calls to `resource_url()` are now optimized away during compilation
 * Renamed `resource_url()` to `asset_url()`
 * Moved code to the `Puli\TwigExtension` namespace

* 1.0.0-beta2 (2015-03-19)

 * Added `resource_url()` function

* 1.0.0-beta (2015-01-12)

 * Renamed `PathResolverInterface` to `PathResolver`
 * Fixed rendering of empty templates. Closes puli/puli#28

* 1.0.0-alpha1 (2014-12-03)

 * first alpha release
