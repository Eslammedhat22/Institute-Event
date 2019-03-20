
$(document).ready(function()
	{
		$('select').on('change', function(event ) 
		{
			var prevValue = $(this).data('previous');
			$('select').not(this).find('option[value="'+prevValue+'"]').show();    
			var value = $(this).val();
			$(this).data('previous',value); $('select').not(this).find('option[value="'+value+'"]').hide();
    
    	if(value != "Python" && value != "SolidWorks")
    	{
	    	var val1='Python';
	    	var val2='SolidWorks';
	    	var val3='Technical';
	    	var first = document.getElementById('firstpref');
	    	var second = document.getElementById('secondpref');
	    	$(this).data(first,val1); $(second).not(this).find('option[value="'+val1+'"]').hide();
	    	$(this).data(first,val2); $(second).not(this).find('option[value="'+val2+'"]').hide();
	    	$(this).data(first,val3); $(second).not(this).find('optgroup[value="'+val3+'"]').hide();
	    }
	});
});