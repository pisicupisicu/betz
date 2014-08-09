    	var parts_count = 0;
	var parts_counted = 0;
	
	document.parts_count_inrease = function()
	{
		parts_count++;
	};
	
	document.parts_counted_inrease = function()
	{
		parts_counted++;
	};
	
	document.have_all_parts = function()
	{
		return parts_count <= parts_counted;
	};
	


var match_history_tab_stages = {17:1,18:2,19:3,20:4,21:5};

var actual_tab = null;
var detail_tab_url_recognize = false;
var detail_selected_tab = [];
var detail_previous_selected_tab = [];
var detail_hashchangeIgnoreNext = false;
var iframe_external = false;

var detail_tabs = {
	selected: "summary",
	tabs: {
		tab_summary: {
			tabElement: {id: "li-match-timeline"},
			contentElement: {id: "tab-match-summary", innerElmId: "summary"},
			valid_status: false,
			additionalTabs: ["live_centre", "commentary_preview", "player_statistics_preview"],
			additionalBlocks: ["odds", "submenu"],
			// additionalBlocks: ["odds", "bonus_offers", "submenu"],
			urlName: "match-summary",
			tabName: "Match Summary"
		},
		tab_match_history: {
			tabElement: {id: "li-match-history"},
			contentElement: {id: "tab-match-history", innerElmId: "match-history"},
			valid_status: false,
			additionalTabs: ["live_centre"],
			additionalBlocks: ["odds", "submenu"],
			// additionalBlocks: ["odds", "bonus_offers", "submenu"],
			selected: "1_history",
			loaded: false,
			tabs: {
				tab_1_history: {
					tabElement: {id: "mhistory-1-history"},
					contentElement: {id: "tab-mhistory-1-history"},
					urlName: "1"
				},
				tab_2_history: {
					tabElement: {id: "mhistory-2-history"},
					contentElement: {id: "tab-mhistory-2-history"},
					urlName: "2"
				},
				tab_3_history: {
					tabElement: {id: "mhistory-3-history"},
					contentElement: {id: "tab-mhistory-3-history"},
					urlName: "3"
				},
				tab_4_history: {
					tabElement: {id: "mhistory-4-history"},
					contentElement: {id: "tab-mhistory-4-history"},
					urlName: "4"
				},
				tab_5_history: {
					tabElement: {id: "mhistory-5-history"},
					contentElement: {id: "tab-mhistory-5-history"},
					urlName: "5"
				}
			},
			urlName: "point-by-point", 			tabName: "Point by Point"
		},
		tab_lineups: {
			tabElement: {id: "li-match-lineups"},
			contentElement: {id: "tab-match-lineups", innerElmId: "lineups"},
			valid_status: false,
			additionalTabs: ["live_centre"],
			additionalBlocks: ["odds", "submenu"],
			// additionalBlocks: ["odds", "bonus_offers", "submenu"],
			selected: "1_lineup",
			loaded: false,
			tabs: {
				tab_1_lineup: {
					tabElement: {id: "lineups-1-lineup"},
					contentElement: {id: "tab-lineups-1-lineup"},
					urlName: "1"
				},
				tab_2_lineup: {
					tabElement: {id: "lineups-2-lineup"},
					contentElement: {id: "tab-lineups-2-lineup"},
					urlName: "2"
				},
				tab_3_lineup: {
					tabElement: {id: "lineups-3-lineup"},
					contentElement: {id: "tab-lineups-3-lineup"},
					urlName: "3"
				},
				tab_4_lineup: {
					tabElement: {id: "lineups-4-lineup"},
					contentElement: {id: "tab-lineups-4-lineup"},
					urlName: "4"
				}
			},
			urlName: "lineups",
			tabName: "Lineups"
		},
		tab_odds_comparison: {
			selected: "1x2",
			loaded: false,
			tabElement: {id: "li-match-odds-comparison"},
			contentElement: {id: "tab-match-odds-comparison", innerElmId: "odds-comparison"},
			valid_status: false,
			urlName: "odds-comparison",
			tabName: "Odds Comparison"
		},
		tab_head_2_head: {
			tabElement: {id: "li-match-head-2-head"},
			contentElement: {id: "tab-match-head-2-head", innerElmId: "head-2-head"},
			valid_status: false,
			additionalBlocks: ["odds"],
			selected: "all_h2h",
			loaded: false,
			tabs: {
				tab_all_h2h: {
					tabElement: {id: "h2h-overall"},
					contentElement: {id: "tab-h2h-overall"},
					urlName: "overall"
				},
				tab_1_h2h: {
					tabElement: {id: "h2h-home"},
					contentElement: {id: "tab-h2h-home"},
					urlName: "home"
				},
				tab_2_h2h: {
					tabElement: {id: "h2h-away"},
					contentElement: {id: "tab-h2h-away"},
					urlName: "away"
				}
			},
			urlName: "h2h",
			tabName: "H2H"
		},
		tab_tv: {
			tabElement: {id: "li-match-tv"},
			contentElement: {id: "tab-match-tv", innerElmId: "tv"},
			valid_status: false,
			additionalBlocks: ["odds"],
			// additionalBlocks: ["odds","bonus_offers"],
			urlName: "tv",
			tabName: "TV"
		},
		tab_highlights: {
			tabElement: {id: "li-match-highlights"},
			contentElement: {id: "tab-match-highlights", innerElmId: "highlights"},
			valid_status: false,
			additionalBlocks: ["odds"],
			urlName: "video",
			tabName: "Video"
		},
		tab_statistics: {
			tabElement: {id: "li-match-statistics"},
			contentElement: {id: "tab-match-statistics", innerElmId: "statistics"},
			valid_status: false,
			additionalTabs: ["live_centre"],
			additionalBlocks: ["odds","submenu"],
			// additionalBlocks: ["odds","bonus_offers","submenu"],
			selected: "0_statistic",
			loaded: false,
			tabs: {
				tab_0_statistic: {
					tabElement: {id: "statistics-0-statistic"},
					contentElement: {id: "tab-statistics-0-statistic"},
					urlName: "0"
				},
				tab_1_statistic: {
					tabElement: {id: "statistics-1-statistic"},
					contentElement: {id: "tab-statistics-1-statistic"},
					urlName: "1"
				},
				tab_2_statistic: {
					tabElement: {id: "statistics-2-statistic"},
					contentElement: {id: "tab-statistics-2-statistic"},
					urlName: "2"
				},
				tab_3_statistic: {
					tabElement: {id: "statistics-3-statistic"},
					contentElement: {id: "tab-statistics-3-statistic"},
					urlName: "3"
				},
				tab_4_statistic: {
					tabElement: {id: "statistics-4-statistic"},
					contentElement: {id: "tab-statistics-4-statistic"},
					urlName: "4"
				},
				tab_5_statistic: {
					tabElement: {id: "statistics-5-statistic"},
					contentElement: {id: "tab-statistics-5-statistic"},
					urlName: "5"
				},
				tab_6_statistic: {
					tabElement: {id: "statistics-6-statistic"},
					contentElement: {id: "tab-statistics-6-statistic"},
					urlName: "6"
				},
				tab_7_statistic: {
					tabElement: {id: "statistics-7-statistic"},
					contentElement: {id: "tab-statistics-7-statistic"},
					urlName: "7"
				},
				tab_8_statistic: {
					tabElement: {id: "statistics-8-statistic"},
					contentElement: {id: "tab-statistics-8-statistic"},
					urlName: "8"
				},
				tab_9_statistic: {
					tabElement: {id: "statistics-9-statistic"},
					contentElement: {id: "tab-statistics-9-statistic"},
					urlName: "9"
				},
				tab_10_statistic: {
					tabElement: {id: "statistics-10-statistic"},
					contentElement: {id: "tab-statistics-10-statistic"},
					urlName: "10"
				}
			},
			urlName: "match-statistics",
			tabName: "Statistics"
		},
		tab_commentary: {
			tabElement: {id: "li-match-commentary"},
			contentElement: {id: "tab-match-commentary", innerElmId: "commentary"},
			valid_status: false,
			additionalTabs: ["live_centre"],
			additionalBlocks: ["odds","submenu"],
			// additionalBlocks: ["odds","bonus_offers","submenu"],
			selected: "0_phrase",
			loaded: false,
			tabs: {
				tab_0_phrase: {
					tabElement: {id: "commentary-0-phrase"},
					contentElement: {id: "tab-commentary-0-phrase"},
					urlName: "0"
				},
				tab_1_phrase: {
					tabElement: {id: "commentary-1-phrase"},
					contentElement: {id: "tab-commentary-1-phrase"},
					urlName: "1"
				},
			},
			urlName: "live-commentary",
			tabName: "LIVE Commentary"
		},
		tab_live: {
			tabElement: {id: "li-match-live-table"},
			contentElement: {id: "tab-match-live-table", innerElmId: "live-table"},
			valid_status: false,
			urlName: "live-table",
			tabName: "LIVE Table"
		},
		tab_standings: {
			tabElement: {id: "li-match-standings"},
			contentElement: {id: "tab-match-standings", innerElmId: "standings"},
			valid_status: false,
			urlName: "standings",
			tabName: "Standings"
		},
		tab_draw: {
			tabElement: {id: "li-match-draw"},
			contentElement: {id: "tab-match-draw", innerElmId: "draw"},
			valid_status: false,
			urlName: "draw",
			tabName: "Draw"
		},
		tab_player_statistics: {
			tabElement: {id: "li-match-player-statistics"},
			contentElement: {id: "tab-match-player-statistics", innerElmId: "player-statistics"},
			valid_status: false,
			additionalTabs: ["live_centre"],
			additionalBlocks: ["odds","submenu"],
			urlName: "player-statistics",
			tabName: "Player Statistics",
			selected: "0_player_statistic",
			tabs: {
				tab_0_player_statistic: {
					tabElement: {id: "player-statistics-0-statistic"},
					contentElement: {id: "tab-player-statistics-0-statistic"},
					urlName: "0"
				},
				tab_1_player_statistic: {
					tabElement: {id: "player-statistics-1-statistic"},
					contentElement: {id: "tab-player-statistics-1-statistic"},
					urlName: "1"
				},
				tab_2_player_statistic: {
					tabElement: {id: "player-statistics-2-statistic"},
					contentElement: {id: "tab-player-statistics-2-statistic"},
					urlName: "2"
				},
				tab_3_player_statistic: {
					tabElement: {id: "player-statistics-3-statistic"},
					contentElement: {id: "tab-player-statistics-3-statistic"},
					urlName: "3"
				}
			}
		}
	}
};

