<?=$this->load->view(branded_view('cp/header'));?>

<h1>All Results Statistics</h1>
<div class="row">
    <div class="span12">
        <a href="<?=site_url('admincp6/livescore/totals_stats')?>" class="btn btn-large btn-info">
        <span class="icon-globe icon-white"></span> Minutes / Goal / Cards Stats
        </a>
        
        <a href="<?=site_url('admincp6/livescore/over_country')?>" class="btn btn-large btn-info">
        <span class="icon-white icon-arrow-up"></span> Over by Country 
        </a>
        
        <a href="<?=site_url('admincp6/livescore/under_country')?>" class="btn btn-large btn-info">
        <span class="icon-white icon-arrow-down"></span> Under by Country
        </a>            

    </div>
</div>
    <br/>
<div class="row">
        <div class="span12">
            <a href="<?=site_url('admincp7/livescore/list_matches_first_goal')?>" class="btn btn-large btn-info">
                <span class="icon-gift icon-white"></span> First Goal By the 10<sup>th</sup> Min (1-90) Stats
            </a>

            <a href="<?=site_url('admincp7/livescore/list_matches_first_goal_not_until')?>" class="btn btn-large btn-info">
                <span class="icon-warning-sign icon-white"></span> First Goal Not Until the 60<sup>th</sup> Min (1-90) Stats
            </a>
        </div>
</div>



<?=$this->load->view(branded_view('cp/footer'));?>









