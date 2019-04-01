<?php

namespace luya\remoteadmin\apis;

/**
 * Site Group Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class SiteGroupController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\remoteadmin\models\SiteGroup';
}