# Middlesex Moodle Plugins Coding Style

Moodle has a large number of coding standards regarding all aspects of plugin development. These standards are listed on [this MoodleDocs page](https://docs.moodle.org/dev/Coding).

___

## PHP

Before you write anything for our plugins, you should read through Moodle's [coding style page](https://docs.moodle.org/dev/Coding_style) in its entirety. This document contains important information about how you should structure and format your PHP code.

If you have already set up Atom by following our [Getting Started Guide](/docs/GETTING_STARTED.md#setting-up-atom), your editor will be ready to help you comply with the indentation and max-line-length rules. PHPCS will also help catch issues with variable naming and other specific Moodle conventions.

Because you code you are writing is not intended to be a part of Moodle's core codebase, it is not essential that you always follow these standards exactly; however, it is definitely a best practice to follow Moodle's conventions whenever possible.

#### Notes

There are a few places where we have deliberately chosen to deviate from Moodle's standards or created more strict standards to improve the readability, concision, and/or consistency of our codebase in some way. These decisions could be revisited in the future, but a change would require somewhat substantial refactoring (or at least a through find-and-replace) to the API Layer.

- We have not prefixed library function names with the plugin's Frankenstyle prefix as this tended to make the functions feel needlessly verbose. See our [local library file](/local/mxschool/locallib.php) for examples. The argument to change this would be to avoid potential conflicts with functions added by future plugins. See [#5](https://github.com/mxschool/MXMoodle/issues/5).

- We often choose to capitalize all words in language strings unless the string is a complete sentence and clearly should be in sentence case. This is mostly a design decision where we treat most language strings as title or headers of some sort. A notable exception is that all language strings which are integrated directly within Moodle's admin settings should conform to Moodle's standard capitalization practices.

- We initialize arrays with the `array()` syntax rather than the `[]` literal in all cases. This is slightly more verbose, but easier to read in certain situations.

- We use curly braces around variables when substituting them directly into strings in all cases to improve the clarity of the code.

- We generally try to avoid writing out complete sql queries whenever one of functions from Moodle's [Data Manipulation API](https://docs.moodle.org/dev/Data_manipulation_API) can generate the query for us. However, if the query is sufficiently complex (e.g. pulling data from multiple tables, querying many fields, or checking against complex where clauses), it often more readable to write out the entire query. If you do need to write out a query, you should place each keyword (SELECT, FROM, WHERE, ORDER BY etc.) on a new line and line them up vertically. If you need to wrap any of these portions of the query, you should line up the field, or connecting keyword (e.g. LEFT JOIN, AND, etc.) vertically. See the database query functions in our [local library file](/local/mxschool/locallib.php) for many specific examples.

- We don't usually specify parameter types or return types in function headers because this was not supported on previous versions of PHP. Now that the current version of Moodle requires PHP 7.1+, this is something that we may want to add in the future. See [#7](https://github.com/mxschool/MXMoodle/issues/7).

- For more on how we have chosen to define subpackages and use namespaces, please refer to the [Plugin Structure Documentation](/docs/PLUGIN_STRUCTURE.md)

___

## JavaScript

The ideas of Moodle's JS coding style are very similar to the PHP coding style. However, if you are planning to write any JavaScript for the project, you should read through [this page](https://docs.moodle.org/dev/Javascript/Coding_Style) before proceeding.

___

## CSS

In general, you should try to write as little custom CSS as possible and rely primarily on the [Bootstrap](https://getbootstrap.com/docs/4.0/getting-started/introduction/) classes which are already automatically included on every page. If you do need to write custom CSS for any reason, you need to follow Moodle's [CSS Style Guidelines](https://docs.moodle.org/dev/CSS_Coding_Style).

___

## Testing

We currently do not have any unit testing or automated continuous integration set up for our custom code. This is somethings that we should probably add in the future, but for now we rely on extensive manual testing on development and mock-deployment servers before any code is used on our live server.
