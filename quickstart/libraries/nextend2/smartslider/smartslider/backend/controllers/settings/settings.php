<?php

class N2SmartsliderBackendSettingsController extends N2SmartSliderController {

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.Settings',
            'models.Sliders'
        ), 'smartslider');
    }

    public function actionDefault() {

        if ($this->validatePermission('smartslider_config')) {

            if (N2Request::getInt('save')) {
                if ($this->validateToken()) {
                    $settingsModel = new N2SmartsliderSettingsModel();
                    if ($settingsModel->save()) {
                        $this->refresh();
                    }
                } else {
                    $this->refresh();
                }
            }

            $this->addViewFile($this->appType->path . '/fragments/', "sidebar-settings", array(), "sidebar");
            $this->addView('default', array(
                "action" => N2Request::getVar("nextendaction")
            ));
            $this->render();

        }
    }

    public function actionItemDefaults() {

        if ($this->validatePermission('smartslider_config')) {

            if (N2Request::getInt('save')) {
                if ($this->validateToken()) {
                    $settingsModel = new N2SmartsliderSettingsModel();
                    if ($settingsModel->saveDefaults(N2Request::getVar('defaults', array()))) {
                        $this->refresh();
                    }
                } else {
                    $this->refresh();
                }
            }

            $this->addViewFile($this->appType->path . '/fragments/', "sidebar-settings", array(), "sidebar");
            $this->addView("defaults");
            $this->render();

        }
    }

    public function actionClearCache() {
        if ($this->validatePermission('smartslider_config')) {
            if ($this->validateToken()) {
                $slidersModel = new N2SmartsliderSlidersModel();
                foreach ($slidersModel->_getAll() AS $slider) {
                    $slidersModel->refreshCache($slider['id']);
                }
                N2Cache::clearGroup('n2-ss-0');
                N2Cache::clearGroup('combined');
                N2Cache::clearAll();
                N2Message::success(n2_('Cache cleared.'));
            }

            $this->redirect(array("settings/default"));
        }
    }

    public function actionFramework() {
        if ($this->canDo('nextend_config')) {

            $data = N2Post::getVar('global');
            if (is_array($data)) {
                if ($this->validateToken()) {
                    N2Settings::setAll($data);
                } else {
                    $this->refresh();
                }
            }


            $this->addViewFile($this->appType->path . '/fragments/', "sidebar-settings", array(
                "appObj" => $this
            ), "sidebar");

            $this->addView("framework");
            $this->render();
        } else {
            $this->noAccess();
        }
    }

    public function actionAviary() {
        if ($this->canDo('nextend_config')) {
            N2Loader::import('libraries.image.aviary');
            $aviary = N2Request::getVar('aviary', false);
            if ($aviary) {
                if ($this->validateToken()) {
                    N2ImageAviary::storeSettings($aviary);
                    N2Message::success(n2_('Saved.'));
                    N2Request::redirect($this->appType->router->createUrl(array("settings/aviary",)));
                } else {
                    $this->refresh();
                }
            }

            $this->addViewFile($this->appType->path . '/fragments/', "sidebar-settings", array(
                "appObj" => $this
            ), "sidebar");

            $this->addView("aviary");
            $this->render();
        }
    }

    public function actionFonts() {
        if ($this->canDo('nextend_config')) {
            $fonts = N2Request::getVar('fonts', false);
            if ($fonts) {
                if ($this->validateToken()) {
                    N2Fonts::storeSettings($fonts);
                    N2Message::success(n2_('Saved.'));
                    N2Request::redirect($this->appType->router->createUrl(array("settings/fonts")));
                } else {
                    $this->refresh();
                }
            }

            $this->addViewFile($this->appType->path . '/fragments/', "sidebar-settings", array(
                "appObj" => $this
            ), "sidebar");

            $this->addView("fonts");
            $this->render();
        }
    }

} 