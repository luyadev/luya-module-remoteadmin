<?php

namespace luya\remoteadmin\apis;

/**
 * Message Template Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class MessageTemplateController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\remoteadmin\models\MessageTemplate';
}