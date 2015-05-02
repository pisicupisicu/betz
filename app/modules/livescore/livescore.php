<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Livescore Module Definition
 *
 * Declares the module, update code, etc.
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 *
 */
class Livescore extends Module 
{

    var $version = '1.0';
    var $name = 'livescore';

    function __construct()
    {
        // set the active module
        $this->active_module = $this->name;
        parent::__construct();
    }

    /*
     * Pre-admin function
     *
     * Initiate navigation in control panel
     */

    function admin_preload()
    {
        $this->CI->admin_navigation->child_link('livescore', 10, 'List competitions', site_url('admincp/livescore/list_competitions'));
        $this->CI->admin_navigation->child_link('livescore', 20, 'List teams', site_url('admincp/livescore/list_teams'));
        $this->CI->admin_navigation->child_link('livescore', 29, 'List matches pre', site_url('admincp3/livescore/list_matches_pre'));
        $this->CI->admin_navigation->child_link('livescore', 30, 'List matches', site_url('admincp/livescore/list_matches'));
        $this->CI->admin_navigation->child_link('livescore', 31, 'List matches with goals and cards', site_url('admincp7/livescore/list_matches'));
        $this->CI->admin_navigation->child_link('livescore', 40, 'Bets List', site_url('admincp2/livescore/list_bets'));
        $this->CI->admin_navigation->child_link('livescore', 50, 'Admin Tools', site_url('admincp2/livescore/admin_tools'));
        $this->CI->admin_navigation->child_link('livescore', 60, 'Parse Matches', site_url('admincp3/livescore/parse_matches'));
        $this->CI->admin_navigation->child_link('livescore', 70, 'Stats Bets', site_url('admincp4/livescore/profit_loss_stats'));
        $this->CI->admin_navigation->child_link('livescore', 80, 'Stats Results', site_url('admincp6/livescore/list_statistics'));
    }

}
