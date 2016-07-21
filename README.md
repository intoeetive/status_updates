	<h1>Status Updates</h1>

	<p>Status Updates is the module for ExpressionEngine 2 that allows site users to post status messages in their profile. They can also crosspost them to their social network accounts (requires <a href="http://www.intoeetive.com/index.php/comments/social-login-pro">Social Login PRO</a>)</p>
    
   	<ul>		
        
        <li><a href="#post">Posting status message</a>
		</li>
        <li><a href="#display">Displaying status updates feed</a>
            
        </li>
        <li><a href="#examples">Examples</a></li>
	</ul>


	<h2><a name="post" href="#top">&uarr;</a>Posting status message</h2>
 
<code>
{exp:status_updates:form}
&lt;textarea name="message_text"&gt;&lt;/textarea&gt;
&lt;input type="submit" value="Send" /&gt;
{/exp:status_updates:form}
</code> 

<p class="important">Crossposting to social networks is possible only if you have <a href="http://www.intoeetive.com/index.php/comments/social-login-pro">Social Login PRO</a> installed and the user has ever logged in (or performed account association) using that module</p>

<p><strong>Tag parameters:</strong>
<ul>
<li><dfn>return</dfn> &mdash; a page to return after posting update. Can be a full URL or URI segments.<br />Use <em>return="SAME_PAGE"</em> to return user to the page used to display form.</li>
<li><dfn>id</dfn> &mdash; form ID (defaults to 'status_updates_form')</li>
<li><dfn>class</dfn> &mdash; form class (defaults to 'status_updates_form')</li>
<li><dfn>name</dfn> &mdash; form name (defaults to 'status_updates_form')</li>
<li><dfn>ajax="yes"</dfn> &mdash; process form in AJAX mode (will return data as JSON array)</li>
<li><dfn>maxlength</dfn> &mdash; maximum allowed length of message to be posted (defaults to 760 characters)</li>
<li><dfn>twitter="yes"</dfn> &mdash; force crossposting to Twitter (if possible for logged in user)</li>
<li><dfn>facebook="yes"</dfn> &mdash; force crossposting to Facebook (if possible for logged in user)</li>
<li><dfn>linkedin="yes"</dfn> &mdash; force crossposting to LinkedIn (if possible for logged in user)</li>
</ul>
</p>

<p><strong>Form fields</strong>:
<ul>
<li><dfn>message_text</dfn> &mdash; text of status message. Required</li>
<li><dfn>checkbox name="twitter" value="yes"</dfn> &mdash; when checked, crosspost to Twitter will be made (if possible)</li>
<li><dfn>checkbox name="facebook" value="yes"</dfn> &mdash; when checked, crosspost to Facebook will be made (if possible)</li>
<li><dfn>checkbox name="linkedin" value="yes"</dfn> &mdash; when checked, crosspost to LinkedIn will be made (if possible)</li>
</ul>
</p>

<p><strong>Variables</strong>:

<ul>
<li><dfn>maxlength</dfn> &mdash; value of maxlength parameter</li>
</ul>

<p>You can use <strong>{providers}</strong> tag pair to display list of available social networks to crosspost. It displays only those networks that are associated with current user's account.</p>
<p>Inside of <strong>{providers}</strong> tag pair following variables are availabe:</p>
<ul>
<li><strong>{provider_name}</strong> &mdash; provider name. This is 'technical' variable, should be used as name for checkboxes.</li>
<li><strong>{provider_title}</strong> &mdash; the 'full' name/title of service provider/social network</li>
<li><strong>{provider_icon}</strong> &mdash; URL for image/button for corresponding provider (from the icons set defined in Social Login Pro settings)</li>
</ul>
</p>

<p>Upon form submission, the user is taked to return page (no success message is shown), or is shown error message if there has been an error.</p>    
<p>If you've specified ajax="yes" parameter then both error and success messages are returned in JSON format. Examples:</p>
<pre>
{
	"result": "error";
	"text"	: "You need to be logged in"
}
</pre>
<pre>
{
	"result": "success";
	"text"	: "This is my status update!";
	"date"	: 
}
</pre>

    
    
    <h3><a name="tags-display" href="#top">&uarr;</a>Displaying status updates feed</h3>

