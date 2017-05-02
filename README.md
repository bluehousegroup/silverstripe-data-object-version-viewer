Data Object Version Viewer
==========================
![Screenshot](https://github.com/bluehousegroup/silverstripe-data-object-version-viewer/blob/master/VersionViewerScreenShot.png)

### Install with Composer  
	composer require bluehousegroup/silverstripe-data-object-version-viewer

## Usage

 - Extend `silverstripe-versioneddataobjects` to add a 'History' button to a GridField or ModelAdmin
 - View, revert to, and publish a previous versions of a data object

## Example code

The implementation is very similar to the versioneddataobjects module, on which this module depends.

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

To use `VersionedDataObject` records in a GridField, `GridFieldDetailForm` needs to be replaced with `VersionedRevertDODetailsForm`:

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
	$config->addComponent(new VersionedRevertDODetailsForm());

	return $fields;
}

// ...
```

### Versioned DataObjects in a ModelAdmin

```php
class SliceAdmin extends VersionedRevertModelAdmin
{
	private static $menu_title = 'Slices';

	private static $url_segment = 'slice';

	private static $managed_models = [
		'Slice'
	];
}
```
