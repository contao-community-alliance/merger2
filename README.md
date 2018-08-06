Merger²
=======

[![Build Status](https://travis-ci.org/contao-community-alliance/merger2.png?branch=master)](https://travis-ci.org/contao-community-alliance/merger2)

The Contao Community Alliance merger² extension provides a powerful frontend module to merge various content:

 - Article inheritance
 - Conditional content
 - Powerful condition language

Requirements
------------

Merger² v4 requires at least Contao 4.4 and is prepared for the Contao Managed Edition.

Condition Reference
-------------------

Each condition is an expression which may contain different functions. Function may be combined with `&&` or `||` constraints.

### rticleExists(column: `string` [, includeUnpublished: `bool`])
Test if an article exists in the column.

 - **column**	Column or section name.
 - **includeUnpublished**	If true also unpublished articles are recognized.


### children(count: `integer` [, includeUnpublished: `bool`]) 
Test if the page have the specific count of children.

 - **count**	Count of children.
 - **includeUnpublished**	Include unpublished pages.


### depth(value: `string`) 
Test the page depth.

 - **value**	Depth with comparing operator, e.g. ">2".


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
Test the user platform

 - **platform**	Platform type. Valid values are desktop, tablet,smartphone or mobile.

### root (pageId: `string|integer`)
Test the root page id or alias.

 - **pageId**	Page id or alias
 

Custom functions
----------------

Merger² is prepared for custom functions. Simply implement the `ContaoCommunityAlliance\Merger2\Functions\FunctionInterface`
and provide it as a `cca.merger2.function` tagged service. 
