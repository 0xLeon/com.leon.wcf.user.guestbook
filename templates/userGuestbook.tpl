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
					
					{capture assign=largeButtons}
						{if $userPermissions.canWriteEntry || $additionalLargeButtons|isset}
							<div class="largeButtons">
								<ul>
									{if $userPermissions.canWriteEntry}<li><a href="index.php?form=UserGuestbookEntryAdd&amp;userID={$userID}{@SID_ARG_2ND}" title="{lang}wcf.user.guestbook.form.entry.action.add{/lang}"><img src="{icon}userGuestbookEntryAddM.png{/icon}" alt="" /> <span>{lang}wcf.user.guestbook.form.entry.action.add{/lang}</span></a></li>{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						{/if}
					{/capture}
					
					{@$largeButtons}
				</div>
				
				{if $entries|count > 0}
					{foreach from=$entries item=entry}
						<a id="guestbookEntry{$entry->entryID}"></a>
						<div class="message border" style="position: relative; display: block;">
							<div class="messageInner">
								<div class="container-1">
									<div class="guestbookEntryAvatar" style="float: left; height: 100%; margin: 0px; padding: 0px;">
										<div class="userAvatar" style="margin: 7px 13px; padding: 0px; float: left;">
											{if $entry->getAuthor()->getAvatar()}
												{assign var=tmp value=$entry->getAuthor()->getAvatar()->setMaxSize(50, 50)}
												
												{if $entry->getAuthor()->userID}<a href="{* profile link *}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}" style="display: block;">{/if}{@$entry->getAuthor()->getAvatar()}{if $entry->getAuthor()->userID}</a>{/if}
											{else}
												{if $entry->getAuthor()->userID}<a href="{* profile link *}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}" style="display: block;">{/if}<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 50px; height: 50px;" />{if $entry->getAuthor()->userID}</a>{/if}
											{/if}
										</div>
									</div>
									
									{* TODO: box doesn't use complete height, DOM Inspector shows error I think... *}
									<div class="guestbookEntryBody" style="border-left: 1px dotted; padding: 0px 15px; margin-left: 76px;">
										<div class="guestbookEntryCredits">
											<p class="username">
												{if $entry->getAuthor()->userID}<a href="{* profile link *}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}">{/if}{$entry->username}{if $entry->getAuthor()->userID}</a>{/if}
											</p>
											<p>{@$entry->time|time}</p>
										</div>
										
										<div id="guestbookEntryMessage{$entry->entryID}" class="messageBody" style="border-top: 1px dotted; margin-top: 4px; padding-top: 2px;">
											<p>{$entry->message}</p>
										</div>
									</div>
								</div>
								
								<div class="container-2">
									<div class="guestbookEntryComments" style="border-top: 1px dotted;">
										<a id="guestbookEntryCommentLink{$entry->entryID}" title="{lang}wcf.user.guestbook.comment.load{/lang}" style="display: block; padding: 2px 15px 2px 13px;">{lang commentCount=$entry->commentCount}wcf.user.guestbook.comment.comments{/lang}</a>
									</div>
								</div>
							</div>
						</div>
					{/foreach}
				
					<div class="contentFooter">
						{@$pagesLinks}
						
						{@$largeButtons}
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
