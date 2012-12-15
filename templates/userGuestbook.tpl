{include file="documentHeader"}
<head>
	<title>{lang}wcf.user.profile.title{/lang} - {lang}wcf.user.profile.members{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	{if $userPermissions.canWriteEntry}
		{include file='multiQuote' formURL="index.php?form=UserGuestbookEntryAdd&ownerID=$user->userID"|concat:SID_ARG_2ND_NOT_ENCODED}
	{else}
		{include file='multiQuote'}
	{/if}
	
	{if $modPermissions.canHandleEntry}
		{include file='guestbookEntryInlineEdit' pageType='profile'}
	{/if}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}wcf.user.profile.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="ownerID" value="{@$user->userID}" />
	<input type="hidden" name="types[]" value="guestbook" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">
	{include file='userProfileHeader'}
	
	<div class="border {if $this|method_exists:'getUserProfileMenu' && $this->getUserProfileMenu()->getMenuItems('')|count > 1}tabMenuContent{else}content{/if}">
		<div class="container-1">
			<div class="contentBox">
				<h3 class="subheadline">{lang}wcf.user.guestbook{/lang}</h3>
				
				<div class="contentHeader">
					{pages print=true assign=pagesLinks link="index.php?page=UserGuestbook&userID=$userID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}
					
					{* large buttons *}
				</div>
				
				{if $entries|count > 0}
					{foreach from=$entries item=entry}
						{* <pre id="entry{$entry->entryID}" style="border: 1px solid #000; padding: 5px;">{@$entry|print_r:true}</pre> *}
						
						<a id="guestbookEntry{$entry->entryID}"></a>
						<div class="message">
							<div class="userAvatar" style="float: left;">
								{if $entry->getAuthor()->getAvatar()}
									{assign var=tmp value=$entry->getAuthor()->getAvatar()->setMaxSize(50, 50)}
									
									{if $entry->getAuthor()->userID}<a href="{* profile link *}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}">{/if}{@$entry->getAuthor()->getAvatar()}{if $entry->getAuthor()->userID}</a>{/if}
								{else}
									{if $entry->getAuthor()->userID}<a href="{* profile link *}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}">{/if}<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 50px; height: 50px;" />{if $entry->getAuthor()->userID}</a>{/if}
								{/if}
							</div>
							
							<p class="username">
								{if $entry->getAuthor()->userID}<a href="{* profile link *}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}">{/if}{$entry->username}{if $entry->getAuthor()->userID}</a>{/if}
							</p>
							
							<p id="guestbookEntryMessage{$entry->entryID}" class="messageBody" style="margin-left: 120px;">
								{$entry->message}
							</p>
						</div>
					{/foreach}
				
					<div class="contentFooter">
						{@$pagesLinks}
						
						{* large buttons *}
					</div>
				{else}
					{if $userPermissions.isOwner}
						{lang}wcf.user.guestbook.owner.noEntries{/lang}
					{else}
						{lang}wcf.user.guestbook.noEntries{/lang}
					{/if}
				{/if}
			</div>
		</div>
	</div>
</div>

{include file='footer' sandbox=false}
</body>
</html>
