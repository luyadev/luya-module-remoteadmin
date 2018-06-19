<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\remoteadmin\Module;

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
            'id' => Module::t('model_id'),
            'billing_product_id' => Module::t('model_site_billing_product_billing_product_id'),
            'site_id' => Module::t('model_site_billing_product_site_id'),
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