<?php

namespace luya\remoteadmin\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\helpers\ArrayHelper;
use luya\remoteadmin\Module;

/**
 * Billing Product.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $name
 * @property integer $month_cycle
 * @property string $price
 */
class BillingProduct extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remote_billing_product';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-remote-billingproduct';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model_id'),
            'name' => Module::t('model_billing_product_name'),
            'month_cycle' => Module::t('model_billing_product_month_cycle'),
            'price' => Module::t('model_billing_product_price'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['month_cycle', 'price'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function genericSearchFields()
    {
        return ['name', 'price'];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'name' => 'text',
            'month_cycle' => ['selectArray', 'data' => ArrayHelper::generateRange(1, 12, 'month')],
            'price' => 'number',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['name', 'month_cycle', 'price']],
            [['create', 'update'], ['name', 'month_cycle', 'price']],
            ['delete', false],
        ];
    }
}