function Browser() {   
    // ---- public properties -----
    this.fullName = 'unknow'; // getName(false);
    this.name = 'unknow'; // getName(true);
    this.code = 'unknow'; // getCodeName(this.name);
    this.fullVersion = 'unknow'; // getVersion(this.name);
    this.version = 'unknow'; // getBasicVersion(this.fullVersion);
    this.mobile = false; // isMobile(navigator.userAgent);
    this.width = screen.width;
    this.height = screen.height;
    this.platform =  'unknow'; //getPlatform(navigator.userAgent);
    
    this.init = function() { 
        var navs = [
            { name:'Opera Mobi', fullName:'Opera Mobile', pre:'Version/' },
            { name:'Opera Mini', fullName:'Opera Mini', pre:'Version/' },
            { name:'Opera', fullName:'Opera', pre:'Version/' },
            { name:'MSIE', fullName:'IE', pre:'MSIE ' },  
            { name:'BlackBerry', fullName:'BlackBerry Navigator', pre:'/' }, 
            { name:'BrowserNG', fullName:'Nokia Navigator', pre:'BrowserNG/' }, 
            { name:'Midori', fullName:'Midori', pre:'Midori/' }, 
            { name:'Kazehakase', fullName:'Kazehakase', pre:'Kazehakase/' }, 
            { name:'Chromium', fullName:'Chromium', pre:'Chromium/' }, 
            { name:'Flock', fullName:'Flock', pre:'Flock/' }, 
            { name:'Galeon', fullName:'Galeon', pre:'Galeon/' }, 
            { name:'RockMelt', fullName:'RockMelt', pre:'RockMelt/' }, 
            { name:'Fennec', fullName:'Fennec', pre:'Fennec/' }, 
            { name:'Konqueror', fullName:'Konqueror', pre:'Konqueror/' }, 
            { name:'Arora', fullName:'Arora', pre:'Arora/' }, 
            { name:'Swiftfox', fullName:'Swiftfox', pre:'Firefox/' }, 
            { name:'Maxthon', fullName:'Maxthon', pre:'Maxthon/' },
            // { name:'', fullName:'', pre:'' } //add new broswers
            // { name:'', fullName:'', pre:'' }
            { name:'Firefox',fullName:'FF', pre:'Firefox/' },
            { name:'Chrome', fullName:'GC', pre:'Chrome/' },
            { name:'Safari', fullName:'Apple Safari', pre:'Version/' }
        ];
        var agent = navigator.userAgent, pre;
        //set names
        for (i in navs) {
           if (agent.indexOf(navs[i].name)>-1) {
               pre = navs[i].pre;
               this.name = navs[i].name.toLowerCase();
               this.fullName = navs[i].fullName; 
                if (this.name=='msie') this.name = 'iexplorer';
                if (this.name=='opera mobi') this.name = 'opera';
                if (this.name=='opera mini') this.name = 'opera';
                break; 
            }
        }
      //set version
        if ((idx=agent.indexOf(pre))>-1) {
            this.fullVersion = '';
            this.version = '';
            var nDots = 0;
            var len = agent.length;
            var indexVersion = idx + pre.length;
            for (j=indexVersion; j<len; j++) {
                var n = agent.charCodeAt(j); 
                if ((n>=48 && n<=57) || n==46) { 
                    if (n==46) nDots++;
                    if (nDots<2) this.version += agent.charAt(j);
                    this.fullVersion += agent.charAt(j);
                }else j=len; 
            }
            this.version = parseInt(this.version);
        }
        if (this.width<700 || this.height<600) this.mobile = true;
        
        if (this.name!='unknow') {
            this.code = this.name+'';
            if (this.name=='opera') this.code = 'op';
            if (this.name=='firefox') this.code = 'ff';
            if (this.name=='chrome') this.code = 'ch';
            if (this.name=='safari') this.code = 'sf';
            if (this.name=='iexplorer') this.code = 'ie';
            if (this.name=='maxthon') this.code = 'mx';
        }
        if (this.name=='safari' && this.platform=='Linux') {
            this.name = 'unknow';
            this.fullName = 'unknow';
            this.code = 'unknow';
        }
    };
    this.init();
}
function browserr(){ 
 var brw = new Browser();
 document.getElementById('browser').value= '' + brw.fullName + '\n' + '  ' + brw.fullVersion;  
}