var detail_additional_tabs = {
	tab_live_centre: {tabElement: {id: "li-match-summary"}},
	tab_commentary_preview: {contentElement: {id: "tab-match-commentary-preview", innerElmId: "commentary-preview"}, valid_status: false},
	tab_player_statistics_preview: {contentElement: {id: "tab-match-player-statistics-preview", innerElmId: "player-statistics-preview"}, valid_status: false}
};

var detail_additional_blocks = {
	b_odds: {contentElement: {id: "tab-odds", display: ["none",""]}},
	b_bonus_offers: {contentElement: {id: "tab-bonus-offers", display: ["none",""]}},
	b_submenu: {contentElement: {id: "detail-submenu-bookmark", display: ["none","block"]}}
};

function detail_hashchange()
{
	if (!detail_hashchangeIgnoreNext)
		detail_tab();
	detail_hashchangeIgnoreNext = false;
};

function get_tab_name(name)
{
	return "tab_"+name.replace(/-/g, "_");
};

function is_visible_tab(tab)
{
	if(tab == detail_tabs.selected)
		return true;

	selected_tab_data = detail_tabs.tabs[get_tab_name(actual_tab)];

	if(typeof selected_tab_data.additionalTabs == 'undefined')
		return false;

	var length = selected_tab_data.additionalTabs.length;
	for(var i = 0; i < length; i++) {
		if(selected_tab_data.additionalTabs[i] == tab) return true;
	}

	return false;
};

