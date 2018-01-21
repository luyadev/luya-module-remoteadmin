<?php
use luya\remoteadmin\Module;

?>
<script>
zaa.bootstrap.register('SitesStatusController', function($scope, $http, $q) {

    $scope.searchQuery = '';
    
    $scope.sites = [];

    $scope.hasError = false;

	$scope.modalState = true;
    
	$scope.modalData;

	$scope.loadModal = function(data) {
		$http.get('admin/api-remote-site/' + data.id +'?expand=packages,safeUrl&fields=packages,safeUrl').then(function(response) {
			$scope.modalData = response.data;
		});
		$scope.modalState = false;
	};

	$scope.packageInfos = {};

    $scope.loadSites = function() {
        $http.get('admin/api-remote-site?expand=safeUrl').then(function(response) {
            $scope.sites = response.data;
          	//INITIAL empty promise
            var promise = $q.when();
            angular.forEach($scope.sites, function(value, key) {
            	promise = promise.then(function () {
                    return $http.get('admin/api-remote-site/' + value.id +'?expand=remote&fields=remote')
                }).then(function (response2) {
                	data = response2.data.remote;
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
});
</script>
<div  ng-controller="SitesStatusController">
	<modal is-modal-hidden="modalState" modal-title="Package details: {{modalData.safeUrl}}">
        <div ng-if="!modalState">
            <table class="table">
	    		<thead>
		    		<tr>
		    		    <th>Package</th>
		    		    <th>Installed</th>
		    		    <th>Latest</th>
		    		    <th>Released</th>
		    		    <th>Packgist</th>
		    		</tr>
	    		</thead>
            	<tr ng-repeat="package in modalData.packages">
            		<td>{{package.name}}</td>
            		<td style="{{package.versionize}}">{{package.installed}}</td>
            		<td>{{ package.latest }}</td>
            		<td>{{ package.released | date: "medium" }}</td>
            		<td><a ng-href="https://packagist.org/packages/{{package.name}}" target="_blank" class="btn">Packagist</a></td>
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
		    			    <th><?= Module::t('model_site_url'); ?></th>
		    			    <th><?= Module::t('status_index_column_time'); ?> *</th>
		    			    <th>YII_DEBUG</th>
		    			    <th><?= Module::t('status_index_column_transferexception'); ?></th>
		    			    <th>Composer Timestamp</th>
		    			    <th>YII_ENV</th>
		    			    <th>LUYA Version</th>
		    			    <th>Yii Version</th>
		    			    <th></th>
		    			</tr>
	    			</thead>
	    	        <tr ng-repeat="site in sites | filter:searchQuery">
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
	                    	<button ng-show="site.status.packages_update_timestamp" class="btn btn-sm" type="button" ng-click="loadModal(site)">Packages</button>
	                    	<a ng-href="{{site.safeUrl}}/admin" target="_blank"><button type="button" class="btn btn-sm"><i class="material-icons">exit_to_app</i></button></a>
	                    </td>
	    	        </tr>
	    		</table>
    		</div>
    		<p><small><?= Module::t('status_index_caching_info'); ?></small></p>
    		<p><small><?= Module::t('status_index_time_info'); ?></small></p>
    	</div>
    </div>
    <div class="card mt-3" ng-if="hasError">
    	<div class="card-content">
    		<p><?= Module::t('stauts_index_error_text'); ?></p>
    		<ul>
    		    <li><?= Module::t('status_index_error_1'); ?></li>
    		    <li><?= Module::t('status_index_error_2'); ?></li>
    		    <li><?= Module::t('status_index_error_3'); ?></li>
    		</ul>
    	</div>
    </div>
</div>