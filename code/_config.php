<?php

CMSPageEditController::add_extension('VersionedRevertCMSPageEditController');

Heyday\VersionedDataObjects\VersionedModelAdmin::add_extension('VersionedRevertModelAdmin');

Heyday\VersionedDataObjects\VersionedDataObjectDetailsForm_ItemRequest::add_extension('VersionedRevertDODetailsForm');