<p>This tag allows you to display status updates feed - by logged in user, or certain user, user group or even everyone.</p>
    
<code>
{exp:status_updates:display paginate="both" limit="10"}<br />
{paginate}{pagination_links}{/paginate}<br />
&lt;p&gt;{count}. &lt;em&gt;{message_date format="%Y-%m-%d %H:%i"}&lt;/em&gt; <br />
{message_text}<br />
&lt;p&gt;<br />
{/exp:status_updates:display}
</code>


<p><strong>Tag parameters</strong>(all optional):
<ul>
<li><dfn>member_id</dfn> &mdash; ID of member to display feed. If omited, will display feed for logged in user. member_id="ALL" will display updates from all users</li>
<li><dfn>username</dfn> &mdash; alternatively, provide username to display status updates for</li>
<li><dfn>group_id</dfn> &mdash; display messages from all members in certain group</li>
<li><dfn>sort</dfn> &mdash; sorting order. Can be "asc" (ascending) or "desc" (descending - default)</li>
<li><dfn>limit</dfn> &mdash; number of codes per page, if you want to use pagination</li>
<li><dfn>paginate</dfn> &mdash; place to display pagination links. Can be 'top', 'bottom' or 'both'. Defaults to 'bottom'</li>
<li><dfn>disable="pagination"</dfn> &mdash; disable pagination completely. Useful if you use pagination URL marker for something else, or want to display onlt last N messages.</li>
<li><dfn>backspace="X"</dfn> &mdash; remove X last characters from tag output result</li>
</ul>
</p>