function get_tab_by_name(tab_name)
{
	tab_name = get_tab_name(tab_name);

	var tab_to_return = detail_tabs.tabs[tab_name];
	if(typeof tab_to_return == 'undefined')
		tab_to_return = detail_additional_tabs[tab_name];

	return tab_to_return;
};

function detail_part_invalidate(tab_name)
{
	var tab_to_invalidate = get_tab_by_name(tab_name);

	if(typeof tab_to_invalidate == 'undefined')
		return null;

	tab_to_invalidate.valid_status = false;
	if(!is_visible_tab(tab_name))
	{
		preload_show(tab_to_invalidate.contentElement.innerElmId+'-preload');
		// nechceme, aby player-statistics-preview problikávalo
		if (tab_name != 'player-statistics-preview' && tab_name != 'player-statistics')
		{
		    $('#'+tab_to_invalidate.contentElement.innerElmId+'-content').empty();
		}
	}
};

function detail_part_validate(tab_name)
{
	var tab_to_validate = get_tab_by_name(tab_name);
	if(typeof tab_to_validate == 'undefined')
		return null;

	tab_to_validate.valid_status = true;
};

function detail_part_valid(tab_name)
{
	var tab_to_validate = get_tab_by_name(tab_name);

	if(typeof tab_to_validate['tabName'] != 'undefined' &&
		!(sport == 'golf' && tab_name == 'summary' && typeof participantEncodedIds != 'undefined' && participantEncodedIds.length == 1))
	{
		add_tab_name_to_page_title(tab_to_validate['tabName']);

		// Pro výpis názvu tabu do title ikony sdílení na FB atd.:
		// add_tab_name_to_title(tab_to_validate['tabName']);
	}

	if(typeof tab_to_validate == 'undefined')
		return null;

	return tab_to_validate.valid_status;
};

function detail_load()
{
	// Call refresh count check
	try
	{
		if(document.have_all_parts())
		{
			detail_loaded();
			document.refresh_alert('detail');
		}
	} catch(e) {}

	backup_eu_odds_and_betslip_in_html('tab-odds');
	get_handicap_in_new_format('tab-odds');
	var oddsFormat = get_odds_format()
	if(oddsFormat != 'eu')
		get_odds_in_new_format(oddsFormat,'tab-odds');
};

function detail_loaded()
{
	if(event_stage_type_id == 1)
		fix_tennis_mh();
	document.getElementById('preload-all').className = 'hidden';
	document.getElementById('content-all').className = '';
};

function detail_event_stage_type_changed()
{
	fix_tennis_mh();
	set_detail_bet_icon_states("tab-odds");
	set_detail_bet_icon_states("tv-content");
	set_detail_bet_icon_states("odds-comparison-content");
};

function DetailPage(hlTitles, hlTitlePeriod)
{
	this.hlTitleIntervalId = null;
	this.hlTitleT = null;
	this.hlTitlePeriod = hlTitlePeriod;
	this.hlTitles = hlTitles;
	this.hlTitleIndex = 0;

	this.hlTitle_on = function(detailPageName, t)
	{
		this.hlTitleT = t * 1000;
		this.hlTitleIntervalId = setInterval(detailPageName + ".hlTitle()", this.hlTitlePeriod);
		this.hlTitle();
	};

	this.hlTitle_off = function()
	{
		clearInterval(this.hlTitleIntervalId);
		document.title = this.hlTitles[0];
	};

	this.hlTitle = function()
	{
		if (this.hlTitleT > 0)
		{
			this.hlTitleIndex = 1 - this.hlTitleIndex;
			document.title = this.hlTitles[this.hlTitleIndex];
			this.hlTitleT -= this.hlTitlePeriod;
		}
		else
			this.hlTitle_off();
	};
};

function get_detail_subtab(tab_path)
{
	var sub_tab = detail_tabs;
	for (var i in tab_path)
	{
		sub_tab = sub_tab.tabs["tab_" + tab_path[i]];
	}
	return sub_tab;
};

var detail_tab_onDetailTabShowCallbacks = [];
var detail_tab_onDetailTabHideCallbacks = [];
var detail_tab_current_tab = null;


