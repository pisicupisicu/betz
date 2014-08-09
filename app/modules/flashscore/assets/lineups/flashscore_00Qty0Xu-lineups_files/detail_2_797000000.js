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
	activeRequests ++;

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
	activeRequests --;
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
		$(this).find('.h2h_away tr').not('.hid').last().find('td').addClass('lastR');
	});
};

var hover_color = null;
function detail_delegate_actions(sport_id, sport, match_id)
{
	// head 2 head hover
	var sport = sport;
	color_init();
	$("div#head-2-head-content").delegate("tbody tr.highlight", "mouseenter", function(event)
	{
		$(this).addClass('highlighted');
		tr_over($(this).find("td"),hover_color);
		$(this).attr("title", "Click for match detail!");
	});

	// head 2 head hover out
	$("div#head-2-head-content").delegate("tbody tr.highlight", "mouseleave", function(event)
	{
		$(this).removeClass('highlighted');
		tr_out($(this).find("td"));
		$(this).removeAttr("title");
	});

	// odds - hover - on
	$("table#default-odds").delegate("td.kx", "mouseenter", function(event)
	{
		var title = '';
		var is_scheduled = (event_stage_type_id === 1);
		var is_clickable = false;

		if (odds_betslip)
		{
						if($(this).text() != '-')
			{
				// use highlight only for scheduled matches
				if (is_clickable || is_scheduled)
				{
					$(this).addClass("odds-hover-highlight");
				}
			}
					}

		if($(this).text() != '-')
		{
			var odds_alt = $(this).find('span').eq(0).attr("alt");
			if(odds_alt != undefined)
				title += odds_alt;

			if (odds_betslip && (is_clickable || is_scheduled))
			{
				title += title ? '[br]' : '';
				title += "Add this match to bet slip on bet365!";
			}
			else
			{
				$(this).css('cursor', 'default');
			}

			$(this).attr("title", title);
			tt.show($(this).get(0), event);
		}
	});

	$("div#odds-comparison-content").delegate("td.kx", "mouseenter", function(event)
	{
		var title = '';

 		if($(this).text() != '-')
		{
			var odds_alt = $(this).find('span').eq(0).attr("alt");
			if(odds_alt != undefined)
				title += odds_alt;

			$(this).attr("title", title);
			tt.show($(this).get(0), event);
		}
//			tt.show($(this).find("span").get(0), event);
	});

	// odds - hover - out
	$("table#default-odds").delegate("td.kx", "mouseleave", function(event)
	{
				if (odds_betslip)
		{
			$(this).removeClass("odds-hover-highlight");
		}
		
		if($(this).text() != '-')
			tt.hide($(this).get(0));
	});

	$("div#odds-comparison-content").delegate("td.kx", "mouseleave", function(event)
	{
		if($(this).text() != '-')
			tt.hide($(this).get(0));
//			tt.hide($(this).find("span").get(0));
	});

	$("div#summary-content").delegate("td.best-of span", "mouseenter", function(event)
	{
		if(sport == 'snooker')
			$(this).attr("title", cjs.Util.trans('TRANS_SNOOKER_BEST_OF_FRAMES').replace('%s', $(this).text().substring(0, $(this).text().length - 2)));
		else
		if(sport == 'darts')
		{
			var leg_set_trans = playingOnSets ? cjs.Util.trans('TRANS_DARTS_BEST_OF_SETS') : cjs.Util.trans('TRANS_DARTS_BEST_OF_LEGS');
			$(this).attr("title", leg_set_trans.replace('%s', $(this).text().substring(0, $(this).text().length - 2)));
		}
		tt.show($(this).get(0), event);
	});

	$("div#summary-content").delegate("td.best-of span", "mouseleave", function(event)
	{
		tt.hide($(this).get(0));
	});

	$("div#summary-content").delegate("table span.dw-icon", "mouseenter", function(event)
	{
		$(this).attr("title", "Advancing to next round");
		if($(this).hasClass('win'))
			$(this).attr("title", "Winner");
		tt.show($(this).get(0), event, true);
	});

	$("div#summary-content").delegate("table span.dw-icon", "mouseleave", function(event)
	{
		tt.hide($(this).get(0));
	});

	// Detail icons title
	$("div#content-all").delegate("span.icon", "mouseenter", function(event)
	{
		var icon_type = $(this).attr("class");
		var title = '';
		var tt_direction = true;
		if(icon_type.match(/r_ico/))
			tt_direction = false;

		if (icon_type.match(/tennis-serve/))
		{
			title = cjs.Util.trans('TRANS_TENNIS_SERVING_PLAYER');
		}
		else if (icon_type.match(/cricket-serve-opposite/))
		{
			title = cjs.Util.trans('TRANS_CRICKET_BATTING_TEAM');
		}
		else if (icon_type.match(/cricket-serve/))
		{
			title = cjs.Util.trans('TRANS_CRICKET_BOWLING_TEAM');
		}
		else if (icon_type.match(/darts-serve/))
		{
			title = cjs.Util.trans('TRANS_DARTS_BEGINNING_PLAYER');
		}
		else if (icon_type.match(/baseball-serve-opposite/))
		{
			title = cjs.Util.trans('TRANS_BASEBALL_BATTING_TEAM');
		}
		else if (icon_type.match(/baseball-serve/))
		{
			title = cjs.Util.trans('TRANS_BASEBALL_PITCHING_TEAM');
		}
		else if (icon_type.match(/american-football-serve/))
		{
			title = cjs.Util.trans('TRANS_AMERICAN_FOOTBALL_TEAM_ON_BALL');
		}
		else if (icon_type.match(/video/))
		{
			title = cjs.Util.trans('TRANS_DETAIL_WATCH_VIDEO');
		}

		if (title)
		{
			$(this).attr("title", title);
			tt.show($(this).get(0), event, tt_direction);
		}
	});

	$("div#content-all").delegate("span.icon", "mouseleave", function(event) { tt.hide($(this).get(0)); });

	// Bind all icons to tooltip if they have title
	$("table.team").delegate(".ico", "mouseenter", function(e)
	{
		$(this).attr("title") && ttb.show(this, e);
	});

	$("table.team").delegate(".ico", "mouseleave", function(e)
	{
		ttb.hide(this);
	});

	$("div#match-history-content").delegate(".ball-type-text", "mouseenter", function(e)
	{
		$(this).attr("title") && ttb.show(this, e);
	});

	$("div#match-history-content").delegate(".ball-type-text", "mouseleave", function(e)
	{
		ttb.hide(this);
	});

	if (odds_betslip)
	{
		// default odds - click
		$("table#default-odds").delegate("td.kx", "click", function()
		{
			var icon_type = $(this).attr("class");
			var is_scheduled = (event_stage_type_id === 1);
			var is_clickable = false;
			var betslip = (is_scheduled ? $(this).find("span").attr("bs") : '');

			if($(this).text() != '-')
			{
				// show odds detail only for scheduled matches, for other terminate
				if (!(is_clickable || is_scheduled))
				{
					return;
				}

				var outcome = null;
				if(icon_type.match(/o_1/))
					outcome = '1';
				else if(icon_type.match(/o_0/))
					outcome = 'x';
				else if(icon_type.match(/o_2/))
					outcome = '2';

				bookmaker_open(bookmaker_link + '?from=betslip-detail&sport=' + sport_id + '&match=' + match_id + '&outcome=' + outcome + (betslip != '' ? '&betslip='+encodeURIComponent(betslip) : ''));
			}
		});
	}

	$("div#odds-comparison-content" + (!odds_betslip ? ', table#default-odds' : '')).delegate("td.kx", "click", function()
	{
		var e = $(this);
		var link = e.parent('tr').find('td.bookmaker a:eq(0)').attr('href');
		if (link)
		{
			bookmaker_open(link);
		}
	});

};

