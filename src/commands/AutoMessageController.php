<?php

namespace luya\remoteadmin\commands;

use Yii;
use luya\remoteadmin\Module;
use luya\remoteadmin\models\Site;
use luya\remoteadmin\models\MessageTemplate;
use luya\console\Command;

class AutoMessageController extends Command
{
    public function actionIndex()
    {
        $tpl = MessageTemplate::findOne(['is_default' => true]);
        
        if ($tpl) {
            $message = $tpl->text;
        } else {
            $message = Module::t('message_defaulttext');
        }
        
        // get all sites with recipients
        foreach (Site::find()->where(['not', ['recipient' => null]])->all() as $item) {
            /* @var \luya\remoteadmin\models\Site $item */
            $data = $item->getRemote();
            
            if (!$data['error'] && isset($data['packages_update_timestamp'])) {
                if ($data['packages_update_timestamp'] > $data['last_message_timestamp']) {
                    $text = $item->parseMessageText($message);
                    $addresses = $item->getRecipients();
                    if ($this->interactive) {
                        $this->outputInfo('Recipient(s): ' . implode(",", $addresses));
                        $this->outputInfo('Message: ' . $text);
                        if (!$this->confirm('Would you like to send this message?')) {
                            continue;
                        }
                    }
                }
            }
        }
    }
}