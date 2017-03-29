<?php

class VersionedRevertModelAdmin extends Extension
{
    private static $allowed_actions = array(
        'HistoryForm',
    );

    public function HistoryForm($request = null)
    {
        $form = $this->owner->getEditForm();
        $fields = $form->Fields();

        $config = $fields[0]->getConfig();
        // Replace the detail form with our own.
        $config->removeComponentsByType('GridFieldDetailForm');
        $config->addComponent(new VersionedRevertDOHistoryForm());

        $form->setFormAction(str_replace('/EditForm', '/HistoryForm', $form->FormAction()));

        return $form;
    }
}
