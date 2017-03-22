<?php

class VersionedRevertDODetailsForm extends Extension {

    public function updateItemEditForm($form) {

        $form->Actions()->push(
            FormAction::create(
                'goHistory',
                'History'
                )
            );
    }

    public function goHistory($data, $form) {
        //get the url parts
        $parts = explode('/', $data['url']);
        $model_name = $parts[6];
        $item = $parts[8];

        //De-pluraize the model name if it is plural
        if(substr($model_name, -1) == 's')
            $model_name = substr($model_name, 0, -1);

        //manually construct the url
        $url = 'admin/' . strtolower($model_name) . '/' . $model_name . '/HistoryForm/field/' . $model_name . '/item/' . $item . '';

        // $url = str_replace('/EditForm/', '/HistoryForm/', $this->owner->Link());
        $controller = Controller::curr();
        $controller->getResponse()->addHeader("X-Pjax", "Content");
        $controller->redirect(Controller::join_links($url, 'history'));
    }
}
