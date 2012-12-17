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
				<h3 class="subHeadline">{lang}wcf.user.guestbook{/lang}</h3>
				
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
					<div class="guestbookEntries">
						{foreach from=$entries item=entry}
							{include file='userGuestbookEntryBox' entry=$entry}
						{/foreach}
					</div>
					
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
