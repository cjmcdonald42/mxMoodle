##### This file needs to be reviewed.

# Middlesex Moodle Local Plugin Structure

Middlesex's custom local plugins are relatively complex, so they require relatively specific structural guidelines to keep the codebase consistent, predictable, and easy to maintain. The structure of our plugins falls into two main categories:

1. Structure required by Moodle's core functionality and coding standards
    - Required and optional file layout (see our [Creating a New Plugin documentation](/docs/CREATING_A_NEW_PLUGIN.md))
    - PHP namespaces and class file directory structure (see [Namespaces and Automatic Class Loading](#namespaces-and-automatic-class-loading) below)
    - Blocks (see [Blocks](#blocks) below)
2. Structure that we have chosen to impose through the subpackages abstraction (see [Subpackage Abstraction](#subpackages-abstraction) below)

___

## Subpackages Abstraction

In order to simplify page setup and index page generation, we have created a subpackage abstraction. The term 'package' refers to each individual plugin (e.g. `local_mxschool` or `local_signout`). The term 'subpackage' refers to a specific subsystem within a package (e.g. `checkin` or `vacation_travel`). It is important to note that not all code needs to be in a subpackage. For example, the `local_peertutoring` package has no subpackage; also, all of the files required by Moodle have no subpackage. Pages which are not within a subpackage are sometimes referred to as being in the _null_ or _root_ subpackage.

###### NOTE: Though we call all our the local plugins besides `local_mxschool` 'subplugins,' we are not actually using Moodle's subplugin system. We made this decision because it seems that the subplugin system is not targeted at the type of interactions that our plugins have, but we may want to reconsider this decision in the future.

### Initializing the Subpackages Table

When you install your plugin or add pages via an upgrade, you need to be sure that those pages are reflected in the `local_mxschool_subpackages` database table. This table stores a record for each subpackage that indicates the `package` (without the `local_` prefix), `subpacakage`, and an json-encoded array of `pages`. The internal name that you use for each of your pages (used in prefix for language strings) must match the name of the page's file. For example the `student_report` page is a part of the `user_management` subpackage of the `mxschool` package, so there is a file at `local/mxschool/user_management/student_report.php` which will be loaded when someone follows the generated link in any index page. All you need to do to make the system recognize your plugin's pages is add a record to the database like this:

```PHP
$DB->insert_record('local_mxschool_subpackage', (object) array(
    'package' => 'PACKAGE',
    'subpackage' => 'SUBPACKAGE',
    'pages' => json_encode(array(
        'PAGE1', 'PAGE2', // ETC.
    ))
));
```

If the file is in the _null_ subpackage, simply omit the `subpackage` key and put the referenced file in the root package directory.

###### NOTE: Pages whose purpose is to edit a record and can only be accessed from a report page as well as index pages should _not_ be included in the subpackage record. These pages use a separate page-setup function, as explained in our [File Structure documentation](/docs/GENERAL_FILE_STRUCTURE.md).

### Generating Index Pages

One of the first files that every package and subpackage should have is an `index.php` file. Having these files in every directory which has pages which are accessible to the browser is a best practice and these should be the first files that you add to a new subpackage. The purpose of this file is to give you a way to navigate between all of the pages without needing to enter URLs directly into the browser's search bar. The users of your package will ultimately navigate to the pages they are allowed to access via a block, but blocks can take more time to develop because UX and presentation is a critical consideration, so index files are much easier to use during development.

Once you have your subpackages in the database, generating index pages is incredibly easy. The first step is to create a link to each index page in the admin settings tree. See [this file](/local/signout/settings.php) as an example. Note that you will need to add a language string to be used in the list of subpackage index pages as well as one to be used as the title of the index page itself. If all you need is an index page for a single subpackage, simply copy [this template](/docs/templates/index.php) to your subpackage directory and replace the placeholders. If you want to create an index of multiple subpackages in one page, see [this file](/local/signout/index.php) as an example. Note that as soon as you add your subpackage to the database, it's contents will automatically be included in the root Middlesex index, so you don't need to do anythin to add it there.

___

## Namespaces and Automatic Class Loading

Moodle provides an automatic classloader which finds all PHP classes defined in the correct files and namespaces and loads them for us so that we do not have to mess around with numerous `require_once` statements in every file. In particular all files which define a PHP class must have the same name as the class they define (only one class per file) and must be somewhere in the `classes/` directory of the plugin. You can read Moodle's [Automatic class loading documentation](https://docs.moodle.org/dev/Automatic_class_loading), but this section will explain the specific choices we have made regard the ACL for our plugins in greater detail.

The `classes/` directory also needs to have a specific structure. We have chosen to reserve the root `classes/` directory for abstract classes that are extended by subpackage-specific classes within the plugin and subplugins. Certain Moodle subsystems such as the [task](https://docs.moodle.org/dev/Task_API), [event](https://docs.moodle.org/dev/Event_2), and [output](https://docs.moodle.org/dev/Output_API) APIs look for classes under the respective directories `classes/task/`, `classes/event/`, `classes/output/`, etc. We don't have specific documentation for these APIs, but we do have templates for each in our [tempates directory](/docs/templates), and Moodle's documentation is generally sufficient. All other classes should go in the `classes/local/` directory or a subdirectory therein. The root level of the `classes/local/` directory should correspond to pages without a subpackage, and all other class files should be in a subdirectory with the same name as that file's subpackage. You can use [`local_mxschool`'s `classes/` directory](/local/mxschool/classes/) as an example.

Lastly, all classes must be namespaced according to the directory they are in. For example, the file `local/mxschool/classes/output/renderer.php` would include the following line:

```PHP
namespace local_mxschool\output;
```

and would define a class called `renderer`.

___

## Blocks

While not technically part of a local plugin, blocks serve as the primary way for non-admin users to access the content that our plugins add to Moodle. Many blocks simply take the form of an index of pages, perhaps with some explanatory text, but others are more complex. Blocks don't require many files and are relatively straightforward to put together. You should definitely read Moodle's [Blocks documenation](https://docs.moodle.org/dev/Blocks) to get a sense of how everything works. [This template](/docs/templates/mxschool_BLOCK) is an example of how a simple block plugin should be structured. If you need to create a new block, you should copy the entire template directory into the `blocks/` directory of your repository and replace all of the placeholders.
