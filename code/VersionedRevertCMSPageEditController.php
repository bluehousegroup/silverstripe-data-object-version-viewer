<?php

class VersionedRevertCMSPageEditController extends Extension
{
    private static $allowed_actions = array(
        'HistoryForm',
    );

    public function HistoryForm($request = null)
    {
        $form = $this->owner->getEditForm();
        $fields = $form->Fields();

        if($root = $fields->fieldByName('Root'))
        {
            foreach($root->Tabs() as $tab)
            {
                foreach($tab->Fields() as $field)
                {
                    if($field->Type() == 'grid' && $field->getName() == $request->param('OtherID'))
                    {
                        $config = $field->getConfig();
                        $config->removeComponentsByType('GridFieldDetailForm');
                        $config->addComponent(new VersionedRevertDOHistoryForm());
                    }
                }
            }
        }

        $form->setFormAction(str_replace('/EditForm', '/HistoryForm', $form->FormAction()));

        return $form;
    }
}
