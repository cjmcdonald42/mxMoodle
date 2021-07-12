##### This file needs to be reviewed.

# Middlesex Moodle API Layer

The API Layer is a collection of abstractions which we have crated in order to make our codebase less repetitive and verbose, more consistent and reliable, and overall easier to maintain. Our "APIs" consist three major abstract classes which serve as superclasses of the majority of the PHP classes in the project as well as a collection of local library functions.

___

## Table Abstraction

The table abstraction builds upon Moodle's `tablelib` which, unfortunately, is very poorly documented. Our abstraction simplifies table definition by distilling the table setup functionality to two essential methods. If you are curious, the source code for the table abstraction is in [this file](/local/mxschool/classes/table.php). There are plenty of examples of how to extend this class (`\local_mxschool\table`) in the subdirectories of each plugin's `clasess/local/` directory. The rest of this section will walk through the steps to create a table file following [this template](/docs/templates/table.php).

### Constructor

The constructor for your table is where you will do all of the work of defining it. This work is split into two parts: defining the table, and defining the SQL that will populate the table.

Every table constructor should take an object `$filter` as a parameter which will contain the url parameters from the report page. Of course, you can also define other parameters as well, but you won't often need them.

If your table is downloadable, you should also include a `$downloading` parameter and include the following line at the top of your constructor:

```PHP
$this->is_downloading($download, 'TITLE', `SHEET_TITLE`);
```

Where `TITLE` will be the filename when the data is downloaded, and `SHEET_TITLE` will be the name of the sheet. Calling this method will automatically make the download options dropdown appear when the table is rendered.

##### Table Definition

The first step in your constructor is to define an array of column identifiers which you will store in `$columns`. These should all be lowercase and not have any word separators. If you want to remove any columns based on the filtering of the table, you must do that now, before you generate the table's headers with something like this for each condition:

```PHP
if (...) {
    unset($columns[array_search('COLUMN', $columns)]);
}
```

If you are using the dorm dropdown with both boarding and day houses, you should include this block:

```PHP
if ($filter->dorm > 0) {
    unset($columns[array_search('dorm', $columns)]);
}
```

You must then generate an array of headers for your columns and store this in the variable `$headers`. Your headers are the actual text which will be displayed above each column. Call this method to generate pull the headers from your language file:

```PHP
$headers = $this->generate_headers($columns, 'NAME:report');
```

The method will look for language strings under `NAME:report:COLUMN` for each column identifier defined in the `$columns` array.

You then need to define an array of the column identifiers of columns which should be sortable and store this in the variable `$sortable`. The first element will be the default sort. Use an empty array if there should be no sortable columns.

You also need to define an array of the column identifiers of columns whose text should be centered in the cell and store this in the variable `$centered`.

Now that you have defined the structure of the table, you need to pass all of these arrays to the `\local_mxschool\table` constructor as follows:

```PHP
parent::__construct('UNIQUE_ID', $columns, $headers, $sortable, $centered, $filter);
```

Where `UNIQUE_ID` is a string identifier for the table which Moodle uses internally in session cookies and other places. You can also add a seventh boolean argument which specifies whether the actions column should be added (defaults to `true`) and an eighth boolean argument which specifies whether the table should default to an ascending (`true`) or descending (`false`) sort (defaults to `true`).

Lastly, if you want to add any column classes for JavaScript or CSS interaction, you can do that with the following method call:

```PHP
$this->add_column_class('COLUMN', 'CLASS');
```

##### SQL Definition

Once you have specified the structure of your table, you need to specify where the data will come from to fill it. This process basically comes down to defining what should go in the `SELECT`, `FROM`, and `WHERE` clauses in your query in the following format.

The first step is to define the array of fields you want to pull from the database as `$fields`. It is **critical** that the first field in your array be _unique_ because this is how Moodle indexes each row internally, so rows with data in common will not otherwise be displayed. In general, you should make the first element in your field list be the `id` field of the primary record you are pulling from. Each field should be prefixed with the abbreviation of the database table it comes from (as specified in the `$from` array below), and you can (and should) rename fields directly in the SQL with a normal `AS` phrase to map them to the column identifiers where appropriate. You will also be able to format your columns in PHP in cases where that is necessary, as explained below. Here is an example of an element of this array:

