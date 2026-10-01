<?php
/**
 * cms_patterns.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Central repository of detection patterns for CMS, frameworks, programming
 * languages, and popular plugins. Add new patterns here to extend detection.
 *
 * Each entry is an associative array with:
 *   'name'       → Display name
 *   'icon'       → Emoji or icon identifier
 *   'color'      → Hex color for the badge
 *   'headers'    → HTTP response header patterns  [header_name => regex]
 *   'html'       → HTML body regex patterns       [regex, ...]
 *   'cookies'    → Cookie name patterns           [regex, ...]
 *   'meta'       → Meta tag patterns              [name => regex]
 *   'scripts'    → Script src URL patterns        [regex, ...]
 *   'links'      → Link href URL patterns         [regex, ...]
 * ─────────────────────────────────────────────────────────────────────────────
 */

return [

    // ─── CMS ─────────────────────────────────────────────────────────────────

    'cms' => [

        'WordPress' => [
            'name'    => 'WordPress',
            'domain'  => 'wordpress.org',
            'icon'    => '🔷',
            'color'   => '#21759b',
            'headers' => ['x-powered-by' => '/wordpress/i'],
            'html'    => [
                '/wp-content\//i',
                '/wp-includes\//i',
                '/wp-json\//i',
                '/<link[^>]+s\d+\.w\.org/i',
            ],
            'cookies' => ['/wordpress_/i', '/wp-settings/i'],
            'meta'    => ['generator' => '/wordpress/i'],
            'scripts' => ['/wp-content\//i', '/wp-includes\//i'],
            'links'   => ['/wp-content\//i'],
        ],

        'Drupal' => [
            'name'    => 'Drupal',
            'domain'  => 'drupal.org',
            'icon'    => '💧',
            'color'   => '#0077c0',
            'headers' => [
                'x-generator'    => '/drupal/i',
                'x-drupal-cache' => '/.+/',
                'x-drupal-dynamic-cache' => '/.+/',
            ],
            'html'    => [
                '/\/sites\/default\/files\//i',    // Drupal default file path
                '/\/sites\/all\/themes\//i',       // Drupal 7 theme path (BRACU uses this)
                '/\/sites\/all\/modules\//i',      // Drupal 7 module path
                '/drupal\.js/i',                   // Drupal core JS
                '/drupal-settings-json/i',         // Drupal 8/9/10 settings
                '/Drupal\.settings/i',             // Drupal 7 settings object
                '/data-drupal-/i',                 // Drupal 8+ data attributes
                '/"\/node\/\d+"/i',               // Drupal node URL pattern
            ],
            'cookies' => ['/SESS[a-f0-9]{32}/i', '/drupal/i'],
            'meta'    => ['generator' => '/drupal/i'],
            'scripts' => [
                '/\/misc\/drupal\.js/i',
                '/drupal\.js/i',
                '/\/sites\/all\/modules\//i',
                '/\/sites\/default\/files\//i',
            ],
            'links'   => [
                '/\/sites\/default\/files\//i',
                '/\/sites\/all\/themes\//i',
            ],
        ],

        'Joomla' => [
            'name'    => 'Joomla',
            'domain'  => 'joomla.org',
            'icon'    => '🟠',
            'color'   => '#f44321',
            'headers' => ['x-content-encoded-by' => '/joomla/i'],
            'html'    => [
                '/\/media\/jui\//i',
                '/joomla!/i',
                '/option=com_/i',
                '/\/components\/com_/i',
            ],
            'cookies' => ['/joomla/i'],
            'meta'    => ['generator' => '/joomla/i'],
            'scripts' => ['/\/media\/jui\/js\//i'],
            'links'   => ['/\/components\/com_/i'],
        ],

        'Magento' => [
            'name'    => 'Magento',
            'domain'  => 'magento.com',
            'icon'    => '🛒',
            'color'   => '#f26322',
            'headers' => ['x-powered-by' => '/phusion passenger/i'],
            'html'    => [
                '/Mage\.Cookies/i',
                '/mage\/cookies/i',
                '/\/skin\/frontend\//i',
                '/Magento/i',
                '/mage-messages/i',
            ],
            'cookies' => ['/frontend$/i', '/PHPSESSID/i'],
            'meta'    => ['generator' => '/magento/i'],
            'scripts' => ['/\/skin\/frontend\//i', '/\/js\/mage\//i'],
            'links'   => ['/\/skin\/frontend\//i'],
        ],

        'Shopify' => [
            'name'    => 'Shopify',
            'domain'  => 'shopify.com',
            'icon'    => '🛍️',
            'color'   => '#96bf48',
            'headers' => [
                'x-shopify-stage'                 => '/.+/',
                'x-shopify-shop-api-call-limit'   => '/.+/',
                'x-shopid'                        => '/.+/',
                'x-shardid'                       => '/.+/',
                'powered-by'                      => '/shopify/i',
            ],
            'html'    => [
                '/cdn\.shopify\.com/i',
                '/shopify\.com\/s\//i',
                '/Shopify\.theme/i',
                '/myshopify\.com/i',
            ],
            'cookies' => ['/shopify/i', '/_shopify_/i'],
            'meta'    => [],
            'scripts' => ['/cdn\.shopify\.com/i'],
            'links'   => ['/cdn\.shopify\.com/i'],
        ],

        'Wix' => [
            'name'    => 'Wix',
            'domain'  => 'wix.com',
            'icon'    => '🎨',
            'color'   => '#0c6efc',
            'headers' => ['x-wix-request-id' => '/.+/'],
            'html'    => [
                '/static\.wixstatic\.com/i',
                '/wix\.com/i',
                '/"wixCode"/i',
                '/X-Wix-/i',
            ],
            'cookies' => ['/wix/i'],
            'meta'    => [],
            'scripts' => ['/static\.wixstatic\.com/i'],
            'links'   => ['/static\.wixstatic\.com/i'],
        ],

        'Squarespace' => [
            'name'    => 'Squarespace',
            'domain'  => 'squarespace.com',
            'icon'    => '⬛',
            'color'   => '#222222',
            'headers' => ['x-servedby' => '/squarespace/i'],
            'html'    => [
                '/squarespace\.com/i',
                '/static\.squarespace\.com/i',
                '/squarespace-cdn\.com/i',
                '/Y\.Squarespace/i',
            ],
            'cookies' => ['/squarespace/i', '/SQS/i'],
            'meta'    => ['generator' => '/squarespace/i'],
            'scripts' => ['/static\.squarespace\.com/i'],
            'links'   => ['/static\.squarespace\.com/i'],
        ],

        'PrestaShop' => [
            'name'    => 'PrestaShop',
            'domain'  => 'prestashop.com',
            'icon'    => '🛒',
            'color'   => '#df0067',
            'headers' => [],
            'html'    => [
                '/prestashop/i',
                '/modules\/blockbanner/i',
                '/\/modules\/ps_/i',
            ],
            'cookies' => ['/PrestaShop/i'],
            'meta'    => ['generator' => '/prestashop/i'],
            'scripts' => ['/\/modules\/ps_/i'],
            'links'   => ['/prestashop/i'],
        ],

        'Ghost' => [
            'name'    => 'Ghost',
            'domain'  => 'ghost.org',
            'icon'    => '👻',
            'color'   => '#15171a',
            'headers' => ['x-ghost-cache-status' => '/.+/'],
            'html'    => [
                '/ghost\.io/i',
                '/ghost\/api\//i',
                '/content\/themes\/casper/i',
            ],
            'cookies' => ['/ghost/i'],
            'meta'    => ['generator' => '/ghost/i'],
            'scripts' => ['/ghost\.io/i'],
            'links'   => ['/ghost\.io/i'],
        ],

        'Webflow' => [
            'name'    => 'Webflow',
            'domain'  => 'webflow.com',
            'icon'    => '🌊',
            'color'   => '#4353ff',
            'headers' => ['x-powered-by' => '/webflow/i'],
            'html'    => [
                '/webflow\.com/i',
                '/assets\.website-files\.com/i',
                '/uploads-ssl\.webflow\.com/i',
            ],
            'cookies' => ['/webflow/i'],
            'meta'    => ['generator' => '/webflow/i'],
            'scripts' => ['/webflow\.com/i', '/assets\.website-files\.com/i'],
            'links'   => ['/assets\.website-files\.com/i'],
        ],

        'TYPO3' => [
            'name'    => 'TYPO3',
            'domain'  => 'typo3.org',
            'icon'    => '🔶',
            'color'   => '#ff8700',
            'headers' => ['x-powered-by' => '/typo3/i'],
            'html'    => ['/typo3/i', '/typo3conf/i', '/typo3temp/i'],
            'cookies' => ['/PHPSESSID/i'],
            'meta'    => ['generator' => '/typo3/i'],
            'scripts' => ['/typo3/i'],
            'links'   => ['/typo3/i'],
        ],

        'OpenCart' => [
            'name'    => 'OpenCart',
            'domain'  => 'opencart.com',
            'icon'    => '🛒',
            'color'   => '#23adf0',
            'headers' => [],
            'html'    => ['/route=common\//i', '/opencart/i', '/catalog\/view\/theme\//i'],
            'cookies' => ['/PHPSESSID/i'],
            'meta'    => ['generator' => '/opencart/i'],
            'scripts' => ['/catalog\/view\/javascript\//i'],
            'links'   => ['/catalog\/view\/theme\//i'],
        ],

        'HubSpot CMS' => [
            'name'    => 'HubSpot CMS',
            'domain'  => 'hubspot.com',
            'icon'    => '🟠',
            'color'   => '#ff7a59',
            'headers' => ['x-hs-cache-id' => '/.+/'],
            'html'    => ['/hubspot/i', '/hs-scripts\.com/i', '/js\.hubspot\.com/i'],
            'cookies' => ['/hubspotutk/i', '/__hstc/i', '/__hssc/i'],
            'meta'    => [],
            'scripts' => ['/js\.hubspot\.com/i', '/hs-scripts\.com/i'],
            'links'   => ['/hubspot\.com/i'],
        ],

        // ── Adobe Experience Manager (AEM) ───────────────────────────────────
        'Adobe Experience Manager' => [
            'name'    => 'Adobe Experience Manager',
            'domain'  => 'adobe.com',
            'icon'    => '🔴',
            'color'   => '#fa0f00',
            'headers' => [
                'x-powered-by'  => '/aem|adobe experience manager|cq5|cq-handle/i',
                'x-cq-request-id' => '/.+/',
            ],
            'html'    => [
                '/\/etc\.clientlibs\//i',
                '/\/content\/dam\//i',
                '/\/etc\/designs\//i',
                '/cq5|CQ\.shared|CQ\.WCM/i',
                '/\/libs\/wcm\//i',
                '/\/jcr:content/i',
                '/data-cq-resource-type/i',
                '/data-sly-/i',
                '/granite\.utils/i',
                '/\/content\/[a-z]+\/en\//i',
            ],
            'cookies' => ['/cq-authoring-mode/i', '/login-token/i', '/granite\.token/i'],
            'meta'    => ['generator' => '/adobe experience manager|aem/i'],
            'scripts' => ['/\/etc\.clientlibs\//i', '/\/etc\/clientlibs\//i', '/\/libs\/granite\//i'],
            'links'   => ['/\/etc\.clientlibs\//i', '/\/etc\/designs\//i'],
        ],

        // ── Contentful ───────────────────────────────────────────────────────
        'Contentful' => [
            'name'    => 'Contentful',
            'domain'  => 'contentful.com',
            'icon'    => '📦',
            'color'   => '#2478cc',
            'headers' => ['x-contentful-request-id' => '/.+/'],
            'html'    => ['/cdn\.contentful\.com/i', '/contentful/i', '/ctfl-/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/cdn\.contentful\.com/i', '/contentful/i'],
            'links'   => ['/cdn\.contentful\.com/i'],
        ],

        // ── Sanity.io ────────────────────────────────────────────────────────
        'Sanity' => [
            'name'    => 'Sanity',
            'domain'  => 'sanity.io',
            'icon'    => '🟥',
            'color'   => '#f03e2f',
            'headers' => [],
            'html'    => ['/cdn\.sanity\.io/i', '/sanity\.io/i', '/sanityClient/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/cdn\.sanity\.io/i'],
            'links'   => ['/cdn\.sanity\.io/i'],
        ],

        // ── Sitecore ─────────────────────────────────────────────────────────
        'Sitecore' => [
            'name'    => 'Sitecore',
            'domain'  => 'sitecore.com',
            'icon'    => '🔵',
            'color'   => '#eb1f1f',
            'headers' => ['x-sitecore-requestid' => '/.+/', 'x-powered-by' => '/sitecore/i'],
            'html'    => [
                '/sitecore/i',
                '/\/sitecore\/shell\//i',
                '/\/\/[^"]+\.sitecore\.net/i',
                '/sitecore-item-id/i',
            ],
            'cookies' => ['/SC_ANALYTICS_GLOBAL_COOKIE/i', '/sitecore_userdata/i'],
            'meta'    => ['generator' => '/sitecore/i'],
            'scripts' => ['/\/sitecore\/shell\//i', '/sitecore/i'],
            'links'   => ['/\/sitecore\/shell\//i'],
        ],

        // ── Kentico ──────────────────────────────────────────────────────────
        'Kentico' => [
            'name'    => 'Kentico',
            'domain'  => 'kentico.com',
            'icon'    => '🟦',
            'color'   => '#f05a22',
            'headers' => ['x-powered-by' => '/kentico/i'],
            'html'    => ['/kentico/i', '/CMSPages\//i', '/KenticoAdmin/i'],
            'cookies' => ['/CMSPreferredCulture/i', '/CMSCookieLevel/i'],
            'meta'    => ['generator' => '/kentico/i'],
            'scripts' => ['/kentico/i', '/CMSPages\//i'],
            'links'   => ['/kentico/i'],
        ],

        // ── Craft CMS ────────────────────────────────────────────────────────
        'Craft CMS' => [
            'name'    => 'Craft CMS',
            'domain'  => 'craftcms.com',
            'icon'    => '🔶',
            'color'   => '#e5422b',
            'headers' => ['x-powered-by' => '/craft/i'],
            'html'    => ['/craft-csrf-token/i', '/craftcms/i', '/siteUrl.*craftcms/i'],
            'cookies' => ['/CraftSessionId/i', '/CRAFT_CSRF_TOKEN/i'],
            'meta'    => ['generator' => '/craft/i'],
            'scripts' => ['/cpresources\//i'],
            'links'   => ['/cpresources\//i'],
        ],

        // ── October CMS ──────────────────────────────────────────────────────
        'October CMS' => [
            'name'    => 'October CMS',
            'domain'  => 'octobercms.com',
            'icon'    => '🍂',
            'color'   => '#e8601c',
            'headers' => [],
            'html'    => ['/october.*cms/i', '\/modules\/system\/assets\//i', '\/plugins\/rainlab\//i'],
            'cookies' => ['/october_session/i'],
            'meta'    => ['generator' => '/october cms/i'],
            'scripts' => ['\/modules\/system\/assets\//i'],
            'links'   => ['\/modules\/system\/assets\//i'],
        ],

        // ── Strapi ───────────────────────────────────────────────────────────
        'Strapi' => [
            'name'    => 'Strapi',
            'domain'  => 'strapi.io',
            'icon'    => '🟣',
            'color'   => '#8e75ff',
            'headers' => ['x-powered-by' => '/strapi/i'],
            'html'    => ['/strapi/i'],
            'cookies' => ['/strapi_jwt/i'],
            'meta'    => [],
            'scripts' => ['/strapi/i'],
            'links'   => ['/strapi/i'],
        ],

        // ── Umbraco ──────────────────────────────────────────────────────────
        'Umbraco' => [
            'name'    => 'Umbraco',
            'domain'  => 'umbraco.com',
            'icon'    => '💙',
            'color'   => '#3544b1',
            'headers' => ['x-powered-by' => '/umbraco/i'],
            'html'    => ['/umbraco/i', '\/umbraco_client\//i', '\/umbraco\/js\//i'],
            'cookies' => ['/UMB_UCONTEXT/i', '/UMB-XSRF-TOKEN/i'],
            'meta'    => ['generator' => '/umbraco/i'],
            'scripts' => ['\/umbraco_client\//i', '\/umbraco\/js\//i'],
            'links'   => ['\/umbraco_client\//i'],
        ],

        // ── Contao ───────────────────────────────────────────────────────────
        'Contao' => [
            'name'    => 'Contao',
            'domain'  => 'contao.org',
            'icon'    => '🟤',
            'color'   => '#f47c00',
            'headers' => [],
            'html'    => ['/contao/i', '\/system\/modules\//i', '\/assets\/contao\//i'],
            'cookies' => ['/contao_csrf_token/i'],
            'meta'    => ['generator' => '/contao/i'],
            'scripts' => ['\/assets\/contao\//i'],
            'links'   => ['\/assets\/contao\//i'],
        ],

        // ── DNN (DotNetNuke) ─────────────────────────────────────────────────
        'DNN (DotNetNuke)' => [
            'name'    => 'DNN (DotNetNuke)',
            'domain'  => 'dnnsoftware.com',
            'icon'    => '🟪',
            'color'   => '#007ecf',
            'headers' => ['x-powered-by' => '/asp\.net/i'],
            'html'    => ['/dnn/i', '\/DesktopModules\//i', '\/Portals\/0\//i', '/dotnetnuke/i'],
            'cookies' => ['/DNNPersonalization/i', '/.DOTNETNUKE/i'],
            'meta'    => ['generator' => '/dotnetnuke|dnn/i'],
            'scripts' => ['\/Resources\/Shared\/scripts\//i', '\/DesktopModules\//i'],
            'links'   => ['\/Portals\/0\//i'],
        ],

        // ── SilverStripe ─────────────────────────────────────────────────────
        'SilverStripe' => [
            'name'    => 'SilverStripe',
            'domain'  => 'silverstripe.org',
            'icon'    => '🌿',
            'color'   => '#005a8e',
            'headers' => ['x-powered-by' => '/silverstripe/i'],
            'html'    => ['/silverstripe/i', '\/themes\/simple\//i', 'SecurityID.*silverstripe/i'],
            'cookies' => ['/PHPSESSID/i'],
            'meta'    => ['generator' => '/silverstripe/i'],
            'scripts' => ['\/themes\/.*\/javascript\//i'],
            'links'   => ['\/themes\/.*\/css\//i'],
        ],

        // ── Concrete CMS (Concrete5) ─────────────────────────────────────────
        'Concrete CMS' => [
            'name'    => 'Concrete CMS',
            'domain'  => 'concretecms.com',
            'icon'    => '🧱',
            'color'   => '#0b5394',
            'headers' => [],
            'html'    => ['/concrete5|concretecms/i', '\/packages\/concrete5\//i', '\/concrete\/js\//i'],
            'cookies' => ['/CONCRETE5/i'],
            'meta'    => ['generator' => '/concrete/i'],
            'scripts' => ['\/concrete\/js\//i'],
            'links'   => ['\/concrete\/css\//i'],
        ],

        // ── Sitefinity ───────────────────────────────────────────────────────
        'Sitefinity' => [
            'name'    => 'Sitefinity',
            'domain'  => 'progress.com',
            'icon'    => '🔷',
            'color'   => '#00b2a9',
            'headers' => ['x-powered-by' => '/sitefinity/i'],
            'html'    => ['/sitefinity/i', '\/Telerik\.Sitefinity\./i', '\/SFRes\//i'],
            'cookies' => ['/sf-auth/i', '\.ASPXAUTH/i'],
            'meta'    => ['generator' => '/sitefinity/i'],
            'scripts' => ['\/SFRes\//i'],
            'links'   => ['\/SFRes\//i'],
        ],

        // ── Netlify CMS / Decap CMS ──────────────────────────────────────────
        'Decap CMS' => [
            'name'    => 'Decap CMS',
            'domain'  => 'decapcms.org',
            'icon'    => '🌐',
            'color'   => '#c62a2f',
            'headers' => ['x-nf-request-id' => '/.+/', 'server' => '/netlify/i'],
            'html'    => ['/netlify-cms|decap-cms/i', '\/admin\/config\.yml/i'],
            'cookies' => ['/netlify/i'],
            'meta'    => [],
            'scripts' => ['/netlify-cms/i'],
            'links'   => [],
        ],

        // ── Payload CMS ──────────────────────────────────────────────────────
        'Payload CMS' => [
            'name'    => 'Payload CMS',
            'domain'  => 'payloadcms.com',
            'icon'    => '🚀',
            'color'   => '#0b1622',
            'headers' => [],
            'html'    => ['/payload-cms|payloadcms/i', '\/payload\//i'],
            'cookies' => ['/payload-token/i'],
            'meta'    => [],
            'scripts' => ['\/payload\//i'],
            'links'   => ['\/payload\//i'],
        ],

        // ── Directus ─────────────────────────────────────────────────────────
        'Directus' => [
            'name'    => 'Directus',
            'domain'  => 'directus.io',
            'icon'    => '🔵',
            'color'   => '#6644ff',
            'headers' => ['x-powered-by' => '/directus/i'],
            'html'    => ['/directus/i'],
            'cookies' => ['/directus_session_token/i'],
            'meta'    => [],
            'scripts' => ['/directus/i'],
            'links'   => [],
        ],

        // ── Adobe Commerce (Magento Enterprise) ──────────────────────────────
        'Adobe Commerce' => [
            'name'    => 'Adobe Commerce',
            'domain'  => 'adobe.com',
            'icon'    => '🛍️',
            'color'   => '#ff6100',
            'headers' => [],
            'html'    => [
                '/adobe.*commerce/i',
                '\/pub\/static\/frontend\//i',
                '/Magento_Catalog/i',
                '/data-mage-init/i',
            ],
            'cookies' => ['/form_key/i', '/mage-/i'],
            'meta'    => [],
            'scripts' => ['\/pub\/static\/frontend\//i'],
            'links'   => ['\/pub\/static\/frontend\//i'],
        ],

        // ── ExpressionEngine ─────────────────────────────────────────────────
        'ExpressionEngine' => [
            'name'    => 'ExpressionEngine',
            'domain'  => 'expressionengine.com',
            'icon'    => '🟡',
            'color'   => '#009ddf',
            'headers' => [],
            'html'    => ['/ExpressionEngine/i', '\/system\/ee\//i'],
            'cookies' => ['/exp_last_visit/i', '/exp_tracker/i'],
            'meta'    => ['generator' => '/ExpressionEngine/i'],
            'scripts' => ['\/system\/ee\//i'],
            'links'   => ['\/system\/ee\//i'],
        ],

        // ── MODx ─────────────────────────────────────────────────────────────
        'MODx' => [
            'name'    => 'MODx',
            'domain'  => 'modx.com',
            'icon'    => '🟢',
            'color'   => '#005a8e',
            'headers' => [],
            'html'    => ['/modx/i', '\/assets\/components\//i', '\/connectors\//i'],
            'cookies' => ['/modx_remember_username/i', '/SN4/i'],
            'meta'    => ['generator' => '/modx/i'],
            'scripts' => ['\/assets\/components\//i'],
            'links'   => ['\/assets\/components\//i'],
        ],

        // ── Processwire ──────────────────────────────────────────────────────
        'ProcessWire' => [
            'name'    => 'ProcessWire',
            'domain'  => 'processwire.com',
            'icon'    => '⚡',
            'color'   => '#ef145d',
            'headers' => ['x-powered-by' => '/processwire/i'],
            'html'    => ['/processwire/i', '\/site\/modules\//i', '\/wire\/modules\//i'],
            'cookies' => ['/wire_challenge/i'],
            'meta'    => ['generator' => '/processwire/i'],
            'scripts' => ['\/wire\/templates-admin\//i'],
            'links'   => ['\/wire\/templates-admin\//i'],
        ],

        // ── Plone ────────────────────────────────────────────────────────────
        'Plone' => [
            'name'    => 'Plone',
            'domain'  => 'plone.org',
            'icon'    => '⚫',
            'color'   => '#1d318c',
            'headers' => ['x-powered-by' => '/zope|plone/i'],
            'html'    => ['/plone/i', '\/portal_skins\//i', '\/++resource++\//i'],
            'cookies' => ['/__ac/i', '/_ZopeId/i'],
            'meta'    => ['generator' => '/plone/i'],
            'scripts' => ['\/portal_skins\//i'],
            'links'   => ['\/portal_skins\//i'],
        ],

        // ── Gatsby (static/headless) ─────────────────────────────────────────
        'Gatsby' => [
            'name'    => 'Gatsby',
            'domain'  => 'gatsbyjs.com',
            'icon'    => '🟣',
            'color'   => '#663399',
            'headers' => [],
            'html'    => ['/__gatsby/i', '/gatsby-image/i', '/window\.___gatsby/i', '\/static\/[a-f0-9]+\/[a-f0-9]+\.json/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['\/gatsby-browser\.js/i', '\/commons\.js/i'],
            'links'   => ['\/static\/[a-f0-9]+/i'],
        ],

        // ── Hugo ─────────────────────────────────────────────────────────────
        'Hugo' => [
            'name'    => 'Hugo',
            'domain'  => 'gohugo.io',
            'icon'    => '🐉',
            'color'   => '#ff4088',
            'headers' => [],
            'html'    => [],
            'cookies' => [],
            'meta'    => ['generator' => '/hugo/i'],
            'scripts' => [],
            'links'   => [],
        ],

        // ── Jekyll ───────────────────────────────────────────────────────────
        'Jekyll' => [
            'name'    => 'Jekyll',
            'domain'  => 'jekyllrb.com',
            'icon'    => '💎',
            'color'   => '#cc0000',
            'headers' => [],
            'html'    => ['/jekyll/i'],
            'cookies' => [],
            'meta'    => ['generator' => '/jekyll/i'],
            'scripts' => [],
            'links'   => [],
        ],

        // ── Hexo ─────────────────────────────────────────────────────────────
        'Hexo' => [
            'name'    => 'Hexo',
            'domain'  => 'hexo.io',
            'icon'    => '📝',
            'color'   => '#0e83cd',
            'headers' => [],
            'html'    => ['/hexo/i'],
            'cookies' => [],
            'meta'    => ['generator' => '/hexo/i'],
            'scripts' => [],
            'links'   => [],
        ],

        // ── Eleventy (11ty) ──────────────────────────────────────────────────
        'Eleventy' => [
            'name'    => 'Eleventy',
            'domain'  => '11ty.dev',
            'icon'    => '⚫',
            'color'   => '#222222',
            'headers' => [],
            'html'    => [],
            'cookies' => [],
            'meta'    => ['generator' => '/eleventy/i'],
            'scripts' => [],
            'links'   => [],
        ],

        // ── VuePress ─────────────────────────────────────────────────────────
        'VuePress' => [
            'name'    => 'VuePress',
            'domain'  => 'vuepress.vuejs.org',
            'icon'    => '💚',
            'color'   => '#3eaf7c',
            'headers' => [],
            'html'    => ['/vuepress/i', '\/vuepress\/'],
            'cookies' => [],
            'meta'    => ['generator' => '/vuepress/i'],
            'scripts' => ['\/vuepress\//i'],
            'links'   => ['\/vuepress\//i'],
        ],

        // ── Jimdo ────────────────────────────────────────────────────────────
        'Jimdo' => [
            'name'    => 'Jimdo',
            'domain'  => 'jimdo.com',
            'icon'    => '🟡',
            'color'   => '#41d6a2',
            'headers' => ['server' => '/jimdo/i'],
            'html'    => ['/jimdo/i', '\/a\.jimdo\.com\//i', '\/jimdoeditor\//i'],
            'cookies' => ['/jimdo/i'],
            'meta'    => ['generator' => '/jimdo/i'],
            'scripts' => ['\/a\.jimdo\.com\//i'],
            'links'   => ['\/a\.jimdo\.com\//i'],
        ],

        // ── Blogger ──────────────────────────────────────────────────────────
        'Blogger' => [
            'name'    => 'Blogger',
            'domain'  => 'blogger.com',
            'icon'    => '📰',
            'color'   => '#fc4f08',
            'headers' => [],
            'html'    => ['/blogspot\.com/i', '/blogger\.com/i', '\/feeds\/posts\/default/i'],
            'cookies' => ['/blogger/i'],
            'meta'    => ['generator' => '/blogger/i'],
            'scripts' => ['/blogger\.com/i', '/blogspot\.com/i'],
            'links'   => ['/blogger\.com/i'],
        ],

        // ── Medium ───────────────────────────────────────────────────────────
        // NOTE: Medium is a hosted blogging platform, NOT an installable CMS.
        // Only detect when very strong Medium-specific signals are present.
        'Medium' => [
            'name'    => 'Medium',
            'domain'  => 'medium.com',
            'icon'    => '✍️',
            'color'   => '#000000',
            'headers' => [
                // Medium sets these specific headers on their own platform
                'x-medium-requestid' => '/.+/',
                'x-envoy-upstream-service-time' => '/.+/',
            ],
            'html'    => [
                '/miro\.medium\.com/i',              // Medium CDN — very specific
                '/cdn-client\.medium\.com/i',        // Medium client scripts
                '/medium\.com\/m\/global-identity/i',// Medium embed tracking
                '/"__APOLLO_STATE__".*medium/i',     // Medium Apollo store
            ],
            'cookies' => ['/medium_/i', '/sid=.*medium/i'],
            'meta'    => ['generator' => '/^medium$/i'],
            'scripts' => ['/cdn-client\.medium\.com/i', '/miro\.medium\.com/i'],
            'links'   => ['/miro\.medium\.com/i'],
        ],

        // ── BigCommerce ───────────────────────────────────────────────────────
        'BigCommerce' => [
            'name'    => 'BigCommerce',
            'domain'  => 'bigcommerce.com',
            'icon'    => '🛒',
            'color'   => '#34313f',
            'headers' => ['x-bc-merchantid' => '/.+/'],
            'html'    => ['/bigcommerce/i', '\/product_images\//i', '/BCData/i'],
            'cookies' => ['/SHOP_SESSION_TOKEN/i', '/XSRF-TOKEN/i'],
            'meta'    => [],
            'scripts' => ['/bigcommerce/i'],
            'links'   => ['/bigcommerce/i'],
        ],

        // ── WooCommerce (standalone detection as CMS-like platform) ──────────
        'osCommerce' => [
            'name'    => 'osCommerce',
            'domain'  => 'oscommerce.com',
            'icon'    => '🛒',
            'color'   => '#2e6da4',
            'headers' => [],
            'html'    => ['/oscommerce/i', '\/catalog\/includes\//i'],
            'cookies' => ['/osCsid/i'],
            'meta'    => ['generator' => '/oscommerce/i'],
            'scripts' => ['\/catalog\/includes\//i'],
            'links'   => ['\/catalog\/includes\//i'],
        ],

        // ── CS-Cart ──────────────────────────────────────────────────────────
        'CS-Cart' => [
            'name'    => 'CS-Cart',
            'domain'  => 'cs-cart.com',
            'icon'    => '🛒',
            'color'   => '#73af41',
            'headers' => [],
            'html'    => ['/cs-cart/i', '\/skins\/[^"]+\/customer\//i'],
            'cookies' => [],
            'meta'    => ['generator' => '/cs-cart/i'],
            'scripts' => ['\/js\/tygh\//i'],
            'links'   => ['\/skins\/[^"]+\/customer\//i'],
        ],

        // ── Liferay ──────────────────────────────────────────────────────────
        'Liferay' => [
            'name'    => 'Liferay',
            'domain'  => 'liferay.com',
            'icon'    => '🔵',
            'color'   => '#1d5396',
            'headers' => ['liferay-portal' => '/.+/'],
            'html'    => ['/liferay/i', '\/html\/portal\//i', '\/o\/frontend-js-web\//i'],
            'cookies' => ['/COMPANY_ID/i', '/GUEST_LANGUAGE_ID/i'],
            'meta'    => ['generator' => '/liferay/i'],
            'scripts' => ['\/html\/portal\//i', '\/o\/frontend-js-web\//i'],
            'links'   => ['\/html\/portal\//i'],
        ],

        // ── Storyblok ────────────────────────────────────────────────────────
        'Storyblok' => [
            'name'    => 'Storyblok',
            'domain'  => 'storyblok.com',
            'icon'    => '📖',
            'color'   => '#00b3b0',
            'headers' => [],
            'html'    => ['/storyblok/i', '\/a\.storyblok\.com\//i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['\/a\.storyblok\.com\//i'],
            'links'   => ['\/a\.storyblok\.com\//i'],
        ],

        // ── Prismic ──────────────────────────────────────────────────────────
        'Prismic' => [
            'name'    => 'Prismic',
            'domain'  => 'prismic.io',
            'icon'    => '🟣',
            'color'   => '#484a7a',
            'headers' => [],
            'html'    => ['/prismic\.io/i', '\/cdn\.prismic\.io\//i'],
            'cookies' => ['/io\.prismic\.preview/i'],
            'meta'    => [],
            'scripts' => ['\/cdn\.prismic\.io\//i', '\/static\.cdn\.prismic\.io\//i'],
            'links'   => ['\/cdn\.prismic\.io\//i'],
        ],

    ],

    // ─── PROGRAMMING LANGUAGES ───────────────────────────────────────────────

    'languages' => [

        'PHP' => [
            'name'    => 'PHP',
            'domain'  => 'php.net',
            'icon'    => '🐘',
            'color'   => '#777bb4',
            'headers' => [
                'x-powered-by' => '/php/i',
                'set-cookie'   => '/PHPSESSID/i',
            ],
            'html'    => ['/\.php(\?|"|\'|#| )/i'],
            'cookies' => ['/PHPSESSID/i'],
            'meta'    => [],
            'scripts' => ['/.php$/i'],
            'links'   => ['/.php(\?|"|\')/i'],
        ],

        'Python' => [
            'name'    => 'Python',
            'domain'  => 'python.org',
            'icon'    => '🐍',
            'color'   => '#3572a5',
            'headers' => [
                'x-powered-by'  => '/python|django|flask|fastapi/i',
                'server'        => '/python|gunicorn|uvicorn|waitress/i',
                'x-framework'   => '/django|flask|fastapi/i',
            ],
            'html'    => ['/django|flask|fastapi/i', '/csrfmiddlewaretoken/i'],
            // csrftoken/sessionid alone are too generic — relying on headers instead
            'cookies' => [],
            'meta'    => [],
            'scripts' => [],
            'links'   => [],
        ],

        'Ruby' => [
            'name'    => 'Ruby',
            'domain'  => 'ruby-lang.org',
            'icon'    => '💎',
            'color'   => '#cc342d',
            'headers' => [
                'x-powered-by' => '/phusion passenger|ruby/i',
                'server'       => '/passenger|puma|unicorn|thin/i',
            ],
            'html'    => ['/rails|ruby on rails/i'],
            'cookies' => ['/rack\.session|_session_id/i'],
            'meta'    => [],
            'scripts' => ['/assets\/.+\.js\?body=1/i'],
            'links'   => [],
        ],

        'Node.js' => [
            'name'    => 'Node.js',
            'domain'  => 'nodejs.org',
            'icon'    => '🟩',
            'color'   => '#339933',
            'headers' => [
                'x-powered-by' => '/express|node\.js|next\.js|nuxt/i',
                'server'       => '/node\.js/i',
            ],
            'html'    => ['/__nuxt|__NEXT_DATA__|__next/i'],
            'cookies' => ['/connect\.sid|express\.sid/i'],
            'meta'    => [],
            'scripts' => ['/_next\/static\//i', '\/_nuxt\//i'],
            'links'   => ['/_next\/static\//i'],
        ],

        'Java' => [
            'name'    => 'Java',
            'domain'  => 'java.com',
            'icon'    => '☕',
            'color'   => '#b07219',
            'headers' => [
                'x-powered-by' => '/servlet|jsp|java|tomcat|jetty/i',
                'server'       => '/tomcat|jetty|jboss|glassfish|websphere/i',
            ],
            'html'    => ['/jsessionid/i'],
            'cookies' => ['/JSESSIONID/i'],
            'meta'    => [],
            'scripts' => ['/\.jsp(\?|"|\')/i'],
            'links'   => ['/\.jsp(\?|"|\')/i'],
        ],

        'ASP.NET' => [
            'name'    => 'ASP.NET',
            'domain'  => 'dotnet.microsoft.com',
            'icon'    => '🟣',
            'color'   => '#512bd4',
            'headers' => [
                'x-powered-by'   => '/asp\.net|microsoft/i',
                'x-aspnet-version' => '/.+/',
                'server'         => '/microsoft-iis/i',
            ],
            'html'    => ['/webforms|aspnet|__VIEWSTATE/i'],
            'cookies' => ['/ASP\.NET_SessionId|\.ASPXAUTH/i'],
            'meta'    => [],
            'scripts' => ['/WebResource\.axd|ScriptResource\.axd/i'],
            'links'   => ['/.aspx(\?|"|\')/i'],
        ],

        'Go' => [
            'name'    => 'Go',
            'domain'  => 'go.dev',
            'icon'    => '🔵',
            'color'   => '#00acd7',
            'headers' => [
                'server'       => '/caddy|gin|echo|fiber/i',
                'x-powered-by' => '/go|golang/i',
            ],
            'html'    => [],
            'cookies' => [],
            'meta'    => [],
            'scripts' => [],
            'links'   => [],
        ],

    ],

    // ─── FRAMEWORKS ──────────────────────────────────────────────────────────

    'frameworks' => [

        'React' => [
            'name'    => 'React',
            'domain'  => 'react.dev',
            'icon'    => '⚛️',
            'color'   => '#61dafb',
            'headers' => [],
            'html'    => [
                '/__react/i',
                '/react-root/i',
                '/data-reactroot/i',
                '/data-reactid/i',
                '/__NEXT_DATA__/i',
                '/react\.development\.js|react\.production\.min\.js/i',
            ],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/react(\.min)?\.js/i', '/_next\/static\/chunks\//i', '/react-dom/i'],
            'links'   => [],
        ],

        'Vue.js' => [
            'name'    => 'Vue.js',
            'domain'  => 'vuejs.org',
            'icon'    => '💚',
            'color'   => '#42b883',
            'headers' => [],
            'html'    => [
                '/__vue_/i',
                '/data-v-[a-f0-9]+/i',
                '/vue\.js|vue\.min\.js/i',
                '/__nuxt/i',
            ],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/vue(\.min)?\.js/i', '/_nuxt\//i'],
            'links'   => [],
        ],

        'Angular' => [
            'name'    => 'Angular',
            'domain'  => 'angular.io',
            'icon'    => '🔴',
            'color'   => '#dd0031',
            'headers' => [],
            'html'    => [
                '/ng-version/i',
                '/ng-app/i',
                '/angular\.js|angular\.min\.js/i',
                '/ngController/i',
            ],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/angular(\.min)?\.js/i', '/main\.[a-f0-9]+\.js/i'],
            'links'   => [],
        ],

        'Next.js' => [
            'name'    => 'Next.js',
            'domain'  => 'nextjs.org',
            'icon'    => '▲',
            'color'   => '#000000',
            'headers' => ['x-powered-by' => '/next\.js/i'],
            'html'    => ['/__NEXT_DATA__/i', '/_next\/static\//i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/_next\/static\/chunks\//i'],
            'links'   => ['/_next\/static\//i'],
        ],

        'Nuxt.js' => [
            'name'    => 'Nuxt.js',
            'domain'  => 'nuxt.com',
            'icon'    => '💚',
            'color'   => '#00c58e',
            'headers' => ['x-powered-by' => '/nuxt/i'],
            'html'    => ['/__NUXT__/i', '/_nuxt\//i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/_nuxt\//i'],
            'links'   => ['/_nuxt\//i'],
        ],

        'Laravel' => [
            'name'    => 'Laravel',
            'domain'  => 'laravel.com',
            'icon'    => '🔴',
            'color'   => '#ff2d20',
            'headers' => ['x-powered-by' => '/laravel/i'],
            'html'    => ['/laravel/i', '/csrf-token.*laravel/i'],
            'cookies' => ['/laravel_session/i', '/XSRF-TOKEN/i'],
            'meta'    => ['csrf-token' => '/.+/'],
            'scripts' => ['/laravel/i'],
            'links'   => [],
        ],

        'Symfony' => [
            'name'    => 'Symfony',
            'domain'  => 'symfony.com',
            'icon'    => '⚫',
            'color'   => '#000000',
            'headers' => ['x-debug-token' => '/.+/', 'x-debug-token-link' => '/.+/'],
            'html'    => ['/symfony/i', '/sf-toolbar/i'],
            'cookies' => ['/symfony/i', '/PHPSESSID/i'],
            'meta'    => [],
            'scripts' => ['/bundles\/framework/i'],
            'links'   => ['/bundles\/framework/i'],
        ],

        'Django' => [
            'name'    => 'Django',
            'domain'  => 'djangoproject.com',
            'icon'    => '🟢',
            'color'   => '#092e20',
            // NOTE: x-frame-options: SAMEORIGIN is set by almost every framework/server
            // and MUST NOT be used as a Django signal — it causes massive false positives.
            'headers' => ['server' => '/django/i'],
            'html'    => [
                '/csrfmiddlewaretoken/i',          // Django CSRF token — very specific
                '/django-debug-toolbar/i',         // Django debug toolbar
                '/__django_admin__/i',             // Django admin reference
            ],
            'cookies' => [
                '/^csrftoken$/i',                  // Django CSRF cookie (exact name)
            ],
            // sessionid alone is too generic (PHP, Rails, etc. use it too)
            'meta'    => [],
            'scripts' => ['/django/i'],
            'links'   => [],
        ],

        'Flask' => [
            'name'    => 'Flask',
            'domain'  => 'flask.palletsprojects.com',
            'icon'    => '🧪',
            'color'   => '#000000',
            'headers' => ['server' => '/werkzeug/i'],
            'html'    => ['/flask/i'],
            'cookies' => ['/session/i'],
            'meta'    => [],
            'scripts' => [],
            'links'   => [],
        ],

        'Ruby on Rails' => [
            'name'    => 'Ruby on Rails',
            'domain'  => 'rubyonrails.org',
            'icon'    => '💎',
            'color'   => '#cc0000',
            'headers' => ['x-powered-by' => '/phusion passenger/i', 'server' => '/passenger/i'],
            'html'    => ['/rails-ujs/i', '/data-remote="true"/i', '/csrf-param/i'],
            'cookies' => ['/rack\.session|_session_id/i'],
            'meta'    => [
                'csrf-param' => '/authenticity_token/i',
                'csrf-token' => '/.+/',
            ],
            'scripts' => ['/rails-ujs|rails\/ujs/i'],
            'links'   => [],
        ],

        'Bootstrap' => [
            'name'    => 'Bootstrap',
            'domain'  => 'getbootstrap.com',
            'icon'    => '🅱️',
            'color'   => '#7952b3',
            'headers' => [],
            'html'    => ['/bootstrap\.min\.css|bootstrap\.css/i', '/class="[^"]*container[^"]*"/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/bootstrap(\.min)?\.js/i'],
            'links'   => ['/bootstrap(\.min)?\.css/i'],
        ],

        'Tailwind CSS' => [
            'name'    => 'Tailwind CSS',
            'domain'  => 'tailwindcss.com',
            'icon'    => '🎨',
            'color'   => '#38bdf8',
            'headers' => [],
            'html'    => ['/class="[^"]*(?:flex|grid|text-\w+-\d+|bg-\w+-\d+|p-\d+|m-\d+)[^"]*"/'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/tailwind(\.min)?\.js/i'],
            'links'   => ['/tailwind(\.min)?\.css/i'],
        ],

        'jQuery' => [
            'name'    => 'jQuery',
            'domain'  => 'jquery.com',
            'icon'    => '🔵',
            'color'   => '#0769ad',
            'headers' => [],
            'html'    => ['/jquery/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/jquery(\.min)?\.js/i', '/jquery-\d+\.\d+/i'],
            'links'   => [],
        ],

        'ASP.NET MVC' => [
            'name'    => 'ASP.NET MVC',
            'domain'  => 'dotnet.microsoft.com',
            'icon'    => '🟣',
            'color'   => '#512bd4',
            'headers' => [
                'x-aspnet-version' => '/.+/',
                'x-powered-by'     => '/asp\.net/i',
            ],
            'html'    => ['/Microsoft\.AspNet|__RequestVerificationToken/i'],
            'cookies' => ['/\.ASPXAUTH|ASP\.NET_SessionId/i'],
            'meta'    => [],
            'scripts' => ['/bundles\/(jquery|bootstrap|modernizr)/i'],
            'links'   => [],
        ],

        'Express.js' => [
            'name'    => 'Express.js',
            'domain'  => 'expressjs.com',
            'icon'    => '🚂',
            'color'   => '#000000',
            'headers' => ['x-powered-by' => '/express/i'],
            'html'    => [],
            'cookies' => ['/connect\.sid/i'],
            'meta'    => [],
            'scripts' => [],
            'links'   => [],
        ],

    ],

    // ─── PLUGINS ─────────────────────────────────────────────────────────────

    'plugins' => [

        'WooCommerce' => [
            'name'    => 'WooCommerce',
            'domain'  => 'woocommerce.com',
            'icon'    => '🛒',
            'color'   => '#96588a',
            'headers' => [],
            'html'    => ['/woocommerce/i', '/wc-api\//i', '/add-to-cart/i'],
            'cookies' => ['/woocommerce/i', '/wc_session_cookie/i'],
            'meta'    => [],
            'scripts' => ['/woocommerce/i', '/wc-block/i'],
            'links'   => ['/woocommerce/i'],
        ],

        'Yoast SEO' => [
            'name'    => 'Yoast SEO',
            'domain'  => 'yoast.com',
            'icon'    => '🔍',
            'color'   => '#a4286a',
            'headers' => [],
            'html'    => ['/yoast seo plugin/i', '/This site is optimized with the Yoast/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/yoast/i'],
            'links'   => ['/yoast/i'],
        ],

        'Elementor' => [
            'name'    => 'Elementor',
            'domain'  => 'elementor.com',
            'icon'    => '🎨',
            'color'   => '#d30c5c',
            'headers' => [],
            'html'    => ['/elementor/i', '/e-container/i', '/elementor-widget/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/elementor/i'],
            'links'   => ['/elementor/i'],
        ],

        'Cloudflare' => [
            'name'    => 'Cloudflare',
            'domain'  => 'cloudflare.com',
            'icon'    => '☁️',
            'color'   => '#f48120',
            'headers' => [
                'cf-ray'    => '/.+/',
                'server'    => '/cloudflare/i',
                'cf-cache-status' => '/.+/',
            ],
            'html'    => ['/cloudflare/i'],
            'cookies' => ['/cf_/i', '/__cf_bm/i', '/cf_clearance/i'],
            'meta'    => [],
            'scripts' => ['/cloudflare\.com/i'],
            'links'   => [],
        ],

        'Google Analytics' => [
            'name'    => 'Google Analytics',
            'domain'  => 'analytics.google.com',
            'icon'    => '📊',
            'color'   => '#e37400',
            'headers' => [],
            'html'    => [
                '/google-analytics\.com\/analytics\.js/i',
                '/gtag\(\'config\'/i',
                '/UA-\d{4,10}-\d+/i',
                '/G-[A-Z0-9]{10}/i',
            ],
            'cookies' => ['/^_ga/i', '/^_gid/i'],
            'meta'    => [],
            'scripts' => ['/google-analytics\.com\/analytics\.js/i', '/googletagmanager\.com\/gtag/i'],
            'links'   => [],
        ],

        'Google Tag Manager' => [
            'name'    => 'Google Tag Manager',
            'domain'  => 'tagmanager.google.com',
            'icon'    => '🏷️',
            'color'   => '#4285f4',
            'headers' => [],
            'html'    => ['/googletagmanager\.com\/gtm\.js/i', '/GTM-[A-Z0-9]{7}/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/googletagmanager\.com\/gtm\.js/i'],
            'links'   => [],
        ],

        'Akismet' => [
            'name'    => 'Akismet',
            'domain'  => 'akismet.com',
            'icon'    => '🛡️',
            'color'   => '#3d596d',
            'headers' => [],
            'html'    => ['/akismet/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/akismet/i'],
            'links'   => ['/akismet/i'],
        ],

        'reCAPTCHA' => [
            'name'    => 'reCAPTCHA',
            'domain'  => 'google.com/recaptcha',
            'icon'    => '🤖',
            'color'   => '#4285f4',
            'headers' => [],
            'html'    => ['/google\.com\/recaptcha/i', '/g-recaptcha/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/google\.com\/recaptcha/i'],
            'links'   => [],
        ],

        'Stripe' => [
            'name'    => 'Stripe',
            'domain'  => 'stripe.com',
            'icon'    => '💳',
            'color'   => '#6772e5',
            'headers' => [],
            'html'    => ['/js\.stripe\.com/i', '/stripe-js/i'],
            'cookies' => [],
            'meta'    => [],
            'scripts' => ['/js\.stripe\.com/i'],
            'links'   => [],
        ],

        'Facebook Pixel' => [
            'name'    => 'Facebook Pixel',
            'domain'  => 'facebook.com',
            'icon'    => '📘',
            'color'   => '#1877f2',
            'headers' => [],
            'html'    => ['/connect\.facebook\.net\/.*fbevents\.js/i', '/fbq\(\'init\'/i'],
            'cookies' => ['/^_fbp/i', '/^_fbc/i'],
            'meta'    => [],
            'scripts' => ['/connect\.facebook\.net/i'],
            'links'   => [],
        ],

        'Hotjar' => [
            'name'    => 'Hotjar',
            'domain'  => 'hotjar.com',
            'icon'    => '🔥',
            'color'   => '#fd3a5c',
            'headers' => [],
            'html'    => ['/static\.hotjar\.com/i', '/hotjar/i'],
            'cookies' => ['/^_hjid/i', '/hotjar/i'],
            'meta'    => [],
            'scripts' => ['/static\.hotjar\.com/i'],
            'links'   => [],
        ],

        'Intercom' => [
            'name'    => 'Intercom',
            'domain'  => 'intercom.com',
            'icon'    => '💬',
            'color'   => '#1f8ded',
            'headers' => [],
            'html'    => ['/widget\.intercom\.io/i', '/intercomSettings/i'],
            'cookies' => ['/intercom/i'],
            'meta'    => [],
            'scripts' => ['/widget\.intercom\.io/i'],
            'links'   => [],
        ],

        'Crisp Chat' => [
            'name'    => 'Crisp Chat',
            'domain'  => 'crisp.chat',
            'icon'    => '💬',
            'color'   => '#1972f5',
            'headers' => [],
            'html'    => ['/crisp\.chat/i', '/CRISP_WEBSITE_ID/i'],
            'cookies' => ['/crisp/i'],
            'meta'    => [],
            'scripts' => ['/client\.crisp\.chat/i'],
            'links'   => [],
        ],

    ],

    // ─── SERVER SOFTWARE ─────────────────────────────────────────────────────

    'servers' => [

        'Apache' => [
            'name'  => 'Apache',
            'domain'  => 'apache.org',
            'icon'  => '🪶',
            'color' => '#d22128',
            'headers' => ['server' => '/apache/i'],
            'html' => [], 'cookies' => [], 'meta' => [], 'scripts' => [], 'links' => [],
        ],

        'Nginx' => [
            'name'  => 'Nginx',
            'domain'  => 'nginx.org',
            'icon'  => '🟩',
            'color' => '#009900',
            'headers' => ['server' => '/nginx/i'],
            'html' => [], 'cookies' => [], 'meta' => [], 'scripts' => [], 'links' => [],
        ],

        'Microsoft IIS' => [
            'name'  => 'Microsoft IIS',
            'domain'  => 'iis.net',
            'icon'  => '🪟',
            'color' => '#00adef',
            'headers' => ['server' => '/microsoft-iis/i'],
            'html' => [], 'cookies' => [], 'meta' => [], 'scripts' => [], 'links' => [],
        ],

        'LiteSpeed' => [
            'name'  => 'LiteSpeed',
            'domain'  => 'litespeedtech.com',
            'icon'  => '⚡',
            'color' => '#e57000',
            'headers' => ['server' => '/litespeed/i', 'x-powered-by' => '/litespeed/i'],
            'html' => [], 'cookies' => [], 'meta' => [], 'scripts' => [], 'links' => [],
        ],

        'Cloudflare' => [
            'name'  => 'Cloudflare',
            'domain'  => 'cloudflare.com',
            'icon'  => '☁️',
            'color' => '#f48120',
            'headers' => ['server' => '/cloudflare/i', 'cf-ray' => '/.+/'],
            'html' => [], 'cookies' => [], 'meta' => [], 'scripts' => [], 'links' => [],
        ],

    ],

];