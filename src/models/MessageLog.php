<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\admin\ngrest\plugins\Html;
use luya\remoteadmin\Module;

/**
 * Message Log.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property integer $site_id
 * @property integer $timestamp
 * @property string $recipients
 * @property text $text
 */
class MessageLog extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remote_message_log';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-remote-messagelog';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model_id'),
            'site_id' => Module::t('model_message_log_site_id'),
            'timestamp' => Module::t('model_message_log_timestamp'),
            'recipients' => Module::t('model_message_log_recipients'),
            'text' => Module::t('model_message_log_text'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'timestamp'], 'integer'],
            [['timestamp', 'recipients', 'text'], 'required'],
            [['text'], 'string'],
            [['recipients'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function genericSearchFields()
    {
        return ['recipients', 'text'];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'site_id' => 'number',
            'timestamp' => 'datetime',
            'recipients' => 'text',
            'text' => ['class' => Html::class, 'nl2br' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            [['list'], ['site_id', 'timestamp', 'recipients', 'text']],
            ['delete', false],
        ];
    }
}