```PHP
's.phone_number AS phone'
```

This example takes the `phone_number` field from the database table with abbreviation `s` and puts it in the `phone` column of the table.

You then need to specify what database tables the data is actually coming from. This information goes in the `$from` array. Each element of the array should start with a database table name in curly braces followed by an abbreviation for the table. All elements besides the first are joined with the `LEFT JOIN` operator and should include an `ON` clause. Here are two examples of elements of this array:

```PHP
'{local_mxschool_student} s'
'{user} u ON s.userid = u.id'
```

The first example specifies that the `local_mxschool_student` table will be used with the abbreviation `s`, and the second specifies that the core `user` table will be used with the abbreviation `u` and will be `LEFT JOIN`ed to the first table with the condition `s.userid = u.id`.

Next you need to specify the what conditions the data has to match to be included. This information goes in the `$where` array. The elements can contain any SQL code which is valid in a `WHERE` clause. The elements will ultimately be joined with `AND` operators to form the complete query, so if a single element has an operator with lower precedence than `AND` (such as `OR`), you should be sure to surround the element with parentheses. Your `$where` array will often depend on the data passed to the table in the `$filter` parameter. If you are using the dorm dropdown with both boarding and day houses, you should include this block after you define your `$where` array:

```PHP
if ($filter->dorm) {
    $where[] = $this->get_dorm_where($filter->dorm);
}
```

Note that this method assumes that the `local_mxschool_dorm` table is referenced as `'d'` in the `$fields` array.

The last thing you need to specify is what columns in your table should be searchable. This information goes in the `$searchable` array. If your table is not searchable, you can skip this step.

Now that you have defined the details of your query, call the following method:

```PHP
$this->define_sql($fields, $from, $where, $searchable, $filter->search);
```

If your table is not searchable, you should omit the final two arguments.

### Data Formatting

In all but the most simple cases, you will need to do some sort of processing to your data to convert it from whatever however it is stored in the database to how you want it to be displayed in your report. Luckily, Moodle's API makes this quite simple. When the table is being rendered, it will automatically go through each column and look for a function in the form of:

```PHP
protected col_COLUMN($values) {
    ...
}
```

The `$values` parameter is an object containing all of the fields queried for the particular record (row), and the value returned is what will be displayed in each cell of the column. Be sure to null check (use `isset()` function or `??` operator) for any indices that might not have data in each row.

There are a couple of columns whose format is used so often, that they are included automatically:

- If you have a `student` column, the data will automatically be formatted using the standard student name format ("last, first (preferred)" or "last, first") if you specify a `userid` field which is the user id of the student. If you don't want this behavior, you should override the `col_student()` method in your class.

- If you have a `dorm` column, the dorm's name will automatically be inserted without needing to include the dorm table if you specify a `dormid` field which is the id of the dorm. If you don't want this behavior, you should override the `col_dorm()` method in your class.

Most of our tables include an `actions` column which often includes actions to edit or delete a record as well as other buttons. In the `col_actions()` method you can specify which icons or buttons you want and simply concatenate them together. For an edit button use this method:

```PHP
$this->edit_icon('URL', $values->ID_FIELD)
```

Where `URL` is the URL of the form page, and `ID_FIELD` stores the id of the record which should be edited. For a delete button use this method:

```PHP
$this->delete_icon($values->ID_FIELD)
```

Where `ID_FIELD` stores the id of the record to be deleted. You can add a second optional string argument which specifies the table to delete from and will passed as the `table` parameter to the URL.

There are some obscure cases where your columns are generated dynamically and you can't define your column transformation methods at compile time. In these cases you can use define the following method to transform any cells which do not already have a transformation function:

```PHP
public function other_cols($column, $row) {
    ...
}
```

Where the `$column` parameter stores the column identifier of the cell that's being transformed, and the `$row` parameter is the same as the `$values` parameter for the normal column transformation functions.

###### WARNING: Remember that table classes are namespaced which means that you will need to use a leading `\` if you `use` any of our renderable classes (e.g. buttons or checkboxes) in any of your columns.

