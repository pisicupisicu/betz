<?=$this->load->view(branded_view('cp/header'));?>

<h1>Admin Tools</h1>
<div class="row">
    <div class="span12">
        <a href="<?=site_url('admincp4/livescore/list_currencies')?>" class="btn btn-large btn-info">
        <span class="icon-globe icon-white"></span> List Currencies
        </a>

        <a href="<?=site_url('admincp2/livescore/list_leagues')?>" class="btn btn-large btn-info offset1">
        <span class="icon-list icon-white"></span> List Leagues
        </a>
        
        <a href="<?=site_url('admincp4/livescore/list_houses')?>" class="btn btn-large btn-info offset1">
        <span class="icon-home icon-white"></span> List Bookmakers
        </a>
    </div>
</div>
<br><br>
<div class="row">
    <div class="span12">
        <a href="<?=site_url('admincp2/livescore/list_methods')?>" class="btn btn-large btn-info">
        <span class="icon-thumbs-up  icon-white"></span> List Strategies&nbsp;
        </a>
        
        <a href="<?=site_url('admincp2/livescore/list_markets')?>" class="btn btn-large btn-info offset1">
        <span class="icon-eye-close icon-white"></span> List Markets&nbsp;
        </a>
        
        <a href="<?=site_url('admincp2/livescore/list_strategies')?>" class="btn btn-large btn-info offset1">
        <span class="icon-road icon-white"></span> List Steps
        </a>
   </div>
</div>
<br><br>
<div class="row">
    <div class="span12">
       <a href="<?=site_url('admincp2/livescore/list_countries')?>" class="btn btn-large btn-info">
        <span class="icon-flag  icon-white"></span> List Countries
        </a>         
    </div>
</div>
<?=$this->load->view(branded_view('cp/footer'));?>