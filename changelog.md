# Trigger Changelog

## 0.3.1 - Unreleased
### Added
- Added `Deployments::EVENT_BEFORE_DEPLOY` and `Deployments::EVENT_CHECK_ELEMENT`.

## 0.3.0 - 2019-11-12
NOTE: this update modifies the database and resets the database schema. A one-time uninstall and reinstall of the plugin will add the new database table. 

### Added
- Added support for Craft sites that use Project Config.
  - Added `%trigger_status` table to the database.
  - Moved `shouldDeploy` from Trigger settings to `%trigger_status` table, as the `status` column.
- Added ability to deploy on element changes.
  - Added `deployOnContentChange` setting to enable this.
- Trigger deployment (or change trigger status) on move of content in a structure and when saving globals.
- Added override of `devMode` check to allow for deployments after content changes while in `devMode`.
  - NOTE: to enable this, create a `./config/trigger.php` file and set `devModeDeploy` to `true`.

### Changed
- Deployments can be triggered for all element types.
- Ignore possible Triggers when element is both a draft or revision.

## 0.2.0 - 2019-10-27
### Added
- Added a Dashboard widget for instantly triggering builds.
- Spiffed things up a bit.

## 0.1.3 - 2019-10-26
### Fixed
- Deploy flag is now set again.

## 0.1.2 - 2019-10-26
### Changed
- `enabled` setting is now `active`, which seems less confusing.
### Added
- More logging.

## 0.1.1 - 2019-10-26
### Added
- Exposed settings, added some logging.

## 0.1.0 - 2019-10-26
### Changed
- Don't trigger deployments for unpublished edits to Drafts and Matrix blocks.

## 0.0.1 - 2019-10-17
### Added
- Initial release.
