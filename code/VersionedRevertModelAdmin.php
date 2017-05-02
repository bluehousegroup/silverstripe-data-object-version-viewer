<?php

use Heyday\VersionedDataObjects\VersionedModelAdmin;

class VersionedRevertModelAdmin extends VersionedModelAdmin {

	public function getEditForm($id = null, $fields = null) {

		$form = parent::getEditForm($id, $fields);

		$form_fields = $form->Fields();
		$grid_field = $form_fields->fieldByName($this->sanitiseClassName($this->modelClass));
		$grid_field->getConfig()->removeComponentsByType('GridFieldDetailForm');
		$grid_field->getConfig()->addComponent(new VersionedRevertDODetailsForm());

		return $form;
	}

}