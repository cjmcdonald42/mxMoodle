# Middlesex Moodle General File Structure

Our plugins include a variety of PHP files which accomplish different tasks. However, there are some commonalities between all of the files and certain subsets of the files which are worth pointing out.

___

## Header Comments

After the opening `<?php` tag, all PHP files (and other files, for that matter) in Moodle must start with a comment indicating the licensing associated with Moodle source code. The follow 14 lines must be present:

```PHP
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
```

Next, all files should a header PHPDoc which provides information about the file. Here is an example of such a header comment with placeholders:

```PHP
/**
 * DESCRIPTION
 *
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @author      YOUR NAME HERE
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
```

For more information about what you should indicate as the subpackage, see our [Plugin Structure documentation](/docs/PLUGIN_STRUCTURE.md#subpackages-abstraction). If the file is in the _root_ subpackage, remove the `@subpackage` line entirely.

___

## Internal vs. External Files

All PHP files in Moodle fall under one of two categories: internal or external. External files are those which are served directly by the browser and define pages. Internal files are every other PHP file in the project and are included or required by PHP code in one of our files or somewhere in Moodle's core code.

All external files must begin with the following line to trigger a chain of require statements that will import all of the relevant core code:

```PHP
require('PATH_TO_PLUGIN_ROOT/../../config.php');
```

Where `PATH_TO_PLUGIN_ROOT` is replaced with a relative path beginning with the magic constant `__DIR__`. For example, if the file is in the root directory of a local plugin, the full line would be:

```PHP
require(__DIR__.'/../../config.php');
```

The next line should import the package's `locallib.php` file:

```PHP
require_once('PATH_TO_PLUGIN_ROOT/locallib.php');
```

###### NOTE: Other than the initial call requiring `config.php`, you should use the `require_once()` function for all imports.

All internal files, on the other hand, must begin by verifying that core has been initialized successfully with the following line:

```PHP
defined('MOODLE_INTERNAL') || die;
```

The rest of this documentation discusses various elements of commonly used page types. For more information about internal files, you should read the relevant API documentation from the [List of Core APIs](https://docs.moodle.org/dev/Core_APIs) and refer to relevant files in our [templates directory](/docs/templates/). For more information about our custom form and table abstractions, you should also refer to our [API Layer documentation](/docs/API_LAYER.md).

___

## Common Page Types

Generally speaking, the pages we add with our custom local plugins, fall into three broad categories:

1. Index pages which simply link to other pages
2. Report pages which display data in tables and often offer filtering options
3. Form pages which allow the user to enter data directly and/or edit existing data

Sometimes pages may include more than one of these elements; for example, some preferences pages include both a form and one or more reports.

We will now discuss the structure of each of these file types at a high level.

### Index Pages

Index pages are by far the most simple. For an index of a single subpackage, all you need to do is copy [this template file](/docs/templates/index.php) to your subpackage directory and replace the placeholders. For more information about adding index pages and more complex options, see our [Plugin Structure documentation](/docs/PLUGIN_STRUCTURE.md#generating-index-pages).

### Report Pages

The following descriptions are a walkthrough of the code in [this template file](/docs/templates/report_page.php). There is an abundance of reports in the existing code, any of which you could use as an example.

##### Permissions Check

The first thing you must do in any report page is validate that the user has permissions to access the page with the following lines:

```PHP
require_login();
require_capability('CAPABILITY', context_system::instance());
```

Where `CAPABILITY` is the capability defined in the plugin's `access.php` file which is required to view the page.

##### URL Parameters

Next you must save any url parameters into a filter object with the following lines:

```PHP
$filter = new stdClass();
$filter->PARAMETER1 = optional_param('PARAMETER1', 'DEFAULT', PARAM_TYPE);
// Etc.
```

Where `PARAMETER1` is the name of the first parameter, `DEFAULT` is a default value for when the parameter is not specified, and `PARAM_TYPE` is the parameter type, for example `PARAM_TEXT`, `PARAM_INT`, or `PARAM_RAW`. You can read about the commonly used types on [this page](https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#Most_Commonly_Used_PARAM_.2A_Types).

One special case is that if the report supports filtering by dorm, you should use this line instead so that the report will default to the currently logged-in faculty member's dorm by default if another dorm has not been selected:

```PHP
$filter->dorm = get_param_faculty_dorm();
```

Pass a value of `false` as the first argument to this function if the report only applies to boarding dorms.

If your report allows for record deletion, you should also include the following two lines which will populate the `$action` and `$id` variables if the user is trying to deleted a record:

```PHP
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
```

##### Page Setup

Assuming you have registered the page using our [subpackages abstraction](/docs/PLUGIN_STRUCTURE.md#initializing-the-subpackages-table), you only need to write one simple line to properly set up the `$PAGE` global:

```PHP
setup_mxschool_page('PAGE', 'SUBPACKAGE', 'PACKAGE');
```

##### Record Deletion

If your report allows for record deletion, you should include the following block which will detect whether the user is trying to delete a record and then reload the page with a message indicating whether the deletion was successful

```PHP
if ($action === 'delete' && $id) {
    $result = $DB->record_exists('TABLE', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('TABLE', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("SUBPACKAGE:PAGE:delete:{$result}", 'PACKAGE'),
        'delete', $result === 'success'
    );
}
```

Where `TABLE` is the database table that the report is pulling from.

###### NOTE: In the plugin's language file, you must define the language strings `'SUBPACKAGE:PAGE:delete:success'` and `'SUBPACKAGE:PAGE:delete:failure'` which will provide feedback to the user when they try to delete a record.

##### Static Querying

The next step is to assemble any arrays with relevant static data. You should not make any direct database call in this file, but rather write local library functions to generate these lists for you, or use existing ones.

##### Object Initialization

The second-to-last step is to create the objects which will then be rendered to the page. The first object is the actual table:

```PHP
$table = new PACKAGE\local\SUBPACKAGE\TABLE_CLASS($filter);
```

Then you must create an array of dropdowns that will be used to filter the report:

```PHP
$dropdowns = array(
    new local_mxschool\output\dropdown('NAME', OPTIONS, 'SELECTED', 'DEFAULT')
    // ETC.
);
```

Where `NAME` must match the url parameter you saved to your filter; `OPTIONS` is the array of options, as queried or defined above; `SELECTED` is the currently selected option, often simply `$filter->NAME`, and `DEFAULT`, is an optional string parameter that adds an option to the top of the list which is selected by default.

One special case is that if the report supports filtering by dorm, you should use this line instead:

```PHP
local_mxschool\output\dropdown::dorm_dropdown($filter->dorm);
```

Pass a value of `false` as the second argument to this function if the report only applies to boarding dorms.

Lastly you must create an array of any buttons that should appear in the filter bar, such as redirect or email buttons:

```PHP
$buttons = array(
    new local_mxschool\output\redirect_button('TEXT', new moodle_url('URL')),
    new local_mxschool\output\email_button('TEXT', 'EMAIL_CLASS')
    // ETC.
);
```

##### Rendering

The last step is to create a renderable and actually render the report and surrounding page into HTML, and `echo` it to the page. You can do all of this with the following lines:

```PHP
$output = $PAGE->get_renderer('PACKAGE');
$renderable = new \local_mxschool\output\report($table, 'SEARCH', $dropdowns, $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
```

Where `SEARCH` is the current search, usually `$filter->search` or `null`, if the report doesn't allow for searching. Optionally, you can add a value of `true` as a fifth parameter to add a print button to the filter bar.

### Form Pages

Form pages fall into three sub-categories:

1. Primary form pages which are primarily used for students or other users to create data that has a meaningful lifespan or time period associated with it. These types of forms are often interactive with relevant fields being shown or hidden by JavaScript and feature additional server-side validation. Records should be timestamped upon creation and modification.

2. Secondary form pages ("edit pages") which primarily allow for a single record or combination of records to be edited, usually without any JavaScript or complex validation being required. These types of data are often relatively static, such as user data or options for primary forms. Records do not need to be timestamped.

3. Preferences form pages which primarily update plugin configs and email notification text rather than any other records in our custom database tables. These pages differ from admin settings pages because they do not require admin capabilities to be accessed.
