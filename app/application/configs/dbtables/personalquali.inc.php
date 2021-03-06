<?php
// START BASE CONF
return array(
	"ConfName" => "personalquali",
	"Title" => "Personalquali",
	"Description" => "",
	"Src" => "",
	"Db" => "mt_rm",
	"Table" => "mr_personal_qualifikationen",
	"PrimaryKey" => "id",
	"readMinAccess" => 0,
	"insertMinAccess" => 2,
	"updateMinAccess" => null,
	"deleteMinAccess" => null,
	"FormInput" => "",
	"FormPreview" => "",
	"FormRead" => "",
	"Events" => array(
		"onBeforeLoadInput" => "",
		"onBeforeLoadData" => "",
		"onBeforeLoadForm" => "",
		"onBeforeValidate" => "",
		"onBeforeInsert" => "",
		"onBeforeSave" => "",
		"onBeforeUpdate" => "",
		"onBeforeDelete" => "",
		"onBeforeForm" => "",
		"onAfterLoad" => "",
		"onAfterLoadConf" => "",
		"onAfterLoadFields" => "",
		"onAfterLoadInput" => "",
		"onAfterLoadData" => "",
		"onAfterLoadForm" => "",
		"onAfterForm" => "",
		"onAfterValidate" => "",
		"onAfterSave" => "",
		"onAfterDelete" => ""
	),
	"Fields" => array(
		"id" => array(
			"dbField" => "id",
			"key" => "PRI",
			"label" => "Id",
			"listlabel" => "Id",
			"fieldPos" => 1,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "int",
			"size" => "11",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "int",
			"htmlType" => "text",
			"default" => "",
			"required" => true,
			"null" => false,
			"unique" => false,
			"min" => null,
			"max" => null,
			"optionsAsJSON" => "",
			"inputRegExMask" => "",
			"inputRepeatField" => "",
			"inputAttribute" => "",
			"readAttribute" => "",
			"createByFunction" => "",
			"checkByFunction" => "",
			"formatEingabeFunction" => "",
			"formatLesenFunction" => "",
			"editByRuntime" => false,
			"readMinAccess" => 0,
			"insertMinAccess" => null,
			"updateMinAccess" => null,
			"deleteMinAccess" => null
		),
		"mid" => array(
			"dbField" => "mid",
			"key" => "MUL",
			"label" => "Mid",
			"listlabel" => "Mid",
			"fieldPos" => 2,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "int",
			"size" => "11",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "int",
			"htmlType" => "text",
			"default" => "",
			"required" => true,
			"null" => false,
			"unique" => false,
			"min" => null,
			"max" => null,
			"optionsAsJSON" => "",
			"inputRegExMask" => "",
			"inputRepeatField" => "",
			"inputAttribute" => "",
			"readAttribute" => "",
			"createByFunction" => "",
			"checkByFunction" => "",
			"formatEingabeFunction" => "",
			"formatLesenFunction" => "",
			"editByRuntime" => false,
			"readMinAccess" => 0,
			"insertMinAccess" => null,
			"updateMinAccess" => null,
			"deleteMinAccess" => null
		),
		"qualifikation" => array(
			"dbField" => "qualifikation",
			"key" => "",
			"label" => "Qualifikation",
			"listlabel" => "Qualifikation",
			"fieldPos" => 3,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "int",
			"size" => "11",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "int",
			"htmlType" => "text",
			"default" => "",
			"required" => true,
			"null" => false,
			"unique" => false,
			"min" => null,
			"max" => null,
			"optionsAsJSON" => "",
			"inputRegExMask" => "",
			"inputRepeatField" => "",
			"inputAttribute" => "",
			"readAttribute" => "",
			"createByFunction" => "",
			"checkByFunction" => "",
			"formatEingabeFunction" => "",
			"formatLesenFunction" => "",
			"editByRuntime" => false,
			"readMinAccess" => 0,
			"insertMinAccess" => null,
			"updateMinAccess" => null,
			"deleteMinAccess" => null
		),
		"einschraenkungen" => array(
			"dbField" => "einschraenkungen",
			"key" => "",
			"label" => "Einschraenkungen",
			"listlabel" => "Einschraenkungen",
			"fieldPos" => 4,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "int",
			"size" => "11",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "int",
			"htmlType" => "text",
			"default" => "",
			"required" => false,
			"null" => true,
			"unique" => false,
			"min" => null,
			"max" => null,
			"optionsAsJSON" => "",
			"inputRegExMask" => "",
			"inputRepeatField" => "",
			"inputAttribute" => "",
			"readAttribute" => "",
			"createByFunction" => "",
			"checkByFunction" => "",
			"formatEingabeFunction" => "",
			"formatLesenFunction" => "",
			"editByRuntime" => false,
			"readMinAccess" => 0,
			"insertMinAccess" => null,
			"updateMinAccess" => null,
			"deleteMinAccess" => null
		),
		"bemerkungen" => array(
			"dbField" => "bemerkungen",
			"key" => "",
			"label" => "Bemerkungen",
			"listlabel" => "Bemerkungen",
			"fieldPos" => 5,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "int",
			"size" => "11",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "int",
			"htmlType" => "text",
			"default" => "",
			"required" => false,
			"null" => true,
			"unique" => false,
			"min" => null,
			"max" => null,
			"optionsAsJSON" => "",
			"inputRegExMask" => "",
			"inputRepeatField" => "",
			"inputAttribute" => "",
			"readAttribute" => "",
			"createByFunction" => "",
			"checkByFunction" => "",
			"formatEingabeFunction" => "",
			"formatLesenFunction" => "",
			"editByRuntime" => false,
			"readMinAccess" => 0,
			"insertMinAccess" => null,
			"updateMinAccess" => null,
			"deleteMinAccess" => null
		)
	),
	"Joins" => array(

	),
	"Lists" => array(

	)
);
// ENDE BASE CONF
?>