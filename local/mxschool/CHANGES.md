## Changes in v3.1
- Introduction of formal subpackage abstraction to greatly simplify page setup and package organization.
- Elimination of driving subpackage: all eSignout configuration (all configs, notification text, capability assignments) will be lost as will all existing eSignout records.
- Significant restructuring to API layer. All code in other packages dependent on v3.0 API must be updated. See the updated API documentation for examples of the new code.
- Addition of student directory picture storage and management.

## Changes in v3.0
- Complete rewrite of the codebase. The new code is restructured and completely incompatible with the old code. As such, any existing installs will need to be completely uninstalled and replaced.