function detail_tab_runOnDetailTabShowCallbacks(tabName, lastSelectedTabName)
{
	var InArray = function(varName, arrayObj)
	{
		if(!Array.prototype.indexOf)
		{
			for (var i =  0, j = arrayObj.length; i < j; i++)
			{
				if (arrayObj[i] === varName)
				{
					return i;
				}
			}
			return -1;
		}

		return arrayObj.indexOf(varName);
	}

	if (detail_tab_current_tab !== null)
	{
		for(var i in detail_tab_onDetailTabHideCallbacks)
		{
			if (typeof detail_tab_current_tab == "string" && detail_tab_onDetailTabHideCallbacks[i].tabName == detail_tab_current_tab
				|| detail_tab_current_tab instanceof Array && InArray(detail_tab_onDetailTabHideCallbacks[i].tabName, detail_tab_current_tab) != -1)
			{
				detail_tab_onDetailTabHideCallbacks[i].callback(tabName, lastSelectedTabName);
			}
		}
	}

	detail_tab_current_tab = tabName;


	for(var i in detail_tab_onDetailTabShowCallbacks)
	{
		if (typeof tabName == "string" && detail_tab_onDetailTabShowCallbacks[i].tabName == tabName
			|| tabName instanceof Array && InArray(detail_tab_onDetailTabShowCallbacks[i].tabName, tabName) != -1)
		{
			detail_tab_onDetailTabShowCallbacks[i].callback(tabName, lastSelectedTabName);
		}
	}

};

function detail_tab_addOnDetailTabShowCallback(callback,tabName)
{
	detail_tab_onDetailTabShowCallbacks.push({callback: callback, tabName: tabName});
};

function detail_tab_addOnDetailTabHideCallback(callback,tabName)
{
	detail_tab_onDetailTabHideCallbacks.push({callback: callback, tabName: tabName});
};

