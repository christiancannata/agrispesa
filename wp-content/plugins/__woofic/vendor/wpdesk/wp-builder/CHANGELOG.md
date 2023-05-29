## [2.0.0] - 2021-00-07
### Added
- plugin shops in WPDesk_Plugin_Info

## [1.4.4] - 2020-06-17
### Fixed
- Replaced class_exists to interface_exists for interfaces

## [1.4.3] - 2020-06-03
### Fixed
- Path for require_once

## [1.4.2] - 2020-05-15
### Changed
- Settings should open in default target

## [1.4.1] - 2019-11-19
### Fixed
- Invalid return type in Hookable interface

## [1.4.0] - 2019-09-26
### Added
- SlimPlugin - abstract class with only most important plugin elements
- AbstractPlugin - docs and cleaning
- Activateable - interface to tag plugin to hook into activation hook
- Deactivateable - interface to tag plugin to hook into deactivation hook
- Conditional - interface to tag classes that should be instantiated/hooked only in given state
### Changed
- WordpressFilterStorage - store plugin using WordPress filter system
- target blank in default plugin links
### Fixed
- Fixed assets and plugin url issues

## [1.3.4] - 2019-09-26
### Fixed
- Gitlab.ci

## [1.3.3] - 2019-09-24
### Added
- Support URL

## [1.3.2] - 2019-09-18
### Fixed
- Forgotten classes
- Fixed require_once

## [1.3.0] - 2019-09-18
### Added
- Plugin classes from wp-requirements

## [1.2.0] - 2019-06-26
### Added
- InfoActivationBuilder with capability to set info if plugin subscription is active
