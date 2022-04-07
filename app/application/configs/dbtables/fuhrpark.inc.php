<?php
// START BASE CONF
return array(
	"ConfName" => "mr_fuhrpark",
	"Title" => "Mr_fuhrpark",
	"Description" => "",
	"Src" => "",
	"Db" => "mt_rm",
	"Table" => "mr_fuhrpark",
	"PrimaryKey" => "fid",
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
		"fid" => array(
			"dbField" => "fid",
			"key" => "PRI",
			"label" => "Fid",
			"listlabel" => "Fid",
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
		"standort" => array(
			"dbField" => "standort",
			"key" => "",
			"label" => "Standort",
			"listlabel" => "Ort",
			"fieldPos" => 1,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "varchar",
			"size" => "20",
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
		"kennzeichen" => array(
			"dbField" => "kennzeichen",
			"key" => "",
			"label" => "Fid",
			"listlabel" => "KZ",
			"fieldPos" => 1,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "int",
			"size" => "20",
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
		"hersteller" => array(
			"dbField" => "hersteller",
			"key" => "",
			"label" => "Hersteller",
			"listlabel" => "Hersteller",
			"fieldPos" => 2,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "char",
			"size" => "50",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "char",
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
		"modell" => array(
			"dbField" => "modell",
			"key" => "",
			"label" => "Modell",
			"listlabel" => "Modell",
			"fieldPos" => 3,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "char",
			"size" => "30",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "char",
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
		"fahrzeugart" => array(
			"dbField" => "fahrzeugart",
			"key" => "",
			"label" => "Fahrzeugart",
			"listlabel" => "Fahrzeugart",
			"fieldPos" => 4,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "char",
			"size" => "30",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "char",
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
		"FKL" => array(
			"dbField" => "FKL",
			"key" => "",
			"label" => "FKL",
			"listlabel" => "FKL",
			"fieldPos" => 5,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "varchar",
			"size" => "20",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "char",
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
		"Erstzulassung" => array(
			"dbField" => "Erstzulassung",
			"key" => "",
			"label" => "Erstzulassung",
			"listlabel" => "Erstzulassung",
			"fieldPos" => 6,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "date",
			"size" => "",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "date",
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
		"Anschaffung" => array(
			"dbField" => "Anschaffung",
			"key" => "",
			"label" => "Anschaffung",
			"listlabel" => "Anschaffung",
			"fieldPos" => 7,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "date",
			"size" => "",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "date",
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
		"NaechsteInspektion" => array(
			"dbField" => "NaechsteInspektion",
			"key" => "",
			"label" => "NaechsteInspektion",
			"listlabel" => "NaechsteInspektion",
			"fieldPos" => 8,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "date",
			"size" => "",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "date",
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
		"Kmst" => array(
			"dbField" => "Kmst",
			"key" => "",
			"label" => "Kmst",
			"listlabel" => "Kmst",
			"fieldPos" => 9,
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
		"laderaum_laenge" => array(
			"dbField" => "laderaum_laenge",
			"key" => "",
			"label" => "Laderaum_laenge",
			"listlabel" => "Laderaum_laenge",
			"fieldPos" => 10,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "float",
			"size" => "",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "float",
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
		"laderaum_breite" => array(
			"dbField" => "laderaum_breite",
			"key" => "",
			"label" => "Laderaum_breite",
			"listlabel" => "Laderaum_breite",
			"fieldPos" => 11,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "float",
			"size" => "",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "float",
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
		"laderaum_hoehe" => array(
			"dbField" => "laderaum_hoehe",
			"key" => "",
			"label" => "Laderaum_hoehe",
			"listlabel" => "Laderaum_hoehe",
			"fieldPos" => 12,
			"fieldGroup" => "main",
			"description" => "",
			"type" => "float",
			"size" => "",
			"selectionSrcType" => "",
			"selectionFreeInput" => false,
			"selectionMultiple" => false,
			"sql" => "",
			"file" => "",
			"selectionByFunction" => "",
			"selectionJsCallback" => "",
			"sysType" => "float",
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
		"ladevolumen" => array(
			"dbField" => "ladevolumen",
			"key" => "",
			"label" => "Ladevolumen",
			"listlabel" => "Ladevolumen",
			"fieldPos" => 13,
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
		"nutzlast" => array(
			"dbField" => "nutzlast",
			"key" => "",
			"label" => "Nutzlast",
			"listlabel" => "Nutzlast",
			"fieldPos" => 14,
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
		"kw" => array(
			"dbField" => "kw",
			"key" => "",
			"label" => "Kw",
			"listlabel" => "Kw",
			"fieldPos" => 15,
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
		"sitze" => array(
			"dbField" => "sitze",
			"key" => "",
			"label" => "Sitze",
			"listlabel" => "Sitze",
			"fieldPos" => 16,
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
		)
	),
	"Joins" => array(

	),
	"Lists" => array(

	)
);
// ENDE BASE CONF
?>