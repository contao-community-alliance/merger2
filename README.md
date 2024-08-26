[![Build Status](https://github.com/contao-community-alliance/merger2/actions/workflows/diagnostics.yml/badge.svg)](https://github.com/contao-community-alliance/merger2/actions)
[![Latest Version tagged](http://img.shields.io/github/tag/contao-community-alliance/merger2.svg)](https://github.com/contao-community-alliance/merger2/tags)
[![Latest Version on Packagist](http://img.shields.io/packagist/v/contao-community-alliance/merger2.svg)](https://packagist.org/packages/contao-community-alliance/merger2)
[![Installations via composer per month](http://img.shields.io/packagist/dm/contao-community-alliance/merger2.svg)](https://packagist.org/packages/contao-community-alliance/merger2)

Merger²
=======

The Contao Community Alliance merger² extension provides a powerful frontend module to merge various content:

 - Article inheritance
 - Conditional content
 - Powerful condition language

Requirements
------------

Merger² v4 requires at least Contao 4.13 with PHP 7.4 or Contao 5 with PHP 8.0 higher and is prepared for the Contao Managed Edition.


Changelog
---------

See [CHANGELOG](CHANGELOG.md)

Documentation
-------------

The [documentation](https://github.com/contao-community-alliance/merger2/wiki) is managed as a wiki on Github. 
Feel free to contribute. 

Condition Reference
-------------------

Each condition is an expression which may contain different functions. Function may be combined with `&&` or `||` constraints and you can turn back conditions with `!` as NOT.

### articleExists(column: `string` [, includeUnpublished: `bool`])
Test if an article exists in the specific column or section.

 - **column**	Column or section name.
 - **includeUnpublished**	If true also unpublished articles are recognized.


### children(count: `integer` [, includeUnpublished: `bool`]) 
Test if the page have the specific count of children.

 - **count**	Count of children.
 - **includeUnpublished**	Include unpublished pages.


### depth(value: `string`) 
Test the page depth.

 - **value**	Depth with comparing operator, e.g. ">2".


### isMobile([cookieOnly: `bool`]) 
Detect if page is rendered as mobile page.

 - **cookieOnly**	If true only the TL_VIEW cookie is recognized. Otherwise the user agent might active mobile view
   if an mobile layout exist.


### language(language: `string`) 
Test the page language.

 - **language**	Page language


### page(pageId: `string|integer`) 
Test the page id or alias.

 - **pageId**	Page id or alias


### pageInPath(pageId: `string|integer`) 
Test if page id or alias is in path.

 - **pageId**	Page id or alias


### platform (platform: `string`) 
Test the user platform.

 - **platform**	Platform type. Valid values are desktop, tablet, smartphone or mobile.


### root (pageId: `string|integer`)
Test the root page id or alias.

 - **pageId**	Page id or alias
 

Custom functions
----------------

Merger² is prepared for custom functions. Simply implement the `ContaoCommunityAlliance\Merger2\Functions\FunctionInterface`
and provide it as a `cca.merger2.function` tagged service. 