function detail_tab(selected_tab, delayed, preventSetDefaultSubtab)
{
	var i, url_update = true;
	var additionalTabs = {};
	var additionalBlocks = {};
	var tab = [];
	var current_url_anchors;
	var url_anchors;

	if (typeof selected_tab == "string")
	{
		tab.push(selected_tab.replace(/-/g, "_"));
	}
	else if (selected_tab instanceof Array)
	{
		for (i in selected_tab)
		{
			tab.push(selected_tab[i].replace(/-/g, "_"));
		}
	}
	else
		detail_tab_url_recognize = true;

	if (!detail_tab_url_recognize && preventSetDefaultSubtab == 1 && typeof this.detail_tabs.tabs['tab_' + tab[0]] != 'undefined')
	{
		this.detail_tabs.tabs['tab_' + tab[0]].preventSetDefaultSubtab = 1;
	}

	if (window.location.hash.length)
	{
		current_url_anchors = window.location.hash.substr(1);
		url_anchors = current_url_anchors.split(";");
	}
	else
	{
		current_url_anchors = "";
		url_anchors = [];
	}

	if (sport_id == 13 && event_stage_type_id == 2 && selected_tab == 'player-statistics' && this.detail_tabs.tabs.tab_player_statistics.preventSetDefaultSubtab != 1 && !delayed)
	{
		var active_subtab = 0;

		if (typeof fs_detail != 'undefined' && fs_detail.DB == 27)
			active_subtab = 2;

		if (typeof fs_detail != 'undefined' && fs_detail.DR == 1)
			active_subtab ++;

		var have_player_stat = $('li[id^=player-statistics-'+ active_subtab +'-statistic]').length;
		if(have_player_stat)
			detail_tabs.tabs.tab_player_statistics.selected = active_subtab + '_player_statistic';
	}

	if (selected_tab != 'match-history' && event_stage_type_id == 2 && typeof match_history_tab_stages[event_stage_id] != 'undefined')
	{
		detail_tabs.tabs.tab_match_history.selected = match_history_tab_stages[event_stage_id] + '_history';
	}

	if (selected_tab == 'match-history')
	{
		if (typeof match_history_tab_stages[event_stage_id] != 'undefined')
		{
			detail_tabs.tabs.tab_match_history.selected = ($("#tab-mhistory-" + match_history_tab_stages[event_stage_id] + "-history").length ?
				match_history_tab_stages[event_stage_id]:
				(match_history_tab_stages[event_stage_id]-1))  + '_history';
		}
	}

	if (!delayed)
	{

		detail_selected_tab = tab;
	}

	var group_tab = detail_tabs;
	var i = 0;
	var result,tab_name;

	while (group_tab && group_tab.tabs && (typeof group_tab.loaded == "undefined" || group_tab.loaded))
	{
		tab_name = null;
		if (i < detail_selected_tab.length)
		{
			tab_name = detail_selected_tab[i];
			url_anchors[i] = group_tab.tabs["tab_" + tab_name].urlName;
		}
		else if (detail_tab_url_recognize)
		{
			for (j in group_tab.tabs)
			{
				if (group_tab.tabs[j].urlName && group_tab.tabs[j].urlName == url_anchors[i])
				{
					result = /^tab_(.+)$/.exec(j);
					if (result)
					{
						contentElement = $("#"+group_tab.tabs[j].contentElement.id).get(0);
						if(typeof contentElement == 'undefined')
							continue;
						tab_name = result[1];
					}
					break;
				}
			}
		}

		if (tab_name != null)
		{
			group_tab.selected = tab_name;
		}
		else
		{
			tab_name = group_tab.selected;
			url_anchors[i] = group_tab.tabs["tab_" + tab_name].urlName;
		}

		group_tab = group_tab.tabs["tab_" + tab_name];
		if (group_tab.additionalTabs)
		{
			for (j in group_tab.additionalTabs)
			{
				additionalTabs["tab_" + group_tab.additionalTabs[j]] = true;
			}
		}
		if (group_tab.additionalBlocks)
		{
			for (j in group_tab.additionalBlocks)
			{
				additionalBlocks["b_" + group_tab.additionalBlocks[j]] = true;
			}
		}
		detail_selected_tab[i] = tab_name;
		i++;
	}

	// select/unselect tabs, shows/hides tab contents
	var tabElement;
	var contentElement;
	group_tab = detail_tabs;
	for (i in detail_selected_tab)
	{
		for (j in group_tab.tabs)
		{
			result = /^tab_(.+)$/.exec(j);
			tab_name = result ? result[1] : null;
			tabElement = (
				group_tab.tabs[j].tabElement
					? document.getElementById(group_tab.tabs[j].tabElement.id)
					: null
			);
			contentElement = (
				group_tab.tabs[j].contentElement
					? document.getElementById(group_tab.tabs[j].contentElement.id)
					: null
			);
			if (tab_name == detail_selected_tab[i])
			{
				if (tabElement)
					$(tabElement).addClass('selected');
				if (contentElement)
					contentElement.style.display = 'block';
				}
			else
			{
				if (tabElement)
					$(tabElement).removeClass('selected');
				if (contentElement)
					contentElement.style.display = 'none';
			}
		}
		if (!group_tab.tabs || !group_tab.tabs["tab_" + detail_selected_tab[i]])
			break;
		group_tab = group_tab.tabs["tab_" + detail_selected_tab[i]];
	}

	for (i in detail_additional_tabs)
	{
		tabElement = (
			detail_additional_tabs[i].tabElement
				? document.getElementById(detail_additional_tabs[i].tabElement.id)
				: null
		);
		contentElement = (
			detail_additional_tabs[i].contentElement
				? document.getElementById(detail_additional_tabs[i].contentElement.id)
				: null
		);
		if (additionalTabs[i])
		{
			if (tabElement)
				$(tabElement).addClass('selected');
			if (contentElement)
				contentElement.style.display = 'block';
			}
		else
		{
			if (tabElement)
				$(tabElement).removeClass('selected');
			if (contentElement)
				contentElement.style.display = 'none';
		}
	}

	for (i in detail_additional_blocks)
	{
		contentElement = (
			detail_additional_blocks[i].contentElement
				? document.getElementById(detail_additional_blocks[i].contentElement.id)
				: null
		);
		if (contentElement)
		{
			if (additionalBlocks[i])
				contentElement.style.display = detail_additional_blocks[i].contentElement.display[1];
			else
				contentElement.style.display = detail_additional_blocks[i].contentElement.display[0];
		}
	}

	if (detail_selected_tab[0] == "summary" && !detail_part_valid("summary"))
	{
		if (event_stage_id == 1)
		{
			var summaryContentElement = document.getElementById("summary-content");
			if (summaryContentElement)
			{
				summaryContentElement.innerHTML = '<div class="nodata-block">No live score information available now, the match has not started yet.</div>';
				preload_hide("summary-preload");
			}
		}
		else
		{
			updater.doc_update("detail-summary");
		}
		detail_part_validate("summary");
	}

	if (detail_selected_tab[0] == "odds_comparison" && !detail_part_valid("odds_comparison"))
	{
		updater.doc_update("detail-odds-comparison");
		detail_part_validate("odds_comparison");
		get_detail_subtab(["odds_comparison"]).loaded = true;
		url_update = false;
	}

	if (detail_selected_tab[0] == "head_2_head" && !detail_part_valid("head_2_head"))
	{
		updater.doc_update("detail-head-2-head");
		detail_part_validate("head_2_head");
		detail_tabs.tabs.tab_head_2_head.loaded = true;
		url_update = false;
	}

	if (detail_selected_tab[0] == "lineups" && !detail_part_valid("lineups"))
	{
		updater.doc_update("detail-lineups");
		detail_part_validate("lineups");
		detail_tabs.tabs.tab_lineups.loaded = true;
		url_update = false;
	}

	if (detail_selected_tab[0] == "match_history" && !detail_part_valid("match_history"))
	{
		updater.doc_update("detail-match-history");
		detail_part_validate("match_history");
		detail_tabs.tabs.tab_match_history.loaded = true;
		url_update = false;
	}

	if (detail_selected_tab[0] == "statistics" && !detail_part_valid("statistics"))
	{
		updater.doc_update("detail-statistics");
		detail_part_validate("statistics");
		detail_tabs.tabs.tab_statistics.loaded = true;
		url_update = false;
	}

	if (detail_selected_tab[0] == "commentary" && !detail_part_valid("commentary"))
	{
		updater.doc_update("detail-commentary");
		detail_part_validate("commentary");
		detail_tabs.tabs.tab_commentary.loaded = true;
		url_update = false;
	}

	if (detail_selected_tab[0] == "live" && !detail_part_valid("live"))
	{
		updater.doc_update("detail-live-table");
		detail_part_validate("live");
	}

	if (detail_selected_tab[0] == "standings")
	{
		fsTable.init_table_proxy();

		if (!detail_part_valid("standings"))
		{
			updater.doc_update("detail-standings");
			detail_part_validate("standings");
		}

		detail_loaded();

		if (document.location.hash.indexOf('#' + detail_tabs.tabs.tab_standings.urlName) === 0) {
			url_update = false;
		}
	}

	if (detail_selected_tab[0] == "draw")
	{
		stats_proxy.restart();

		try
		{
			fsTable.init_table_proxy();
		}
		catch (e)
		{
		}

		if (!detail_part_valid("draw"))
		{
			updater.doc_update("detail-draw");
			detail_part_validate("draw");
		}

		detail_loaded();

		if (document.location.hash.indexOf('#' + detail_tabs.tabs.tab_draw.urlName) === 0) {
			url_update = false;
		}
	}

	if (additionalTabs["tab_commentary_preview"] && !detail_part_valid("commentary_preview") && document.getElementById("tab-match-commentary-preview"))
	{
		updater.doc_update("detail-commentary-preview");
		detail_part_validate("commentary_preview");
	}

	if (additionalTabs["tab_player_statistics_preview"] && !detail_part_valid("player_statistics_preview") && document.getElementById("tab-match-player-statistics-preview"))
	{

		updater.doc_update("detail-player-statistics-preview");
		detail_part_validate("player_statistics_preview");
	}

	if (detail_selected_tab[0] == "tv" && !detail_part_valid("tv"))
	{
		updater.doc_update("detail-tv");
		detail_part_validate("tv");
	}

	if (detail_selected_tab[0] == "highlights" && !detail_part_valid("highlights"))
	{
		updater.doc_update("detail-highlights");
		detail_part_validate("highlights");
	}


	if (detail_selected_tab[0] == "player_statistics" && !detail_part_valid("player-statistics"))
	{
		updater.doc_update("detail-player-statistics");
		detail_part_validate("player-statistics");
		url_update = false;
	}

	if (!delayed)
	{
		actual_tab = detail_selected_tab[0].replace(/_/g, "-");
		if (url_update)
		{
			url_anchors = url_anchors.slice(0, detail_selected_tab.length).join(";");
			if (current_url_anchors != url_anchors)
			{
				detail_hashchangeIgnoreNext = true;
				window.location.hash = "#" + url_anchors;
			}
			detail_tab_url_recognize = false;
		}
	}

	detail_tab_runOnDetailTabShowCallbacks(tab, detail_previous_selected_tab);
	detail_previous_selected_tab = tab.slice(0);
};

