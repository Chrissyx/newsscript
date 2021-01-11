  <h3>{Title::getInstance()->getTitle()}</h3>
{include file='Messages.tpl'}
  <form action="index.php?action=login{$smarty.const.SID_AMPER}" class="form-horizontal" method="post" role="form">
   <div class="form-group">
    <label class="col-sm-2 control-label" for="userName">{Language::getInstance()->getString('user_name')}</label>
    <div class="col-sm-10">
     <input class="form-control" id="userName" name="userName" placeholder="{Language::getInstance()->getString('enter_user_name')}" type="text" value="{$userName|escape}" />
    </div>
   </div>
   <div class="form-group" style="margin-bottom:0;">
    <label class="col-sm-2 control-label" for="password">{Language::getInstance()->getString('password')}</label>
    <div class="col-sm-10">
     <input class="form-control" id="password" name="password" placeholder="{Language::getInstance()->getString('enter_password')}" type="password" value="{$password|escape}" />
     <p class="help-block text-info"><a href="index.php?action=login&amp;mode=request{$smarty.const.SID_AMPER}">{Language::getInstance()->getString('password_forgotten')}</a></p>
    </div>
   </div>
   <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
     <div class="checkbox">
      <label>
       <input{if $stayLoggedIn} checked="checked"{/if} id="stayLoggedIn" name="stayLoggedIn" type="checkbox" value="true" /> {Language::getInstance()->getString('login_automatically_each_visit')}
      </label>
     </div>
    </div>
   </div>
   <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
     <input class="btn btn-primary" type="submit" value="{Language::getInstance()->getString('login')}" />
    </div>
   </div>
   <input name="mode" type="hidden" value="login" />
  </form>