<p><strong>Single variables:</strong>
<ul>
<li><dfn>total_updates</dfn> &mdash; total number of updates</li>
<li><dfn>total_results</dfn> &mdash; total number of updates displayed on page</li>
<li><dfn>count</dfn> &mdash; invites counter on each page</li>
<li><dfn>absolute_count</dfn> &mdash; invites counter throughout all pages</li>
<li><dfn>message_text</dfn> &mdash; text of status update</li>
<li><dfn>message_date format="%Y-%m-%d"</dfn> &mdash; the date when status has been posted. Standard EE date formatting rules apply.</li>
</ul>
</p>    
    
    
<p><strong>Variable pairs:</strong></p>  

  
<p><strong>{paginate}{/paginate}</strong> &mdash; used to format and display pagination links (if you have limit parameter set and there's more than one page)</p>
<ul>
<li><dfn>pagination_links</dfn> &mdash; the actual pagination links</li>
</ul>  
<p>Adavaced pagination (like with channel entries) using {pagination_links}{/pagination_links} tag pair is also possible</p>





	<h2><a name="examples" href="#top">&uarr;</a>Examples</h2>
 
<p>Posting form with posting to social networks and remaining characters counter</p> 
 
<code>
{exp:jquery:script_tag}<br />
&lt;script type="text/javascript" src="http://www.intoeetive.com/themes/third_party/social_update/jquery.maxlength.min.js"&gt;&lt;/script&gt;<br />
&lt;link type="text/css" src="http://www.intoeetive.com/themes/third_party/social_update/jquery.maxlength.css" rel="stylesheet" /&gt;<br />
{exp:status_updates:form return="SAME_PAGE"}<br />
&lt;script type="text/javascript"&gt;<br />
$(document).ready(function(){ <br />
$('#message_text').maxlength({ <br />
    max: {maxlength},<br />
    truncate: true,<br />
    showFeedback: true,<br />
    feedbackTarget: '#maxlength',<br />
    feedbackText: '{r}'<br />
}); <br />
}); <br />
&lt;/script&gt;<br />
&lt;p&gt;Enter your message, {maxlength} characters max, &lt;span id="maxlength">{maxlength}&lt;/span&gt; characters left&lt;/p&gt;<br />
&lt;p&gt;&lt;textarea name="message_text" id="message_text" style="width: 350px; height: 100px;"&gt;&lt;/textarea&gt;&lt;/p&gt;<br />
{providers}<br />
&lt;input type="checkbox" name="{provider_name}" value="yes" /&gt; Post to {provider_title}&lt;br /&gt;<br />
{/providers}<br />
&lt;p&gt;&lt;input type="submit" value="Send" /&gt;&lt;/p&gt;<br />
{/exp:status_updates:form}
</code> 



<p>Status messages page with AJAX form</p> 
 
<code>
{exp:jquery:script_tag}<br />
&lt;script type="text/javascript"&gt;<br />
$(document).ready(function(){ <br />
    $('#status_updates_form').live('submit', function(event){<br />
        event.preventDefault();<br />
        $('#error_container').hide();<br />
        $('#loader').show();<br />
        $('#submit_button').parent().hide();<br />
        $.post(<br />
            '/',<br />
            $('#status_updates_form').serialize(),<br />
            function(msg) {<br />
            	var obj = $.parseJSON(msg);<br />
            	if (obj.status=='error')<br />
            	{<br />
           			$('#error_container').text(obj.text);<br />
           			$('#error_container').show();<br />
           		}<br />
           		else<br />
           		{<br />
           			var toclone = $('.status_message').first();<br />
	   				var cloned = toclone.clone();<br />
	   				cloned.find('.message_text').text(obj.text);<br />
	   				var msgdate = new Date(1000*obj.date);<br />
	   				cloned.find('.message_date').text(msgdate.getFullYear()+'-'+msgdate.getMonth()+'-'+msgdate.getDate()+' '+msgdate.getHours()+':'+msgdate.getMinutes());<br />
	   				cloned.prependTo(toclone);<br />
	   				$('#message_text').val('');<br />
     			}<br />
            	$('#loader').hide();<br />
            	$('#submit_button').parent().show();<br />
            	}
            	);<br />
         });   	<br />
}); <br />
&lt;/script&gt;<br />
&lt;div&gt;<br />
{exp:status_updates:display paginate="top" limit="10"}<br />
{paginate}<br />
{pagination_links}<br />
        &lt;ul&gt;<br />
                {first_page}<br />
                        &lt;li&gt;&lt;a href="{pagination_url}" class="page-first"&gt;First Page&lt;/a&gt;&lt;/li&gt;<br />
                {/first_page}
<br />
                {previous_page}<br />
                        &lt;li&gt;&lt;a href="{pagination_url}" class="page-previous"&gt;Previous Page&lt;/a&gt;&lt;/li&gt;<br />
                {/previous_page}<br />

                {page}<br />
                        &lt;li&gt;&lt;a href="{pagination_url}" class="page-{pagination_page_number} {if current_page}active{/if}"&gt;{pagination_page_number}&lt;/a&gt;&lt;/li&gt;<br />
                {/page}<br />

                {next_page}<br />
                        &lt;li&gt;&lt;a href="{pagination_url}" class="page-next"&gt;Next Page&lt;/a&gt;&lt;/li&gt;<br />
                {/next_page}<br />

                {last_page}<br />
                        &lt;li&gt;&lt;a href="{pagination_url}" class="page-last"&gt;Last Page&lt;/a&gt;&lt;/li&gt;<br />
                {/last_page}<br />
        &lt;/ul&gt;<br />
{/pagination_links}<br />
{/paginate}<br />
&lt;div class="status_message"&gt;<br />
&lt;p&gt;&lt;em class="message_date"&gt;{message_date format="%Y-%m-%d %H:%i"}&lt;/em&gt;&lt;p&gt; <br />
&lt;div class="message_text"&gt;{message_text}&lt;/div&gt;<br />
&lt;/div&gt;<br />
{/exp:status_updates:display}<br />
&lt;/div&gt;<br />
{exp:status_updates:form ajax="yes"}<br />
&lt;p id="error_container" style="color: red; display: none;"&gt;&lt;/p&gt;<br />
&lt;p&gt;&lt;textarea name="message_text" id="message_text" style="width: 350px; height: 100px;"&gt;&lt;/textarea&gt;&lt;/p&gt;<br />
&lt;p&gt;&lt;input type="submit" id="submit_button" value="Send" /&gt;&lt;/p&gt;<br />
&lt;p id="loader" style="display: none"&gt;please wait...&lt;/p&gt;<br />
{/exp:status_updates:form}
</code>  
