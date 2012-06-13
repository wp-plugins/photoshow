var photoshowAdmin = function($){
	function loadWindow(){
		
		$(' <div title="'+str.title+'"><div style="padding:20px;">'+
		   str.width+'*: <br /><input type="text" id="flickrImgWidth" style="width:250px;" /><br />'+
		   str.height+'*: <br /><input type="text" id="flickrImgHeight" style="width:250px;" /><br />'+
		   str.terms+'*:  <br /><input type="text" id="flickrImgTerms" style="width:250px;" /><br />'+
		   str.limit+':  <br /><input type="text" id="flickrImgNum" style="width:100px;" /> <span style="font-size:10px">10 '+str.default+'</span><br />'+
		   str.timeout+': <br /><input type="text" id="flickrImgTimeout" style="width:100px;" /> <span style="font-size:10px">'+str.seconds+', 10 '+str.default+'</span><br />'+
		   '<span style="font-size:10px">'+str.required+'.</sapan>'+
		   '</div></div>'
		  ).dialog({
			dialogClass: 'wp-dialog',
            modal: true,
            closeOnEscape: true,
            buttons: [
                {text: str.insert, click: function() {
					var fiw  = $("#flickrImgWidth").val(),
						fih  = $("#flickrImgHeight").val(),
						fit  = $("#flickrImgTerms").val(),
						fin  = $("#flickrImgNum").val(),
						fito = $("#flickrImgTimeout").val();
						
					if(!/^\s*$/.test(fiw) && !/^\s*$/.test(fih) && !/^\s*$/.test(fit)){
						if(send_to_editor){
							send_to_editor('<div class="photoshow" title=\'{"filter":"'+fit+'", "timeout":'+((fito) ? fito*1000 : 10000 )+', "limit":'+((fin) ? fin : 10)+'}\' style="width:'+fiw+'px; height:'+fih+'px;"></div>');
						}
						$(this).dialog("close"); 
					}else{
						alert(str.required);
					}	
				}}
            ]
        });
	}
	
	var obj = {};
	
	obj.open = loadWindow;
	return obj;
}(jQuery);