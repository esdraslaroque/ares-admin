<div ng-app="myApp">
        <div ng-controller="myCtrl as vm">
            <p>You are using <b>{{vm.data.browser}}</b> on <b>{{vm.data.os}} ({{vm.data.os_version}})</b>.</p>
            <p>Your <b>{{vm.data.browser}}</b> version is <b>{{vm.data.browser_version}}</b></p>
            <p>Device - <b></b>{{vm.data.device}}</p>
            <p>Complete deviceDetector data - <b></b><pre>{{vm.allData}}<pre></p>
        </div>
    
</div>

<script src="/js/angular.min.js"></script>
<script src="/js/ng-device-detector.min.js"></script>
<script src="/js/re-tree.min.js"></script>
