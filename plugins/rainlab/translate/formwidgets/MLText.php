<?php

namespace RainLab\Translate\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * MLText renders a multi-lingual text field.
 *
 * @author Alexey Bobkov, Samuel Georges
 */
class MLText extends FormWidgetBase
{
    use \RainLab\Translate\Traits\MLControl;

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'mltext';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->initLocale();
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareLocaleVars();

        if ($this->isAvailable) {
            return $this->makePartial('mltext');
        } else {
            return $this->renderFallbackField();
        }
    }

    /**
     * getSaveValue returns an array of translated values for this field
     *
     * @return array
     */
    public function getSaveValue($value)
    {
        return $this->getLocaleSaveValue($value);
    }

    /**
     * {@inheritDoc}
     */
    protected function loadAssets()
    {
        $this->loadLocaleAssets();
    }
}
