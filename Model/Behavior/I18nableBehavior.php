<?php
/**
 * Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * i18nable Behavior
 *
 * @package     i18n
 * @subpackage  i18n.models.behaviors
 */
class I18nableBehavior extends ModelBehavior {

/**
 * Default settings
 *
 * @var array
 */
    public $defaults = array(
        'languageField' => 'language_id'
    );

/**
 * Settings array
 *
 * @var array
 */
    public $settings = array();

/**
 * Setup
 *
 * @param AppModel $model
 * @param array $settings
 */
    public function setup(Model $model, $config = array()) {
        if (!isset($this->settings[$model->alias])) {
            $this->settings[$model->alias] = $this->defaults;
        }
        $this->settings[$model->alias] = array_merge($this->settings[$model->alias], is_array($config) ? $config : array());
    }

/**
 * Add language filter
 *
 * @param Model $model
 * @param array $query
 * @return array|bool
 */
    public function beforeFind(Model $model, $query) {
        if (empty($this->settings[$model->alias])) {
            return;
        }
        $settings = $this->settings[$model->alias];
        $language = Configure::read('Config.language');
        if ($model->hasField($settings['languageField']) && (!isset($query['ignoreLanguage']))) {
            if (empty($query['conditions'][$model->alias . '.' . $settings['languageField']])) {
                if (isset($query['language'])) {
                    $language = $query['language'];
                }
                $query['conditions'][$model->alias . '.' . $settings['languageField']] = $language;
            }
        }
        return $query;
    }

/**
 * Set current language
 *
 * @param Model $model
 * @return void
 */
    public function beforeSave(Model $model, $options = array()) {
        if (empty($this->settings[$model->alias])) {
            return;
        }
        $settings = $this->settings[$model->alias];
        $language = Configure::read('Config.language');
        if ($model->hasField($settings['languageField'])) {
            if (empty($model->data[$model->alias][$settings['languageField']])) {
                $model->set(array($settings['languageField'] => $language));
            }
        }
        return true;
    }
}
