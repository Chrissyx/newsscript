{foreach Messages::getInstance()->getMessages() as $curMessageClass => $curMessages}  <div class="alert alert-{if $curMessageClass == Messages::SUCCESS}success{elseif $curMessageClass == Messages::INFO}info{elseif $curMessageClass == Messages::WARNING}warning{elseif $curMessageClass == Messages::ERROR}danger{/if} alert-dismissable" role="alert">
   <button aria-hidden="true" class="close" data-dismiss="alert" type="button">&times;</button>
   <ul>{foreach $curMessages as $curMessage}
    <li>{$curMessage}</li>{/foreach}
   </ul>
  </div>{/foreach}