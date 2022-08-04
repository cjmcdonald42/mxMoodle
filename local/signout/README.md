# Middlesex's eSignout Subplugin

Local Moodle plugin written for Middlesex. Middlesex is an independent secondary school for boarding and day students in grades 9-12. Learn more at <https://mxschool.edu>.

Moodle is the world’s open source learning platform. Learn more at <https://moodle.org>.

## Package Description
This package provides a system for students to sign out to both on- and off-campus locations and a means for faculty and the deans to view and manage these signouts.

## Dependencies
This plugin has the following dependencies:
- Moodle 3.7 (2019052000)
- [local_mxschool v3.1](/local/mxschool/README.md) (2019081400)

## Subpackages
This package houses the following subpackages and pages:
- **_null_**
    - _combined_report_ Report for dorm faculty to view and manage the active on- and off-campus records for students in their dorm.
- **_on_campus_** — Provides a means for students to sign out of their dorms to an on-campus location during and after study hours.
    - _preferences, location_edit_ — Preferences page to enable or disable features of the form, configure form text, and manage the available on-campus locations.
    - _form_ — Form for students to sign out to an on-campus location.
    - _report_ — Report for dorm faculty and deans to view and manage students' on-campus signouts. Also logs historical data.
    - _duty_report_ — Report for faculty on evening duty to view and confirm students' on-campus signouts on a given evening.
- **_off_campus_** — Provides a means for students to sign out to an off-campus location on a weekday.
    - _preferences, type_edit_ — Preferences page to enable or disable features of the form, configure form text as well as the text in email notifications, and manage the available off-campus signout types.
    - _form_ — Form for students to sign out to an off-campus location.
    - _report_ — Report for dorm faculty and deans to view and manage students' off-campus signouts. Also logs historical data.

## Credits
v3.1 of this plugin was developed alongside v3.1 of the local_mxschool plugin in 2019 by:
- Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
- Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>

_See [our documentation](/docs/README.md) to learn more about this project_
