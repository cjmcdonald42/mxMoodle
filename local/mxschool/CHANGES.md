## Changes in v3.1
- Rework of email notification system to be object oriented and less redundant.
- Introduction of formal subpackage abstraction to greatly simplify page setup and package organization, especially with subplugins. See the [updated plugin structure documentation](/docs/PLUGIN_STRUCTURE.md#subpackages-abstraction) for more information.
- Elimination of driving subpackage: all eSignout configuration (all configs, notification text, capability assignments) will be lost as will all existing eSignout records.
- Significant restructuring to API layer. All code in other packages dependent on v3.0 API must be updated. See the [updated API documentation](/docs/API_LAYER.md) for examples of the new code.
- Reorganization of all class files to take advantage of Moodle's automatic class loading.
- Addition of student directory picture storage and management.

## Changes in v3.0
- Complete rewrite of the codebase. The new code is restructured and completely incompatible with the old code. As such, any existing installs will need to be completely uninstalled and replaced.