function add_tab_name_to_title(tabName)
{
	$("div#share-buttons-detail").find("span.share-button").each(function () {
	var originalTitle = $(this).attr('title').split(":");
	$(this).attr('title', originalTitle[0] + ': ' + tabName);
	});
};

function add_tab_name_to_page_title(tabName)
{
	var originalPageTitle = $('title').text();
	var originalPageTitleWithTabName = originalPageTitle.match(/.*\|.*\|/);

	if (originalPageTitleWithTabName == null)
	{
		$(document).attr('title',originalPageTitle + ' | ' + tabName);
	}
	else
	{
		$(document).attr('title', originalPageTitleWithTabName + ' ' + tabName);
	}
};

function processHighlights()
{
	try {
		var $tabContent = $(detail_tabs.tabs.tab_highlights.contentBackup);
		var totalVideos = $tabContent.find(".highlight-video a").length;
		$tabContent.find(".highlight-video a").replaceWith(
			function(i) {
				var $el = $(this);
				var highlightUrl = $el.attr('href');
				var forceIframe = typeof $el.data('force-iframe') != 'undefined';
				if (totalVideos > 1)
				{
					return $('<div>', {
						"class": "highlight-overlay",
						"data-url": highlightUrl,
						"data-force-iframe": forceIframe * 1,
						"click": function(){
							var $el = $(this);
							var highlightUrl = $el.data('url');
							var forceIframe = $el.data('force-iframe');
							$el.replaceWith(getHighlightObject(highlightUrl, forceIframe));
						}
					});
				}
				return getHighlightObject(highlightUrl, forceIframe);
			});
		$('#highlights-content').html($tabContent);
	}
	catch(e)
	{}
};

function getHighlightObject(highlightUrl, forceIframe)
{
	forceIframe = !!forceIframe;
	var iframeResult = '<iframe width="100%" height="100%" src="' + highlightUrl + '" frameborder="0" allowfullscreen></iframe>';

	if(forceIframe)
	{
		return iframeResult;
	}

	if($.browser.msie && $.browser.version < 9)
	{
		if(highlightUrl.match(/embed.swf/))
			return '<embed src="' + highlightUrl + '" width="100%" height="100%" />';
		else
			return iframeResult;
	}
	else
	{
		return '<object data="' + highlightUrl + '" width="100%" height="100%"></object>';
	}

};

