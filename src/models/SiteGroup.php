<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

/**
 * Site Group.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $title
 */
class SiteGroup extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%remote_site_group}}';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-remote-sitegroup';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'title' => 'text',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['title']],
            [['create', 'update'], ['title']],
            ['delete', false],
        ];
    }
}