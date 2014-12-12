var AMILIO = (function($,w,undefined){

    var config = {}

    var debugDiv = null;
    var debug = function(val){

        if (! config.debug)
            return;

        if (debugDiv === null) {
            debugDiv = document.createElement("div");
            debugDiv.id = "debugDiv";
            debugDiv.style.position="absolute";debugDiv.style.opacity=0.7;debugDiv.style.width="400px";debugDiv.style.height="300px";debugDiv.style.minHeight="300px";debugDiv.style.maxHeight="300px";debugDiv.style.minWidth="400px";debugDiv.style.maxWidth="400px";debugDiv.style.backgroundColor="lightgrey";debugDiv.style.top="40px";debugDiv.style.right="5px";debugDiv.style.overflow="scroll";debugDiv.style.zIndex=1000;

            document.body.appendChild(debugDiv);
        }

        if (val instanceof Object) {
            for (var key in val) {
                if (val[key] instanceof Object || val[key] instanceof Array) {
                    debug(key + ": " + debug(val[key]));
                } else {
                    debug(key + ": " + val[key]);
                }
            }
            return;
        } else if (val instanceof Array) {
            var len = val.length;

            for (var i = 0; i < len; i++) {
                if (val[i] instanceof Object || val[i] instanceof Array) {
                    debug(i + ": " + debug(val[i]));
                } else {
                    debug(i + ": " + val[i]);
                }
            }
            return;
        }

        debugDiv.appendChild(document.createTextNode(val));
        debugDiv.appendChild(document.createElement("br"));
    }

    var loadConfig = function(cfg) {
        for (var k in cfg) {
            config[k] = cfg[k]
        }
    }

    var initCalled = false;

    var init = function(cfg){
        if (initCalled) {
            AMILIO.debug("init() was already called.")
            return;
        }

        initCalled = true;

        loadConfig(cfg)
        debug(config)
    }

    // PLUGIN STUFF
    var registeredPlugins = {}

    var plugin = {
        register: function(name, fnc){
            if (!registeredPlugins[name]) {
                registeredPlugins[name] = fnc;
            }
        },
        call: function(name, parameter) {
            if (registeredPlugins[name]) {
                registeredPlugins[name](parameter)
            }
        }
    }

    return {
        config: config,
        init: init,
        debug: debug,
        plugin: plugin
    }
})($,window)
