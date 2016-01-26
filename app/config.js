var config_data = {
    'cfg': {
        //Application name
        'app_name': 'SmartHome Module Upload UI',
        // Application version
        'app_version': '1.0.0',
        // Server base url
        'server_url': '',
        // List of API URLs 
        'api': {
            'usersession': '?uri=usersession',
            'mymodules': '?uri=mymodules',
            'modules': '?uri=modules',
            'module': '?uri=module',
            'moduleupdate': '?uri=moduleupdate',
            'moduledelete': '?uri=moduledelete',
            'moduleupload': '?uri=moduleupload',
            'moduleverify': '?uri=moduleverify',
            'tokens': '?uri=tokens',
            'tokencreate': '?uri=tokencreate',
            'tokendelete': '?uri=tokendelete',
            'skins': '?uri=skins',
            'skin': '?uri=skin',
            'skincreate': '?uri=skincreate',
            'skinupdate': '?uri=skinupdate',
            'skindelete': '?uri=skindelete',
            'skinupload': '?uri=skinupload',
            'skinimgupload': '?uri=skinimgupload',
            'userread': '?uri=userread',
            'userupdate': '?uri=userupdate',
            'adminmodules': '?uri=adminmodules',
            'adminmoduleverify': '?uri=adminmoduleverify',
            'adminmoduleemail': '?uri=adminmoduleemail',
            'adminusercreate': '?uri=adminusercreate',
            'adminusers': '?uri=adminusers',
            'adminuserread': '?uri=adminuserread',
            'adminresetpassword': '?uri=adminresetpassword',
            'adminuserdelete': '?uri=adminuserdelete',
            'comments': '?uri=api-comments',
            'commentscreate': '?uri=api-comments-create',
            'commentdelete': '?uri=commentdelete',
            'archive': '?uri=api-module-archive'

        },
        'path': {
            'module': 'modules/',
            'archive': 'archiv/',
            'skin': 'storage/skins/'
        },
        // List of image pathes
        'img': {
            'module': 'modules/',
            'archive': 'archiv/',
            'skin': 'storage/skins/',
            'iconPlaceholder': 'storage/icon-placeholder.png'
        },
        'categories': [
            {
                "id": "basic_gateway_modules",
                "name": "Basic Gateway Modules",
                "description": "Basic Gateway Modules",
                "icon": ""
            },
            {
                "id": "legacy_products_workaround",
                "name": "Legacy Products / Workaround",
                "description": "Legacy Products / Workaround",
                "icon": ""
            },
            {
                "id": "support_external_ui",
                "name": "Support of external UIs",
                "description": "Support of external UIs",
                "icon": ""
            },
            {
                "id": "support_external_dev",
                "name": "Support of external Devices/Services",
                "description": "Support of external Devices/Services",
                "icon": ""
            },
            {
                "id": "automation_basic",
                "name": "Automation Basics",
                "description": "Automation Basics",
                "icon": ""
            },
            {
                "id": "device_enhancements",
                "name": "Device enhancements",
                "description": "Device enhancements",
                "icon": ""
            },
            {
                "id": "developers_stuff",
                "name": "Developers Stuff",
                "description": "Developers Stuff",
                "icon": ""
            },
            {
                "id": "complex_applications",
                "name": "Complex Applications",
                "description": "Complex Applications",
                "icon": ""
            },
            {
                "id": "automation",
                "name": "Automation",
                "description": "Create home automation rules",
                "icon": ""
            },
            {
                "id": "security",
                "name": "Security",
                "description": "Enhance security",
                "icon": ""
            },
            {
                "id": "peripherals",
                "name": "Peripherals",
                "description": "Z-Wave and other peripherals",
                "icon": ""
            },
            {
                "id": "surveillance",
                "name": "Video surveillance",
                "description": "Support for cameras",
                "icon": ""
            },
            {
                "id": "logging",
                "name": "Data logging",
                "description": "Logging to third party services",
                "icon": ""
            },
            {
                "id": "scripting",
                "name": "Scripting",
                "description": "Create custom scripts",
                "icon": ""
            },
            {
                "id": "devices",
                "name": "Devices",
                "description": "Create devices",
                "icon": ""
            },
            {
                "id": "scheduling",
                "name": "Schedulers",
                "description": "Time related functions",
                "icon": ""
            },
            {
                "id": "climate",
                "name": "Climate",
                "description": "Climate control",
                "icon": ""
            },
            {
                "id": "environment",
                "name": "Environment",
                "description": "Environment related data",
                "icon": ""
            },
            {
                "id": "scenes",
                "name": "Scenes",
                "description": "Light scenes",
                "icon": ""
            },
            {
                "id": "notifications",
                "name": "Notifications",
                "description": "SMS, E-mail and push notifications",
                "icon": ""
            },
            {
                "id": "tagging",
                "name": "Tagging",
                "description": "Tagging widgets",
                "icon": ""
            }
        ],
         // Default toke
        'default_token': 'Beta_zwe_internal',
        // Maturity
        'maturity': ['stable', 'beta', 'alpha'],
        // UI versions
        'ui_version': ['1.0.1', '1.0.2', '1.0.3'],
        // Local data path
        'local_data_url': 'storage/data/',
        // Language directory
        'lang_dir': 'app/lang/',
        // Default language
        'lang': 'en', // !!!!Do not change it
        // List of supported languages
        'lang_list': ['en', 'de', 'ru', 'cn', 'fr'],
        // Results per page
        'page_results': 20
    }
};
