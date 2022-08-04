## Changes in v3.4
- Enhancements to the Deans Permission form
- Clean up and streamline the dashboard blocks, consolidating single link blocks
- Expand documentation for readibility and alignment to open source standards
- Many tweaks and minor issue resolutions and enhancements

## Changes in v3.3
Summer 2021 Upgrades and Enhancements.
- New Deans' Permission Form functionality
- New Healthtest Auditing functionality
- Numerous Language String tweaks
- Block enhancements
- Readiness for Moodle v3.11, php8, Sequel 5

## Changes in v3.2.1
- Create new Healthtest system to schedule and track COVID19 testing events

## Changes in v3.2
- Create new COVIDpass system for daily screening during COVID19 event
- Create new Deans' Permission Form for off-campus travel
- Update student permissions to match new Magnus permission system
- Reimpliment Dorm Proctor Attendance Form

## Changes in v3.1
- Rework of email notification system to be object oriented and less redundant.
- Introduction of formal subpackage abstraction to greatly simplify page setup and package organization, especially with subplugins. See the [updated plugin structure documentation](/docs/PLUGIN_STRUCTURE.md#subpackages-abstraction) for more information.
- Elimination of driving subpackage: all eSignout configuration (all configs, notification text, capability assignments) will be lost as will all existing eSignout records.
- Significant restructuring to API layer. All code in other packages dependent on v3.0 API must be updated. See the [updated API documentation](/docs/API_LAYER.md) for examples of the new code.
- Reorganization of all class files to take advantage of Moodle's automatic class loading.
- Addition of student directory picture storage and management.

## Changes in v3.0
- Complete rewrite of the codebase. The new code is restructured and completely incompatible with the old code. As such, any existing installs will need to be completely uninstalled and replaced.
