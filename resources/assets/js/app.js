window.Angular = require('angular');
require('angular-elastic');
require('ng-tags-input');
require('ng-currency');
require('angular-sortable-view');
require('angular-selector');
require('angular-sanitize');
require('angularjs-scroll-glue');
require('angular-numeric-input');
require('angular-tooltips');
require('angularjs-slider');
require('ng-dialog');

require('./bootstrap/app');
require('./bootstrap/config');

require('./controllers/settings.merchant');
require('./controllers/settings.general');
require('./controllers/products.show');
require('./controllers/products.edit');
require('./controllers/collections');
require('./controllers/messages');
require('./controllers/comments');
require('./controllers/stream');
require('./controllers/aside');
require('./controllers/order');
require('./controllers/profile');
require('./controllers/nav');

require('./modules/timeago');
require('./modules/anchorScroll');
require('./modules/onFileChange');
require('./modules/loader');
require('./modules/showMore');
require('./modules/money');
require('./modules/ngEnter');
require('./modules/ngHeight');
require('./modules/preloadImage');
require('./modules/toTrusted');
require('./modules/ngFormCommit');
require('./modules/arrayPrototype');
require('./modules/boolean');
require('./modules/confirmClick');