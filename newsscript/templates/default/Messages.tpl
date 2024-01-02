{foreach Messages::getInstance()->getMessages() as $curMessageClass => $curMessages}
  <p class="{if $curMessageClass == Messages::SUCCESS}success{elseif $curMessageClass == Messages::INFO}info{elseif $curMessageClass == Messages::WARNING}warning{elseif $curMessageClass == Messages::ERROR}error{/if}">
   <ul>{foreach $curMessages as $curMessage}
    <li>{$curMessage}</li>{/foreach}
   </ul>
  </p>{/foreach}