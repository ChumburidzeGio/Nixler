if (!Array.prototype.last){
    Array.prototype.last = function(){
        return this[this.length - 1];
    };
};

if (!Array.prototype.inArray){
    Array.prototype.inArray = function(comparer) { 
        for(var i=0; i < this.length; i++) { 
            if(comparer(this[i])) return true; 
        }
        return false; 
    }; 
};