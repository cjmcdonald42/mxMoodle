# Steps for Creating a New Local Plugin

Local plugins enable us to add arbitrary pages and database tables to Moodle as well as customize other existing plugins. Unfortunately, the type of additions we are making are definitely within the realm of "non-standard" from the Moodle community's perspective, so the relevant documentation is severely lacking. Moodle's current documentation for local plugins is short and very out-of-date (written for Moodle 2.0). I would _not_ recommend that you read Moodle's [local plugins documentation](https://docs.moodle.org/dev/Local_plugins) because it might be rather misleading. You should look at [this page](https://docs.moodle.org/dev/Plugin_files), though, which addresses the plugin files that are included in all plugin types and is more up-to-date. In this document, I will try to provide a more targeted and accurate overview that combines the information of these pages as well as details specific to our plugins.

___

## Naming Convention

The first thing you need to do when creating your plugin is to choose a name. There are some rules, but you can pretty much choose anything you want. In general, it will be better to have a shorter name, because you will need to be typing it a lot, but make it long enough that it is clear. If you need to have multiple words in your name, delimit them with an underscore, rather than a hyphen as the latter is not a legal character. Once you have a name, for example `plugin_name`, create the directory `local/plugin_name/` in the root of your repository. This is where all of the files for your local plugin will go. Note that if you add blocks as a means for people to access the pages added by your plugin, they will be _separate_ plugins and each go in the `blocks/` directory in the root of your repository. See our [Plugin Structure documentation](/docs/PLUGIN_STRUCTURE.md#blocks) for more information about blocks.

___

## Necessary Files

There are a number of files and directories that should be added immediately when you create your plugin, even if you don't have anything to add to them right away.

###### NOTE: All of these internal Moodle files must begin with a standard licensing comment, a header comment, and the `MOODLE_INTERNAL` check. See our [File Structure documentation](/docs/COMMON_FILE_STRUCTURE.md) for more information.

### Version File

The first file you need to add when creating any Moodle Plugin is a version file. This file must have the name `version.php` and be in the root directory of your plugin. Moodle has a useful page that outlines all of the options [here](https://docs.moodle.org/dev/version.php). You can also use [this file](/local/signout/version.php) as an example. Be sure to include `local_mxschool` as a dependency — the version number for `v3.1` is `2019081400`. Whenever you change any of the files referenced in this document, you will need to increase the version number of the plugin and navigate to `Site administration` > `Notifications` where you will be prompted to upgrade the plugins. This will Moodle to regenerate certain files and reset caches so that your changes are actually detected.

### Language File

You also need to create an English language file for your plugin. _All_ strings which are displayed to the user will be stored here for localization purposes. We do not expect that our code will ever need to be translated; however, this is still entirely necessary. The file must be in the directory `lang/en/` from the root directory of your plugin, and the filename must be the same as the component name in your version file. For now, you must provide the external name of your plugin by setting `$string['pluginname']` to the appropriate string value. You should definitely read Moodle's [String API documentation](https://docs.moodle.org/dev/String_API) for more information.

### `db/` Directory

This directory holds a number of files which Moodle references automatically whenever you install or update your plugin.

##### `install.xml`

This file specifies any database tables that you add. It is used whenever someone installs your plugin to a new server. When you are adding new tables or changing existing ones, you need to do so through the XMLDB Editor which can be found on your server at `Site administration` > `Development` > `XMLDB editor`, and the file will be generated automatically. I recommend adding this page to your admin bookmarks because you will be using it a lot. Moodle has documentation for the editor [here](https://docs.moodle.org/dev/XMLDB_defining_an_XML_structure#The_XMLDB_editor). There is also a large amount of documentation discussing the entire database system abstractly, though it might be more technical than it is practical while you are just starting out.

The editor interface is pretty intuitive, so it shouldn't be too tricky to figure out. Note that because Moodle supports a wide array of database types, there are a limited number of options for the data types of your columns. Notably, you are not able to use the `Date`, `Time`, or `DateTime` data types. Instead, you should express all times as [Unix Timestamps](https://en.wikipedia.org/wiki/Unix_time) with a data type of `int` and a length of `10`.

If you don't have any database tables yet, you can omit this file.

###### WARNING: When you use the editor, you are editing the install.xml file _in your Moodle installation_. After saving any changes, you should be careful to copy it back to your working copy before proceeding with the `moodlePullDBSchema` command. If you run the `moodlePushPlugins` command before you pull the changes, you will overwrite them irrevocably.

##### `install.php`

This file contains code that is executed as soon as the plugin is installed and after any custom database tables are added. This is usually used for setting plugin configs to default values and populating any database tables that have meaningful defaults. Notably, you will need to specify your subpackages here. See our [Plugin Structure documentation](/docs/PLUGIN_STRUCTURE.md#subpackages-abstraction) for more information about the subpackages abstraction. Even if you don't have anything to add at the moment, you should still include an empty function called `xmldb_local_PLUGINNAME_install()` where `PLUGINNAME` is replaced with the name of your plugin. You can use [this file](/local/mxschool/db/install.php) as an example.

##### `upgrade.php`

This file contains code that is executed every time the plugin is upgraded. It is usually used to mirror any changes to the `intall.xml` or `intall.php` files for servers that already have the plugin installed. I would highly recommend that you read Moodle's [Upgrade API documentation](https://docs.moodle.org/dev/Upgrade_API) for more information about this file and the two install files. Even if you don't have anything to add at the moment, you should still include an empty function called `xmldb_local_PLUGINNAME_upgrade($oldversion)` where `PLUGINNAME` is replaced with the name of your plugin. You can use [this file](/local/mxschool/db/upgrade.php) as an example.

Because this function is executed every time the plugin is upgraded, you should put any changes within a block that specifies the relevant version number, for example:

```PHP
if ($oldversion < XXXXXXXXXX) {
    // Upgrade code for version XXXXXXXXXX.
}
```

If your upgrade involves changes to the database structure, Moodle is able to generate the code for you. In the XMLDB editor, select `View PHP code` and select the appropriate changes. Be careful to only update the plugin savepoint once per upgrade even if you have multiple changes.

###### NOTE: Because of the nature of the Upgrade API, you will most likely not be able to delete portions of the upgrade.php file without potentially breaking the upgrade process for someone else's server unless there is a major change that requires a complete re-install. Instead, you should just add additional `if` blocks to the upgrade function.

##### `access.php`

This file is where you specify all the capabilities for your plugin. Capabilities basically serve as permissions which are assigned to roles to enable users to access or interact with certain elements of the plugin. In general, we have a system-level role for all types of users who have some sort of elevated permissions (e.g. Faculty, Deans, Proctors, Peer Tutors, etc.), and students do not have a system-level role. You should read Moodle's [Access API documentation](https://docs.moodle.org/dev/Access_API) for more information. You can use [this file](/local/mxschool/db/access.php) as an example.

###### NOTE: The Access API as well as many other systems in Moodle use the idea of a 'context' which has to do with the scope of the permissions. Our local plugins use the highest context, `CONTEXT_SYSTEM`, for everything.

### `classes/` Directory

The classes directory will contain all of the PHP classes which your plugin uses. The structure within this directory is very important, because Moodle's automatic class loading requires that classes be namespaced and in particular directories to be detected. See our [Plugin Structure documentation](/docs/PLUGIN_STRUCTURE.md#namespaces-and-automatic-class-loading) for more information.

##### Privacy File

To be compliant with the EU's GDPR regulations, Moodle added a Privacy API in version 3.5 which all plugins must integrate with. We have made the deliberate choice not to comply with these regulations because they allow students more access to their data than we want to provide. As such, we have come up a method of circumventing this requirement. Your plugin must have a file `classes/privacy/provider.php`. In this file you should copy the contents of [this template file](/docs/templates/provider.php) and replace `PACKAGE` with the Frankenstyle name of your plugin (the component name in your version file).

### Settings File

This file (`settings.php`) is where you will specify the administration configuration options for your plugin. Any configs which are relevant to the entire plugin rather than a single subpackage should be specified here. Read Moodle's [Admin settings documention](https://docs.moodle.org/dev/Admin_settings) for more information about adding to the admin tree. You also need to specify your admin index pages in this file. See our [Plugin Structure documentation](/docs/PLUGIN_STRUCTURE.md#generating-index-pages) for more information about index pages. You can use [this file](/local/signout/settings.php) as an example.

###### NOTE: The admin settings pages are available to administrators (i.e. Chuck) only, so any configs that you want to be accessible to anyone else should be put in preferences pages instead.

### Local Library File

This file (`locallib.php`) is where you will add all of the functions which your code relies on. The file is not included automatically, so you will need to put an `require_once` statement at the top of _every_ page you create so that you have access to these functions. At the top of this file, you should add the following line:

```PHP
require_once(__DIR__.'/../mxschool/locallib.php');
```

This will cause all of the [API Layer](/docs/API_LAYER.md) functions to be accessible from every page once your `locallib` file is required. Without this line, all of you pages will crash. You can use [this file](/local/signout/locallib.php) as an example.

### `README.md`

This file should explain basic information about your plugin such as its dependencies, subpackages, credits, and license information. You can use [this file](/local/signout/README.md) as an example.

### `CHANGES.md`

This file should give a basic summary of what has changed between major versions of your plugin. You can use [this file](/local/mxschool/CHANGES.md) as an example.

___

## Other Important Files

### Library File

This file (`lib.php`) allows you to add hooks so that core can execute your code in certain situations. There are some places that this may be helpful, but you don't need to add this file unless you are in such a situation.

### Adding AMD Modules

If you want to add JavaScript to your plugin to make it interactive and have the ability to update elements of your webpage without needing to reload it entirely, you will need to create an AMD module which makes use of Moodle's Web Service and External Function APIs. The process to add such features is somewhat cumbersome, but the verbosity is necessary to prevent the significant security issues which could otherwise arise from someone maliciously manipulating your web services with unintended parameters or insufficient permissions. In order to add an AMD module which uses a web service, you will need to add code to three different files. Moodle has a detailed tutorial on [this page](https://docs.moodle.org/dev/Adding_a_web_service_to_a_plugin).

##### Services File

This file (`db/services.php`) is where you specify a high-level description of the service that you are creating. Read Moodle's [Web services API documentation](https://docs.moodle.org/dev/Web_services_API) for more information. You can use [this file](/local/mxschool/db/services.php) as an example.

##### External Library File

This file (`externallib.php`) is where you actually write the external functions as specified in the services file. Each function also two additional functions which describe the parameters and return values of the function: `FUNCTIONNAME_parameters()` and `FUNCTIONNAME_returns()` where `FUNCTIONNAME` is the name of your external function. Read Moodle's [External Functions API documentation](https://docs.moodle.org/dev/External_functions_API) for more information. You can use [this file](/local/mxschool/externallib.php) as an example.

##### `amd/` Directory

This directory is where you actually write your AMD modules. All JavaScript files must be in the `amd/src` directory. When you are done writing or changing a file, be sure to run the following command to style-check and minify the module:

```bash
moodleBuild
```

This command will run Moodle's build scripts (which may take some time) then copy the minified versions of your modules into the `amd/bin` directory. These are the versions that will be run on production servers, so it is **very important** that you not skip this step. Read Moodle's [Javascript Modules

### `templates/` Directory

This is the directory where you will put any mustache templates that your plugin might need. See Moodle's [Tempates documentation](https://docs.moodle.org/dev/Templates) for more information.

### CSS Styles File

You should avoid adding custom CSS where possible, but if your plugin does require something specific, you should add it in the `styles.css` file which will be imported automatically.
