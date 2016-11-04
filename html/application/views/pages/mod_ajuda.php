<div ng-controller="AjudaCtrl">

	<ul class="navtitulo">
		<li><a><i class="glyphicon glyphicon-question-sign"></i> &nbsp;<b>Manual de Ajuda</b></a></li>
	</ul>
	<hr>
	
	<uib-accordion close-others="true" ng-init="open=false">
		<uib-accordion-group is-open="open" ng-repeat="manual in manuais">
			<uib-accordion-heading>
				{{manual.indice}}. {{manual.titulo}} <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': open, 'glyphicon-chevron-right': !open}"></i>
			</uib-accordion-heading>
			
			<div ng-include="tplUrl(manual)"></div>
		</uib-accordion-group>
	</uib-accordion>

</div>