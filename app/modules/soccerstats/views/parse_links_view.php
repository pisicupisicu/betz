<?=$this->load->view(branded_view('cp/header'));?>
<h1>Parse link for all stats</h1>
<a href="<?=site_url('admincp/soccerstats/parse_introstats')?> " class="button action_button">Parse Intro Stats zone</a></br>
<a href="<?=site_url('admincp/soccerstats/parse_teams')?> " class="button action_button">Parse Teams by Year</a></br>
<a href="<?=site_url('admincp/soccerstats/parse_introstats')?> " class="button action_button">Parse Goals zone</a></br>
<a href="<?=site_url('admincp/soccerstats/parse_introstats')?> " class="button action_button">Parse Head to Head zone</a></br>
<?=$this->load->view(branded_view('cp/footer'));?>