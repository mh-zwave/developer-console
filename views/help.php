<!-- Help -->
<h1>How to create an automation module</h1>
<br>
<h2>General structure</h2>
<p align="justify">
All automation modules have to be located in a folder with your exact module name. If the name doesn’t match the one in your description the module won’t be generated or recognized by the controller.
The folder must contain 2 files and 2 subfolders. The files are “index.js” and “module.json”. The “index.js” is for the functionality while the “module.json” describes the structure of the module. The subfolders are “lang” and “htdocs”. <br>
In “htdocs” you can put an icon.png of the size 64x64 that will be displayed alongside the title in the “Apps” section. <br>
The “lang” folder is for your different language files. A language file is used for localization and has to be named either “de.json”, “en.sjon” or “ru.json”. You can choose if you want to support only one or up to all three languages. Depending on the selected language of the profile the user will see the app in the selected language.
Again, it is very important to name the files and folders exactly as mentioned.<br>
</p>

<br>
Example-structure
<br><br>
<ul>
<li>/htdocs - contains the icon</li>
<li>/lang - contains the languagefiles like de.json, en.json,....</li>
<li>index.js</li>
<li>module.json</li>
</ul>
<br> 
You can add different files too but these ones are the important-files for a running module.<br>
<br>
<h2>Language files</h2>
<br>
<p align="justify">
A language file is a simple JSON file. Simply add the property name and the text as its value. The property names have to be the same in every language file that you make while the value for each property should be in the specific language.<br>
You can access the language file in the “index.js” file by saving the result of this.controller.loadModuleLang(“your Module name”); into a variable. It will then be a simple object as in the language file.
A language file is a simple JSON file. Simply add the property name and the text as its value. The property names have to be the same in every language file that you make while the value for each property should be in the specific language.<br>
To access the language file from the “module.json” you can use __yourLanguagePropertyName__ to get the value of the language property. <br>
</p>
<br>
<h2>The index.js</h2>
<br>
In the “index.js” there are only a few functions that must be used. Start with:<br>

<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
function ModuleName (id, controller) {<br>
    // Call superconstructor first (AutomationModule)<br>
    ModuleName.super_.call(this, id, controller);<br>
}<br>
<br>
inherits(ModuleName, AutomationModule);<br>
_module = ModuleName;<br>
<br>
 and add the stop and init functions:<br>
<br>
ModuleName.prototype.init = function (config) {<br>
    ModuleName.super_.prototype.init.call(this, config);<br>
<br>
    //set virtual device to null<br>
    this.vDev = null;<br>
<br>
    //add a timer to run functions periodically. Time set in milliseconds*1000<br>
    this.timer = setInterval(function() {<br>
        this.functionToCall();<br>
    }, 900*1000);<br>
};<br>
<br>
Modulename.prototype.stop = function () {<br>
    ModuleName.super_.prototype.stop.call(this);<br>
<br>
    //clear timer if you have set one<br>
    if (this.timer)<br>
    {<br>
      clearInterval(this.timer);<br>
    }<br>
<br>
    //filters the devices for all devices created by this module<br>
    var filterId = "ModuleName_" + this.id,<br>
    filterArray = this.controller.devices.filter(function(device){<br>
      return device.id.indexOf(filterId) > -1;<br>
    });<br>
<br>
    //delete all devices created by this module when you deactivate/delete it<br>
    filterArray.forEach(function(device){<br>
      this.controller.devices.remove(device.id);<br>
    });<br>
};<br>
</div><br>
<p align="justify">
These functions are required to initiate, start and stop a module. You can do all your required setup in the init function. In the stop function you should delete all devices your module created since they can’t be excluded and would remain forever. If you create your devices with an ID that starts with your module name you can filter all devices of the controller as shown above.
To create a device you can use:
</p>
<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
This.vDev = this.controller.devices.create({<br>
deviceId: "ID, must be unique. Preferably ‘moduleName_’+this.id and more if   you have multiple devices”,<br>
	        defaults: {<br>
	            deviceType: 'the device type. You can find all types in the zwave.js',<br>
	            metrics: {<br>
	            	//define the metrics like title, scale, level and other values<br>
	            }<br>
	        },<br>
	        overlay: {<br>
	            //values that are changing when updating the device<br>
	        },<br>
          	       handler: function (command, args)<br>
          	      {<br>
//handle all commands. Commands are received when a control on the widget is clicked. Args is the value if a value was selected such as 23 as temperature<br>
          	      },<br>
moduleId: this.id //has to be this.id so the controller can associate the device with the module<br>
	   });<br>
