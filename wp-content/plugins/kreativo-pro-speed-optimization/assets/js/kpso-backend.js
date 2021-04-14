jQuery(document).ready(function()
{
	jQuery("#kpso_css_mobile_disabled").click(function(){
		
		if( jQuery("#kpso_css_mobile_disabled").attr("value") == "yes" )
		{ jQuery("#kpso_css_mobile_disabled").attr("value","no"); }
		else
		{ jQuery("#kpso_css_mobile_disabled").attr("value","yes"); }
	});

	jQuery("#kpso_js_mobile_disabled").click(function(){
		if( jQuery("#kpso_js_mobile_disabled").attr("value") == "yes" )
		{ jQuery("#kpso_js_mobile_disabled").attr("value","no"); }
		else
		{ jQuery("#kpso_js_mobile_disabled").attr("value","yes"); }
	});
	
	jQuery("#kpso_woo_optimization").click(function(){
		if( jQuery("#kpso_woo_optimization").attr("value") == "yes" )
		{ jQuery("#kpso_woo_optimization").attr("value","no"); }
		else
		{ jQuery("#kpso_woo_optimization").attr("value","yes"); }
	});
	
});