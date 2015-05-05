<?php

class VersionViewerDataObject extends DataExtension {

	public function addVersionViewer(FieldList $fields) {
		if($this->owner->hasExtension('Versioned')) {
			// Get the object where this function was called for reference purposes
			$object = $this->owner;

			// Get all tabs in the current model admin and prepart to put within a tabset called "Current"
			$current_tabs = $fields->find('Name', 'Root')->Tabs();
			$fields = FieldList::create(
				TabSet::create("Root",
					$currenttab = TabSet::create("Current"),
					$historytab = TabSet::create("History")->addExtraClass("vertical-tabs")
				)
			);

			// Add all existing tabs to "Current" tabset
			$first = true;
			foreach($current_tabs as $tab) {
				// If we have the getVersionedState function,
				// add a notice regarding the versioned state to the first tab
				// TODO incorporate VersionedDataObjectState extension into this module
				if($first && $object->hasMethod('getVersionedState')) {
					$fields->addFieldToTab("Root.Current." . $tab->title,
						LiteralField::create('VersionedState', 
							'<div class="message notice"><p>' . $object->getVersionedState() . '</p></div>'
						)
					);
					$first = false;
				} 
				$fields->addFieldsToTab("Root.Current." . $tab->title, $tab->Fields());
			}

			// Remove any fields that have VersionViewerVisibility turned off
			foreach($current_tabs as &$tab) {
				foreach($tab->Fields() as $field) {
					// echo '<pre>'.$field->Name.' Viewable? '; print_r($field->versionViewerVisibility);
					if(!$field->versionViewerVisibility && $tab->fieldByName($field->Name)) {
						$tab->removeByName($field->Name);
					}
				}
			}
				 // die();


			// Also, as of now, Versioned does not track has_many or many_many relationships
			// So find fields relating to those relationships, remove them,
			// and add a message regarding this
			$untracked_msg = "";
			foreach($current_tabs as &$tab) {
				foreach($tab->Fields() as $field) {
					$rel_class = $object->getRelationClass($field->Name);
					if($rel_class) {
						if(in_array($rel_class, $object->has_many()) || in_array($rel_class, $object->many_many())) {
							if($tab->fieldByName($field->Name)) {
								$tab->removeByName($field->Name);
								if(!$untracked_msg) {
									// $untracked_msg = '<div class="message notice"><p>Note: the following relationships are not tracked by versioning because they involve multiple records:<br />';
									$untracked_msg = '<p>' . $field->Title();
								} else {
									$untracked_msg .= "<br />" . $field->Title();
								}
							}
						}
					}
				}
			}
			if($untracked_msg) {
				$untracked_msg .= '</p>';
			}

			// Get all past versions of this data object and put the relevant data in a set of tabs
			// within a tabset called "History"
			$versions = $object->allVersions();
			foreach($versions as $version) {
				// Get a record of this version of the object
				$record = Versioned::get_version($object->ClassName, $object->ID, $version->Version);

				// Make a set of read-only fields for use in assembling the History tabs
				$read_fields = $current_tabs->makeReadonly();

				// Make a form using the relevant fields and load it with data from this record
				$form = new Form($object, "old_version", $read_fields, $object->getCMSActions());
				$form->loadDataFrom($record);

				// Add the version number to each field name so we don't have duplicate field names
				if($form->fields->dataFields()) {
					foreach($form->fields->dataFields() as $field) {
						$field->Name = $field->Name . "version" . $version->Version;
					}
				}

				// Generate some heading strings describing this version
				$was_published = $version->WasPublished ? "P" : "D";
				$was_published_full = $version->WasPublished ? "Published" : "Saved as draft";

				$publishedby = Member::get()->byId($version->PublisherID);
				$authoredby = Member::get()->byId($version->AuthorID);

				$publisher_heading = "";
				$author_heading = "";

				if($publishedby) {
					$publisher_heading = " by " . $publishedby->getName();
				}

				if($authoredby) {
					$author_heading = " (Authored by " . $authoredby->getName() . ")";
				}

				$up_date = new SS_Datetime('update');
				$up_date->setValue($version->LastEdited);

				$nice_date = $up_date->FormatFromSettings();
				$tab_title = $version->Version . " - " . $nice_date . " (" . $was_published . ")";
				$latest_version_notice = "";
				if($version->isLatestVersion()) {
					$latest_version_notice = " (latest version)";
				}
				$tab_heading = "<div class='message notice'><p><strong>Viewing version " . $version->Version . $latest_version_notice . ".</strong><br>" . $was_published_full . $publisher_heading . " on " . $nice_date . $author_heading . "</p></div>";

				// Add fields to a tab headed with a description of this version
				$fields->addFieldsToTab('Root.History.' . $tab_title, LiteralField::create('versionHeader'.$version->Version, $tab_heading));
				$fields->addFieldsToTab('Root.History.' . $tab_title, $form->fields);
				// Add notice regarding untracked relationships
				if($untracked_msg) {
					$fields->addFieldsToTab('Root.History.' . $tab_title, HeaderField::create('untrackedMessageHeader'.$version->Version, 'Note: the relationships listed below are not tracked by versioning because they involve multiple records', 4));
					$fields->addFieldsToTab('Root.History.' . $tab_title, LiteralField::create('untrackedMessage'.$version->Version, $untracked_msg));
					// $fields->addFieldsToTab('Root.History.' . $tab_title, LiteralField::create('untrackedMessage'.$version->Version, $untracked_msg));
				}
			}
		}
		return $fields;
	}
}