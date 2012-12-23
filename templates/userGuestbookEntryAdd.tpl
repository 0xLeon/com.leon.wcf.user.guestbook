{include file='documentHeader'}
<head>
	<title>{lang}wcf.user.profile.title{/lang} - {lang}wcf.user.profile.members{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}wcf.user.profile.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="userID" value="{@$user->userID}" />
	<input type="hidden" name="types[]" value="guestbook" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">
	{capture append='additionalMessages'}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
	{/capture}
	
	{include file='userProfileHeader'}
	
	<form method="post" action="index.php?form=UserGuestbookEntry{@$action|ucfirst}{if $action == 'add'}&amp;userID={$user->userID}{elseif $action == 'edit'}&amp;entryID={$entry->entryID}{/if}">
		<div class="border {if $this|method_exists:'getUserProfileMenu' && $this->getUserProfileMenu()->getMenuItems('')|count > 1}tabMenuContent{else}content{/if}">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wcf.user.guestbook.form.entry.action.{@$action}{/lang}</h3>
				
				{if $preview|isset && $preview}
					<div class="messagePreview guestbookEntryPreview">
						{include file='userGuestbookEntryBox'}
					</div>
				{/if}
				
				{if !$this->user->userID || $additionalSettingsFields|isset}
					<fieldset>
						<legend>{lang}wcf.user guestbook.form.entry.settings{/lang}</legend>
						
						{if !$this->user->userID}
							<div class="formElement{if $errorField == 'authorname'} formError{/if}">
								<div class="formFieldLabel">
									<label for="authorname">{lang}wcf.user.username{/lang}</label>
								</div>
								<div class="formField">
									<input type="text" class="inputText" name="authorname" id="authorname" value="{$authorname}" tabindex="{counter name='tabindex'}" />
									{if $errorField == 'authorname'}
										<p class="innerError">
											{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
											{if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
											{if $errorType == 'notAvailable'}{lang}wcf.user.error.username.notUnique{/lang}{/if}
										</p>
									{/if}
								</div>
							</div>
						{/if}
						
						{* TODO: thrash/delete *}
						
						{if $additionalSettingsFields|isset}{@$additionalSettingsFields}{/if}
					</fieldset>
				{/if}
				
				<fieldset>
					<legend>{lang}wcf.user.guestbook.form.entry.message{/lang}</legend>
					
					<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
						<div class="formFieldLabel">
							<label for="text">{lang}wcf.user.guestbook.form.entry.message{/lang}</label>
						</div>
						
						<div class="formField">				
							<textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
							{if $errorField == 'text'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'tooLong'}{lang}wcf.message.error.tooLong{/lang}{/if}
									{* {if $errorType == 'censoredWordsFound'}{lang}wcf.message.error.censoredWordsFound{/lang}{/if} *}
								</p>
							{/if}
						</div>
						
					</div>
					
					{include file='messageFormTabs'}
				</fieldset>
				
				{include file='captcha'}
				
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
		</div>
	</form>
</div>

{include file='footer' sandbox=false}
</body>
</html>
