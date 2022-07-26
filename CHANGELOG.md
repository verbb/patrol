# Changelog

## 5.0.0 - 2022-07-26

### Changed
- Now requires PHP `8.0.2+`.
- Now requires Craft `4.0.0+`.
- Renamed `verbb\patrol\models\SettingsModel` to `verbb\patrol\models\Settings`.
- Renamed `verbb\patrol\services\PatrolService` to `verbb\patrol\services\Service`.

## 4.0.1 - 2022-07-26

### Fixed
- Fix incorrect return value when detecting requested IP address.

## 4.0.0 - 2022-07-26

> {note} The plugin’s package name has changed to `verbb/patrol`. Patrol will need be updated to 4.0 from a terminal, by running `composer require verbb/patrol && composer remove selvinortiz/patrol`.

### Changed
- Migration to `verbb/patrol`.
- Now requires Craft 3.7+.

## 3.1.3 - 2019-12-19

### Fixed
- Fixed issue [#16] where `sslRoutingBaseUrl` was causing issue when set to `empty` or `/`

[#16]: https://github.com/selvinortiz/craft-plugin-patrol/issues/16

## 3.1.2 - 2019-11-23

### Fixed
- Fixed issue [#12] where the requesting IP could not be determined if behind some proxies
- Fixed critical issue where Patrol would explode on sites without a proper `baseUrl` set

[#12]: https://github.com/selvinortiz/craft-plugin-patrol/issues/12

## Updated
- Updated default value of baseUrl to `app.request.hostInfo`
- Updated documentation for baseUrl

> {note} Thank you, Simon Davies and Chris Rowe for your feedback and PRs

## 3.1.1 - 2019-08-25

### Fixed
- Fixed typo in previous CHANGELOG entry
- Fixed an issue with plugin store versioning (maybe)

### Updated
- Updated composer dependencies

## 3.1.0 - 2019-08-22

### Added
- Added the ability to configure the `redirect status code`
- Added the ability to enforce a primary domain
- Added the ability to use **access tokens** for dynamic IP whitelisting
- Added the ability to send a custom HTTP response if no `offline` template is set

### Fixed
- Fixed an issue with improperly registered user permission for Patrol

### Updated
- Updated config settings and APIs to align with Craft

## 3.0.2 - 2017-04-01

### Changed
- Compatibility with newer Craft 3 versions

## 3.0.1 - 2017-02-03

### Fixed
- Fixed an issue where SSL Routing was forced by default

### Changed
- Updated file based settings support

## 3.0.0 - 2017-02-02
- Initial (beta) release for Craft 3
