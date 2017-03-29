Data Object Version Viewer
==========================
![Screenshot](https://github.com/bluehousegroup/silverstripe-data-object-version-viewer/blob/master/VersionViewerScreenShot.png)

### Install with Composer  
	composer require bluehousegroup/silverstripe-data-object-version-viewer

## Usage

 - Extend `silverstripe-versioneddataobjects` to add a 'History' button to a GridField or ModelAdmin
 - View, revert to, and publish a previous versions of a data object

## Example code

When required/installed via composer, this module will automatically extend [silverstripe-versioneddataobjects](https://github.com/heyday/silverstripe-versioneddataobjects). Simply follow the versioneddataobjects module's README to get up and running (code examples reproduced below).

Note: To use this on a GridField, you must place the GridField in a tab on Root (as shown in the second code snippet below).

### Within your DataObject class

```php
class Slice extends DataObject
{
	private static $db = [
		'Content' => 'Text'
	];

	private static $has_one = [
		'Parent' => 'SiteTree'
	];

	private static $extensions = [
		'Heyday\VersionedDataObjects\VersionedDataObject'
	];
}
```

To use `VersionedDataObject` records in a GridField, `GridFieldDetailForm` needs to be replaced with `VersionedDataObjectDetailsForm`:

```php
// ...

public function getCMSFields()
{
	$fields = parent::getCMSFields();

	$fields->addFieldToTab(
		'Root.Slices',
		new GridField(
			'Slices',
			'Slices',
			$this->Slices(),
			$config = GridFieldConfig_RelationEditor::create()
		)
	);

	$config->removeComponentsByType('GridFieldDetailForm');
	$config->addComponent(new Heyday\VersionedDataObjects\VersionedDataObjectDetailsForm());

	return $fields;
}

// ...
```

### Versioned DataObjects in a ModelAdmin

```php
class SliceAdmin extends Heyday\VersionedDataObjects\VersionedModelAdmin
{
	private static $menu_title = 'Slices';

	private static $url_segment = 'slice';

	private static $managed_models = [
		'Slice'
	];
}
```

## TO DO
 - Make history form accessible on subtabs, not just those present on Root.
