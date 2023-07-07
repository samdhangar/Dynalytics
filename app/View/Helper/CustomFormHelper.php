<?php
App::uses('FormHelper', 'View/Helper');

class CustomFormHelper extends FormHelper
{
    public $helpers = array('Form', 'Html');
    private $languageField = array();
    private $l10n;
    private $setting = array();

    public function __construct(View $View, $settings = array())
    {
        parent::__construct($View, $settings);
    }

    public function create($model = null, $options = array())
    {
        if (isset($options['id'])) {
            $this->setting['id'] = $options['id'];
        } else {
            $domId = isset($options['action']) ? $options['action'] : $this->request['action'];
            $this->setting['id'] = $this->domId($model . '.' . $domId . 'Form');
        }
        if (isset($options['inputDefaults']['dir'])) {
            $this->setting['dir'] = $options['inputDefaults']['dir'];
        }
        $this->setting['model'] = $model;
        return parent::create($model, $options);
    }

    public function input($fieldName, $options = array())
    {
        if (strpos($fieldName, '.') === false) {
            $fieldName = $this->model() . '.' . $fieldName;
        }
        if (empty($options['dir']) && empty($this->setting['dir'])) {
            $options['dir'] = $this->getLanguageDetail('direction');
        }
        $languages = Configure::read('languages');

        if (isset($options['language']) && $options['language'] && count($languages) > 1) {
            $languages = array_unique(array_merge(array(Configure::read('Config.language')), $languages));
            $this->languageField[] = $fieldName;
            $input = '';
            $between = isset($options['between']) ? $options['between'] : '';
            $after = isset($options['after']) ? $options['after'] : '';
            $cnt = 0;
            foreach ($languages as $language) {
                if ($cnt++ > 0) {
                    $options['label'] = false;
                }
                $options['between'] = $between . '<div class="input-group form-group"><span class="input-group-addon">' . $this->getLanguageDetail('language', $language) . '</span>';
                $options['after'] = $after . '</div><div class="error" generated="true" for="' . $this->Html->domId($fieldName . '.' . $language) . '" style="margin-bottom:10px;margin-top:-10px"></div>';

                $options['dir'] = $this->getLanguageDetail('direction', $language);
                $input .= parent::input($fieldName . '.' . $language, $options);
            }
            return $input;
        } else {
            return parent::input($fieldName, $options);
        }
    }

    public function getLanguageDetail($key = null, $language = null)
    {
        $language = empty($language) ? Configure::read('Config.language') : $language;
        $this->l10n = new L10n();
        $languageDetail = $this->l10n->catalog($language);
        if (!empty($languageDetail[$key])) {
            return $languageDetail[$key];
        } else {
            return $languageDetail;
        }
    }

    public function setValidation($arrValidation = array(), $model = null, $formId = null)
    {
        $formId = !empty($formId) ? $formId : $this->setting['id'];
        $model = !empty($model) ? $model : $this->setting['model'];
        $validation = '';
        if (!empty($arrValidation) && !empty($formId)) {
            $validation = "jQuery(document).ready(function(){

                            jQuery('#{$formId}').validate({
                              
                             debug: false,
                             errorClass: 'authError',
                             errorElement: 'div',
                                rules: {
                                    %s
                                },
                                messages:{
                                    %s
                                }
                                });
                            });

            ";
            return $this->Html->scriptBlock(__($validation, $this->__getValidation($arrValidation['Rules'], $model), $this->__getValidation($arrValidation['Messages'], $model, true)));
        }
    }

    private function __getValidation($data, $model, $noComma = false)
    {
        $languages = Configure::read('languages');
        $fieldRulles = array();
        foreach ($data as $field => $rules) {
            if (!empty($rules)) {
                $languageRule = array();
                foreach ($rules as $rule => $val) {
                    if ($noComma) {
                        $languageRule[] = "{$rule}:'{$val} %s'";
                    } else {
                        $languageRule[] = (is_numeric($val)) ? "{$rule}:{$val}" : "{$rule}:'{$val}'";
                    }
                }
                if (count($languages) > 1 && in_array($model . '.' . $field, $this->languageField)) {
                    foreach ($languages as $language) {
                        $fieldRulles[] = "'" . $this->__getFieldName($field, $model, $language) . "'" . ":{" . str_replace('%s', " in " . $this->getLanguageDetail('language', $language) . " language", implode(',', $languageRule)) . "}";
                    }
                } else {
                    $fieldRulles[] = "'" . $this->__getFieldName($field, $model) . "'" . ":{" . str_replace('%s', "", implode(',', $languageRule)) . "}";
                }
            }
        }
        return implode(',', $fieldRulles);
    }

    private function __getFieldName($name, $model, $language = null)
    {
        $name = explode('.', $name);
        if (count($name) == 1) {
            array_unshift($name, $model);
        }
        if (!empty($language)) {
            array_push($name, $language);
        }
        return 'data[' . implode('][', $name) . ']';
    }

    public function submit($caption = null, $options = array())
    {
        parent::submit($caption, $options);
        $options['type'] = 'submit';
        $icon = (!empty($options['icon']) ? ($options['icon'] == 'search' ? 'fa-search' : $options['icon']) : 'fa-check');
        return $this->button('<i class="fa ' . $icon . '"></i> ' . $caption, $options);
    }

    public function setSearchPanel($contents = array(), $onlyForm = false)
    {
        if (empty($contents)) {
            return 1;
        }
        $html = '';
        if (empty($onlyForm)) {
            $html = '<div class="row">';
        }
        if (isLive()) {
            $url = !empty($contents['options']['url']) ? $contents['options']['url'] : '';
            $contents['options']['url'] = str_replace( 'http://', 'https://', $url );
        }
        $html .= $this->Form->create($contents['name'], $contents['options']);
        foreach ($contents['fields'] as $field) {

            $html .= $this->Form->input($field['name'], $field['options']);
        }
        $html .= '<label>&nbsp</label>';
        $html .= '<div class="' . $contents['searchDivClass'] . ' form-group">';
        $contents['search']['options']['icon'] = 'search';
        $html .= $this->Form->submit(__($contents['search']['title']), $contents['search']['options']);
        $html .= $contents['reset'];
        $html .= '</div>';
        $html .= $this->Form->end();
        if (empty($onlyForm)) {
            $html .= '</div>';
        }
        return $html;
    }
}