function detail_statsCheckTableWidth(id) {
	for (var i in detail_standings_tab.tabs) {
		// sirka horniho menu - od nej se odvodi sirka tabulky
		boxEl = document.getElementById('box-table-type-' + detail_standings_tab.tabs[i].typeId);
		boxWidth = 0;
		if (boxEl) {
			boxWidth = boxEl.offsetWidth;
		}

		// sirka tabulky
		tableEl = document.getElementById('table-type-' + detail_standings_tab.tabs[i].typeId);
		origTableWidth = 0;
		if (tableEl) {
			origTableWidth = tableEl.offsetWidth;
		}

		// sirka TD s jmenem participanta
		participantEl = document.getElementById('table-type-participant_name-' + detail_standings_tab.tabs[i].typeId);
		if (participantEl) {
			participantWidth = participantEl.offsetWidth;
		}

		// pokud je rozdil sirek vetsi nez 3, budeme zkracovat
		diff = origTableWidth - boxWidth;

		if (boxWidth > 0 && diff > 3 && participantEl) {
			diff = origTableWidth - boxWidth;

			// nova sirka je puvodni sirka nazvu minus rozdil a minus padding hodnotu
			newParticipantWidth = participantWidth - diff - 16;

			// tabulka
			var b1 = tableEl.childNodes.length;
			for (var i1 = 0; i1 < b1; i1++) {
				if (tableEl.childNodes[i1].tagName && tableEl.childNodes[i1].tagName.toLowerCase() == 'tbody') {
					tbody = tableEl.childNodes[i1];
					var b2 = tbody.childNodes.length;
					// TR
					for (var i2 = 0; i2 < b2; i2++) {
						if (tbody.childNodes[i2].tagName && tbody.childNodes[i2].tagName.toLowerCase() == 'tr') {
							tr = tbody.childNodes[i2];
							var b3 = tr.childNodes.length;
							// TD
							for (var i3 = 0; i3 < b3; i3++) {
								if (tr.childNodes[i3].tagName && tr.childNodes[i3].tagName.toLowerCase() == 'td' && tr.childNodes[i3].className == 'col_name') {
									td = tr.childNodes[i3];
									var b4 = td.childNodes.length;
									// SPAN
									for (var i4 = 0; i4 < b4; i4++) {
										if (td.childNodes[i4].tagName && td.childNodes[i4].tagName.toLowerCase() == 'span' && td.childNodes[i4].className == 'team_name_span') {
											spanWidth = td.childNodes[i4].offsetWidth;

											if (spanWidth > newParticipantWidth) {
												//alert('ano: (' + td.childNodes[i4].innerHTML + ') ' + spanWidth + ' / ' + newParticipantWidth);

												// zkraceni nazvu participanta
												detail_shortParticipantText(td.childNodes[i4], newParticipantWidth);
												// nastaveni nove sirky - neni nutne, ale je mozne
												//td.childNodes[i4].style.width = newParticipantWidth + "px";
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
};

/**
 * zkrati text ve prvku na zadanou sirku v pixelech, na konec doplni 3 tecky
 */
function detail_shortParticipantText(span, finalWidth) {
    text = span.innerHTML;
    span.title = text;
    while (span.offsetWidth > finalWidth) {
        text = text.substr(0, (text.length -1));

        newText = text + '...';
        span.innerHTML = newText;
    }
};


/* random ID */
function getRandID() {
	return Math.floor(1000000 * Math.random());
};

/* bwin link */
function B( sport_id )
{
	window.open( odds_comparison_bwin + '?from=detail&sport=' + sport_id, 'odds_comparison_blank' + getRandID() );
};

/* bookmaker link link */
function BL( url )
{
	window.open( 'sdfsdfsdf', 'odds_comparison_blank' + getRandID() );
};

/* tv station link */
function open_tv( url )
{
	window.open( url, 'url_blank' + getRandID() );
};

function win_resize()
{
	window.resizeTo(520, 500);
	if(document.getElementById('detail'))
	{
		var width = document.getElementById('detail').offsetWidth;
		var height = document.getElementById('detail').offsetHeight;
	}

	width += 40;
	height += 42;

	swidth  = screen.width - 60;
	sheight = screen.height - 46;

	if(ie)
	{
		sheight += 10;
	}

	if(swidth < width)
		width = swidth;

	if(sheight < height)
		height = sheight;

	if(ie)
	{
		width += 10;
		height += 10;
	}

	if(height < 500)
		height = 500;

	window.resizeTo(width, height);
};

// sets states of live bet icons
function set_detail_bet_icon_states(tab_id)
{
	var tvRowCount = 0;
	var contentElement = (
		tab_id
			? $("#" + tab_id)
			: $("#detail")
	);
	if (event_stage_type_id == 2)
	{
		contentElement.find(".live-offer1").removeClass("live-offer1").addClass("live-offer2");

		$("#detail").find(".live-betting-strip").html('<div id="lb-strip-2"><a href="'+live_betting_strip_url+'" target="_blank"><span>'+live_betting_strip_text+'</span></a></div>');

		tvRowCount = $("#tv-content").find("tbody > tr").length;
		for(var i = 0; i < tvRowCount; i++)
		{
			if ($("#tv-content").find("tbody tr:eq("+i+") td:eq(2)").length==0 &&
			$("#tv-content").find("tbody tr:eq("+i+") span.watch-live").length == 0 &&
			$("#tv-content").find("tbody tr:eq("+i+") a.elink:not(.no-bm)").length != 0)
			{
				$("#tv-content").find("tbody tr:eq("+i+") a.elink").prepend('<span class="watch-live">WATCH LIVE VIDEO NOW!</span>');
			}
		}
	}
	else if (event_stage_type_id == 3)
	{
		contentElement.find(".live-icon-bookmaker").remove();
		$("#detail").find(".live-betting-strip").html('');
		$("#tv-content").find("span.watch-live").remove();
	}
	else
	{
		contentElement.find(".live-offer2").removeClass("live-offer2").addClass("live-offer1");
		$("#detail").find(".live-betting-strip").html('');
		$("#tv-content").find("span.watch-live").remove();
	}
};

// shows/hide match history
function fix_tennis_mh()
{
	var mh = $(".tennis #li-match-history");
	if(mh.length == 1)
	{
		var mh_h = $("#match-history-hidder");
		var disabled = mh_h.length == 1;
		var collapse = $("#li-match-statistics").length == 0;
		var lc_submenu = $("#detail-submenu-bookmark");
		if(event_stage_type_id == 1 && !disabled)
		{
			mh.wrap('<li id="match-history-hidder" style="display:none;" />').wrap('<ul/>');
			if(collapse)
			{
				tennis_switch_summary_timeline_text();
				lc_submenu.wrap('<div id="detail-submenu-bookmark-hidder" style="display:none;" />');
			}
		}
		else if(event_stage_type_id != 1 && disabled)
		{
			mh.unwrap().unwrap();
			if(collapse)
			{
				tennis_switch_summary_timeline_text();
				lc_submenu.unwrap();
			}

		}
	}
};

function tennis_switch_summary_timeline_text()
{
	var summary = $("#a-match-summary");
	var timeline = $("#a-match-timeline");
	var summary_text = summary.text();
	summary.text(timeline.text());
	timeline.text(summary_text);
};

// detects present and selected odds tabs
function load_detail_odds_comparison_tabs()
{
	var selected_type_tab_name, selected_scope_tab_name;
	var type_tab_name, scope_tab_name;
	var type_tab_exp, scope_tab_exp;
	var type_element, scope_element;
	var type_tab_id, scope_tab_id;
	var odds_comparison_tab = get_detail_subtab(["odds_comparison"]);
	var i, j, result;
	var spreadTrans = getSpreadTrans();

	var type_tabs = {
		tab_1x2: {urlName: "1x2-odds", fullName: "1X2 odds"},
		tab_moneyline: {urlName: "home-away", fullName: "Home/Away"},
		tab_under_over: {urlName: "over-under", fullName: "Over/Under"},
		tab_asian_handicap: {urlName: "asian-handicap", fullName: spreadTrans['full']},
		tab_european_handicap: {urlName: "european-handicap", fullName: "European handicap"},
		tab_double_chance: {urlName: "double-chance", fullName: "Double chance"},
		tab_ht_ft: {urlName: "ht-ft", fullName: "Half Time/Full Time"},
		tab_correct_score: {urlName: "correct-score", fullName: "Correct score"},
		tab_oddeven: {urlName: "odd-even", fullName: "Odd/Even"},
		tab_to_qualify: {urlName: "to-qualify", fullName: "To qualify"},
		tab_both_teams_to_score: {urlName: "both-teams-to-score", fullName: "Both teams to score"}
	};
	var scope_tabs = {
		tab_ft: {urlName: "full-time"},
		tab_ft_include_ot: {urlName: "ft-including-ot"},
		tab_1hf: {urlName: "1st-half"},
		tab_2hf: {urlName: "2nd-half"},
		tab_1per: {urlName: "1st-period"},
		tab_1qrt: {urlName: "1st-qrt"},
		tab_set1: {urlName: "set-1"}
	};
	odds_comparison_tab.tabs = {};
	selected_type_tab_name = null;
	for (i in type_tabs)
	{
		result = /^tab_(.+)$/.exec(i);
		if (!result)
			continue;
		type_tab_name = result[1];
		type_tab_id = type_tab_name.replace(/_/g,"-");
		type_element = document.getElementById("bookmark-" + type_tab_id);
		if (type_element)
		{
			odds_comparison_tab.tabs["tab_" + type_tab_name] = {
				tabElement: {id: "bookmark-" + type_tab_id},
				contentElement: {id: "block-" + type_tab_id},
				tabs: {},
				urlName: type_tabs[i].urlName,
				fullName: type_tabs[i].fullName
			};
			selected_scope_tab_name = null;
			for (j in scope_tabs)
			{
				result = /^tab_(.+)$/.exec(j);
				if (!result)
					continue;
				scope_tab_name = result[1];
				scope_tab_id = type_tab_id + "-" + scope_tab_name.replace(/_/g,"-");
				scope_element = document.getElementById("bookmark-" + scope_tab_id);
				if (scope_element)
				{
					odds_comparison_tab.tabs["tab_" + type_tab_name].tabs[j] = {
						tabElement: {id: "bookmark-" + scope_tab_id},
						contentElement: {id: "block-" + scope_tab_id},
						urlName: scope_tabs[j].urlName
					};
					if ($(scope_element).hasClass("selected") || selected_scope_tab_name == null)
						selected_scope_tab_name = scope_tab_name;
				}
			}
			if (selected_scope_tab_name)
				odds_comparison_tab.tabs["tab_" + type_tab_name].selected = selected_scope_tab_name;

			if ($(type_element).hasClass("selected") || selected_type_tab_name == null)
				selected_type_tab_name = type_tab_name;
		}
	}

	for (i in type_tabs)
	{
		result = /^tab_(.+)$/.exec(i);
		type_tab_name = result[1];
		type_tab_id = type_tab_name.replace(/_/g,"-");
		type_element = document.getElementById("bookmark-" + type_tab_id);

		if(typeof type_tabs[i].fullName != "undefined")
		{
			var tabText = $("div#tab-match-odds-comparison").find("ul.ifmenu li#bookmark-"+type_tab_id+" a");
			$(tabText).attr("title", type_tabs[i].fullName);
		}
	}

	if (selected_type_tab_name)
		odds_comparison_tab.selected = selected_type_tab_name;
};

function detail_odds_comparison_type_tab(tab)
{
	detail_tab(["odds-comparison", tab]);
};

function detail_odds_comparison_scope_tab(type_tab, scope_tab)
{
	detail_tab(["odds-comparison", type_tab, scope_tab]);
};

function mark_last_row_in_h2h()
{
	$('.h2h_home').parent().each(function () {
		$(this).find('.h2h_home tr').not('.hid').last().find('td').addClass('lastR');
	});
	$('.h2h_away').parent().each(function () {
		$(this).find('.h2h_away tr').not('.hid').last().find('td').addC