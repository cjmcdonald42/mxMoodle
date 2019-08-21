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

The rest of this documentation discusses various elements of commonly used page types. For more information about internal files, you should read the relevant API documentation from the [List of Core APIs](https://docs.moodle.org/dev/Core_APIs) and refer to relevant files in our [templates directory](/docs/templates/). For more information about our custom table, form, and email notification abstractions, you should also refer to our [API Layer documentation](/docs/API_LAYER.md).

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

The following descriptions are a walkthrough of the code in [this template file](/docs/templates/report_page.php) as well as some additional information. There is an abundance of reports in the existing code, any of which you could use as examples.

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

If your report allows for record deletion, also include the following two lines which will populate the `$action` and `$id` variables if the user is trying to deleted a record:

```PHP
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
```

If report is downloadable, also include the following line:

```PHP
$download = optional_param('download', '', PARAM_ALPHA);
```

##### Page Setup

Assuming you have registered the page using our [subpackages abstraction](/docs/PLUGIN_STRUCTURE.md#initializing-the-subpackages-table), you only need to write one simple line to properly set up the `$PAGE` global:

```PHP
setup_mxschool_page('PAGE', 'SUBPACKAGE', 'PACKAGE');
```

If your report includes timely data, you may want to have the page automatically refresh on a period basis. You can do that with the following two lines:

```PHP
$PAGE->set_url(new moodle_url($PAGE->url, (array) $filter));
$PAGE->set_periodic_refresh_delay($refresh);
```

Where `$refresh` is the amount of time to wait before refreshing the page in seconds.

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

The next step is to assemble any arrays with relevant static data. You should avoid making any direct database calls in this file, but rather write local library functions to generate these lists for you, or use existing ones.

##### Object Initialization

The second-to-last step is to create the objects which will then be rendered to the page. The first object is the actual table:

```PHP
$table = new PACKAGE\local\SUBPACKAGE\TABLE_CLASS($filter);
```

If your report is downloadable, you will also need to pass the `$download` variable into your table's constructor.

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

The final step is to create a renderable and actually render the report and surrounding page into HTML, and `echo` it to the page. You can do all of this with the following lines:

```PHP
$output = $PAGE->get_renderer('PACKAGE');
$renderable = new \local_mxschool\output\report($table, 'SEARCH', $dropdowns, $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
```

Where `SEARCH` is the current search, usually `$filter->search` or `null`, if the report doesn't allow for searching. Optionally, you can add a value of `true` as a fifth argument to add a print button to the filter bar.

If your report is downloadable, insert the following lines between the first and second lines of the last snippet:

```PHP
if ($table->is_downloading()) {
    $renderable = new local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
```

### Form Pages

Form pages fall into three sub-categories:

1. Primary form pages which are primarily used for students or other users to create data that has a meaningful lifespan or time period associated with it. These types of forms are often interactive with relevant fields being shown or hidden by JavaScript and feature additional server-side validation. Records should be timestamped upon creation and modification.

2. Secondary form pages ("edit pages") which primarily allow for a single record or combination of records to be edited, usually without any JavaScript or complex validation being required. These types of data are often relatively static, such as user data or options for primary forms. Records do not need to be timestamped.

3. Preferences form pages which primarily update plugin configs and email notification text rather than any other records in our custom database tables. These pages differ from admin settings pages because they do not require admin capabilities to be accessed.

Form pages of all of these categories have the same general structure, though there are some differences. The following descriptions are a walkthrough of the code in [this template file](/docs/templates/edit_page.php). The template file is specifically geared towards edit pages, but this walkthrough will also include information about how to adapt this two the other two major categories of form pages. There is an abundance of each type of form page in the existing code, any of which you could use as examples.

##### Permissions Check

Just like with report pages, the first thing you must do in any form page is validate that the user has permissions to access the page with the following lines:

```PHP
require_login();
require_capability('CAPABILITY', context_system::instance());
```

Where `CAPABILITY` is the capability defined in the plugin's `access.php` file which is required to view the page.

It is often the case in primary form pages that the form should display differently to a student or non-student and requires a capability only for a non-student (because we have no student role and thus cannot assign capabilities to students specifically). In this case, you would want to replace the capability check line with the following:

```PHP
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('CAPABILITY', context_system::instance());
}
```

Note that this snippet uses our `user_is_student()` local library function which will populate the `$isstudent` variable with a boolean value specifying whether the currently logged in user has a record in our student table. You should use this variable throughout the file to adjust the form data accordingly.

##### URL Parameters

All form pages use an `id` parameter to specify what record is currently being edited. If the form is creating a new record, this value will be `0`, and the database will automatically assign a value upon insertion. Save the `id` from the URL with this line:

```PHP
$id = optional_param('id', 0, PARAM_INT);
```

##### Page Setup

Setting up the `$PAGE` global is very similar to that of report pages, except that secondary form pages use a different function. For pages that have been registered using our [subpackages abstraction](/docs/PLUGIN_STRUCTURE.md#initializing-the-subpackages-table) (i.e. primary and preference form pages), you need to write the same line as you do for reports:

```PHP
setup_mxschool_page('PAGE', 'SUBPACKAGE', 'PACKAGE');
```

For secondary form pages which should not be registered in the subpackage table, use this line instead:

```PHP
setup_edit_page('PAGE', 'PARENT', 'SUBPACKAGE', 'PACKAGE');
```

Primary form pages often require an AMD module. Add one here with the following line:

```PHP
$PAGE->requires->js_call_amd('PACKAGE/MODULE', 'setup');
```

Where `setup` is the name of the function in the object returned by the module which you with to call to inialize the JavaScript for the page.

##### Specifying Query Fields

The next step is an abstraction that we have come up with to reduce repetitive code and make the code more readable. In primary and secondary forms, you specify what data your form interacts with with an array of the following form:

```PHP
$queryfields = array(
    'TABLE' => array(
        'abbreviation' => 'ABBREVIATION',
        'fields' => array(
            'DATABASE_FIELD' => 'FORM_FIELD'
            // ETC.
        )
    )
    // ETC.
);
```

This format specifies one or more database tables (`TABLE`) to draw from which each are referenced by an abbreviation (`ABBREVIATION`). If pull data from multiple tables for your query, you must include a element with the key `join` in each table after the first which specifies the text for the `ON` clause in the SQL join (using the specified abbreviations). For each table, the `fields` array maps each field in the database (`DATABASE_FIELD`) to the reference used within the form class (`FORM_FIELD`). This array is used both for encoding the data in order to populate the form, and for decoding it after the form is submitted.

##### Data Population

You now need to populate a data object to use for your form based on whether or not you have an existing record. The permission checks for students also occur at this stage for primary form pages. It is important to ensure that a given student has permissions not only to access the page, but also to access the particular record that they have specified with the `id` parameter. If you determine that the user should not have access to the form they are requesting for any reason, you have two options to redirect them:

```PHP
redirect_to_fallback();

redirect($PAGE->url);
```

The first of these options will redirect the user to the form's parent page (index or report) if they are an admin, otherwise it will direct them to their dashboard. The second option will redirect the user to an empty form so that they can create a new record. This is especially useful in cases where students should be able to create a new form but not edit an existing one.

A common data population block would have this general form:

```PHP
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('TABLE', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 'WHERE_STRING', array($id));
    // TODO: Data transformations.
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    // TODO: Default data.
}
```

Where `TABLE` is the table where the record with the specified `$id` must exist and `WHERE_STRING` is a string that will specify which record to select — usually something like `"ABBREVIATION.id = ?"`. The third argument to the `get_record` local library function is an array of parameters which are used to fill in placeholders in the where clause, as in many of Moodle's [Data Manipulation API](https://docs.moodle.org/dev/Data_manipulation_API#Placeholders) functions.

If there is an existing record, you may have to apply some data transformations in order to convert the data from the format in which it is stored in the database to the format that the form will accept. Examples include replacing `null` values and decoding JSON-formatted fields.

If there is not an existing record, you may have to set default values for your form. examples include timetamps for date/time selectors and the `userid` of a the user to populate the student field in primary forms. It is also common to set any radio field to a value of `-1` to prevent the `0` option from being auto selected.

If you are using our custom 12 hour `time_selector`, you will need to call the `generate_time_selector_fields()` local library function which takes the data object as the first argument, the string prefix of the `time_selector` field as the second argument, and an optional third argument to specify a step size other than 1 minute.

Preference forms will usually populate the `$data` object directly without using the `get_record()` local library function. For this purpose, use Moodle's `get_config()` function for each plugin config that can be edited by the page. This function takes the component name as the first argument and the config name as the second. If the preference page has any email text editors, use the `generate_email_preference_fields()` local library function which takes the email class as the first argument, the data object as the second argument, and a string prefix for the form fields as an optional third argument.

##### Static Querying

The next step is to assemble any arrays with relevant static data. Just like in report files, you should avoid making any direct database calls in this file, but rather write local library functions to generate these lists for you, or use the existing ones.

###### WARNING: Any lists of options for select or radio elements must include _all_ possible options regardless of the currently selected student or other fields. The options should be adjusted appropriately in JavaScript, but due to the way that validation works, the data won't be saved if the option is not in the original array.

##### Form Object Initialization

You are now ready to create the actual form object and add the data to it. This is done with two simple lines:

```PHP
$form = new PACKAGE\local\SUBPACKAGE\FORM_CLASS(array($PARAMETER1, ETC.));
$form->set_data($data);
```

All parameters to the form (usually just lists that are used as options) must be passed to the form in an array as shown above because of the way that we build on Moodle's form API.

Most of the time the default redirect URL is the desired option, but sometimes you may want to set some specific. In this case use the following method:

```PHP
$form->set_fallback($redirect);
```

Where `$redirect` is the `moodle_url` that the user should be redirected to when they submit or cancel the form.

##### Dealing with Submitted Data

When a form is submitted or cancelled, the page is reloaded with the form data in a `POST` request. Understanding the technical details of this process is not important, but this is the reason why we process the form before we actually render the page (if we are in a state of processing, we are going to redirect, so the form should not be rendered). Fortunately, the [PEAR HTML_QuickForm2 library](https://pear.php.net/manual/en/package.html.html-quickform2.php), which Moodle's forms library (and therefore our form abstraction) is built upon, provides convenient hooks to process the data. Your code will look something like this:

```PHP
if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    // TODO: Data transformations.
    update_record($queryfields, $data);
    $action = $data->id ? 'update' : 'create';
    logged_redirect($form->get_redirect(), get_string("SUBPACKAGE:FORM_PREFIX:{$action}:success", 'PACKAGE'), $action);
}
```

The first `if` block is run when the form is cancelled. In this case all we need to do is redirect to the form's redirect URL. This will be the referrer if it exists, otherwise it will be the default fallback as explained in the [data population](#data-population) section above.

The second block is run when the form is submitted. The first step in this case is to perform any transformations to the data so that it is ready to be entered into the database. In particular, care should be taken to `unset` all fields which should have null values as the form may often provide other default values when nothing is submitted, such as an empty string.

If you are using our custom 12 hour `time_selector`, you will need set the appropriate field of your data object to the result of a call to the `generate_timestamp()` local library function which takes the data object as the first argument and the string prefix of the `time_selector` field as the second argument.

For a primary or secondary form page, all you need to do is call the `update_record()` local library function to save the data as you already specified in your `$queryfields` variable. On the other hand, for a preferences form you will have to save the data manually. Use Moodle's `get_config()` function to save any plugin configs. This function takes the config name as the first argument, the data to set as the second argument, and the component name as the third argument. To update email text, use the `update_notification()` local library function which takes the email class as the first argument, the data object as the second argument, and a string prefix from the form fields as an optional third argument.

Lastly, use the logged redirect local library function to redirect the user to the default fallback and log the record submission. For primary and secondary forms, this will take the form of the lines above, but for preference forms, it will look more like this:

```PHP
logged_redirect($form->get_redirect(), get_string('SUBPACKAGE:preferences:update:success', 'PACKAGE'), 'update');
```

##### Rendering

Just as with report pages, the final step is to create a renderable and actually render the report and surrounding page into HTML, and `echo` it to the page. You can do all of this with the following lines:

```PHP
$output = $PAGE->get_renderer('PACKAGE');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
```
