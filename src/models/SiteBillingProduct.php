<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

/**
 * Site Billing Product.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property integer $billing_product_id
 * @property integer $site_id
 */
class SiteBillingProduct extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remote_site_billing_product';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-remote-sitebillingproduct';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'billing_product_id' => Yii::t('app', 'Billing Product ID'),
            'site_id' => Yii::t('app', 'Site ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['billing_product_id', 'site_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function genericSearchFields()
    {
        return [''];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'billing_product_id' => 'number',
            'site_id' => 'number',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['billing_product_id', 'site_id']],
            [['create', 'update'], ['billing_product_id', 'site_id']],
            ['delete', false],
        ];
    }
}