___

## Form Abstraction

The form abstraction builds upon Moodle's [Form API](https://docs.moodle.org/dev/Form_API). This API builds upon the [PEAR HTML_QuickForm2 library](https://pear.php.net/manual/en/package.html.html-quickform2.php) and is much more thoroughly documented than the `tablelib` ([this](https://docs.moodle.org/dev/lib/formslib.php_Form_Definition) is an additional page). Our abstraction greatly simplifies the process of creating new forms and makes the form definitions far less verbose. If you are curious, the source code for the table abstraction is in [this file](/local/mxschool/classes/form.php). There are plenty of examples of how to extend this class (`\local_mxschool\form`) in the subdirectories of each plugin's `clasess/local/` directory. The rest of this section will walk through the steps to create a form file following [this template](/docs/templates/form.php).

### Definition Method

Defining a form is in fact quite simple. In general, you only need to define one method with this header:

```PHP
protected function definition() {
    ...
}
```

When the form is created, the inherited constructor will do all of the general set up for you and then call this method. Any parameters which you pass in an array to the first argument of the constructor in your form page will be available to you in the `$this->_custom_data` array with the same keys. If any of your form elements require static options, you should define them at the beginning of your definition method so that the `$fields` array is easy to read. If you are using any `date_selector` or `date_time_selector` elements, the default options whose range is the school year as defined in the checkin sheets preferences page can obtained from a call to the method `self::date_options_school_year()`. If you give a value of `true` as the first argument to this method, the `date_selector` or `date_time_selector` will be optional.

The goal of the definition method is really just to define the fields of your form. You do so in the following format:

```PHP
$fields = array(
    '' => array(
        'id' => self::ELEMENT_HIDDEN_INT
        // Other hidden fields.
    ),
    'HEADER' => array(
        'FIELD' => array(
            'element' => 'TYPE', // ETC.
        )
        // ETC.
    )
    // ETC.
);
```

The first level in this nested structure is the headers. Each header defines a collapsable section of the form with fields in it. The `''` header indicates a section of the form which has no header and is not collapsible. In most cases, we have chosen to use this section only for hidden fields.

