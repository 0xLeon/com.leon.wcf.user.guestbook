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
	<input type="hidden" name="types[]" value="guestbookEntry" />
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
					{pages print=true assign=pagesLinks link="index.php?page=UserGuestbook&userID=$user->userID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}
					
					{* large buttons *}
				</div>
				
				{if $entries|count > 0}
					{foreach from=$entries item=entry}
						{* entry output *}
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
