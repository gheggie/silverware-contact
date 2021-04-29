$(function(){
    
    $('#ContactPage form.contact-form').entwine({
        
        // Select Contact Recipient via URL Hash:
        
        onmatch: function() {
            
            // Obtain Hash:
            
            var hash = $(location).attr('hash').replace(/^#/, "");
            
            // Select Recipient:
            
            if (hash) {
                
                // Obtain Recipient Data:
                
                var data = this.data('recipients');
                
                // Locate Recipient ID and Select:
                
                if (hash in data) {
                    this.find(':input[name=RecipientID]').val(data[hash]);
                }
                
            }
            
        }
        
    });
    
});
