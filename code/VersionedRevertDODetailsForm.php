<?php

class VersionedRevertDODetailsForm extends Extension
{
    public function updateItemEditForm($form) {
        $form->Actions()->push(FormAction::create(
            'goHistory',
            'History'
        ));
    }

    public function goHistory($data, $form)
    {
        $url = str_replace('/EditForm/', '/HistoryForm/', $this->owner->Link());

        $controller = Controller::curr();
        $controller->getResponse()->addHeader("X-Pjax", "Content");
        $controller->redirect(Controller::join_links($url, 'history'));
    }
}