</div><br>
<p align="justify">
You can add further functions as you wish and see fit. In the developer documentation (http://razberry.z-wave.me/docs/zwayDev.pdf) you can find a lot of functions that you can use. Those functions are described in chapter 3 JavaScript API.<br>
<br>
You can also throw and catch events in the automation system.  You can find all about events in chapter 4.2 of the developer documentation. <br>
In short events can be emitted and listened to by modules to take certain steps or report errors. You create an event by calling:<br>

<i>this.controller.emit(“arguments as described in the developer documentation”);</i><br>
<br>
If you want to listen to a certain event you can call:<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
this.controller.on(mymodule.testevent, function (name,eventarray) );</div><br>

If you want to stop listening to an event simply call:<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
this.controller.off(mymodule.testevent, function (name,eventarray) ) 
</div><br>
A special form of event is a notification. A notification contains human readable information that is displayed in the UI. It is solely for the information of users and can be found in the “Notifications” tab of the UI menu. They are further described in chapter 5.2 and can be created by calling:<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
this.controller.addNotification(severity, message, origin);</div>
<br></p>
<h2>The module.json</h2>
<br>
<p align="justify">
The module.json uses Alpaca to create powerful UI elements, which are displayed in the setup of the module, and stores all important data. You can define global variables to be used in the index.js and save input from the setup. Therefore the module.json is the core alongside the index.js.<br>
<br>
You can find everything about Alpaca under http://alpacajs.org.  The easiest way to learn the structure is to follow the tutorial. This should give you a good idea of how to use Alpaca. The attached module.json file has a skeleton for you to use so you can start right away.<br>
<br>
A few important things should be mentioned here as well. <br>
You access properties of the module.json by calling this.config.porpertyName. You can also modify the properties as needed.<br>
<br>
To create global variables for your index.js you can define properties in the “defaults” section. The “defaults” section defines default values for properties of fields. However, you can add properties you don’t use for any field. Those properties can be set and retrieved by calling “this.config.propertyname”. <br>
<br>
You can create fields consisting of other fields. For example you can create an array with complex objects as indexes. You simply create a property in the schema section and reference the complex subfield in the “definitions”.<br>
</p>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
"arrayName":{<br>
                "type":"array",<br>
                "items": {<br>
                    "minItems":1,<br>
                    "$ref":"#/definitions/subFieldName"<br>
                }<br>
            }<br>
<br>
</div>
<br>
In the definitions you can then define the subfield:<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
"definitions":{<br>
            "subFieldName": {<br>
                "type":"object",<br>
                "properties":{<br>
   }<br>
}<br>
}<br>
</div><br>
<p align="justify">
You can define the properties as usual. This will create a subfield with own properties and store it in the array that references it. You can call it in the index.js by this.config.arrayName.[index of subField]<nr>
<br>
The only way to fetch devices or rooms in the module is via namespaces. Namespaces filter all devices by defined criteria and give back an array of matching values.<br>
The structure of a namespace is always an enum. An example could look like:<br>
</p><br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
"selectedDevice":{<br>
			"field":"enum",<br>
			"datasource": "namespaces",<br>
"enum": "namespaces:devices_sensorBinary:deviceId,namespaces:devices_sensorMultilevel:deviceId"<br>
}<br>
</div><br>
<p align="justify">

The datasource is either “namespaces” for devices or “locations” for rooms. The “enum” receives the array. In this case it would consist of the device IDs of all binary and multilevel sensors.
Namespace masks are always in the following scheme:<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
deviceType : CommandClass : subCommandClass : id OR name
</div><br>
If you want to select all devices regardless of the command class or sub command class you can just type “devices_all”. Have a look at the ZAutomation API (http://docs.zwayhomeautomation.apiary.io) to get a grasp of the location and device structure.<br>
</p>

<br>
Sample - Module.json
<br>
<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
{<br>
   "author" : "#your_name",<br>
   "category" : "#category",<br>
   "defaults" : {<br>
      "title" : "__m_title__",<br>
      "description" : "__m_descr__"<br>
   },<br><br>
   "dependencies" : [],<br>
   "homepage" : "#homepage",<br>
   "icon" : "icon.png",<br>
   "maturity" : "stable",<br>
   "moduleName" : "BaseModule",<br>
   "options" : {},<br>
   "repository" : {<br>
      "source" : "#repositroysource",<br>
      "type" : "#repositorytype"<br>
   },<br>
   "schema" : {},<br>
   "singleton" : true,<br>
   "version" : 1.02<br>
}<br>

</div><br>
<br>
<i><b>IMPORTANT:</i></b> the values __m_title__ and __m_descr__ defines a Languagelink in our System. That means you need to define a Language - Json with all linked entry´s. This file should be located in the "lang" - Directory of your Module.<br>
<br>
Example Language - Json: EN.json<br>
<br>

<div style="border-style: dashed; border-width: 2px; border-color: grey; padding: 5px; background-color: #DDDDDD">
{<br>
    "m_title" : "modulename",<br>
   "m_descr" : "moduledescription"<br>
}<br>

</div><br>
<br><br>
The value #category defines the category of your module. You can chose from one of the following category´s:<br><br>
<ul>
<li>Basic Gateway Modules - basic_gateway_modules</li>
<li>Legacy Products / Workaround - legacy_products_workaround</li>
<li>Support of external UIs - support_external_ui</li>
<li>Support of external Devices/Services  - support_external_dev</li>
<li>Automation Basics  - automation_basic</li>
<li>Device enhancements  - device_enhancements</li>
<li>Developers Stuff - developers_stuff</li>
<li>Complex Applications - complex_applications</li>
<li>Peripherals  - peripherals</li>
<li>Video surveillance  - surveillance</li>
<li>Data logging  - logging</li>
<li>Tagging  - tagging</li>
</ul>

<br><br>
<h2>Example Module</h2>
<br>
<li><a href="http://developer.zwave.eu/modules/DownloadDummy.tar.gz">Download Dummy</a></li>
<br>
<h2>Upload - Process</h2>
<br>
The first step before you are able to upload your first own module is to pack them correct. At the moment we only support the filetype "tar.gz". Make sure that you use this filetype. If you have finished your
module and packed them correct then you can start uploading on the main-screen of our developer-center and click "Add new module" and follow the instrutctions. Was your upload successful then you should 
see your module in your overview with all the correct values like "Title", "Icon", "Category", "Version", "Maturity" and the last-updated-timestamp.
<h2>Publishing - Process</h2>
<br>
Before your apps is fully integrated in our app-store you need to verify them (read more on "Verification - Process"). That means the we prove your applikation and test them. 
This cost some time and maybe you don´t want to wait or want that your friends oder co-workers are also able to test them. In this case you can define your own "token" in your module-management. 
You only need to click on edit (at the right side on your module-overview) and add your own token. Then you can share them like you want and everyone which have these token can download the application 
in his software.  
<h2>Verification - Process</h2>
<br>
The verification-process can startet by a click on "Send a request" in your module-overview. Now is your verification in process. You got a mail from us if we accept or decline your module. In that case if we decline you also
got a reason from us and can fix/change them and start the verification again. After a successful verification everyone can access your module in our app-store. 
<br><br>
<b>
Important: if you update your module you need to verify them again.</b>
<br><br>
<h2>Interesting Links</h2>
<ul>

<li><a href="https://github.com/Z-Wave-Me/home-automation/wiki">Documentation</a></li>
<li><a href="http://docs.zwayhomeautomation.apiary.io/">API Documentation</a></li>
<li><a href="http://razberry.z-wave.me/docs/zwayDev.pdf">Z-Way Developers Documentation</a></li>
<li><a href="https://github.com/Z-Wave-Me/home-automation/issues">Issues, bugs and feature requests are welcome</a></li>
</ul>

<br><br>

<br><br>