Under each header there is an array of fields. Note that this snippet includes the `id` field which is included in all primary and secondary form pages but not preferences pages. For descriptions of how specify each field see the [Field Options section](#field-options) below.

###### WARNING: Form elements (both fields and headers) can never have the same name or else an exception will be thrown.

Once you have defined your fields, you need to call the following method:

```PHP
parent::set_fields($fields, 'SUBPACKAGE:FORM_PREFIX', $topactions, 'PACKAGE');
```

Where `$topactions` is a boolean value specifying whether or not there should be submit and cancel buttons at the top of the form in addition to the bottom. This method will automatically generate form labels from the language file of the specified package. In general, it will look for language strings in the format `SUBPACKAGE:FORM_PREFIX:HEADER:FIELD` where any of those terms is omitted if it is null.

The last thing you might want to do in this method is add some very basic visibility conditions. Moodle provides two options for this which are documented [here](https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#disabledIf). As an example, the following lines set the `student` field to be hidden if the `isstudent` field is equal to `1` and disable the `student` field if the `id` field is not equal to `0`.

```PHP
$mform = $this->_form;
$mform->hideIf('student', 'isstudent', 'eq');
$mform->disabledIf('student', 'id', 'neq', '0');
```

If you want anything more complicated than this, you will have to do it with JavaScript directly.

### Field Options

When specifying the fields of your form, there are a number of types you can choose from which each have particular options. Our form abstraction has been updated to support the majority of the field types which Moodle defines though not all of them. Here is the list of available types to be assigned as the `element` of each field:

- `hidden`
- `submit`
- `cancel`
- `text`
- `textarea`
- `static`
- `editor`
- `filepicker`
- `filemanager`
- `checkbox`
- `advcheckbox`
- `date_selector`
- `date_time_selector`
- `select`
- `autocomplete`
- `radio`
- `group`

For all of these types, you can read the general description and parameter options on [Moodle's formslib documentation page](https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#addElement). Note that because we use this form abstraction, we do not need to (and should not) call the `addElement()` method in any of our form classes.

If you want to have multiple fields on the same line, you should create a `group` element and assign the key `children` to an array of fields that should be in the group which are each specified just as any other field would be.

Moodle's `date_time_selector` uses exclusively 24 hour time which is not the preferred time format of our users. As such we have created a time selector pseudo-element which combines three select elements into a group to allow users to select a time with the 12 hour format. You can use this field type with the following line:

```PHP
'time' => self::time_selector()
```

If you want a step size other than 1 minute, you can pass it as an integer argument to this method.

Another field type that is commonly used in preference pages is a static field that enumerates the tags which are available for an email. You can automatically generate a such a field by passing any `local_mxschool\notification` object to the `self::email_tags()` method.

If you want to add any basic rules to the field (options listed [here](https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#addRule)) you can do so by assigning the key `rules` to an array of rule strings.

There is a large number of constants defined at the top of the [`\local_mxschool\form` class](/local/mxschool/classes/form.php) which are commonly used elements that should be referenced instead of restated if they match any fields in your form. For example a field in your definition could be defined as such:

```PHP
'email' => self::ELEMENT_EMAIL_REQUIRED
```

These constants also serve as good examples of how you might define other fields that you are using. There are some other special cases, but if you want a field to do something in particular that is not described here, try using similar fields in existing forms as examples.

### Validation Method

You can add the `required` rule to an element to check whether or not it is empty, but for anything more complex than that, you will need to write a validation method for your form. This method must have the following signature:

```PHP
public function validation($data, $files) {
    ...
}
```

The `$data` parameter is the data which the user submitted as an array (rather than an object). The `$files` parameter would contain any files that the user submitted, but this is not something that we really use. The first thing you _must_ do in the method is call the validation method that you are overriding like this:

```PHP
$errors = parent::validation($data, $files);
```

You should then perform whatever validation you need to. If you determine that a field has an error, add an element to the `$errors` array which has the field as its key and a language string that describes the error to the user as its value. When you are done, return the `$errors` array. If there are no errors, the form will be submitted as normal, but if there are any errors, the user will have to correct them before the data the entered can be saved.

___

## Email Notifications Abstraction

The email notification abstraction defines email notifications in an object-oriented way in order to abstract out the mechanics of sending emails and make the definitions concise while still allowing each class of notification to be highly flexible and configurable. The essential abstract class is the `\local_mxschool\notification` which defines a singular notification to be send to one or more recipients and is defined in [this file](/local/mxschool/classes/notification.php). There is also the abstract `\local_mxschool\bulk_notification` class which serves as a wrapper that holds a collection of similar `\local_mxschool\notification`s to be sent collectively and is defined in [this file](/local/mxschool/classes/bulk_notification.php). There are a number of examples of how to extend these classes in the subdirectories of each plugin's `clasess/local/` directory. The rest of this section will walk through the steps to create a notification following [this template](/docs/templates/notification.php) and a bulk notification following [this template](/docs/templates/bulk_notification.php).

Defining a new class of email notification is quite simple. In the constructor, you first need to call the parent constructor with the email class as it is stored in the database as a parameter. You should then make whatever SQL queries are necessary to gather the relevant data for your notification and populate the `$this->data` array with key-value pairs that specify what data will be used to fill placeholders in both the subject line and the email body. Lastly, you need to populate the `$this->recipients` array with the records from the `user` table for each user to whom the notification should be sent. If the notification should be sent to the deans, use the `self::get_deans_user()` method to get the appropriate object to include. You also need to define the following method which lists all of the available tags so that they can be displayed in the preferences page:

```PHP
/**
 * @return array The list of strings which can serve as tags for the notification.
 */
public function get_tags() {
    return array_merge(parent::get_tags(), array(
        'TAG1', 'TAG2', // ETC.
    ));
}
```

Defining a bulk wrapper is even easier. You still define the regular email class which is instantiated for each individual email, but then you also define another class (the convention we have used is to prefix the class name of the individual email class with `bulk_`). This class's sole purpose is to create an array of emails with different data which should all be sent to their respective recipients. All of the actual sending logic is abstracted, so all you need to do in such a class is define a constructor which populates the `$this->notifications` array.

___

## Useful Local Library Functions

The local library file of the `local_mxschool` plugin contains a large assortment of useful functions. Some of these functions are designed to be used generally, while others are subpackage-specific and aren't particularly useful if you are working on something else. They do serve as good examples, though, if you are looking for something to model a new function off of. All of the functions in this file are well documented, so reading the headers and looking at examples (⇧⌘F is your friend) should be enough in most cases. However, there are a few especially important functions that merit some extra description in this documentation. Some of these functions such the page setup abstractions and the database manipulation abstractions are already covered in context in other parts of this documentation, so I will not repeat those descriptions here. In this section I will discuss a few of the utility-type functions that you should definitely be aware of as they are likely to come up in a variety of contexts.

#### Date/Time Functions

While you should always use [Unix Timestamps](https://en.wikipedia.org/wiki/Unix_time) to store, you should **never** do math on timestamps directly. If you want to manipulate timestamps, you need to use a [PHP `DateTime` object](https://www.php.net/manual/en/class.datetime.php). Moodle also provides a small [Time API](https://docs.moodle.org/dev/Time_API) to build upon this specifically with regard to timezones and distinguishing between the server and user. We generally don't expect that timezones will be relevant for our use case; however, it is still a best practice to use this in order to be robust to server configuration and avoid any fringe cases where times could theoretically be rendered based on the timezone set in a user's browser.

With that said, we have created two general local library functions in order to mitigate any consistency problems and make `DateTime` instantiation less verbose. To create a `DateTime` object with the timezone guaranteed to be correct, call the function `generate_datetime()`. This function takes one argument which can be a timestamp or a time string in any format that is [supported by the `DateTime` constructor](https://www.php.net/manual/en/datetime.formats.php). If no argument is passed, the current time is used. Here are some examples of how you might use this function:

```PHP
generate_datetime('Sunday this week') // A DateTime object at midnight on Sunday of this week.
generate_datetime('-1 day')->getTimestamp() // The timestamp from exactly 24 hours ago.
generate_datetime('midnight')->getTimestamp() // The timestamp from midnight of the current day.
```

Rather than use PHP's `date()` function directly, you should use our `format_date()` local library function which supports all of the same formats as `date()` as the first argument, and a time as the second argument which works exactly as the argument to `generate_datetime()`. Here are some examples of how you might use this function:

```PHP
(int) format_date('Y') // The current year as an integer.
format_date('n/j/y', $values->tutoringdate) // The tutoring date in the format "mm/dd/yy".
format_date('g:i A', $values->signouttime) // The signout time in the format "h:mm AM/PM".
```

#### Formatting Functions

We also have a number of functions whose purpose is to format commonly used strings:

- To format a boolean value into a yes/no language string, call the `format_boolean($boolean)` function.

- To format a student's name in the standard format ("Last, First (Preferred)" or "Last, First") from their user id, call the `format_student_name($userid)` function.

- To format a faculty's name in the inverted format ("Last, First") from their user id, call the `format_faculty_name($userid)` function. Pass the value `false` as the second argument for the standard format ("First Last").

- To format a dorm's name from its dorm id, call the `format_dorm_name($id)` function.

One benefit of all of these functions is that they check to see if the relevant record has been deleted, and if so, return an empty string.

#### Type Conversion Functions

The last category of utility functions is functions which convert back and forth between arrays of record objects and associative arrays:

- To convert an array of objects which each have properties `id` and `value` to an associative array of the form `id => value`, call the `convert_records_to_list($records)` function. This function is useful for converting a list of records into a format that can be used in select elements in forms and dropdowns in report filters.

- To convert an array of objects with each have property `id` that references a student's `user` record to an associative array of the form `id => formatted_name`, call the `convert_student_records_to_list($records)` function. This function is useful for converting a list of student records into a format that can be used in select elements in forms.

- To convert an associative array into an array of object with properties `value` (array keys) and `text` (array values), call the `convert_associative_to_object($list)` function. This function is useful for converting from the associative array format for select elements in forms to a format that can be passed from an external function to an AMD module via an AJAX request.
