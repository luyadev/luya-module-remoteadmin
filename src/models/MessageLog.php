<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

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
            'id' => Yii::t('app', 'ID'),
            'site_id' => Yii::t('app', 'Site ID'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'recipients' => Yii::t('app', 'Recipients'),
            'text' => Yii::t('app', 'Text'),
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
            'timestamp' => 'number',
            'recipients' => 'text',
            'text' => 'textarea',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['site_id', 'timestamp', 'recipients', 'text']],
            [['create', 'update'], ['site_id', 'timestamp', 'recipients', 'text']],
            ['delete', false],
        ];
    }
}