// {{{
/*
Table sorting script  by Joost de Valk, check it out at http://www.joostdevalk.nl/code/sortable-table/.
Based on a script from http://www.kryogenix.org/code/browser/sorttable/.
Distributed under the MIT license: http://www.kryogenix.org/code/browser/licence.html .

Copyright (c) 1997-2007 Stuart Langridge, Joost de Valk.

Version 1.5.7

For oddsportal.com modified by Livesport, s.r.o.
*/

/* You can change these values */
var image_path = "http://www.joostdevalk.nl/code/sortable-table/";
var image_up = "arrow-up.gif";
var image_down = "arrow-down.gif";
var image_none = "arrow-none.gif";
var activeUp = "active-up";
var inactiveUp = "inactive-up";
var activeDown = "active-down";
var inactiveDown = "inactive-down";
var europeandate = true;
var alternate_row_colors = true;
var firstSort = new Array();
firstSort['column'] = 0;
firstSort['sort-dir'] = 'up';
var defaultSort = 'down';

/* Don't change anything below this unless you know what you're doing */
//addEvent(window, "load", sortables_init);

var SORT_COLUMN_INDEX;
var thead = false;

function sortables_init(settings) {



    // Find all tables with class sortable and make them sortable
    if (!document.getElementsByTagName) return;
    tbls = document.getElementsByTagName("table");
    for (ti=0;ti<tbls.length;ti++) {
        thisTbl = tbls[ti];
        if (((' '+thisTbl.className+' ').indexOf("sortable") != -1) && (thisTbl.id)) {
            ts_makeSortable(thisTbl);
        }
    }
};

