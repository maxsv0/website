{assign var="items" value=$menu['bottom']}

<ul class="list-inline">
{section name=index loop=$items} 

{if $items[index].url == $page.url}
    <li class="active"><a href="{$items[index].url}">{$items[index].name}</a></li>
{else}
    <li><a href="{$items[index].url}">{$items[index].name}</a></li>
{/if}

{/section}
</ul>
