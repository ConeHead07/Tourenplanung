<?php
if ($_REQUEST['root'] == "source"):
?>
[
	{
		"text": "Mitarbeiter",
		"classes": "important",
		"children":
		[
			{
				"text": "Oliver Kowalski"
			},
		 	{
				"text": "Jörg Rong"
			},
		 	{
				"text": "Frank Barthold"
			}
		]
	},
	{
		"text": "Fuhrpark",
		"children":
		[
			{
				"text": "LKW 1"
			},
		 	{
				"text": "LKW 2"
			},
		 	{
				"text": "LKW 3"
			},
		 	{
				"text": "LKW 4"
			},
		 	{
				"text": "LKW 5"
			},
		 	{
				"text": "Rent",
				"hasChildren": true
			}
		]
	},
	{
		"text": "Werkzeuge",
		"children":
		[
			{
				"text": "XYZ-Schlüssel"
			},
		 	{
				"text": "Bohrmaschine"
			},
		 	{
				"text": "Kreissäge"
			},
		 	{
				"text": "Leiter"
			},
		 	{
				"text": "Scanner"
			}
		]
	},
	{
		"text": "Material",
		"id": "36",
		"hasChildren": true
	}
]
<?php else: sleep(1); 
// Nachladen
?>

[
	{
		"text": "RentLKW-Spezial",
		"expanded": true,
		"children":
		[
			{
				"text": "xyz1"
			},
		 	{
				"text": "xyz2"
			}
		]
	},
	{
		"text": "7,5T"
	},
	{
		"text": "14T"
	},
	{
		"text": "Mit Anhänger"
	}
	
]
<?php endif; ?>