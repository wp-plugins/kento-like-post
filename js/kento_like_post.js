jQuery(document).ready(function()
	{
		
		
		
		jQuery("#black-bg").click(function()
			{
				jQuery(this).hide();
				jQuery("#kento-like-post-login").css("display","none");
			
			});
		
		
		
		
		
		
		
	var is_logged = jQuery('#kento-fb-vote').attr('logged')
	if(is_logged=='notlogged')
		{
			jQuery("#vote-button").click(function()
			{
				
				jQuery("#kento-like-post-login").css("display","block");
				jQuery("#black-bg").css("display","block");
				
			});
		}
	else if(is_logged=='logged')
		{
			jQuery("#vote-button").click(function()
				{	
					jQuery(".notvoted, .voted").toggleClass("voted notvoted" );
					var postid = jQuery(this).attr("postid");
					var votestatus = jQuery(this).attr("votestatus");
				if(votestatus=="notvoted")
					{
						jQuery(this).attr('votestatus','voted');
					}
				else if(votestatus=="voted")
					{
						jQuery(this).attr('votestatus','notvoted');
						
					}
					jQuery.ajax(
						{
					type: 'POST',
					url: kento_like_post_ajax.kento_like_post_ajaxurl,
					data: {"action": "kento_like_post_insert", "postid":postid, "votestatus":votestatus},
					success: function(data)
							{
									
								jQuery(".who-voted").html(data);
								var count_current = jQuery("#has-vote-update").text();
								jQuery("#vote-info").html(count_current);
								
							}
						});
				});
		}
	});