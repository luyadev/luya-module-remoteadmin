<?php

namespace luya\remoteadmin\apis;

use Yii;
use luya\remoteadmin\models\Site;
use yii\web\NotFoundHttpException;
use luya\remoteadmin\models\MessageLog;
use luya\TagParser;
use luya\admin\models\Config;
use luya\remoteadmin\Module;

/**
 * Site model API.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class SiteController extends \luya\admin\ngrest\base\Api
{
    public $pagination = false;
    
    public $modelClass = 'luya\remoteadmin\models\Site';

    public function prepareIndexQuery()
    {
        return parent::prepareIndexQuery()->andWhere(['!=', 'status', 4]);
    }
    
    /**
     * Send a message from post to a given site id from post.
     * 
     * @since 1.1.0
     */
    public function actionSendMessage()
    {
        $siteId = (int) Yii::$app->request->getBodyParam('siteId');
        $text = Yii::$app->request->getBodyParam('text');
        $subject = Yii::$app->request->getBodyParam('subject');
        
        $model = Site::findOne($siteId);
        
        if (!$model) {
            throw new NotFoundHttpException("Unable to find the given site id.");
        }
        
        $message = $model->parseMessageText($text);
        
        $mail = Yii::$app->mail->compose($subject, $message)->addresses($model->getRecipients());
        
        if ($mail->send()) {
            $log = new MessageLog();
            $log->timestamp = time();
            $log->recipients = implode("; ", $model->getRecipients());
            $log->text = $message;
            $log->site_id = $model->id;
            $log->save(false);
            
            $model->updateAttributes(['last_message_timestamp' => time()]);
            return true;    
        }
        
        return $this->sendArrayError(['message' => $mail->getError()]);
    }
}
