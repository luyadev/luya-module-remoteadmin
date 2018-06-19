<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\remoteadmin\Module;

/**
 * Message Template.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property boolean $is_default
 * @property text $title
 * @property text $text
 */
class MessageTemplate extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remote_message_template';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-remote-messagetemplate';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model_id'),
            'title' => Module::t('model_message_template_title'),
            'text' => Module::t('model_message_template_text'),
            'is_default' => Module::t('model_message_template_is_default'),
        ];
    }
    
    public function attributeHints()
    {
        return [
            'text' => Module::t('model_message_template_text_hint'),  
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'title'], 'required'],
            [['text', 'title'], 'string'],
            [['is_default'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function genericSearchFields()
    {
        return ['text'];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'title' => 'text',
            'text' => 'textarea',
            'is_default' => 'toggleStatus',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            [['list', 'create', 'update'], ['title', 'text', 'is_default']],
            ['delete', false],
        ];
    }
}