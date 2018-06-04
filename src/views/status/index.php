<?php
use luya\remoteadmin\Module;
use luya\admin\helpers\Angular;


$defaultText = Module::t('message_defaulttext');
?>
<script>
zaa.bootstrap.register('SitesStatusController', ['$scope', '$http', '$q', 'AdminToastService', function($scope, $http, $q, AdminToastService) {
    
    /* package modal */
    
    $scope.packageModalState = true;
    
    $scope.packageModalData;

    $scope.loadPackageModal = function(site) {
        $http.get('admin/api-remote-site/' + site.id +'?expand=packages,safeUrl&fields=packages,safeUrl').then(function(response) {
            $scope.packageModalData = response.data;
        });
        $scope.packageModalState = false;
    };

    /* message modal */ 
    
    $scope.templates;
    
    $scope.loadMessageTemplates = function() {
        $http.get('admin/api-remote-messagetemplate').then(function(response) {
            angular.forEach(response.data, function(value) {
                if (value.is_default) { 
                    $scope.assignMessageText(value.text, value.id);
                };
            });
            $scope.templates = response.data;
        });
    };

    $scope.assignMessageText = function(text, id) {
        $scope.messageText = text;
        $scope.messageTextId = id;
    };

    $scope.messageTextId = 0;
    
    $scope.messageText = "<?= $defaultText; ?>";
    
    $scope.messageModalState = true;

    $scope.messageModalData;

    $scope.loadMessageModal = function(site) {
        $scope.messageModalData = site;
        $scope.messageModalState = false;
    };

    $scope.sendMessage = function(text) {
        $http.post('admin/api-remote-site/send-message', {siteId: $scope.messageModalData.id, text: text}).then(function(response) {
        	AdminToastService.success('<?= Module::t('message_sent_success'); ?>');
            $scope.messageModalState = true;
        }, function(errors) {
        	AdminToastService.errorArray(errors.data);
        });
    };
    
    /* generic app */
    
    $scope.searchQuery = '';
    
    $scope.sites = [];

    $scope.hasError = false;
    
	$scope.packageInfos = {};

    $scope.orderField = 'safeUrl';
    
    $scope.loadSites = function() {
        $http.get('admin/api-remote-site?expand=safeUrl').then(function(response) {
            $scope.sites = response.data;
          	//INITIAL empty promise
            var promise = $q.when();
            angular.forEach($scope.sites, function(value, key) {
            	promise = promise.then(function () {
                    return $http.get('admin/api-remote-site/' + value.id +'?expand=remote,messageLogs&fields=remote')
                }).then(function (response2) {
                	data = response2.data.remote;
                    $scope.sites[key]['messageLogs'] = response2.data.messageLogs;
                    $scope.sites[key]['status'] = {
                       loading:false, 
                       error:data.error,
                       time:data.app_elapsed_time, 
                       debug: data.app_debug, 
                       debugstyle: data.app_debug_style,
                       exceptions:data.app_transfer_exceptions, 
                       exceptionsstyle: data.app_transfer_exceptions_style,
                       online:data.admin_online_count, 
                       env:data.app_env, 
                       luya:data.luya_version, 
                       luyastyle: data.luya_version_style,
                       yii:data.yii_version,
                       packages_update_timestamp:data.packages_update_timestamp,
                       packages:data.packages
                    };
                    if (data.error) {
                        $scope.hasError = true;
                    }
                });
            });

            return promise;
        });
    };

    $scope.loadSites();
    $scope.loadMessageTemplates();
}]);
</script>
<div ng-controller="SitesStatusController">
    <modal is-modal-hidden="messageModalState" modal-title="<?= Module::t('message_modal_title'); ?>">
        <div ng-if="!messageModalState">
            <form method="post" ng-submit="sendMessage(messageText)">
                <div ng-repeat="tpl in templates" class="form-side">
                    <input type="radio" class="form-check-input" for="{{tpl.id}}" ng-checked="messageTextId == tpl.id" ng-click="assignMessageText(tpl.text, tpl.id)" ng-model="messageTextId" value="{{tpl.id}}" />
                    <label class="form-check-label" for="{{tpl.id}}" ng-click="assignMessageText(tpl.text, tpl.id)">
                     {{tpl.title}}
                    </label>
                </div>
                <div class="form-side">
                    <input type="radio" class="form-check-input" for="default" ng-checked="messageTextId == 0" ng-click="assignMessageText('<?= $defaultText; ?>', 0)" ng-model="messageTextId" ng-value="0" />
                    <label class="form-check-label" for="0" ng-click="assignMessageText('<?= $defaultText; ?>', 0)">
                     <?= Module::t('message_defaulttext_title'); ?>
                    </label>                
                </div>
                <?= Angular::textarea('messageText', Module::t('message_text_label')); ?>
                <p><?= Module::t('message_modal_recipients'); ?></p>
                <button type="submit" value="Submit" class="btn btn-icon btn-save"><?= Module::t('message_modal_submit_label'); ?></button>
            </form>
            
            <div class="list-group mt-3">
              <div ng-repeat="log in messageModalData.messageLogs" class="list-group-item list-group-item-action flex-column align-items-start pb-0">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1">{{ log.timestamp * 1000 | date:"short" }}</h5>
                  <small><?= Module::t('message_modal_history_recipients'); ?></small>
                </div>
                <div compile-html ng-bind-html="log.text | trustAsUnsafe"></div>
              </div>
            </div>
        </div>
    </modal>
	<modal is-modal-hidden="packageModalState" modal-title="<?= Module::t('package_modal_title'); ?>">
        <div ng-if="!packageModalState">
            <table class="table">
	    		<thead>
		    		<tr>
		    		    <th><?= Module::t('package_modal_column_package'); ?></th>
		    		    <th><?= Module::t('package_modal_column_installed'); ?></th>
		    		    <th><?= Module::t('package_modal_column_latest'); ?></th>
		    		    <th><?= Module::t('package_modal_column_released_time'); ?></th>
		    		    <th></th>
		    		</tr>
	    		</thead>
            	<tr ng-repeat="package in packageModalData.packages">
            		<td>{{package.name}}</td>
            		<td style="{{package.versionize}}">{{package.installed}}</td>
            		<td>{{ package.latest }}</td>
            		<td>{{ package.released | date: "medium" }}</td>
            		<td><a ng-show="package.released" ng-href="https://packagist.org/packages/{{package.name}}" target="_blank" class="btn"><?= Module::t('package_modal_column_packagist_button'); ?></a></td>
            	</tr>
            </table>
        </div>
     </modal>
    <div class="card">
    	<div class="card-body">
    		<h3><?= Module::t('status_index_heading'); ?></h3>
            <p><?= Module::t('status_index_intro', ['version' => $currentVersion['version'], 'date' => Yii::$app->formatter->asDate(strtotime($currentVersion['time']))]); ?></p>
    		<input type="text" ng-model="searchQuery" class="form-control" />
    		<div class="table-responsive-wrapper">
	            <table class="table table-striped">
	    			<thead>
		    			<tr>
		    			    <th ng-click="orderField='safeUrl'"><?= Module::t('model_site_url'); ?></th>
		    			    <th ng-click="orderField='site.status.time'"><?= Module::t('status_index_column_time'); ?> *</th>
		    			    <th ng-click="orderField='site.status.debug'">YII_DEBUG</th>
		    			    <th ng-click="orderField='site.status.exceptions'"><?= Module::t('status_index_column_transferexception'); ?></th>
		    			    <th ng-click="orderField='site.status.packages_update_timestamp'"><?= Module::t('composer_vendor_timestamp'); ?></th>
		    			    <th>YII_ENV</th>
		    			    <th>LUYA Version</th>
		    			    <th>Yii Version</th>
		    			    <th></th>
		    			</tr>
	    			</thead>
	    	        <tr ng-repeat="site in sites | filter:searchQuery | orderBy: orderField">
	    	            <td style="text-align:left;"><a ng-href="{{site.safeUrl}}" target="_blank">{{site.safeUrl}}</a></td>
	    	            <td ng-if="!site.status.error && !site.status.loading">{{site.status.time}}</td>
	    	            <td ng-if="!site.status.error && !site.status.loading" style="{{site.status.debugstyle}}">{{site.status.debug}}</td>
	    	            <td ng-if="!site.status.error && !site.status.loading" style="{{site.status.exceptionsstyle}}">{{site.status.exceptions}}</td>
	    	            <td ng-if="!site.status.error && !site.status.loading"><span ng-show="site.status.packages_update_timestamp">{{site.status.packages_update_timestamp * 1000 | date:"short"}}</span></td>
	    	            <td ng-if="!site.status.error && !site.status.loading">{{site.status.env}}</td>
	    	            <td ng-if="!site.status.error && !site.status.loading" style="{{site.status.luyastyle}}">{{site.status.luya}}</td>
	    	            <td ng-if="!site.status.error && !site.status.loading">{{site.status.yii}}</td>
	    	            <td ng-if="site.status.error" colspan="7"><div style="background-color:#FF8A80; padding:4px; color:white;"><?= Module::t('status_index_table_error'); ?></div></td>
	                    <td ng-if="site.status.loading" colspan="7">
	                        <div class="progress">
							    <div class="bg-info progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
							</div>
	                    </td>
	                    <td>
	                    	<button ng-show="site.status.packages_update_timestamp" class="btn btn-sm" type="button" ng-click="loadPackageModal(site)">Packages</button>
                            <button ng-show="site.status.packages_update_timestamp > site.last_message_timestamp && site.recipient" class="btn btn-sm" type="button" ng-click="loadMessageModal(site)">Message</button>
	                    	<a ng-href="{{site.safeUrl}}/admin" target="_blank"><button type="button" class="btn btn-sm"><i class="material-icons">exit_to_app</i></button></a>
	                    </td>
	    	        </tr>
	    		</table>
    		</div>
    		<p><small><?= Module::t('status_index_caching_info'); ?></small></p>
    		<p class="m-0"><small><?= Module::t('status_index_time_info'); ?></small></p>
    	</div>
    </div>
    <div class="card mt-3 text-white bg-danger" ng-if="hasError">
    	<div class="card-body p-3">
    		<p><?= Module::t('stauts_index_error_text'); ?></p>
    		<ul class="m-0">
    		    <li><?= Module::t('status_index_error_1'); ?></li>
    		    <li><?= Module::t('status_index_error_2'); ?></li>
    		    <li><?= Module::t('status_index_error_3'); ?></li>
    		</ul>
    	</div>
    </div>
</div>
