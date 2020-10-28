# Middlesex's Dorm and Student Functions Plugin

Local Moodle plugin written for Middlesex. Middlesex is an independent secondary school for boarding and day students in grades 9-12. Learn more at <https://mxschool.edu>.

Moodle is the world’s open source learning platform. Learn more at <https://moodle.org>.

## Package Description
This package defines basic information about students, faculty, and dorms and provides a number of subsystems which implement management functionality for dorm faculty and administrative functionality for the deans. The package also defines a number of abstractions and provides its own API on top of the core Moodle functionality in order to simplify development and provide consistency in all of the Middlesex Moodle Subplugins.

## Dependencies
This plugin has the following dependencies:
- Moodle 3.7 (2019052000)
and is currently running on and being developed with
- Moodle 3.9 (2020061500)

## Subpackages
This package houses the following subpackages and pages:
- **_user_management_** — Defines students, faculty, and dorms and provides an interface to edit static information used throughout the package.
    - _student_report, student_edit, parent_edit_ — Student management, permissions, and parents report and corresponding edit pages.
    - _faculty_report, faculty_edit_ — Faculty management report and edit page.
    - _dorm_report, dorm_edit_ — Dorm management report and edit page.
    - _vehicle_report, vehicle_edit_ — Student registered vehicles report and edit page.
    - _picture_import_ — Student picture bulk import page.
- **_checkin_** — Generates dorm check-in sheets for a variety of situations and provides a way for students to sign out for the weekend with the Weekend Form.
    - _preferences_ — Preferences page to specify when each semester starts and ends and the type of each weekend and to configure the text in email notifications.
    - _generic_report_ — Generic check-in sheet to be used for all checked events.
    - _weekday_report_ — Weekly check-in sheet to be used within the dorm.
    - _weekend_form_ — Form for students to submit weekend travel plans.
    - _weekend_report_ — Weekend check-in sheet to be used within the dorm which includes weekend form information. Also logs historical data.
    - _weekend_calculator_ — Report for students and dorm faculty to see how many weekends a student has spent off campus.
- **_advisor_selection_** — Provides a system for students to specify their preference of advisor to the deans.
    - _preferences_ — Preferences page to enable or disable the form, configure form text as well as the text in email notifications, and specify the status of each faculty's advisory.
    - _form_ — Form for students to specify their preference of advisor.
    - _report_ — Report for deans to view and manage students' responses.
- **_rooming_** — Provides a system for students to specify their rooming preferences to the deans.
    - _preferences_ — Preferences page to enable or disable the form and configure form text as well as the text in email notifications.
    - _form_ — Form for students to specify their rooming preferences.
    - _report_ — Report for deans to view and manage students' responses.
- **_vacation_travel_** — Provides a system for students to submit their vacation travel plans and transportation needs in order to coordinate with the deans, dorm faculty, and the transportation manager,
    - _preferences, site_edit_ — Preferences page to enable or disable the form, configure the text in email notifications, and manage the list of available sites.
    - _form_ — Form for students to submit their vacation travel plans and transportation needs.
    - _report_ — Report for dorm faculty to view and manage students' responses.
    - _transportation_report_ — Report for the transportation manager to view students' responses in order to organize transportation.
- **_healthpass_** — Daily reporting system for COVID19.
    - _preferences_ — Preferences page to enable or disable the form.
    - _form_ — Form to submit intake data.
    - _report_ — Report for health center staff to view and manage intake responses.

## Credits
v3.2 of this plugin was developed in 2020 by:
- Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
- Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>

v3.1 of this plugin was developed in 2019 by:
- Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
- Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>

v3.0 of this plugin was developed in 2018 by:
- Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
- Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>

## License
As Moodle itself, this plugin is provided freely under the [GNU General Public License v3.0](/COPYING.txt).

© 2018-2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
