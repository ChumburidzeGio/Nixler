<?php

return [
	'age_ranges' => [
		[14,24], [25,34], [35,44], [45,54], [55,100]
	],
	'edu_levels' => [
		'low', 'middle', 'high'
	],
	'product_categories' => [
            'FS' => [
                'name' => 'Fashion',
                'items' => [
                    'FSCL' => 'Clothing',
                    'FSSB' => 'Shoes & Bags',
                    'FSAC' => 'Accessories',
                ]
            ],
            'BB' => [
                'name' => 'Kids & Babe',
                'items' => [
                    'BBCS' => 'Car Safety Seats',
                    'BBBC' => 'Baby Carriages',
                    'BBKR' => 'Kids room',
                    'BBTY' => 'Toys',
                    'BBBP' => 'Babies & Parents',
                    'BBEA' => 'Education & Art',
                    'BBSC' => 'School',
                ]
            ],
            'EL' => [
                'name' => 'Electronics',
                'items' => [
                    'ELPH' => 'Phones & Accessories',
                    'ELCA' => 'Cameras',
                    'ELAV' => 'Audio & Video',
                    'ELPD' => 'Portable Devices',
                    'ELCG' => 'Consoles & Games',
                    'ELCE' => 'Car Electronics',
                    'ELSC' => 'Scopes',
                    'ELRC' => 'Radio Communication',
                ]
            ],
            'CP' => [
                'name' => 'Computers',
                'items' => [
                    'CPPC' => 'PC',
                    'CPLN' => 'Laptops & Notbooks',
                    'CPPA' => 'Parts & Accessories',
                    'CPPE' => 'Peripherals',
                    'CPNT' => 'Networking',
                    'CPOC' => 'Office Supplies & Consumables',
                    'CPMM' => 'Movies, Music, Software',
                ]
            ],
            'VH' => [
                'name' => 'Vehicles',
                'items' => [
                    'VHCA' => 'Cars',
                    'VHME' => 'Moto & Equipment',
                    'VHTS' => 'Trucks & Special Vehicles',
                    'VHWT' => 'Water Transport',
                    'VHPA' => 'Parts & Accessories',
                ]
            ],
            'RE' => [
                'name' => 'Real Estate',
                'items' => [
                    'REAP' => 'Apartments',
                    'RERM' => 'Rooms',
                    'REHV' => 'Houses, Villas, Cottages',
                    'RELA' => 'Land',
                    'REGC' => 'Garages & Car Places',
                    'RECP' => 'Commercial Property',
                    'REIE' => 'International Real Estate',
                ]
            ],
            'HM' => [
                'name' => 'Home',
                'items' => [
                    'HMAP' => 'Appliances',
                    'HMFD' => 'Furniture & Decor',
                    'HMKD' => 'Kitchen & Dining',
                    'HMTX' => 'Textile',
                    'HMHG' => 'Household Goods',
                    'HMBR' => 'Building & Repair',
                    'HMCG' => 'Country House & Garden',
                ]
            ],
            'BH' => [
                'name' => 'Beauty & Health',
                'items' => [
                    'BHMK' => 'Makeup',
                    'BHFR' => 'Frangances',
                    'BHSC' => 'Skin Care',
                    'BHTA' => 'Tools & Accessories',
                    'BHGL' => 'Glasses',
                ]
            ],
            'SL' => [
                'name' => 'Sport & Leisure',
                'items' => [
                    'SLOD' => 'Outdoors',
                    'SLTR' => 'Tourism',
                    'SLHF' => 'Hunting & Fishing',
                    'SLGF' => 'Gym & Fitness Equipment',
                    'SLGM' => 'Games'
                ]
            ],
            'SG' => [
                'name' => 'Spare Time & Gifts',
                'items' => [
                    'SGTT' => 'Tickets & Tours',
                    'SGBM' => 'Books & Magazines',
                    'SGCL' => 'Collectibles',
                    'SGMI' => 'Musical Instruments',
                    'SGTG' => 'Table Games',
                    'SGGC' => 'Gift Sets & Certificates',
                    'SGGF' => 'Gifts & Flowers',
                    'SGCR' => 'Crafts',
                ]
            ],
            'PT' => [
                'name' => 'Pets',
                'items' => [
                    'PTDG' => 'Dogs',
                    'PTCT' => 'Cats',
                    'PTRD' => 'Rodents',
                    'PTBR' => 'Birds',
                    'PTFS' => 'Fish',
                    'PTOP' => 'Other Pets',
                    'PTFE' => 'Feeding & Accessories',
                ]
            ],
            'FD' => [
                'name' => 'Food',
                'items' => [
                    'FDGR' => 'Grocery',
                    'FDOR' => 'Organic',
                    'FDBF' => 'Baby Food',
                    'FDDO' => 'Food to Order',
                    'FDDR' => 'Drinks',
                ]
            ],
            'SR' => [
                'name' => 'Services',
                'items' => [
                    'SRPV' => 'Photo & Video',
                    'SRFL' => 'Freelancers',
                    'SREV' => 'Events',
                    'SRBH' => 'Beauty & Health',
                    'SRES' => 'Equipment Service',
                    'SRHI' => 'Home Improvement',
                    'SRED' => 'Education',
                    'SRFS' => 'Financial services',
                    'SRCN' => 'Consulting',
                ]
            ],
        ],
    'stats_results' => [
    	// criteria     id           action      target         object     weight     location
    	[  'edu',       'low',       'spend',    'category',    'FSCL',     '100',      'PL'], //PLN121/m
    ]
];