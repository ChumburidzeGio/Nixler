<?php

return [

    'cartu' => [

        'baseUrl' => 'https://e-commerce.cartubank.ge/servlet/Process3DSServlet/3dsproxy_init.jsp?%s',

        'certPath' => '/app/certificates/CartuBankKEY.pem',

        'countryCode' => 268,

        // 981 - GEL, 840 - USD, 978 - EUR
        'currencyCode' => 981,

        'merchantCity' => 'Tbilisi',

        // Merchant ID in format 0000000XXXXXXXX-00000001
        'merchantId' => '000000008001266-00000001',

        // 01 - GEO,  02 - ENG, 03 - RUS, 04 - DEU, 05 - TUR
        'xDDDSProxyLanguage' => '01',

    ]

];