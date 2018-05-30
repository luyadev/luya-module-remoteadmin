<?php
namespace luya\remoteadmin;

use luya\base\CoreModuleInterface;
use luya\admin\components\AdminMenuBuilder;

/**
 * Remote Admin Module.
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
final class Module extends \luya\admin\base\Module implements CoreModuleInterface
{
    public $apis = [
        'api-remote-site' => 'luya\remoteadmin\apis\SiteController',
        'api-remote-messagelog' => 'luya\remoteadmin\apis\MessageLogController',
        'api-remote-messagetemplate' => 'luya\remoteadmin\apis\MessageTemplateController',
        'api-remote-billingproduct' => 'luya\remoteadmin\apis\BillingProductController',
        'api-remote-sitebillingproduct' => 'luya\remoteadmin\apis\SiteBillingProductController',
        
    ];
    
    public function getMenu()
    {
        return (new AdminMenuBuilder($this))->node('Remote', 'settings_remote')
            ->group('Daten')
                ->itemRoute('Status', 'remoteadmin/status/index', 'update')
                ->itemApi('Pages', 'remoteadmin/site/index', 'cloud', 'api-remote-site')
            ->group('Message')
                ->itemApi('Templates', 'remoteadmin/message-template/index', 'label', 'api-remote-messagetemplate')
                ->itemApi('History', 'remoteadmin/message-log/index', 'label', 'api-remote-messagelog')
            ->group('Billing')
                ->itemApi('Products', 'remoteadmin/billing-product/index', 'label', 'api-remote-billingproduct')
                ->itemApi('Site Billing Product', 'remoteadmin/site-billing-product/index', 'label', 'api-remote-sitebillingproduct', ['hiddenInMenu' => true]);
                
                
    }
    public static function onLoad()
    {
        self::registerTranslation('remoteadmin', '@remoteadmin/messages', [
            'remoteadmin' => 'remoteadmin.php',
        ]);
    }
    
    /**
     * Remoteadmin
     *
     * @param string $message The message key to translation
     * @param array $params Optional parameters to pass to the translation.
     * @return string The translated message.
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('remoteadmin', $message, $params);
    }
}
