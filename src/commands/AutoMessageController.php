<?php

namespace luya\remoteadmin\commands;

use Yii;
use luya\remoteadmin\Module;
use luya\remoteadmin\models\Site;
use luya\remoteadmin\models\MessageTemplate;
use luya\console\Command;
use luya\remoteadmin\models\MessageLog;

class AutoMessageController extends Command
{
    public function actionIndex()
    {
        $tpl = MessageTemplate::findOne(['is_default' => true]);
        
        if ($tpl) {
            $message = $tpl->text;
            $subject = $tpl->subject;
        } else {
            $message = Module::t('message_defaulttext');
            $subject = Module::t('message_subject');
        }
        
        // get all sites with recipients
        foreach (Site::find()->andWhere(['not', ['recipient' => null]])->andWhere(['auto_update_message' => true])->all() as $item) {
            /* @var \luya\remoteadmin\models\Site $item */
            $data = $item->getRemote();
            // see if remote data is available
            if (!$data['error'] && isset($data['packages_update_timestamp'])) {
                // check whether latest message timestamp is lower the latest composer vendor update timestamp
                if ($data['packages_update_timestamp'] > $item->last_message_timestamp) {
                    $text = $item->parseMessageText($message);
                    $addresses = $item->getRecipients();
                    if ($this->interactive) {
                        $this->outputInfo('Recipient(s): ' . implode(",", $addresses));
                        $this->outputInfo('Message: ' . $text);
                        if (!$this->confirm('Would you like to send this message?')) {
                            continue;
                        }
                    }
                    
                    if (Yii::$app->mail->compose($subject, $text)->addresses($addresses)->send()) {
                        $this->outputSuccess("Mail has been sent to: " . implode(",", $addresses));
                        
                        // add log entry for message.
                        $log = new MessageLog();
                        $log->timestamp = time();
                        $log->recipients = implode("; ", $addresses);
                        $log->text = $text;
                        $log->site_id = $item->id;
                        $log->save(false);
                        
                        $item->updateAttributes(['last_message_timestamp' => time()]);
                    } else {
                        $this->outputError("Error while sending email: " . Yii::$app->mail->getError());
                    }
                    
                    
                }
            }
        }
    }
}