function ts_makeSortable(t) {
    if (t.rows && t.rows.length > 0) {
        if (t.tHead && t.tHead.rows.length > 0) {
            var firstRow = t.tHead.rows[t.tHead.rows.length-1];
            thead = true;
        } else {
            var firstRow = t.rows[0];
        }
    }
    if (!firstRow) return;

    // We have a first row: assume it's the header, and make its contents clickable links
    for (var i = 0; i < firstRow.cells.length; i++) {
        var cell = firstRow.cells[i];
        var txt = ts_getInnerText(cell);
        txt = txt.replace(/(\r\n|\n|\r)/gm,"");
        if (cell.className != "unsortable" && cell.className.indexOf("unsortable") == -1) {
			if (i != firstSort['column'] || cell.className.indexOf("no-sort-default") != -1) {
                cell.innerHTML = '<a href="#" class="sortheader inactive-' + defaultSort + '" onclick="ts_resortTable(this, '+i+');return false;"><span class="txt">'+txt+'</span><span class="sortarrow" sortdir="' + defaultSort + '"></span></a>';
			} else {
				var sortDirection = firstSort['sort-dir'];
				if (cell.className.indexOf("default_sort_up") == -1)
				{
					sortDirection = 'up';
				}
				else if (cell.className.indexOf("default_sort_down") == -1)
				{
					sortDirection = 'down';
				}

                cell.innerHTML = '<a href="#" class="sortheader active-' + sortDirection + '" onclick="ts_resortTable(this, '+i+');return false;"><span class="txt">'+txt+'</span><span class="sortarrow" sortdir="' + (sortDirection == 'down' ? 'up' : 'down') + '"></span></a>';
            }
        }
    }
    if (alternate_row_colors) {
        alternate(t);
    }
};

function ts_getInnerText(el) {
    if (typeof el == "string") return el;
    if (typeof el == "undefined") { return el };
    if (el.innerText) return el.innerText;	//Not needed but it is faster
    var str = "";

    var cs = el.childNodes;
    var l = cs.length;
    for (var i = 0; i < l; i++) {
        switch (cs[i].nodeType) {
            case 1: //ELEMENT_NODE
                str += ts_getInnerText(cs[i]);
                break;
            case 3:	//TEXT_NODE
                str += cs[i].nodeValue;
                break;
        }
    }
    return str;
};

var ts_onResortTableStartCallbacks = [];

function ts_addOnResortTableStartCallback (callback,table) {
	ts_onResortTableStartCallbacks.push({callback:callback,table:table});
}

function ts_runOnResortTableStartCallbacks (table) {
	for(var i in ts_onResortTableStartCallbacks)
	{
		if($(ts_onResortTableStartCallbacks[i].table).get(0) == $(table).get(0))
		{
			ts_onResortTableStartCallbacks[i].callback(table);
		}
	}
}

var ts_onResortTableStopCallbacks = [];

function ts_addOnResortTableStopCallback (callback,table) {
	ts_onResortTableStopCallbacks.push({callback:callback,table:table});
}

function ts_runOnResortTableStopCallbacks (table) {
	for(var i in ts_onResortTableStopCallbacks)
	{
		if($(ts_onResortTableStopCallbacks[i].table).get(0) == $(table).get(0))
		{
			ts_onResortTableStopCallbacks[i].callback(table);
		}
	}
}

