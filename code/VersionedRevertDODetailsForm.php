<?php

use Heyday\VersionedDataObjects\VersionedDataObjectDetailsForm;
use Heyday\VersionedDataObjects\VersionedDataObjectDetailsForm_ItemRequest;
use Heyday\VersionedDataObjects\VersionedReadingMode;

class VersionedRevertDODetailsForm extends VersionedDataObjectDetailsForm {

}

class VersionedRevertDODetailsForm_ItemRequest extends VersionedDataObjectDetailsForm_ItemRequest {

    private static $allowed_actions = array(
        'edit',
        'view',
        'history',
        'ItemEditForm',
        'ItemHistoryForm',
    );

    private static $url_handlers = array(
        'history//$VersionID' => 'history',
        '$Action!' => '$Action',
        '' => 'edit',
    );

    public function ItemEditForm() {
        $form = parent::ItemEditForm();

        $form->Actions()->push(FormAction::create('goHistory','History'));

        return $form;
    }

    public function goHistory($data, $form) {
        $controller = Controller::curr();
        $controller->getResponse()->addHeader("X-Pjax", "Content");
        $controller->redirect(Controller::join_links($this->Link(), 'history'));
    }

    /**
     * @return Form
     */
    public function ItemHistoryForm() {
        VersionedReadingMode::setStageReadingMode();

        $selectedVersionId = $this->request->param('VersionID');

        $fields = $this->component->getFields();
        if(!$fields) $fields = $this->record->getCMSFields();
        $fields = $fields->makeReadonly();

        $actions = new FieldList();

        $form = new Form(
            $this,
            'ItemHistoryForm',
            $fields,
            $actions,
            $this->component->getValidator()
        );

        // get the selected version
        if ($selectedVersionId) {
            $selectedVersion = Versioned::get_version($this->record->ClassName, $this->record->ID, $selectedVersionId);
        }
        else {
            // no version parameter so get the current version.
            $selectedVersion = $this->record;
            $selectedVersionId = $selectedVersion->Version;
        }

        $actions->push(
            FormAction::create(
                'goBackToEdit',
                'Back to Edit'
                )
                ->setUseButtonTag(true)
                ->setAttribute('data-icon', 'back')
            );

        $actions->push(
            $rollback = FormAction::create(
                'goRevert',
                'Revert to this version'
                )
                ->setUseButtonTag(true)
            );

        // This is hidden with CSS and triggered by the select links using jQuery.
        $actions->push(
            $selectbtn = FormAction::create(
                'goSelectVersion',
                'Select Version'
                )
            );

        // Don't allow rollback to the version we're already on.
        if ($selectedVersionId == $this->record->Version) {
            $rollback->setReadonly(true);
        }

        $form->loadDataFrom($selectedVersion, Form::MERGE_DEFAULT);

        $versions = $this->record->allVersions();

        // Build an ArrayList of history ready for rendering in a template.
        $historyList = ArrayList::create();

        foreach($versions as $version) {

            $publishedby = Member::get()->byId($version->PublisherID);
            $authoredby = Member::get()->byId($version->AuthorID);

            $lastEditDT = new SS_Datetime();
            $lastEditDT->setValue($version->LastEdited);

            $historyList->push (
                ArrayData::create(array(
                    'version' => $version->Version,
                    'published_status' => $version->WasPublished ? 'Published' : 'Draft',
                    'published_by' => $publishedby ? $publishedby->getName() : '',
                    'authored_by' => $authoredby ? $authoredby->getName() : '',
                    'last_edit_dt' => $lastEditDT,
                    'is_selected' => $version->Version == $selectedVersionId,
                ))
            );
        }

        $data = new ArrayData(array(
            'historyList' => $historyList,
            'url' => '/' . Controller::join_links($this->Link(), 'history')
            ));

        // render the list and add it to the form as literal html.
        $fields->push(
            LiteralField::create('historyList', $data->renderWith('HistoryList'))
        );

        // we need this for the rollback action.
        $fields->push(
            HiddenField::create('version_id', '', $selectedVersionId)
            );

        // TODO Coupling with CMS
        $toplevelController = $this->getToplevelController();
        // if($toplevelController && $toplevelController instanceof LeftAndMain) {
            // Always show with base template (full width, no other panels),
            // regardless of overloaded CMS controller templates.
            // TODO Allow customization, e.g. to display an edit form alongside a search form from the CMS controller
            $form->setTemplate('LeftAndMain_EditForm');
            $form->addExtraClass('cms-content cms-edit-form center');
            $form->setAttribute('data-pjax-fragment', 'CurrentForm Content');
            if($form->Fields()->hasTabset()) {
                $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
                $form->addExtraClass('cms-tabset');
            }

            $form->Backlink = $this->getBackLink();
        // }

        $cb = $this->component->getItemEditFormCallback();
        if($cb) $cb($form, $this);
        $this->extend("updateItemHistoryForm", $form);

        VersionedReadingMode::restoreOriginalReadingMode();

        return $form;
    }

    public function history($request) {
        $controller = $this->getToplevelController();
        $form = $this->ItemHistoryForm($this->gridField, $request);

        $return = $this->customise(array(
            'Backlink' => $controller->hasMethod('Backlink') ? $controller->Backlink() : $controller->Link(),
            'ItemEditForm' => $form,
        ))->renderWith($this->template);

        if($request->isAjax()) {
            return $return;
        } else {
            // If not requested by ajax, we need to render it within the controller context+template
            return $controller->customise(array(
                // TODO CMS coupling
                'Content' => $return,
            ));
        }
    }

    public function goRevert($data, $form) {
        // this does basically the same as page revert.
        VersionedReadingMode::setStageReadingMode();

        $selectedVersionId = $data['version_id'];
        $this->record->doRollbackTo($selectedVersionId);

        VersionedReadingMode::restoreOriginalReadingMode();

        $controller = $this->getToplevelController();
        $controller->getResponse()->addHeader("X-Pjax", "Content");
        $controller->redirect(Controller::join_links($this->Link(), 'history'));
    }

    public function goBackToEdit($data, $form) {
        $controller = $this->getToplevelController();
        $controller->getResponse()->addHeader("X-Pjax", "Content");
        $controller->redirect(Controller::join_links($this->Link(), 'edit'));
    }

    public function goSelectVersion($data, $form) {
        $controller = $this->getToplevelController();
        $controller->getResponse()->addHeader("X-Pjax", "Content");
        $url = Controller::join_links($this->Link(), 'history');

        // Add the version to the end of the URL if we're selecting other than the current version.
        if (isset($data['vid']) && $data['vid'] != $this->record->Version) {
            $url = Controller::join_links($url, $data['vid']);
        }

        $controller->redirect($url);
    }
}
