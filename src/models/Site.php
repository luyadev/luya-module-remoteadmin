<?php

namespace luya\remoteadmin\models;

use yii\helpers\Json;
use Curl\Curl;
use luya\helpers\Url;
use luya\traits\CacheableTrait;
use luya\admin\ngrest\base\NgRestModel;
use luya\remoteadmin\Module;
use luya\helpers\StringHelper;
use luya\admin\traits\SoftDeleteTrait;
use luya\admin\ngrest\plugins\CheckboxRelationActiveQuery;
use luya\TagParser;

/**
 * This is the model class for table "remote_site".
 *
 * @property int $id
 * @property string $token
 * @property string $url
 * @property int $auth_is_enabled
 * @property string $auth_user
 * @property string $auth_pass
 * @property string $recipient
 * @property int $last_message_timestamp
 * @property int $is_deleted
 * @property int $billing_start_timestamp
 * @property int $status
 * @property int $auto_update_message
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Site extends NgRestModel
{
    use CacheableTrait;
    use SoftDeleteTrait;
    
    public $adminBillingProducts = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remote_site';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->on(self::EVENT_AFTER_UPDATE, function() {
            $this->deleteHasCache($this->getCacheKey());
        });
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token', 'url'], 'required'],
            [['auth_is_enabled', 'last_message_timestamp', 'is_deleted', 'billing_start_timestamp', 'status', 'auto_update_message', 'group_id'], 'integer'],
            [['token', 'url', 'auth_user', 'auth_pass'], 'string', 'max' => 120],
            [['recipient'], 'string', 'max' => 255],
            [['adminBillingProducts'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model_id'),
            'token' => Module::t('model_site_token'),
            'url' => Module::t('model_site_url'),
            'auth_is_enabled' => Module::t('model_site_auth_is_enabled'),
            'auth_user' => Module::t('model_site_auth_user'),
            'auth_pass' => Module::t('model_site_auth_pass'),
            'recipient' => Module::t('model_site_recipient'),
            'last_message_timestamp' => Module::t('model_site_last_message_timestamp'),
            'is_deleted' => Module::t('model_site_is_deleted'),
            'billing_start_timestamp' => Module::t('model_site_billing_start_timestamp'),
            'status' => Module::t('model_site_status'),
            'adminBillingProducts' => Module::t('model_site_adminBillingProducts'),
            'auto_update_message' => Module::t('model_site_auto_update_message'),
            'group_id' => 'Group',
        ];
    }
    
    /**
     * Get an array of recipients based on the {{$recipients}} variable.
     * 
     * @return array
     * @since 1.1.0
     */
    public function getRecipients()
    {
        $recipients = str_replace([",", " "], ";", $this->recipient);
        
        return StringHelper::explode($recipients, ';', true, true);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'recipient' => Module::t('model_site_recipient_hint'),
            'billing_start_timestamp' => Module::t('model_site_billing_start_timestamp_hint'),
            'auto_update_message' => Module::t('model_site_auto_update_message_hint'),
        ];
    }
    
    /**
     * @return BillingProduct
     */
    public function getBillingProducts()
    {
        return $this->hasMany(BillingProduct::class, ['id' => 'billing_product_id'])->viaTable(SiteBillingProduct::tableName(), ['site_id' => 'id']);
    }

    /**
     * Parse a given text and replace the predefined variables.
     * 
     * @param string $text
     * @return string A markdown and variable parsed text.
     * @since 1.1.0
     */
    public function parseMessageText($text)
    {
        return TagParser::convertWithMarkdown(strtr($text, [
            '{{timestamp}}' => strftime("%c", $this->getRemote()['packages_update_timestamp']),
            '{{domain}}' => $this->getSafeUrl(),
        ]));
    }
    
    /**
     * @inheritdoc
     */
    public function genericSearchFields()
    {
        return ['url'];
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-remote-site';
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'token' => ['text', 'encoding' => false],
            'url' => 'text',
            'status' => ['selectArray', 'data' => [1 => Module::t('model_site_status_1'), 2 => Module::t('model_site_status_2'), 3 => Module::t('model_site_status_3'), 4 => Module::t('model_site_status_4')]],
            'recipient' => 'text',
            'billing_start_timestamp' => 'date',
            'last_message_timestamp' => 'datetime',
            'auto_update_message' => 'toggleStatus',
            'auth_is_enabled' => 'toggleStatus',
            'auth_user' => ['text', 'condition' => '{auth_is_enabled}==1'],
            'auth_pass' => ['password', 'condition' => '{auth_is_enabled}==1'],
            'group_id' => ['selectModel', 'modelClass' => SiteGroup::class, 'labelField' => ['title']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestExtraAttributeTypes()
    {
        return [
            'adminBillingProducts' => [
                'class' => CheckboxRelationActiveQuery::class,
                'query' => $this->getBillingProducts(),
                'labelField' => ['name'],
            ]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['url', 'group_id', 'status', 'last_message_timestamp', 'auto_update_message']],
            [['create', 'update'], ['group_id', 'url', 'token', 'status', 'recipient', 'billing_start_timestamp', 'last_message_timestamp', 'auto_update_message','adminBillingProducts', 'auth_is_enabled', 'auth_user', 'auth_pass', ]],
            ['delete', true],
        ];
    }

    /**
     * @since 2.0.0
     */
    public function ngRestGroupByField()
    {
        return 'group_id';
    }
    
    /**
     * Get group relation
     * 
     * @since 2.0.0
     */
    public function getGroup()
    {
        return $this->hasOne(SiteGroup::class, ['id' => 'group_id']);
    }

    /**
     * Ensure the input URL.
     *
     * @return string
     */
    public function getEnsuredUrl()
    {
        return Url::ensureHttp(Url::trailing($this->url));
    }
    
    /**
     * Get clickable url.
     *
     * @return string
     */
    public function getSafeUrl()
    {
        return rtrim($this->url, '/');
    }
    
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['remote', 'safeUrl', 'packages', 'messageLogs', 'adminBillingProducts'];
    }
    
    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getMessageLogs()
    {
        return $this->hasMany(MessageLog::class, ['site_id' => 'id']);
    }
    
    /**
     * Return all packages
     * 
     * @return array
     */
    public function getPackages()
    {
    	$pkgs = [];
    	foreach ($this->getRemote()['packages'] as $pkg) {
    		$name = $pkg['package']['name'];
    		$remote = $this->getPackageVersion($name);
    		$version = $remote['version'] ? $remote['version'] : null;
    		$pkgs[$name] = [
    			'name' => $name,
    			'installed' => $pkg['package']['version'],
    			'latest' => $version,
    			'released' => $remote['time'] ? $remote['time'] : null,
    			'versionize' => $this->versionize($pkg['package']['version'], $version),
    		];
    	}
    	
    	return $pkgs;
    }
    
    /**
     * Generate cache key array.
     * 
     * @return array
     * @since 1.0.3
     */
    public function getCacheKey()
    {
        return [__CLASS__, $this->getEnsuredUrl()];
    }
    
    /**
     * Get the remote data.
     *
     * @return array|boolean
     */
    public function getRemote()
    {
        return $this->getOrSetHasCache($this->getCacheKey(), function () {
            $curl = new Curl();
            if ($this->auth_is_enabled) {
                $curl->setBasicAuthentication($this->auth_user, $this->auth_pass);
            }
            $curl->get($this->getEnsuredUrl(). 'admin/api-admin-remote?token=' . sha1($this->token));
            $data = $curl->isSuccess() ? Json::decode($curl->response) : false;
            $curl->close();
            
            if ($data) {
                $data['app_elapsed_time'] = round($data['app_elapsed_time'], 2);
                $data['app_debug_style'] = $this->colorize($data['app_debug'], true);
                $data['app_debug'] = $this->textify($data['app_debug']);
                $data['app_transfer_exceptions_style'] = $this->colorize($data['app_transfer_exceptions']);
                $data['app_transfer_exceptions'] = $this->textify($data['app_transfer_exceptions']);
                $data['luya_version_style'] = $this->versionize($data['luya_version'], $this->getLuyaCore()['version']);
                $data['error'] = false;
            } else {
                $data['error'] = true;
            }
            
            return $data;
        }, (60*60*2)); // cache for 2 hours
    }
    
    /**
     * Boolean expression to On/Off message.
     *
     * @param string $value
     * @return string
     */
    public function textify($value)
    {
        return !empty($value) ? Module::t('model_site_on') :  Module::t('model_site_off') ;
    }
    
    /**
     *
     * @param string $value
     * @param string $invert
     * @return string
     */
    public function colorize($value, $invert = false)
    {
        if ($invert) {
            $state = empty($value);
        } else {
            $state = !empty($value);
        }
        return $state ? 'background-color:#dff0d8' : 'background-color:#f2dede';
    }
    
    /**
     * Compare a the current package version with the latest version.
     * 
     * @param string $version
     * @param string $latestVersion
     * @return string
     */
    public function versionize($version, $latestVersion)
    {
        if ($version == $latestVersion) {
            return 'background-color:#dff0d8';
        } elseif (StringHelper::contains('dev', $version)) {
            return 'background-color:#fcf8e3';
        }
        
        if (version_compare($version, $latestVersion) >= 0) {
        	return 'background-color:#dff0d8';
        }
        
        return 'background-color:#f2dede';
    }
    
    /**
     * Get packge version informations for a given package.
     * @param string $package
     * @return mixed|boolean
     * @since 1.0.1
     */
    public function getPackageVersion($package)
    {
    	return $this->getOrSetHasCache([__CLASS__, 'packagist', 'package', $package], function() use ($package) {
    		$curl = new Curl();
    		$curl->get('https://packagist.org/packages/'.$package.'.json');
    		$json = Json::decode($curl->response);
    		$curl->close();
    		 
    		if (!isset($json['package']['versions'])) {
    			return false;
            }
    		
    		foreach ($json['package']['versions'] as $version => $package) {
    			if (StringHelper::contains(['dev-master', 'dev'], $version) || !is_numeric(substr($version, 0, 1))) {
    				continue;
    			}
    			 
    			return $package;
    		}
    	}, 60*60);
    }
    
    /**
     *
     * @return string
     * @since 1.0.1
     */
    public function getLuyaCore()
    {
    	return $this->getPackageVersion('luyadev/luya-core');
    }
}