function ts_resortTable(lnk, clid) {

	var closestTable = $(lnk).closest('table');
	ts_runOnResortTableStartCallbacks(closestTable);

    var span;
    for (var ci=0;ci<lnk.childNodes.length;ci++) {
        if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
    }
    var spantext = ts_getInnerText(span);
    var td = lnk.parentNode;
    var tr = td.parentNode;
    var l = tr.childNodes.length;

    var th;
    // Set all active to inactive
    for (var i = 0; i < l; i++) {
        if (tr.childNodes[i].tagName && tr.childNodes[i].tagName.toLowerCase() == 'th') {
            th = tr.childNodes[i];
            var thNodes = th.childNodes.length;
            for (var j = 0; j < thNodes; j++) {
                if (th.childNodes[j].tagName && th.childNodes[j].tagName.toLowerCase() == 'a') {
                    var active = false;
                    if ($(th.childNodes[j]).hasClass(activeDown)) {
                        th.childNodes[j].className = th.childNodes[j].className.replace(activeDown, inactiveDown);
                        active = true;
                    } else if ($(th.childNodes[j]).hasClass(activeUp)) {
                        th.childNodes[j].className = th.childNodes[j].className.replace(activeUp, inactiveUp);
                        active = true;
                    }

                    // if change active to inactive change sort directiof for the same direction after reactivate
                    if (active) {
                        var activeText = th.childNodes[j].innerHTML;
                        activeText = stripTags(activeText);
                        var oldActive = th.childNodes[j];
                        if (active && activeText != stripTags(lnk.innerHTML)) {
                            lnkCountNodes = th.childNodes[j].childNodes.length;
                            for (var k = 0; k < lnkCountNodes; k++) {
                                if (th.childNodes[j].childNodes[k].tagName && th.childNodes[j].childNodes[k].tagName.toLowerCase() == 'span') {
                                    var lnkSpan = th.childNodes[j].childNodes[k];
                                    if (lnkSpan.getAttribute('sortdir') == 'down') {
                                        lnkSpan.setAttribute('sortdir', 'up');
                                    } else {
                                        lnkSpan.setAttribute('sortdir', 'down');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Set active sort
    if ($(lnk).hasClass(inactiveDown)) {
        lnk.className = lnk.className.replace(inactiveDown, activeDown);
    } else if ($(lnk).hasClass(inactiveUp)) {
        lnk.className = lnk.className.replace(inactiveUp, activeUp);
    }


    var column = clid || td.cellIndex;
    var t = getParent(td,'TABLE');

    // Work out a type for the column
    if (t.rows.length <= 1) return;
    var itm = "";
    var i = 0;
    while (itm == "" && i < t.tBodies[0].rows.length) {
        var itm = ts_getInnerText(t.tBodies[0].rows[i].cells[column]);
        itm = trim(itm);
        if (itm.substr(0,4) == "<!--" || itm.length == 0) {
            itm = "";
        }
        i++;
    }
    if (itm == "") return;

	sortfn = ts_getPreferedSortTypeFunction($(lnk).parent('th').attr('class'));
	if (!sortfn)
	{
		sortfn = ts_sort_caseinsensitive;
		if (itm.match(/^\d\d[\/\.-][a-zA-z][a-zA-Z][a-zA-Z][\/\.-]\d\d\d\d$/)) sortfn = ts_sort_date;
		if (itm.match(/^\d\d[\/\.-]\d\d[\/\.-]\d\d\d{2}?$/)) sortfn = ts_sort_date;
		if (itm.match(/^-?[L$?Uc´]\d/)) sortfn = ts_sort_numeric;
		if (itm.match(/^-?(\d+[,\.]?)+(E[-+][\d]+)?%?$/)) sortfn = ts_sort_numeric;
		if (itm.match(/^\d+\/\d+$/)) sortfn = ts_sort_frac;
		if (itm.match(/^\d+\/\d+$/)) sortfn = ts_sort_frac;
		if (itm.match(/^\d+:\d+$/)) sortfn = ts_sort_time;
	}
    SORT_COLUMN_INDEX = column;
    var firstRow = new Array();
    var newRows = new Array();
    for (k=0;k<t.tBodies.length;k++) {
        for (i=0;i<t.tBodies[k].rows[0].length;i++) {
            firstRow[i] = t.tBodies[k].rows[0][i];
        }
    }
    for (k=0;k<t.tBodies.length;k++) {
        if (!thead) {
            // Skip the first row
            for (j=1;j<t.tBodies[k].rows.length;j++) {
                newRows[j-1] = t.tBodies[k].rows[j];
            }
        } else {
            // Do NOT skip the first row
            for (j=0;j<t.tBodies[k].rows.length;j++) {
                newRows[j] = t.tBodies[k].rows[j];
            }
        }
    }

    newRows.sort(sortfn);
    if (span.getAttribute("sortdir") == 'down') {
        ARROW = '';
        newRows.reverse();
        span.setAttribute('sortdir','up');
        // change direction class
        if ($(lnk).hasClass(activeUp)) {
            $(lnk).removeClass(activeUp);
            $(lnk).addClass(activeDown);
        }
    } else {
        ARROW = '';
        span.setAttribute('sortdir','down');
        // change direction class
        if ($(lnk).hasClass(activeDown)) {
                $(lnk).removeClass(activeDown);
                $(lnk).addClass(activeUp);
        }
    }
    // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
    // don't do sortbottom rows
    for (i=0; i<newRows.length; i++) {
        if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('sortbottom') == -1))) {
            t.tBodies[0].appendChild(newRows[i]);
        }
    }
    // do sortbottom rows only
    for (i=0; i<newRows.length; i++) {
        if (newRows[i].className && (newRows[i].className.indexOf('sortbottom') != -1))
            t.tBodies[0].appendChild(newRows[i]);
    }
    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for (var ci=0;ci<allspans.length;ci++) {
        if (allspans[ci].className == 'sortarrow') {
            if (getParent(allspans[ci],"table") == getParent(lnk,"table")) { // in the same table as us?
                allspans[ci].innerHTML = '';
            }
        }
    }
    span.innerHTML = ARROW;
    alternate(t);

	ts_runOnResortTableStopCallbacks(closestTable);
};

function getParent(el, pTagName) {
    if (el == null) {
        return null;
    } else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase()) {
        return el;
    } else {
        return getParent(el.parentNode, pTagName);
    }
};

function sort_date(date) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    dt = "00000000";
    if (date.length == 11) {
        mtstr = date.substr(3,3);
        mtstr = mtstr.toLowerCase();
        switch(mtstr) {
            case "jan": var mt = "01"; break;
            case "feb": var mt = "02"; break;
            case "mar": var mt = "03"; break;
            case "apr": var mt = "04"; break;
            case "may": var mt = "05"; break;
            case "jun": var mt = "06"; break;
            case "jul": var mt = "07"; break;
            case "aug": var mt = "08"; break;
            case "sep": var mt = "09"; break;
            case "oct": var mt = "10"; break;
            case "nov": var mt = "11"; break;
            case "dec": var mt = "12"; break;
            // default: var mt = "00";
        }
        dt = date.substr(7,4)+mt+date.substr(0,2);
        return dt;
    } else if (date.length == 10) {
        if (europeandate == false) {
            dt = date.substr(6,4)+date.substr(0,2)+date.substr(3,2);
            return dt;
        } else {
            dt = date.substr(6,4)+date.substr(3,2)+date.substr(0,2);
            return dt;
        }
    } else if (date.length == 8) {
        yr = date.substr(6,2);
        if (parseInt(yr) < 50) {
            yr = '20'+yr;
        } else {
            yr = '19'+yr;
        }
        if (europeandate == true) {
            dt = yr+date.substr(3,2)+date.substr(0,2);
            return dt;
        } else {
            dt = yr+date.substr(0,2)+date.substr(3,2);
            return dt;
        }
    }
    return dt;
};


function ts_sort_date(a,b) {
    dt1 = sort_date(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
    dt2 = sort_date(ts_getInnerText(b.cells[SORT_COLUMN_INDEX]));

    if (dt1==dt2) {
        return 0;
    }
    if (dt1<dt2) {
        return -1;
    }
    return 1;
};
function ts_sort_numeric(a,b) {
    var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    aa = clean_num(aa);
    var bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    bb = clean_num(bb);
    return compare_numeric(aa,bb);
};

function ts_sort_field_goals(a,b) {
	var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    var bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
	var decode = function(str) {
		str = (str + "").replace('/', '-');
		var parts = str.split('-');
		if (parts[0])
		{
			parts[0] = parts[0] * 1 || 0;
		}
		else
		{
			parts[0] = 0;
		}
		if (parts[1])
		{
			parts[1] = parts[1] * 1 || 0;
		}
		else
		{
			parts[1] = 0;
		}
		return parts[0] * 10000 + 9999 - parts[1];
	};
	return compare_numeric(decode(aa), decode(bb));
};

function ts_sort_time(a,b) {
    var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    aa = (aa + "").replace(':', '.');
    var bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    bb = (bb + "").replace(':', '.');
    return compare_numeric(aa,bb);
};
function ts_sort_frac(a,b) {
    var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    aa = frac_val(aa);
    var bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    bb = frac_val(bb);
    return compare_numeric(aa,bb);
};
function compare_numeric(a,b) {
    var a = parseFloat(a);
    a = (isNaN(a) ? 0 : a);
    var b = parseFloat(b);
    b = (isNaN(b) ? 0 : b);
    return a - b;
};
function ts_sort_caseinsensitive(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
    if (aa==bb) {
        return 0;
    }
    if (aa<bb) {
        return -1;
    }
    return 1;
};
function ts_sort_default(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa==bb) {
        return 0;
    }
    if (aa<bb) {
        return -1;
    }
    return 1;
};

function ts_getPreferedSortTypeFunction(className)
{
	var sortTypeRegexp = /sortable-type-([a-z]*)/;
	var preferedSortStr = sortTypeRegexp.exec(className);
	if (!preferedSortStr || !preferedSortStr[1])
	{
		return null;
	}
	switch (preferedSortStr[1])
	{
		case 'num': return ts_sort_numeric;
		case 'str': return ts_sort_caseinsensitive;
		case 'time': return ts_sort_time;
		case 'fg': return ts_sort_field_goals;
	}
	return null;
};

function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,	NS6 and Mozilla
// By Scott Andrew
{
    if (elm.addEventListener){
        elm.addEventListener(evType, fn, useCapture);
        return true;
    } else if (elm.attachEvent){
        var r = elm.attachEvent("on"+evType, fn);
        return r;
    } else {
        alert("Handler could not be removed");
    }
};
function clean_num(str) {
    str = str.replace(new RegExp(/[^-?0-9.]/g),"");
    return str;
};
function frac_val(str) {
    arr = str.split('/');
    str = arr[0] / arr[1];
    return str;
};
function trim(s) {
    return s.replace(/^\s+|\s+$/g, "");
};


// Alternate? .. really? Alternate?! And what about "improvise()" or "doSomething()"!
function alternate(table) {
    // Take object table and get all it's tbodies.
    var tableBodies = table.getElementsByTagName("tbody");
    // Loop through these tbodies
    for (var i = 0; i < tableBodies.length; i++) {
        // Take the tbody, and get all it's rows
        var tableRows = tableBodies[i].getElementsByTagName("tr");
        // Loop through these rows
        // Start at 1 because we want to leave the heading row untouched
        for (var j = 0; j < tableRows.length; j++) {
            // Check if j is even, and apply classes for both possible results
            if ( (j % 2) == 0  ) {
                if ( !(tableRows[j].className.indexOf('even') == -1) ) {
                    tableRows[j].className = tableRows[j].className.replace('even', 'odd');
                } else {
                    if ( tableRows[j].className.indexOf('odd') == -1 ) {
                        tableRows[j].className += " odd";
                    }
                }
            } else {
                if ( !(tableRows[j].className.indexOf('odd') == -1) ) {
                    tableRows[j].className = tableRows[j].className.replace('odd', 'even');
                } else {
                    if ( tableRows[j].className.indexOf('even') == -1 ) {
                        tableRows[j].className += " even";
                    }
                }
            }
        }
    }
};

function stripTags(text){
    var re= /<\S[^>]*>/g;
    text = text.replace(re,"");
    return text;
};

// }}}

function detail_switch_odds_format(odds_format, switch_element, init)
{
	if(!$(switch_element).hasClass("active-odds-format"))
	{
		if(init && odds_format != 'eu')
		{
			get_odds_in_new_format(odds_format,'odds-comparison-content');
			recalculate_max_odds_for_new_format();
		}
		else if(!init)
		{
			get_odds_in_new_format(odds_format);
			recalculate_max_odds_for_new_format();
		}
	}

	clientStorage.store('fs_of', odds_format, 365*86400, 'self', '/');
	$("div#odds-comparison-content div.odds-comparison-spacer").find("span").removeClass("active-odds-format");
	$("div#odds-comparison-content div.odds-comparison-spacer").find("span."+odds_format).addClass("active-odds-format");

	// Aktualizuje odkaz na Oddsportal podle aktuálního formátu kurzů
	var op_link = $("div#odds-comparison-content div.bottom-block a").attr("href");
	if(typeof op_link != 'undefined')
	{
		op_link = op_link.split(/of=/);
		$("div.bottom-block a").attr("href",op_link[0]+'of='+odds_format);
	}
};

function backup_eu_odds_and_betslip_in_html(content_div)
{
	$("div#"+content_div).find("td.kx span").each(function () {
		var alt = '';
		var betslip = '';
		var eu_alt = '';
		alt = $(this).attr('alt');

		if(typeof(alt) != 'undefined')
		{
			alt = alt.split(":");
			if(alt[0] == '')
			{
				eu_alt = $(this).text();
				$(this).removeAttr('alt');
			}
			else
			{
				eu_alt = alt[0];
				$(this).attr('alt', alt[0]);
			}

			if(alt[1] != '')
				$(this).attr('bs', alt[1]);

		}

		$(this).attr('eu', eu_alt);
	});
};

function get_handicap_in_new_format(content_div)
{
	$("div#"+content_div).find("td.ah").each(function () {
		var handicap = $(this).text();
		if(handicap != 0 && handicap != '-')
		{
			handicap = get_single_handicap_in_new_format(handicap_format,handicap);
			if(isSwapped)
			{
				handicap = handicap.split("/");
				handicap = handicap[1]+'/'+handicap[0];
			}
			$(this).text(handicap);
		}
	});
};

function get_odds_in_new_format(odds_format, content_div)
{
	if(typeof(content_div) == "undefined")
		content_div = "content-all";

	$("div#"+content_div).find("td.kx span").each(function () {
		var odds = '';
		var odds_old = '';
		var new_alt = '';
		var eu_backup = $(this).attr('eu');
		var divider = eu_backup.match(/\[[ud]\]/);
		eu_backup = eu_backup.split(divider);

		if(divider)
		{
			var movement = (divider == '[u]' ? 'up' : 'down');
			odds_old = get_single_odds_in_new_format(odds_format,eu_backup[0]);
			odds = get_single_odds_in_new_format(odds_format,eu_backup[1]);

			if(odds != odds_old)
			{
				$(this).attr('alt', odds_old+divider+odds);
				var match_movement = movement+'_bak';
				if($(this).attr('class').match(match_movement))
				{
					$(this).removeClass(match_movement);
					$(this).addClass(movement);
				}
			}
			else
			{
				$(this).removeAttr('alt');
				$(this).removeClass(movement);
				$(this).addClass(movement+'_bak');
			}
		}
		else
		{
			if(eu_backup[0] != '-')
				odds = get_single_odds_in_new_format(odds_format,eu_backup[0]);
			else
				odds = eu_backup[0];
		}
		$(this).text(odds);
	});
};

function recalculate_max_odds_for_new_format()
{
	$("div#odds-comparison-content").find("td.kx").removeClass("max_too");

	var columns = $("div#odds-comparison-content").find("tr:eq(1) td.kx").length;
	$("div#odds-comparison-content").find("table").each(function () {
		$(this).find("tr:gt(0)").each(function () {
			for(var i=0; i<columns; i++)
			{
				$(this).find("td.kx:eq("+i+")").each(function () {
					if($(this).attr('class').match(/max/))
					{
						var max = $(this).find("span").text();
						$(this).closest("table").find("tr:gt(0)").each(function () {
							$(this).find("td.kx:eq("+i+")").each(function () {
								var maybe_max = $(this).find("span").text();
								if(maybe_max == max)
									$(this).addClass("max_too");
							});
						});
					}
				});
			}
		});
	});
};
