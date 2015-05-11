Data Object Version Viewer
==========================
![Screenshot](https://github.com/bluehousegroup/silverstripe-data-object-version-viewer/blob/master/VersionViewerScreenShot.png)

### Install with Composer  
	composer require bluehousegroup/silverstripe-data-object-version-viewer

## Usage

 - Register the extension class and the css file in your config.yml (see below)
 - Call function addVersionViewer from within getCMSFields on your DataObject
 - Pass a FieldList object and a DataObject object as arguments
 - Function will return FieldList object containing:
   - All fields passed to it in the FieldList argument, within a tab named 'Current'
   - Previous versions of this data object displayed in a tab called 'History'

## Example code

### config.yml

	DataObject:  
	  extensions:  
	    - VersionViewerExtension  
	LeftAndMain:  
	  extra_requirements_css:  
	    - 'silverstripe-data-object-version-viewer/css/styles.css'  

### Within your DataObject class

	class MyDataObject extends DataObject {

		// ... your class code here ...

		public function getCMSFields() {
			$fields = parent::getCMSFields();

			// If this is an exisiting record, add Version Viewer tabs
			if($this->ID) {
				$fields = $this->addVersionViewer($fields, $this);
			}

			return $fields;
		}
	}
