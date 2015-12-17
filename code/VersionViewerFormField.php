<?php

class VersionViewerFormField extends Extension
{
    public $versionViewerVisibility = true;

    public function getVersionViewerVisibility()
    {
        return $this->versionViewerVisibility;
    }

    public function setVersionViewerVisibility(Boolean $is_visible)
    {
        return $this->versionViewerVisibility = $is_visible;
    }
}
