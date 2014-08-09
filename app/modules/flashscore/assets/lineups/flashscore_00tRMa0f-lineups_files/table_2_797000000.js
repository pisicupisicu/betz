	//  Definition of Livestreaming event's timestamp backup arrays
	var kickoffEventArray = new Array();
	var eventEventArray = new Array();

	/** returns given timestamp in desired format
	 * @author celly
	 *
	 * @param	string format - foramt of output string (uses default "d. m. Y H:i", if nothing given)
	 * The following characters are recognized in the format parameter string
	 * --------------------------------------------------------------------------------------------------------------
	 * | fromat 	|																|								|
	 * | character	| description													| example return val			|
	 * --------------------------------------------------------------------------------------------------------------
	 * |	d		|	Day of the month, 2 digits with leading zeros				|	01 to 31					|
	 * |	j		|	Day of the month without leading zeros						|	1 to 31						|
	 * |	F		|	A full textual representation of a month					|	January through December	|
	 * |	m		|	Numeric representation of a month, with leading zeros		|	01 through 12				|
	 * |	M		|	A short textual representation of a month, three letters	|	Jan through Dec				|
	 * |	n		|	Numeric representation of a month, without leading zeros	|	1 through 12				|
	 * |	y		|	A two digit representation of a year						|	Examples: 99 or 03			|
	 * |	Y		|	A full numeric representation of a year, 4 digits			|	Examples: 1999 or 2003		|
	 * |	g		|	12-hour format of an hour without leading zeros				|	1 through 12				|
	 * |	G		|	24-hour format of an hour without leading zeros				|	0 through 23				|
	 * |	h		|	12-hour format of an hour with leading zeros				|	01 through 12				|
	 * |	H		|	24-hour format of an hour with leading zeros				|	00 through 23				|
	 * |	i		|	Minutes with leading zeros									|	00 to 59					|
	 * |	s		|	Seconds, with leading zeros									|	00 through 59				|
	 * |	a		|	Lowercase Ante meridiem and Post meridiem					|	am or pm					|
	 * |	A		|	Uppercase Ante meridiem and Post meridiem					|	AM or PM					|
	 * --------------------------------------------------------------------------------------------------------------
	 * @param	string|number timestamp - unix timestamp (uses actual client timestamp, if nothing given)
	 * @param	number offset - GMT offset in minutes (0 is used, if nothing given)
	 *
	 * @returns string formated date
	 */
	function timestamp2date(format, timestamp, offset)
	{
		if (typeof format != "string")
		{
			format = "d. m. Y H:i";
		}

		if (typeof timestamp == "string")
		{
			timestamp = parseInt(timestamp);
		}
		if (typeof timestamp != "number")
		{
			timestamp = Math.round((new Date()).getTime() / 1000);
		}

		if (typeof offset != "number")
		{
			offset = 0;
		}

		var monthName = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var monthNameShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

		var time = new Date();
		time.setTime((timestamp + (time.getTimezoneOffset() * 60) - offset) * 1000);

		var	zerosPrepend =
			function (val, len)
			{
				if (typeof val != "string")
				{
					val = String(val);
				}

				if (typeof len != "number")
				{
					len = 2;
				}

				var _len = val.length;
				while (_len < len)
				{
					val = "0" + val;
					_len = val.length;
				}

				return val;
			};
		var d = time.getDate();
		var m = time.getMonth() + 1;
		var y = time.getFullYear();
		var H = time.getHours();
		var i = time.getMinutes();
		var s = time.getSeconds();
		var dateParts =
		{
			d: zerosPrepend(d),
			j: d,
			F: monthName[m-1],
			m: zerosPrepend(m),
			M: monthNameShort[m-1],
			n: m,
			y: String(y).slice(2),
			Y: y,
			g: H % 12 || 12,
			G: H,
			h: zerosPrepend(H % 12 || 12),
			H: zerosPrepend(H),
			i: zerosPrepend(i),
			s: zerosPrepend(s),
			a: (H > 11 ? 'pm' : 'am'),
			A: (H > 11 ? 'PM' : 'AM')
		};

		var formatedTime = format.replace(/[djFmMnyYgGhHisaA]|"[^"]*"|'[^']*'/g,
				function ($0)
				{
					return $0 in dateParts ? dateParts[$0] : $0.slice(1, $0.length - 1);
				}
		);

		return formatedTime;
	}

	/** left menu script
	* @param	string	param	id og menu element
	*/
	function lmenu_show(c_id)
	{
		var getToggleButton = function(leagueKey)
		{
			if (typeof cjs != 'undefined' && cjs.hasOwnProperty('myLeagues') && cjs.myLeagues.isEditable())
			{
				return cjs.myLeagues.getToggleIcon(null, leagueKey);
			}
			return '';
		};

		var $lmenu = $('#lmenu_' + c_id);

		if ($lmenu.find('.submenu[data-ajax="false"]').length && !$lmenu.hasClass('active'))
		{
			// menu already exists, so we only show it
			if($lmenu.data('prepared-for-nonajax') != true)
			{
				$lmenu.find('.submenu li').each(function(){
					$(this).prepend(getToggleButton($(this).data('mt')));
				});

				$lmenu.find('.submenu li:last').addClass('last');
			}

			$lmenu.prepend($('<span class="active-top"></span>'));
			$lmenu.append($('<span class="active-bottom"></span>'));
			$lmenu.addClass('active').data('prepared-for-nonajax', true);
			$lmenu.find('.submenu').hide().removeClass('hidden').slideDown(100, function(){
				refreshWaypoints();
			});

			tlist['vars_' + c_id] = [];
			return;
		}

		tlist['vars_' + c_id]['loading'] = false;
		//lmenu_show_loading(c_id);
		var elm = document.getElementById('lmenu_' + c_id);
		var str = elm.innerHTML;
		var tmp_link = str.match(/<a(.|\n|\s)+?<\/a>/gi);
		if (tmp_link.length > 1)
		{
			tmp_link = tmp_link[0];
		}
		else
		{
			tmp_link = tmp_link;
		}

		var lastClass = get_attr(elm, 'class').split(" ");

		if (str.match(/<span class="active-top">/gi))
		{
			var str = elm.innerHTML.toString();
			var tmp_submenu = str.match(/<ul(.|\n|\s)+?<\/ul>/gi);
			elm.innerHTML = tmp_link + tmp_submenu;
			var ul = elm.getElementsByTagName('ul');

			if (typeof ul != 'undefined')
			{
				$(ul[0]).slideUp(100, 'linear', function(){
					$(this).parent().removeClass('active');
					refreshWaypoints();
				});
			}
			else
			{
				$(elm).removeClass('active');
			}
		}
		else
		{

			var x = tlist['c_' + c_id];
			var x_length = x.length - 1;
			y = '<ul class="submenu hidden">';

			for (var i in x)
			{
				var tclass = ' class="';
				//if (x[i]['class']!=0)
				//	tclass += x[i]['class'];
				if (i == x_length)
				{
					tclass += 'last';
				}
				tclass += '"';
				y += '<li' + tclass +'>' + getToggleButton(x[i].leagueKey) + '<a href=\"' + (x[i].short_url !== undefined ? '/' + x[i].short_url : sport_url + x[i].url) + '/' +'\">' + x[i].name + '</a></li>';
			}

			y += '</ul>';

			elm.innerHTML = '<span class="active-top"></span>' + tmp_link + y + '<span class="active-bottom"></span>';

			$(elm).addClass('active');
			$(elm).find('.submenu').hide().removeClass('hidden').slideDown(100,function(){
				refreshWaypoints();
			});
		}
		return false;
	};

	function refreshWaypoints()
	{
		if (typeof $.waypoints == 'undefined')
		{
			return;
		}

		$.waypoints('refresh');
	}

	/** left menu script - check data (fill or show)
	* @param	string	param	c_id og menu element
	* @param	string	param	url of data request
	* @param	string	param	s_id id of sport
	*/
	function lmenu(c_id,url,s_id)
	{
		if (typeof tlist['c_' + c_id] != 'undefined' || $('#lmenu_' + c_id + ' .submenu[data-ajax="false"]').length)
		{
			lmenu_show(c_id);
			return false;
		}
		tlist['vars_' + c_id] = Array();
		tlist['vars_' + c_id]['loading'] = true;
		setTimeout('lmenu_show_loading('+c_id+')', 500);
		url += 'm_' + s_id + '_' + c_id;
		var menu_ajax = new ajaxObject(url, lmenu_response, 'menu');
		menu_ajax.update();
		return false;
	};

	/** left menu script - check data (fill or show)
	*/
	function lmenu_response(r_status, r_headers, r_content, r_trigger)
	{
		var data = r_content.split(JS_ROW_END);
		var c_id = null;
		var c_url = null;
		for (var i in data)
		{
			var row = data[i].split(JS_CELL_END);
			for (var j in row)
			{
				var text = row[j];
				if (!text)
				{
					continue;
				}

				var key = text.substring(0,2);
				var val = text.substring(3);

				if (key == 'MC')
				{
					c_id = val;
					continue;
				}

				if (key == 'ML')
				{
					c_url = val;
					continue;
				}

				if (c_id == null || c_url == null)
				{
					continue;
				}

				if (typeof tlist['c_' + c_id] == 'undefined')
				{
					tlist['c_' + c_id] = Array();
				}

				if (typeof tlist['c_' + c_id][i] == 'undefined')
				{
					tlist['c_' + c_id][i] = Array();
				}

				switch (key)
				{
					case 'MN':
						tlist['c_' + c_id][i]['name'] = val;
						break;
					case 'MU':
						tlist['c_' + c_id][i]['url'] = c_url + '/' + val;
						break;
					case 'MT':
						tlist['c_' + c_id][i]['leagueKey'] = val;
						break;
					case 'MV':
						tlist['c_' + c_id][i]['short_url'] = val;
						break;
				}
			}
		}

		if (c_id != null && typeof tlist['c_' + c_id] != 'undefined')
		{
			lmenu(c_id);
		}
	};

	function lmenu_show_loading(c_id)
	{
		var elm = document.getElementById('lmenu_' + c_id);
		var str = elm.innerHTML;
		var tmp_link = str.match(/<a(.|\n|\s)+?<\/a>/gi);
		if (tmp_link.length > 1)
		{
			tmp_link = tmp_link[0];
		}
		else
		{
			tmp_link = tmp_link;
		}

		if (tlist['vars_' + c_id]['loading'])
		{
			// another check if it is loaded
			if (tlist['vars_' + c_id]['loading'])
			{
				set_attr(elm, 'class', 'active'+' '+get_attr(elm, 'class'));
				elm.innerHTML = '<span class="active-top fake"></span>' + tmp_link + '<ul class="submenu"><li class="last"><a class="no-underline"><div class="menu_loading">&nbsp;</div></a></li></ul><span class="active-bottom"></span>';
			}
		}
	};

	/**
	 * Finds elements, that intersects given element
	 * @param HTML element
	 * @param array of HTML elements
	 *
	 * @returns array of intersecting elements
	 */
	function findIntersections(element, elements)
	{
		var intersections = [];
		var dimensions = getElementDimensions(element);
		for (var i = 0, _len = elements.length; i < _len; i++)
		{
			if (isElementsIntersects(dimensions, getElementDimensions(elements[i])))
			{
				intersections.push(elements[i]);
			}
		}

		return intersections;
	};

	/**
	 * Gets dimension and position of given element
	 * @param HTML element
	 *
	 * @returns object dimensions of given element
	 */
	function getElementDimensions(element)
	{
		var dimensions = $(element).offset();
		dimensions.bottom = dimensions.top + $(element).height();
		dimensions.right = dimensions.left + $(element).width();
		return dimensions;
	};

	/**
	 * Returns true if two given iso orientaded boxes intersects
	 * @param object of dimensions of first box
	 * @param object of dimensions of second box
	 *
	 * @returns bool
	 */
	function isElementsIntersects(dim1, dim2)
	{
		if (dim1.right < dim2.left)
		{
			return false;
		}
		if (dim1.left > dim2.right)
		{
			return false;
		}
		if (dim1.bottom < dim2.top)
		{
			return false;
		}
		if (dim1.top > dim2.bottom)
		{
			return false;
		}
		return true;
	};

	/** Required for selenium tests
	 * @param int activeRequests Number of running requests
	 */
	var activeRequests = 0;

	/** Utimate Ajax Object
	 * http://www.hunlock.com/blogs/The_Ultimate_Ajax_Object
	 */
	function ajaxObject(url, callbackFunction, actionTrigger, callbackObject)
	{
		var that = this;
		this.updating = false;
		this.aborting = false;
		this.ajax_async = true;
		this.container = null;
		this.return_text_after_update = false;

		this.abort = function()
		{
			if (that.updating)
			{
				that.updating = false;
				that.aborting = true;
				that.AJAX.abort();
				that.AJAX = null;
			}
		};

		this.async = function(val)
		{
			that.ajax_async = val ? true : false;
		};

		this.rtext = function(val)
		{
			that.return_text_after_update = val ? true : false;
			that.async(!that.return_text_after_update);
		};

		this.update = function(passData, postMethod, headers)
		{
			if (that.updating)
			{
				return false;
			}

			activeRequests ++;
			that.AJAX = null;

			if (window.XMLHttpRequest)
			{
				that.AJAX = new XMLHttpRequest();
			}
			else
			{
				that.AJAX = new ActiveXObject("Microsoft.XMLHTTP");
			}

			if (that.AJAX == null)
			{
				return false;
			}
			else
			{
				that.AJAX.onreadystatechange = function()
				{
					try
					{
						if (that.AJAX.readyState == 4)
						{
							that.updating = false;
							if (!that.aborting)
							{
								if (that.AJAX.status == 200 || that.AJAX.status == 304 || that.AJAX.status == 204 || that.AJAX.status == 1223 || that.AJAX.status == 0)
								{
									if (that.return_text_after_update)
									{
										return that.AJAX.responseText;
									}
									else
									{
										if (that.callbackObject)
										{
											that.callbackObject[that.callback](that.AJAX.status, that.AJAX.getAllResponseHeaders(), that.AJAX.responseText, actionTrigger, that._getCustomHeaders(that.AJAX));
										}
										else
										{
											that.callback(that.AJAX.status, that.AJAX.getAllResponseHeaders(), that.AJAX.responseText, actionTrigger, that._getCustomHeaders(that.AJAX));
										}
									}
								}
							}
							that.AJAX = null;
						}
					}

					catch (e)
					{

					}
				};

				if (typeof passData == 'undefined')
				{
										passData = '';
				}
				that.updating = new Date();

				if (/post/i.test(postMethod))
				{
					var uri = urlCall;
					that.AJAX.open("POST", uri, that.ajax_async);
					if (typeof callbackFunction != 'undefined' && !webkit)
					{
						that.AJAX.setRequestHeader('User-Agent', 'core' + (ie6 ? '/ie6' : ''));
					}
					that.AJAX.setRequestHeader('Accept-Language', '*');
					that.AJAX.setRequestHeader('Accept', '*/*');
					that._setHeaders(headers);
					that.AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					//that.AJAX.setRequestHeader("Content-Length", passData.length);
					that.AJAX.send(passData);
				}
				else
				{
					var uri = urlCall + (passData == '' ? '' : ('?' + passData));
					that.AJAX.open("GET", uri, that.ajax_async);
					if (typeof callbackFunction != 'undefined' && !webkit)
					{
						that.AJAX.setRequestHeader('User-Agent', 'core' + (ie6 ? '/ie6' : ''));
					}
					that.AJAX.setRequestHeader('Accept-Language', '*');
					that.AJAX.setRequestHeader('Accept', '*/*');
					that._setHeaders(headers);

					if (typeof u_304 != 'undefined' && u_304 != '' && (actionTrigger == 'update' || actionTrigger == 'game'))
					{
						that.AJAX.setRequestHeader('X-Signature', u_304);
					}

					if (typeof feed_sign != 'undefined')
					{
						that.AJAX.setRequestHeader('X-Fsign', feed_sign);
					}

					that.AJAX.send(null);
				}

				if (!that.ajax_async && that.AJAX != null)
				{
					if (that.AJAX.status == 200 || that.AJAX.status == 304 || that.AJAX.status == 204 || that.AJAX.status == 1223 || that.AJAX.status == 0)
					{
						if (that.return_text_after_update)
						{
							return that.AJAX.responseText;
						}
						else
						{
							if (that.callbackObject)
							{
								that.callbackObject[that.callback](that.AJAX.status, that.AJAX.getAllResponseHeaders(), that.AJAX.responseText, actionTrigger, that._getCustomHeaders(that.AJAX));
							}
							else
							{
								that.callback(that.AJAX.status, that.AJAX.getAllResponseHeaders(), that.AJAX.responseText, actionTrigger, that._getCustomHeaders(that.AJAX));
							}
						}
						that.AJAX = null;
					}
				}

				activeRequests --;
				return true;
			}
		};

		this._setHeaders = function(headers)
		{
			if (typeof headers != 'undefined')
			{
				for (var i in headers)
				{
					that.AJAX.setRequestHeader(i, headers[i]);
				}
			}
		};

		this._getCustomHeaders = function(AJAX)
		{
			var headers = {};
			headers['X-GeoIP'] = AJAX.getResponseHeader('X-GeoIP');
			headers['X-utime'] = AJAX.getResponseHeader('X-utime');
			return headers;
		};

		var urlCall = url;
		this.callback = callbackFunction || function () {};
		this.callbackObject = (callbackObject && typeof callbackObject == 'object') ? callbackObject : false;
	};


	/** Get an attribute of an element
	 * @param	element	an element to set an atribute for
	 * @param	name	the attribute name
	 */
	function get_attr(element, name)
	{
		var attr = '';

		if (element)
		{
			if (ie)
			{
				switch (name)
				{
					default:
						break;

					case 'class':
						attr = element.className;
						break;

					case 'id':
						attr = element.id;
						break;

					case 'rowspan':
						attr = element.rowSpan;
						break;

					case 'colspan':
						attr = element.colSpan;
						break;
				}
			}
			else
			{
				attr = element.getAttribute(name);
			}
		}

		if (!attr)
		{
			attr = '';
		}

		return attr.toString();
	};

	/** Set an attribute of an element
	 * @param	element	an element to set an atribute for
	 * @param	name	the attribute name
	 * @param	content	content of the attribute
	 */
	function set_attr(element, name, content)
	{
		if (ie)
		{
			switch (name)
			{
				default:
					break;

				case 'class':
					element.className = content;
					break;

				case 'rowspan':
					element.rowSpan = content;
					break;

				case 'colspan':
					element.colSpan = content;
					break;
				case 'id':
					element.id = content;
					break;

				case 'title':
					element.title = content;
					break;

				case 'type':
					element.type = content;
					break;

				case 'name':
					element.name = content;
					break;
			}
		}
		else
		{
			element.setAttribute(name, content);
		}
	};

	/** Remove attribute on element
	 * @param	object	element	an object element e.g. TD, TR, ...
	 * @param	string	attr	attribute to be removed
	 */
	function rem_attr(element, attr)
	{
		if (ie)
		{
			set_attr(element, attr, '');
		}
		else
		{
			element.removeAttribute(attr);
		}
	};

	function element_indexOfClassName(element, className)
	{
		return (" " + element.className + " ").indexOf(" " + className + " ");
	};

	function element_hasClass(element, className)
	{
		return (element_indexOfClassName(element, className) >= 0);
	};

	function element_addClass(element, className)
	{
		if (element_indexOfClassName(element, className) >= 0)
		{
			return false;
		}
		element.className += " " + className;
		return true;
	};

	function element_removeClass(element, className)
	{
		var position = element_indexOfClassName(element, className);
		if (position < 0)
		{
			return false;
		}
		var str = element.className;
		element.className  = (
			position > 0
				? str.substr(0, position - 1) + str.substr(position + className.length)
				: str.substr(0, position) + str.substr(position + className.length + 1)
		);
		return true;
	};

	/** String prototype: Append text (case insensitive)
	* @param	string	append	an append part
	* @param	bool 	[unique = true] append only if append part is not allready included in text
	* @param	bool	[last = true]	if true append last, false append before
	* @return	string	string after to be appended
	*/
	String.prototype.append = function(append, unique, last)
	{
		var unique = (typeof unique == 'undefined' || unique == true) ? true : false;
		var last = (typeof last == 'undefined' || last == true) ? true : false;

		var tmp_text = this.toLowerCase();
		var tmp_append = append.toLowerCase();

		if (!tmp_text.match(tmp_append) && tmp_append.indexOf(' ') == 0)
		{
			tmp_append = tmp_append.substr(1);
		}

		if (this.length == 0)
		{
			return append;
		}
		else if (!unique || (unique && tmp_text.match(tmp_append) == null))
		{
			if (last)
			{
				return this + append;
			}
			else
			{
				return append + this;
			}
		}
	};

	/* Remove text (case insensitive)
	* @param	string	text	a text to be removed from
	* @param	string	remove	a part to be removed
	* @return	string	text	after to be removed
	*/
	function text_remove(text, remove)
	{
		var tmp_text = text.toLowerCase();
		var tmp_remove = remove.toLowerCase();

		// try to remove leading gap
		if (tmp_text.match(tmp_remove) == null && tmp_remove.indexOf(' ') == 0)
		{
			tmp_remove = tmp_remove.substr(1);
		}

		if (tmp_text.match(tmp_remove))
		{
			var index_start = tmp_text.indexOf(tmp_remove);
			var index_stop = tmp_remove.length;

			tmp_remove = text.substr(index_start, index_stop);

			if (tmp_remove.length > 0)
			{
				text = text.replace(tmp_remove, '');
			}
		}

		return text;
	};

	/** fill begining zero if number is less then 10
	* @param	integer	num	number to be corrected
	*/
	function fill_zero(num)
	{
		if (num < 10)
		{
			return '0' + num;
		}
		else
		{
			return num;
		}
	};

	/** Test if element is in array checked by its key
	* @param	array	sa		source array
	* @param	var		key		key to be checked
	* @return	bool	true if element is in array, false otherwise
	*/
	function test_array_key(sa, key)
	{
		if (typeof sa == 'undefined' || typeof sa[key] == 'undefined')
		{
			return false;
		}

		return true;
	};

	/** Test if value exist in array
	 * @param	array	sa		source array
	 * @param	string	val		value to be checked
	 * @return	bool	true if value is in array, false otherwise
	 */
	function test_array_val(sa, val)
	{
		for (var i in sa)
		{
			if (sa[i] == val)
			{
				return true;
			}
		}

		return false;
	};

	/** Try to find value and return its key
	 * @param	array 	sa		source array
	 * @param	string	val		value to be checked
	 * @return	var		key if value is found, false otherwise
	 */
	function get_array_key(sa, val)
	{
		for (var i in sa)
		{
			if (sa[i] == val)
			{
				return i.to_number();
			}
		}

		return false;
	};

	/** Try to find value and return its string key
	 * @param	array 	sa		source array
	 * @param	string	val		value to be checked
	 * @return	var		key if value is found, false otherwise
	 */
	function get_array_string_key(sa, val)
	{
		for (var i in sa)
		{
			if (sa[i] == val)
			{
				return i;
			}
		}

		return false;
	};

	/** Remove an element from array by its key
	* @param	array	sa		source array
	* @param	var		key		remove array key
	* @return	array	affected array
	*/
	function remove_array_key(sa, key)
	{
		var tmp_sa = new Array();
		for (var i in sa)
		{
			if (i != key)
			{
				tmp_sa[i] = sa[i];
			}
		}

		return tmp_sa;
	};

	/** Window prototype: Open help window
	* @url		string	url		url of window
	*/
	window.open_help = function(url)
	{
		var id = Math.floor(Math.random() * 1000);
		return this.open( url, id, 'hotkeys=no, resizable=no, toolbar=no, status=no, dependent=yes, scrollbars=1, width=520, height=500' );
	};

	/** Number prototype: Convert string to number
	* @return	number
	*/
	Number.prototype.to_number = function()
	{
		return this;
	};

	/** String prototype: Convert string to number
	* @return	number
	*/
	String.prototype.to_number = function()
	{
		var pom = this - 0;

		if (isNaN(pom))
		{
			return this;
		}
		else
		{
			return pom;
		}
	};

	/** Convert utime to dbdate
	* @param	integer	utime	unix timestamp
	* @return	string	date in db format (YYYY-MM-DD)
	*/
	function utime2dbdate(utime)
	{
		var tmp = new Date();
		tmp.setTime(utime * 1000);

		return tmp.getFullYear() + '-' + fill_zero((tmp.getMonth() + 1)) + '-' + fill_zero(tmp.getDate());
	};

	/** Convert utime to GMT dbdate
	* @param	integer	utime	unix timestamp
	* @return	string	GMT date in db format (YYYY-MM-DD)
	*/
	function utime2gmt_dbdate(utime)
	{
		var tmp = new Date();
		tmp.setTime(utime * 1000);

		return tmp.getUTCFullYear() + '-' + fill_zero((tmp.getUTCMonth() + 1)) + '-' + fill_zero(tmp.getUTCDate());
	};

	/** Convert dbdate to utime
	* @param	string	date in db format (YYYY-MM-DD)
	* @return	integer	utime
	*/
	function dbdate2utime(dbdate)
	{
		var tmp = new Date();
		dbdate = dbdate.split('-');

		if (dbdate.length == 3)
		{
			tmp.setFullYear(dbdate[0]);
			tmp.setMonth((dbdate[1] - 1));
			tmp.setDate(dbdate[2]);
			tmp.setHours(0);
			tmp.setMinutes(0);
			tmp.setSeconds(0);
			tmp.setMilliseconds(0);

			return tmp.getTime() / 1000;
		}

		return 0;
	};

	/** Odds format
	* @param	float	odds number
	*
	* @return	return formated odds number
	*/
	function odds_format(odds)
	{
		if (odds < 10)
		{
			return odds.toPrecision(3);
		}
		else if (odds < 100)
		{
			return odds.toPrecision(4);
		}
		else
		{
			return odds.toPrecision(5);
		}
	};

	/** Replace parenthesis for Hebrew version
	*
	* @param	string	str string to be converted
	*
	* @return	string	converted string
	*/
	function fix_entities(str)
	{
		return str.replace(/\(/g, "&rlm;(");
	};

	/** Detect client browser
	 * @param	string	browser schortcat (ie|ff)
	 * @return	bool	true|false true if browser match the shortcut
	 */
	function browser_detect(type)
	{
		if (type == 'ie' && navigator.userAgent.match(/MSIE/))
		{
			return true;
		}
		else if (type == 'ie6' && navigator.userAgent.match(/MSIE 6/))
		{
			return true;
		}
		else if (type == 'ie7' && navigator.userAgent.match(/MSIE 7/))
		{
			return true;
		}
		else if (type == 'ff' && navigator.userAgent.match(/Gecko/))
		{
			return true;
		}
		else if (type == 'webkit' && navigator.userAgent.match(/WebKit/))
		{
			return true;
		}

		return false;
	};

	var ie = browser_detect('ie');
	var ie6 = browser_detect('ie6');
	var ie7 = browser_detect('ie7');
	var ff = browser_detect('ff');
	var webkit = browser_detect('webkit');

	var bench_result_start = null;
	var bench_result_stop = null;

	function bench_start(pom)
	{
		return;
		var tmp_date = new Date();
		typeof pom == 'undefined' ? pom = 'debug' : '';

		if ($('body div#bdebug').length == 0)
		{
			$('body').append('<div id="bdebug" style="position: absolute; right: 5px; top: 5px"></div>');
		}

		$('body div#bdebug').append('<div style="debug" id="debug-' + pom + '"></div>');

		bench_result_start = tmp_date.getTime();
	};

	function bench_stop(pom)
	{
		return;
		var tmp_date = new Date();
		typeof pom == 'undefined' ? pom = 'debug' : '';

		bench_result_stop = tmp_date.getTime();

		$('body div#bdebug div#debug-' + pom).html(pom + ' - time: ' + (bench_result_stop - bench_result_start) + ' [ms]');
	};


	function log(text)
	{
		return;		if ($('body div#bdebug').length == 0)
		{
			$('body').append('<div id="bdebug" style="position: absolute; right: 5px; top: 5px"></div>');
		}

		$('body div#bdebug').append('<div style="debug">' + text + '</div>');
	};


	function replace_query_string(url, param, value)
	{
		var re = new RegExp("([?|&])" + param + "=.*?(&|#|$)","i");
		if (url.match(re))
		{
			return url.replace(re,"$1" + param + "=" + value + "$2");
		}
		param = (url.indexOf("?") == -1 ? "?" : "&") + param + "=" + value;
		re = new RegExp("(.+)#(.+)");
		if (url.match(re))
		{
			return url.replace(re,"$1" + param + "#$2");
		}
		else
		{
			return url + param;
		}
	};

	function close_caption_box(element_ident, cookie_ident, expiredays, force_cookie)
	{
		if (typeof force_cookie == 'undefined')
		{
			force_cookie = true;
		}
		if ($("#"+element_ident).hide(100).length)
		{
			if (force_cookie)
			{
				setTimeout( 'clientStorage.store_cookie( \'' + cookie_ident + '\', 0, ' + (expiredays*86400) + ', \'self\', \'/\' )', 100);
			}
			else
			{
				clientStorage.store(cookie_ident, 0, expiredays*86400);
			}
		}
	};

	function set_caption_box_expire(box_time_ident, expiredays)
	{
		if (!clientStorage.get(box_time_ident))
		{
			clientStorage.store(box_time_ident, new Date().getTime() + 24 * 60 * 60 * 1000 * parseInt(expiredays), 30 * 24 * 60 * 60);
		}
	};

	/** Display/Hide element (calendar)
	*/
	function display_hide(id)
	{
		var element = document.getElementById(id);

		if (element)
		{
			$(element).remove();
		}
		else
		{
			$("#ifmenu-calendar > span.today").append(date_calendar());
		}
	};

	/** Display/Hide element
	*/
	function display_hide_element(id)
	{
		var element = document.getElementById(id);

		if (element)
		{
			if (element.style.display == 'block')
			{
				element.style.display = 'none';
			}
			else
			{
				element.style.display = 'block';
			}
		}
	};

	function display_element(id)
	{
		var element = document.getElementById(id);
		if (element)
		{
			element.style.display = 'block';
		}
	};

	function hide_element(id)
	{
		var element = document.getElementById(id);
		if (element)
		{
			element.style.display = 'none';
		}
	};

	function get_scrollbar_width() {
		var div = $('<div style="width:50px;height:50px;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
		// Append our div, do our calculation and then remove it
		$('body').append(div);
		var w1 = $('div', div).innerWidth();
		div.css('overflow-y', 'scroll');
		var w2 = $('div', div).innerWidth();
		$(div).remove();
		return (w1 - w2);
	};

	function clog() {
		try
		{
			if (window.console)
			{
				for (var i in arguments)
				{
					console.log(arguments[i]);
				}
			}
		}
		catch (err) {}
	};

	function cerr() {
		try
		{
			for (var i in arguments)
			{
				console.error(arguments[i]);
			}
		}
		catch (err) {

		}
	};

	function cdir() {
		try
		{
			for (var i in arguments)
			{
				console.dir(arguments[i]);
			}
		} catch (err) {}
	};
	/*
	function alog(content, type) {
		var buf;
		switch (type)
		{
			case 'k': //show top level keys
				var keys = [];
				for (var key in content)
					keys.push(key);
				buf = keys.join(', ');
				break;
			default:
				buf = JSON.stringify(content, null, 4);
		}
		alert(buf);
	};
	*/
	/** Add livestreaming table "day labels" (ASS), if need be, clones nation lable
	*/
	function addLiveStreamingDayLabels()
	{
		var user_gmt_offset = new Date();
		user_gmt_offset = user_gmt_offset.getTimezoneOffset() * 60;

		var local_gmt_offset = get_gmt_offset();
		var dayBefore = '';
		var firstLabel = true;
		$('#block-live-streaming-kickoff td.time').each(function()
		{
			var date = new Date(eval(($(this).text() - local_gmt_offset + user_gmt_offset)) * 1000);
			var day = date.getDay();
			var translatedDay = TXT_CAL_FULL[day];

			if (day != dayBefore)
			{
				var dayLabel = '<tr class="date'+(firstLabel == true ? ' first-date' : '')+'">'+
										'<td colspan="3">'+
											translatedDay+' '+timestamp2date("d.m.Y", $(this).text(), local_gmt_offset)+
										'</td>'+
									'</tr>';

				if ($(this).closest('tr').prev().hasClass('label'))
				{
					$(this).closest('tr').prev().before(dayLabel);
				}
				else
				{
					var labelElement = $(this).closest('tr').prevAll('tr.label:first');
					$(this).closest('tr').before(dayLabel);
					$(labelElement).clone().addClass('cloned').insertBefore($(this).closest('tr'));
					$(this).closest('tr').addClass('tr-first');
				}
				firstLabel = false;
			}
			dayBefore = day;
		});
	};

	/** Backs up timestamp values of livestreaming events (ASS), so they could be changed by TZ
	*/
	function makeTemporaryEventObjects()
	{
		var className1 = '#block-live-streaming-kickoff td.time';
		var className2 = '#block-live-streaming-event td.time';
		var k = 0;
		var e = 0;

		$(className1).each(function()
		{
			kickoffEventArray[k] = $(this).text();
			k++;
		});
		$(className2).each(function()
		{
			eventEventArray[e] = $(this).text();
			e++;
		});
	};

	/** Regenerates livestreaming tables (ASS) when TZ changes
	*/
	function regenerateLiveStreamingContent(className, UStimeFormat)
	{
		if (className == 'kickoff')
		{
			$('#block-live-streaming-kickoff tr.date').remove();
			$('#block-live-streaming-kickoff tr.cloned').remove();
		}

		var element = '#block-live-streaming-'+className+' td.time';
		var i = 0;
		$(element).each(function() {

			$(this).text($(this).text().replace(/.*/, (className == 'kickoff' ? kickoffEventArray[i] : eventEventArray[i])));
			i++;
		});

		if (className == 'kickoff')
		{
			addLiveStreamingDayLabels();

			$(element).each(function() {
				if ($(this).closest('tr').prev().hasClass('tr-first'))
				{
					$(this).closest('tr').removeClass('tr-first');
				}
			});
		}
		from_unixtime_to_datetime(className,'text','#block-live-streaming-'+className+' td.time', UStimeFormat);
	};

	/** Takes element and extract timestamp value, then sets target date format and call date function
	*/
	function from_unixtime_to_datetime(yearFormat, titleOrText, className, UStimeFormat)
	{
		var local_gmt_offset = get_gmt_offset();

		$(className).each(function() {

			if (titleOrText == 'title')
			{
				var title = $(this).attr('title');
				if (typeof title != 'undefined')
				{
					var data = title.split("\n");
					var dataMax = data.length - 1;
					var timestamp = data[dataMax];
				}
			}
			else
			{
				var timestamp = $(this).text();
			}

			if (typeof timestamp != 'undefined')
			{
				var format = UStimeFormat ? "M d" : "d.m.";
				if (yearFormat == 'tv')
				{
					format += UStimeFormat ? ", g:i A" : " G:i";
				}
				else if (yearFormat == 'short')
				{
					format += (UStimeFormat ? ', ' : '') + "y";
				}
				else if (yearFormat == 'kickoff')
				{
					format = UStimeFormat ? "h:i A" : "H:i";
				}
				else if (yearFormat == 'event')
				{
					format += UStimeFormat ? ", h:i A" : " H:i";
				}

				var startDateTimeStr = timestamp2date(format, timestamp, local_gmt_offset);
				if (titleOrText == 'title')
				{
					var dataTmp = data[0] + "\n";
					if (dataMax == 2)
					{
						dataTmp += data[1] + "\n";
					}
					$(this).attr('title', dataTmp + startDateTimeStr);
				}
				else
				{
					$(this).text(startDateTimeStr);
				}
			}
		});
	};

	function show_media(url, width, height)
	{
		var resizable = false;
		if (!width && !height)
		{
			width = 400;
			height = 400;
			var resizable = true;
		}
		else
		{
			width = !width ? 300 : width + 10;
			height = !height ? 300 : height + 10;

			if (width < 100)
			{
				width = 100;
			}

		}

		if (document.all)
		{
			var x = Math.round(window.screen.availWidth / 2 - width / 2);
			var y = Math.round(window.screen.availHeight / 2 - height / 2);
			if (x < 0)
			{
				x = 10;
			}
			if (y < 0)
			{
				y = 10;
			}
		}
		else
		{
			var x = 200,y = 200;
		}


		var features = 'height='+height+', left='+x+', location=no, menubar=no, resizable='+(resizable ? 'yes' : 'no')+', '
			+'scrollbars=no, status=no, titlebar=no, toolbar=no, top='+y+', width='+width;

		window.open(url, '_blank', features);
	};
function tooltip(div_input_id, ident, disable)
{
	this.max_width = 400;
	this.is_init = false;
	this.div = null;
	this.div_content = null;
	this.span_parent = null;
	this.td_parent = null;
	this.tr_parent = null;
	this.span_parent_title = '';
	this.td_parent_title = '';
	this.tr_parent_title = '';
	this.isDisabled = disable || false;

	this.div_id = (typeof div_input_id == 'undefined') ? null:div_input_id;
	this.ident = (typeof ident == 'undefined') ? 1 : ident;
	this.container_id = 'tooltip-' + this.ident;

	this.init = function()
	{
		if (this.is_init || this.isDisabled)
		{
			return;
		}

		// create new or use existing tooltip element
		if (this.createTooltipElement())
		{
			this.is_init = true;
		}
	};

	this.show = function(elm, elm_event, opposite_direction, border_elm)
	{
		if (!this.is_init || this.isDisabled)
		{
			return;
		}

		var title = elm.title;
		var title_length  = title.length;

		// formating
		title = title.replace(/\[b\]/i, '<strong>');
		title = title.replace(/\[\/b\]/i, '</strong>');
		title = title.replace(/\[br\]/ig, '<br />');
		title = title.replace(/\[u\]/i, ' &raquo; ');
		title = title.replace(/\[d\]/i, ' &raquo; ');
		title = title.replace(/\n/g, "<br \/>");
		title = title.replace(/\\'/g, '\'');

		if (title_length > 0)
		{
			var x = parseInt(elm_event.clientX);
			var y = parseInt(elm_event.clientY);

			if (typeof window.pageYOffset != 'undefined')
			{
				var window_top = window.pageYOffset;
				var window_left = window.pageXOffset;
			}
			else
			{
				var window_top = document.documentElement.scrollTop;
				var window_left = document.documentElement.scrollLeft;
			}

			this.div_content.innerHTML = title;
			elm.title = '';

			this.span_parent = elm.parentNode;
			this.span_parent_title = this.span_parent.title;
			this.span_parent.title = '';

			this.td_parent = this.span_parent.parentNode;
			this.td_parent_title = this.td_parent.title;
			this.td_parent.title = '';

			this.tr_parent = this.td_parent.parentNode;
			this.tr_parent_title = this.tr_parent.title;
			this.tr_parent.title = '';

			this.div.style.display = 'block';
			this.div.style.width = this.div.offsetWidth + 'px';

			var div_width = this.div.offsetWidth;
			if (div_width > this.max_width)
			{
				div_width = this.max_width;
				this.div.style.width = this.max_width + 'px';
				this.div_content.style.whiteSpace = 'normal';
			}

			if (typeof opposite_direction == 'undefined')
			{
				opposite_direction = (($(window).width()/2 - x) > 0);
			}
			else if (typeof opposite_direction != 'undefined' && opposite_direction == null && typeof border_elm != 'undefined')
			{
				var fence = $("div#"+border_elm);

				opposite_direction = true;
				if (x+div_width > fence.width())
				{
					opposite_direction = false;
				}
			}

			if (opposite_direction == true)
			{
				$(this.div).addClass("revert");
			}

			// IE6 fixes
			document.getElementById(this.container_id + '-lt').style.height = this.div.offsetHeight + 'px';
			document.getElementById(this.container_id + '-rt').style.height = this.div.offsetHeight + 'px';
			document.getElementById(this.container_id + '-cb').style.width = this.div.offsetWidth + 'px';

			this.div.style.zIndex = '999';

			// indent
			var tooltip_indent_r = (project_type_name === '_fs' || project_type_name === '_diretta-2' ? 11 : 10); // right
			var tooltip_indent_l = (project_type_name === '_fs' || project_type_name === '_diretta-2' ? 11 : 10); // left
			var tooltip_indent_t = 10; // top

			if (project_type_name === '_fs'/* || project_type_name === '_diretta-2' */)
			{
				var $elm = $(elm);
				var elm_coords = $elm.offset();
				var elm_width = $elm.width();
				var elm_height = $elm.height();
				var pos_top = (elm_coords.top + tooltip_indent_t + elm_height);
				var elm_midpoint = (elm_coords.left ? Math.floor(elm_width / 2) : Math.ceil(elm_width / 2));
				var pos_left1 = (elm_coords.left + elm_midpoint - tooltip_indent_r);
				var pos_left2 = (elm_coords.left - div_width + Math.ceil(elm_width / 2) + tooltip_indent_l);
				var pos_left = opposite_direction ? pos_left1 : pos_left2;
			}
			else
			{
				var pos_left;
				if (opposite_direction)
				{
					pos_left = (x + tooltip_indent_l + window_left);
				}
				else
				{
					pos_left = (x - div_width - tooltip_indent_r + window_left);
				}
				var pos_top = (y + tooltip_indent_t + window_top);
			}

			this.div.style.top = pos_top + 'px';
			this.div.style.left = pos_left + 'px';
		}
	};

	this.hide = function(elm)
	{
		if (!this.is_init || this.isDisabled)
		{
			return;
		}

		var title = this.div_content.innerHTML.replace(/<br( \/){0,1}>/gi, "\n");
		title = title.replace(/\<strong\>/i, '[b]');
		title = title.replace(/\<\/strong\>/i, '[/b]');

		if (title.length > 0)
		{
			if (elm.title == '')
			{
				elm.title = title;
			}

			this.div.style.display = 'none';
			this.div.style.width = 'auto';
			this.div_content.innerHTML = '';
			$(this.div).removeClass("revert");

			if (this.span_parent.title == '')
			{
				this.span_parent.title = this.span_parent_title;
			}
			if (this.td_parent.title == '')
			{
				this.td_parent.title = this.td_parent_title;
			}
			if (this.tr_parent.title == '')
			{
				this.tr_parent.title = this.tr_parent_title;
			}
		}
	};

	this.hide_all = function()
	{
		if (!this.is_init || this.isDisabled)
		{
			return;
		}

		this.div.style.display = 'none';
		this.div.style.width = 'auto';
		$(this.div).removeClass("revert");
	};

	this.set_max_width = function(width)
	{
		this.max_width = width - 0;
	};

	/**
	 * Returns element as tooltip wrapper.
	 * @return {Object}
	 */
	this.getTooltipWrapper = function()
	{
		return this.div_id ? document.getElementById(this.div_id) : document.getElementsByTagName('body')[0];
	};

	/**
	 * Creates tooltip element. If wrapper element could not be created
	 * returns false otherwise true.
	 * @return {Boolean}
	 */
	this.createTooltipElement = function()
	{
		this.div = document.getElementById(this.container_id);

		// use existing tooltip element in DOM
		if (this.div !== null)
		{
			this.div_content = this.div.getElementsByTagName('span')[0];
			return true;
		}

		// tooltip wrapper
		var wrapper = this.getTooltipWrapper();
		if (!wrapper)
		{
			return false;
		}

		// create new tooltip element
		this.div = document.createElement('div');
		set_attr(this.div, 'id', this.container_id);
		set_attr(this.div, 'class', 'tooltip');

		this.div_content = document.createElement('span');
		this.div.appendChild(this.div_content);

		var div_lt = document.createElement('div');
		set_attr(div_lt, 'id', this.container_id + '-lt');
		set_attr(div_lt, 'class', 'tooltip-lt');
		this.div.appendChild(div_lt);

		var div_rt = document.createElement('div');
		set_attr(div_rt, 'id', this.container_id + '-rt');
		set_attr(div_rt, 'class', 'tooltip-rt');
		this.div.appendChild(div_rt);

		var div_lb = document.createElement('div');
		set_attr(div_lb, 'id', this.container_id + '-lb');
		set_attr(div_lb, 'class', 'tooltip-lb');
		this.div.appendChild(div_lb);

		var div_cb = document.createElement('div');
		set_attr(div_cb, 'id', this.container_id + '-cb');
		set_attr(div_cb, 'class', 'tooltip-cb');
		this.div.appendChild(div_cb);

		var div_rb = document.createElement('div');
		set_attr(div_rb, 'id', this.container_id + '-rb');
		set_attr(div_rb, 'class', 'tooltip-rb');
		this.div.appendChild(div_rb);

		var div_rb = document.createElement('div');
		set_attr(div_rb, 'id', this.container_id + '-ct');
		set_attr(div_rb, 'class', 'tooltip-ct');
		this.div.appendChild(div_rb);

		wrapper.appendChild(this.div);
		return true;
	};

	this.init();
};
function glib_show_hidden(surface_table_class, table_id, show_next_limit)
{
	if (surface_table_class)
	{
		surface_table_class = '#' + surface_table_class + ' ';
	}

	var $tbody = $(surface_table_class + '.' + table_id + ' tbody');

	var visibleRows = $tbody.data('visibleRows');
	var $rows = $tbody.find('tr.hidden:not(.filtered-out)');

	if (show_next_limit)
	{
		$rows = $rows.slice(0, show_next_limit);
	}

	$rows.removeClass('hidden');

	if (visibleRows)
	{
		$tbody.data('visibleRows', visibleRows + $rows.length);
	}

	fix_row_parity($tbody);

	toggle_show_more($tbody);
};

function toggle_show_more($tbody)
{
	var numHidden = $tbody.find('tr').not(':visible,.filtered-out').length;

	$tbody.parent().find('tr.hid').toggleClass('hidden', numHidden === 0);
}

function fix_row_parity($tbody)
{
	
	$($tbody).find('tr:visible').removeClass('even').removeClass('odd');
	$($tbody).find('tr:visible:even').addClass('odd');
	$($tbody).find('tr:visible:odd').addClass('even');
};
var $jq = jQuery;
if ($jq === null)
{
	$jq = jQuer;
}
var iframe_external = false;

function StatsDrawViewClass()
{
	this.public = {};

	this.options = {
		link_hide_class: 'scroll-text-inactive',
		height_fixes: {
			'default': 1,
			'mozilla': 2,
			'webkit': 4,
			'chrome': 4,
			'safari': 4,
			'msie-7': 0,
			'msie-8': 0
		}
	};

	this.limits = {
		"min_x": 0,
		"min_y": 0,
		"max_x": undefined,
		"max_y": undefined,
		"scroll_x": undefined
	};

	this.dimensions = {
		height: undefined,
		heightHeader: undefined,
		heightInternal: undefined,
		offsetBottom: 10,
		offsetTop: undefined
	};

	this.position = { "x": undefined, "y": undefined, "xp": 0 };
	this.keyCodes = { 33: 'page-up', 34: 'page-down', 35: 'end', 36: 'home', 37: 'left', 38: 'up', 39: 'right', 40: 'down'};
	this.scroll = { "horizontal": false, "vertical": false, "disabled": false };
	this.item = { "height": 0, "width": 0 };
	this.el = {};    // element jQuery object swap
	this.swap = {};
	this.ready = false;

	this.scrollEnv = null;
	this.scrollContent = null;
	this.scrollHeader = null;
	this.boxes = {};
	this.box_titles = {};

	this.browserScrollbarWidth = null;
	this.searchInScrollAreaHackEnabled = true;

	this.detailVersion = 1; // version of detail and detail url

	// HELPERS {
	// check on browser version
	this.getBrowser = function()
	{
		var browser = '';
		$jq.each($jq.browser, function(attr, value)
		{
			if (value == true)
			{
				browser = attr;
			}
		});

		return browser + (browser == 'msie' ? '-' + parseInt($jq.browser.version) : '');
	};

	this.mobile = function()
	{
		return $.browser.mobile || /android|ipad|iphone|ipod/i.test(navigator.userAgent.toLowerCase());
	};

	// show/hide scroll whole links box if necessary
	this.update_scrollbox = function()
	{
		((!this.scroll.disabled && !this.scroll.horizontal) || this.limits.min_x >= this.limits.max_x) ? this.scrollHeader.links.hide() : this.scrollHeader.links.show();
		this.dimensions.offsetTop = this.scrollEnv.offset().top + this.dimensions.heightHeader;
	};

	// show/hide scroll links on the top
	this.update_links = function()
	{
		this.position.x < this.limits.max_x ? this.show_links(this.el.lr) : this.hide_links(this.el.lr);
		this.position.x > this.limits.min_x ? this.show_links(this.el.ll) : this.hide_links(this.el.ll);
	};

	this.hide_links = function(context)
	{
		context.addClass(this.options.link_hide_class);
	};

	this.show_links = function(context)
	{
		context.removeClass(this.options.link_hide_class);
	};

	// shorten too long names
	this.shorten_result_names = function()
	{
		$jq(".match").each(function(index, match)
		{
			match = $jq(match);

			match.find(".participant").each(function(i, part)
			{
				part = $jq(part);
				var name = part.find(".name");
				var score = 0;
				score = part.find(".score");
				if (score.width() == null)
				{
					score = part.parent().find(".score-final");
				}
				name.width(part.width() - score.width() - (score.width() ? 5 : 0));

				//var nameParts = [];
				//nameParts = name.html().split('</span>');
				//var title = nameParts ? nameParts[nameParts.length - 1] : name.html();
				//name.attr({title: title});
			}.bind(this));
		}.bind(this));
	};
	// }

	// MOVEMENT {

	// Keyboard movement or move by one match
	this.touch = function(dir, e)
	{
		var m;
		var count = 1;

		e.preventDefault();
		(typeof dir == 'number') && (dir = this.keyCodes[parseInt(dir)]);
		if (typeof dir == 'undefined' || !dir)
		{
			return;
		}

		var newpos;
		if ((this.scroll.disabled || this.scroll.horizontal) && ((dir == 'left' && this.position.x > this.limits.min_x) || (dir == 'right' && this.position.x < this.limits.max_x)))
		{
			newpos = this.position.x + count * (dir == 'left' ? -1 : 1);
			if (typeof newpos != 'undefined' && newpos != '')
			{
				var pos = newpos == this.limits.max_x ? this.limits.scroll_x : Math.min(0, Math.max(this.limits.scroll_x, -(this.item.width) * (newpos - this.limits.min_x)));
				this.set_content_displacement('horizontal', pos, 100, true);
				this.position.x = newpos;
				this.update_links();
			}
		}
		else if (this.scroll.vertical && ((dir.indexOf('up') >= 0 && (m = 'up')) || (dir.indexOf('down') >= 0 && (m = 'down'))))
		{
			var d = (dir == 'down' || dir == 'page-down') ? 1 : -1;
			var q = dir.indexOf('page') === 0 ? this.limits.min_y : 1;

			// don't scroll if there is nowhere to go
			if ((m == 'down' && this.position.y < this.limits.max_y) || (m == 'up' && this.position.y > this.limits.min_y))
			{
				newpos = Math.max(this.limits.min_y, Math.min(this.limits.max_y, this.position.y + (count * d * q)));
				this.position.y = newpos;

				if (newpos == this.limits.max_y)
				{
					this.el.env.tinyscrollbar_update("bottom");
					return;
				}

				var limit = this.scrollContent.outerHeight() - (this.limits.min_y * this.item.height * 1.13);
				pos = newpos == this.limits.max_y ? limit : Math.max(0, Math.min(limit, this.item.height * (newpos - this.limits.min_y)));
				this.set_content_displacement('vertical', pos, 100, true);
			}
		}
		else if (this.scroll.vertical && dir == 'home')
		{
			this.el.env.tinyscrollbar_update("top");
			this.position.y = this.limits.min_y;
		}
		else if (this.scroll.vertical && dir == 'end')
		{
			this.el.env.tinyscrollbar_update("bottom");
			this.position.y = this.limits.max_y;
		}
	};

	// update position when scrolling (to preserve keyboard scroll position)
	this.update_vertical_position = function(p)
	{
		if (this.scroll.vertical)
		{
			this.position.y = this.limits.min_y + Math.floor(-p / this.item.height);
		}
	};

	// update horizontal position when scrolling (to preserve keyboard scroll position)
	this.update_horizontal_position = function(p)
	{
		if (this.scroll.horizontal)
		{
			this.position.x = p > this.limits.scroll_x ? this.limits.min_x + Math.floor(-p / this.item.width) : this.limits.max_x;
			this.update_links();
		}
	};

	// move content by to px posistion
	this.set_content_displacement = function(dir, p, duration)
	{
		typeof duration == 'undefined' && (duration = 0);

		if (dir == 'horizontal')
		{
			if (!this.scroll.disabled)
			{
				var scroll_pos = p == 0 ? 0 : Math.floor(p * (this.el.sx.width() - this.el.sx.find(".hthumb").width()) / this.limits.scroll_x);
				this.el.sx.find(".hthumb").animate({"left": scroll_pos}, 150);
			}

			this.scrollContent.animate({"left": Math.round(p) + 'px'}, duration, 'swing');
			this.scrollHeader.content.animate({"left": Math.round(p) + 'px'}, duration, 'swing');
		}

		dir == 'vertical' && this.el.env.tinyscrollbar_update(p);
	};

	// move scrollbar
	this.set_scrollbar_displacement = function(dir, p, duration)
	{
		typeof duration == 'undefined' && (duration = 0);

		var cont_pos;
		if (dir == 'horizontal')
		{
			cont_pos = -(p == 0 ? 0 : Math.floor((p * this.limits.scroll_x) / (this.el.sx.width() - this.el.sx.find(".hthumb").width())));

			this.scrollContent.animate({"left": Math.round(cont_pos) + 'px'}, duration, 'swing');
			this.scrollHeader.content.animate({"left": Math.round(cont_pos) + 'px'}, duration, 'swing');
			this.el.sx.find(".hthumb").animate({"left": -p}, duration);
		}
		else if (dir == 'vertical')
		{
			cont_pos = p == 0 ? 0 : Math.floor(-(p * this.limits.scroll_x) / (this.el.sx.width() - this.el.sx.find(".hthumb").width()));

			this.scrollContent.animate({"left": Math.round(cont_pos) + 'px'}, duration, 'swing');
			this.scrollHeader.content.animate({"left": Math.round(cont_pos) + 'px'}, duration, 'swing');
			this.el.sx.find(".hthumb").animate({"left": p}, duration);
		}
	};

	// }

	// SIZE CONTROL {

	/* Resize draw to match window
	 * @return void
	 */
	this.resize_viewport = function(dont_grab_size)
	{
		var dt = $('.detail-terminator');
		var dth = parseInt(dt.css('margin-top')) + parseInt(dt.css('margin-bottom'));
		var wh = $jq(window).height() - $jq("body").outerHeight() + $jq("body").height() - dth;
		this.scrollEnv.width(this.scrollEnv.closest('#playoff-env').width());
		this.update_scrollbox();

		this.dimensions.heightHeader = this.scrollHeader.height();
		this.dimensions.offsetTop = this.scrollEnv.offset().top;
		this.dimensions.offsetBottom = +$jq(".closer").outerHeight() - this.scrollEnv.outerHeight() + this.scrollEnv.height() + this.scrollHeader.height();

		this.scrollEnv.height(wh - this.dimensions.offsetTop - this.dimensions.offsetBottom);
		this.dimensions.height = this.scrollEnv.outerHeight() - this.dimensions.heightHeader;
		if (!dont_grab_size)
		{
			this.dimensions.heightInternal = this.scrollContent.children().height();
		}
	};

	/* Show or hide scrollbars
	 * @return void
	 */
	this.update_scrollbars = function()
	{
		// diff_x was reduced by 20 due to vertical scrollbars' width
		var denied = this.el.env.hasClass('default-scroll');
		var diff_x = this.scrollContent.outerWidth() - this.scrollEnv.width() - 20;
		var diff_y = this.scrollContent.outerHeight() - this.scrollEnv.height();

		if (this.scroll.horizontal = (!denied && diff_x > 0))
		{
			this.scrollHeader.links.addClass('scrolls-x');
			if (!((typeof tournament != 'undefined' && tournament) || (typeof tournamentPage != 'undefined' && tournamentPage)))
			{
				this.el.sx.show();
			}
		}
		else
		{
			this.scrollHeader.links.removeClass('scrolls-x');
			this.el.sx.hide();
			this.scrollContent.css("left", 0);
			this.el.sx.find(".hthumb").css("left", 0);
			this.scrollHeader.content.css("left", 0);
		}

		if (this.scroll.vertical = (!denied && diff_y > 0))
		{
			this.scrollHeader.links.addClass('scrolls-y');
			if (!((typeof tournament != 'undefined' && tournament) || (typeof tournamentPage != 'undefined' && tournamentPage)))
			{
				this.el.sy.show();
			}
		}
		else
		{
			this.scrollHeader.links.removeClass('scrolls-y');
			this.el.sy.hide();
			this.scrollContent.css("top", 0);
		}
		this.el.sx.width(this.el.env.width() - (this.scroll.horizontal && !this.scroll.vertical ? 0 : (this.el.sy.width() - 1)));
	};
	// }

	// INIT functions {
	// general resize
	this.update_size = function(dont_grab_size)
	{
		if (this.ready && this.scrollEnv.parent().is(":visible"))
		{
			this.searchInScrollAreaHackInit();

			dont_grab_size = typeof dont_grab_size == 'undefined' ? false : !!dont_grab_size;

			if (!this.scroll.disabled)
			{
				this.resize_viewport(dont_grab_size);
				this.update_scrollbars();
			}
			else
			{
				this.dimensions.heightInternal = this.scrollContent.children().height();
			}

			// calculate positions {
			var browser = this.getBrowser();
			var hfix = (typeof this.options.height_fixes[browser] != 'undefined') ? this.options.height_fixes[browser] : this.options.height_fixes['default'];
			var fr = this.scrollContent.find(".round").first();

			this.item.height = fr.find("div.relation").first().height() + fr.find("div.relation").next().height();
			this.item.width = this.scrollEnv.find(".round").first().width();
			this.limits.min_x = Math.round(this.scrollEnv.width() / this.item.width);
			this.limits.min_y = Math.round(this.scrollEnv.height() / this.item.height);
			this.limits.max_x = Math.ceil(this.scrollHeader.find("li").length);
			this.limits.max_y = Math.ceil(fr.find("div.match").length / 2);
			// }

			// reset current positions {
			this.position.x = Math.min(this.limits.max_x, Math.max(this.limits.min_x, Math.floor(-(parseInt(this.scrollContent.css('left')) - 5) / this.item.width) + this.limits.min_x));
			this.position.y = Math.min(this.limits.max_y, Math.max(this.limits.min_y, Math.floor(parseInt(this.scrollContent.css('top')) / this.item.height) + this.limits.min_y));
			// }

			if (!this.scroll.disabled)
			{
				// set up correct hscrollbar handle size {
				var tmp_w = this.el.env.children(".hcrollbar").width();
				this.el.sx.find(".hthumb").width(Math.floor(Math.min(tmp_w, tmp_w / (this.scrollContent.width() / tmp_w))));
				// }

				// save scrollbar sizes {
				this.dimensions.scrollbarSize = this.el.sy.children(".thumb").width();
				// }
			}

			this.limits.scroll_x = this.scrollEnv.width() - this.scrollContent.width() - (isNaN(this.dimensions.scrollbarSize) ? 0 : this.dimensions.scrollbarSize) - 1;

			// update scroll links on top {
			this.update_links();
			this.update_scrollbox();

			if (!this.scroll.disabled)
			{
				this.resize_viewport(dont_grab_size);
				this.el.env.tinyscrollbar_update();  // tinyscrollbars' internal
			}
			// }
			this.scrollEnv.width(this.scrollEnv.parent().width());

			this.searchInScrollAreaHack();
		}
	};

	this.getBrowserScrollbarWidth = function()
	{
		if (this.browserScrollbarWidth != null)
		{
			return this.browserScrollbarWidth;
		}

		var scrollTestElement = $("<div style='width: 100px; height: 100px; overflow: scroll; position: absolute; top: -9999px;'></div>");
		$('body').append(scrollTestElement);
		var scrollbarWidth = $(scrollTestElement).get(0).offsetWidth - $(scrollTestElement).get(0).clientWidth;

		$(scrollTestElement).remove();
		this.browserScrollbarWidth = scrollbarWidth;

		return scrollbarWidth;
	};

	this.searchInScrollAreaHackInit = function()
	{
		if ((!$.browser.mozilla || !this.searchInScrollAreaHackEnabled) && !this.mobile())
		{
			return;
		}
		$('#playoff-env').find('.viewport-wrap').css({'width': 'auto', 'height': 'auto'});
	};

	this.searchInScrollAreaHack = function()
	{
		if ((!$.browser.mozilla || !this.searchInScrollAreaHackEnabled) && !this.mobile())
		{
			return;
		}

		var scrollbarWidth = this.getBrowserScrollbarWidth();
		var viewport = $('#playoff-env').find('.viewport');
		var viewportWrap = $('#playoff-env').find('.viewport-wrap');

		$(viewportWrap).css({'width': $(viewport).outerWidth(true) + 'px', 'height': $(viewport).outerHeight(true) + 'px'});
		$(viewport).css({'width': ($(viewport).outerWidth(true) + scrollbarWidth), 'height': ($(viewport).outerHeight(true) + scrollbarWidth) + 'px'});
		$(viewportWrap).css('overflow', 'hidden');
		$(viewport).css('overflow', 'scroll');
	};

	this.participantWayHighlight = function()
	{
		var matches = $(this.scrollEnv).find('.match');
		var highlightClass = 'participant-way-highlight';

		$(matches).hover(function()
		{
			var participantsClasses = [];

			if ($(this).is(':not(.has-events)'))
			{
				$(this).addClass('participant-way-highlight');
			}

			$(this).find('.participant').each(function()
			{
				var participantClass = ($(this).attr('class').match(/\bglib\-participant\-([^\s]*)\b/));
				if (participantClass != null && typeof participantClass[0] != 'undefined')
				{
					participantsClasses.push(participantClass[0]);
				}

			});

			for (var i in participantsClasses)
			{
				$(matches).has('.participant.' + participantsClasses[i]).not(this).addClass(highlightClass);
			}

		}, function()
		{
			$(this).removeClass('participant-way-highlight');
			$(matches).removeClass(highlightClass);
		});
	};

	// init hscroll (run only once) (HC Roll Bar)
	this.create_horizontal_scrollbar = function()
	{
		this.el.sx.thumb.unbind(".hcb").bind("mousedown", {}, function(e)
		{
			e.preventDefault();
			e.stopPropagation();

			// prevent IE from selecting text
			try
			{
				this.options.original_onselectstart = document.onselectstart;
				document.onselectstart = function()
				{
					return false;
				}
			} catch (e)
			{
			}

			typeof this.public.i == 'undefined' && (this.public.i = 0);
			this.public.i++;

			var hmax = this.el.sx.width() - this.el.sx.thumb.width();
			$jq("body").bind("mousemove", { "start": e.pageX, "max": hmax, "h_quotient": hmax / this.limits.scroll_x }, function(e)
			{
				// set up limits && count position
				pos = Math.max(0, Math.min(e.data.max, parseInt(this.el.sx.thumb.css('left')) - (e.data.start - e.pageX)));
				e.data.start = e.pageX;                                                               // reset click position to last position save
				p = pos / e.data.h_quotient;                                                            // count content scroll

				this.scrollContent.css({"left": Math.round(p) + 'px'});
				this.scrollHeader.content.css({"left": Math.round(p) + 'px'});
				this.el.sx.thumb.css("left", pos + 'px');
				this.update_horizontal_position(p);
			}.bind(this));
		}.bind(this));

		this.el.sx.find(".htrack").mousedown(function(e)
		{
			var t = this.el.sx.find('.hthumb');
			var position = parseInt(t.css('left'));
			var width = parseInt(t.width());
			var yScrollbarWidth = (this.scroll.vertical ? parseInt(this.el.sy.width()) : 0);
			var maxClickWidth = $(window).width()-yScrollbarWidth;
			var click=(e.pageX >= maxClickWidth ? maxClickWidth : e.pageX);
			this.set_scrollbar_displacement('horizontal', -click + (click >= position + width ? width : 0), 100);
		}.bind(this));

		$jq(document).mouseup(function(e)
		{
			try
			{
				document.onselectstart = this.options.original_onselectstart;
			} catch (e)
			{
			}

			$jq("body").unbind('mousemove');
			hscroll = undefined;
		}.bind(this));
	};

	this.restart = function()
	{
		this.ready = false;
	};

	// secondary constructor, general init
	this.init = function(detailVersion)
	{
		if (typeof detailVersion != 'undefined')
		{
			this.detailVersion = detailVersion;
		}

		window.dw = this; // ??
		this.el.env = $jq("#playoff-env");

		// Don't do anything if you don't see draw
		if (this.el.env.length != 1)
		{
			return;
		}

		// Check all participants for long names
		(!$jq.browser.msie || ($jq.browser.msie && $jq.browser.version >= 7)) && this.shorten_result_names();

		this.scrollEnv = this.el.env.find(".viewport");
		this.scrollContent = this.el.env.find(".overview");
		this.scrollHeader = $jq("#playoff-header");
		this.scrollHeader.content = this.scrollHeader.find("ul").first();
		this.scrollHeader.links = $jq("#playoff-links, .playoff-scroll-buttons");

		if ((typeof tournament != 'undefined' && tournament) || (typeof tournamentPage != 'undefined' && tournamentPage))
		{
			this.searchInScrollAreaHackEnabled = false;
			var columnCount = parseInt($('#draw_column_count').text());
			var columnWidth = parseInt($('#detail .round').css('width'));
			this.scrollContent.css('width', columnWidth * columnCount);
			this.scrollEnv.css('height', this.scrollContent.outerHeight());
			$('#playoff-header ul').css('width', columnWidth * columnCount);
		}

		if (this.scroll.disabled != true && !(this.scroll.disabled = this.el.env.hasClass('default-scroll')))
		{
			this.scrollEnv.closest('#playoff-env').prepend('<div class="scrollbar"><div class="track"><div class="thumb scroll-box"><div class="end"></div></div></div></div>');
			this.scrollEnv.closest('#playoff-env').prepend('<div class="hcrollbar"><div class="htrack"><div class="hthumb scroll-box"><div class="hend"></div></div></div></div>');

			this.el.sx = this.el.env.children(".hcrollbar");
			this.el.sx.thumb = this.el.sx.find(".hthumb");
			this.el.sy = this.el.env.children(".scrollbar");
			this.el.env.tinyscrollbar(); // vertical scrollbar

			this.create_horizontal_scrollbar();
		}

		this.el.lr = this.scrollHeader.links.find(".scroll-right");
		this.el.ll = this.scrollHeader.links.find(".scroll-left");

		$jq(window).bind('resize', {"dw": this}, function(e)
		{
			e.data.dw.update_size();
		});
		$jq(document).keydown(function(e)
		{
			e.keyCode in this.keyCodes && this.touch(e.keyCode, e)
		}.bind(this));

		if (this.mobile())
		{
			var trackV = $('.track');
			var trackH = $('.htrack');
			var thumbV = $('.scrollbar .track .thumb');
			var thumbH = $('.hcrollbar .htrack .hthumb');
			var viewport = $('.viewport');
			var overview = $('.overview');

			$jq('.viewport').bind('scroll', function(e)
			{
				var d = (trackV.height() - thumbV.height()) / (overview.height() - viewport.height());
				var dx = (trackH.width() - thumbH.width()) / (overview.width() - viewport.width());
				thumbV.css('top', Math.floor(viewport.get(0).scrollTop * d));
				thumbH.css('left', Math.floor(viewport.get(0).scrollLeft * dx));
				this.position.x = Math.floor((viewport.width() + viewport.get(0).scrollLeft) / this.item.width);
				this.update_links();
			}.bind(this));
		}

		this.update_size();

		/* Fix CSS tables for madafaq IE
		 */
		if ($jq.browser.msie && $jq.browser.version <= 7)
		{
			$jq(".match.has-events ul").wrap('<table cellspacing="0" cellpadding="0" style="padding:0 !Important; width:100%;" />');
			$jq(".match.has-events").each(function(i, el)
			{
				var rows = $jq(el).find(".playoff-box-result-inner li span.row");

				rows.each(function(ri, row)
				{
					$jq(row).find("span").each(function(index, td)
					{
						var tds = $jq(td).hasClass('info') ? "<td colspan='3' />" : "<td />";
						$jq(td).wrap(tds);
					});

					$jq(row).wrapInner("<tr class='" + ($jq(row).parent().attr('class')) + "' />");

					var table = $jq(el).find('tr');
					table.unwrap().unwrap().unwrap();
					$jq(el).find('table tr td:first-child').css('border-left', 'none');
				});
			});
		}

		// Set up box actions
		$jq(".match.has-events").unbind('.draw').bind('click.draw', {'dw': this}, this.match_cell_callback);

		// setup scroll links
		this.el.ll.unbind('click').bind('click', {'dw': this}, function(e)
		{
			e.data.dw.touch('left', e);
		});
		this.el.lr.unbind('click').bind('click', {'dw': this}, function(e)
		{
			e.data.dw.touch('right', e);
		});
		$jq('a.scroll-box').unbind('.draw').bind('click.draw', {'dw': this}, function(e)
		{
			e.data.dw.touch(null, e);
		});

		this.participantWayHighlight();
		this.ready = true;
	};

	this.match_cell_callback = function(e)
	{
		var dw = e.data.dw;
		var box = $jq(this);
		var id = box.attr('id');
		var matches = box.find(".matches");
		typeof dw.swap.a == 'undefined' && (dw.swap.a = 1);

		if (matches.length)
		{
			if (box.hasClass('unpacked'))
			{
				box.removeClass('unpacked');
				var bb = box.find('.matches');
				var bbp = bb.prev();

				bb.remove();
				bb.insertAfter(bbp);
				typeof dw.box_titles[id] != 'undefined' && box.attr("title", dw.box_titles[id]);

				if (box.hasClass('shrink'))
				{
					box.removeClass('shrink');
				}
				delete dw.boxes[id];
			}
			else
			{
				dw.swap.a++;
				box.css('z-index', dw.swap.a).addClass('unpacked');
				matches.css('z-index', dw.swap.a);
				box.parent().css('z-index', dw.swap.a);
				dw.boxes[id] = matches.outerHeight() + box.outerHeight() + box.position().top + box.parent().position().top + 5;
				dw.box_titles[id] = box.attr("title");
				box.removeAttr("title");

				if (dw.boxes[id] > (dw.dimensions.heightInternal))
				{
					box.addClass('shrink');
					if (dw.boxes[id] > (dw.scrollContent.height()))
					{
						if (dw.scrollEnv.height() < dw.boxes[id])
						{
							dw.scrollEnv.height(dw.boxes[id] + 2);
						}

						dw.scrollContent.height(dw.boxes[id]);
						dw.update_size(true);
						!dw.scroll.disabled && dw.el.env.tinyscrollbar_update('bottom');
					}
				}

				if (typeof detail_open == 'function')
				{
					box.find("li").click(function(e)
					{
						e.stopPropagation();
					});
					box.find("a.match-detail-link").click(function(e)
					{
						var classes = $jq(this).attr("class").split(" ");
						var mid;

						for (i in classes)
						{
							if (classes[i].match(/match\-[a-z]_[0-9]+_[a-zA-Z0-9]+/))
							{
								mid = classes[i].substr(6);
								break;
							}
						}

						if (typeof mid != 'undefined' && mid)
						{
							if (dw.detailVersion == 2)
							{
								var re = / glib-partnames-([^ ]+) /
								var partnames = re.exec(' ' + $jq(this).attr('class') + ' ');
								if (partnames && typeof partnames[1] != 'undefined')
								{
									partnames = partnames[1].split(';');
									detail_open(mid, null, partnames[0], typeof partnames[1] != 'undefined' ? partnames[1] : null, $('#season_url').text());
								}
							}
							else
							{
								detail_open(mid);
							}

							e.stopPropagation();
							e.preventDefault();
							return false;
						}
					});
				}
				else
				{
					box.find("a.match-detail-link").click(function(e)
					{
						e.stopPropagation();
					});
				}
			}
		}
	};
};

var StatsDrawView = new StatsDrawViewClass();

// tinyscrollbar for jQuery, fixed for IE
// used in draw

(function(a)
{
	function b(b, c)
	{
		function scrollbar_jump(a)
		{
			if (!(g.ratio >= 1))
			{
				var offset = i.obj.offset();
				var click = k ? a.pageX : a.pageY - (k ? offset['left'] : offset['top']);
				var size = parseInt(j.obj.css(k ? 'width' : 'height'));
				var pos_increment = size + o.now < click ? size : 0;

				o.now = (click - pos_increment);
				n = o.now * h.ratio;
				g.obj.css(l, -n);
				j.obj.css(l, o.now);
				window.dw.update_vertical_position(-n);
			}
			return false
		}

		function w(a)
		{
			if (!(g.ratio >= 1))
			{
				o.now = Math.min(i[c.axis] - j[c.axis], Math.max(0, o.start + ((k ? a.pageX : a.pageY) - p.start)));
				n = o.now * h.ratio;
				g.obj.css(l, -n);
				j.obj.css(l, o.now);
				window.dw.update_vertical_position(-n)
			}
			return false
		}

		function v(b)
		{
			a(document).unbind(".scrollbar");
			a(document).unbind("mousemove", w);
			a(document).unbind("mouseup", v);
			j.obj.unbind("mouseup", v);
			document.ontouchmove = j.obj[0].ontouchend = document.ontouchend = null;
			return false;
		}

		function u(b)
		{
			if (!(g.ratio >= 1))
			{
				var b = b || window.event;
				var d = b.wheelDelta ? b.wheelDelta / 120 : -b.detail / 3;
				n -= d * c.wheel;
				n = Math.min(g[c.axis] - f[c.axis], Math.max(0, n));
				j.obj.css(l, n / h.ratio);
				g.obj.css(l, -n);
				b = a.event.fix(b);
				window.dw.update_vertical_position(-n);
				b.preventDefault()
			}
		}

		function t(b)
		{
			p.start = k ? b.pageX : b.pageY;
			var c = parseInt(j.obj.css(l));
			o.start = c == "auto" ? 0 : c;
			a(document).bind("mousemove", w);
			a(document).bind('mouseup.scrollbar', v);
			document.ontouchmove = function(b)
			{
				a(document).unbind("mousemove");
				w(b.touches[0])
			};
			a(document).bind("mouseup", v);
			j.obj.bind("mouseup", v);
			j.obj[0].ontouchend = document.ontouchend = function(b)
			{
				a(document).unbind("mouseup");
				j.obj.unbind("mouseup");
				v(b.touches[0])
			};

			return false
		}

		this.s = function()
		{
			j.obj.bind("mousedown", t);
			j.obj[0].ontouchstart = function(a)
			{
				a.preventDefault();
				a.stopPropagation();
				j.obj.unbind("mousedown");
				t(a.touches[0]);
				return false
			};
			i.obj.bind("mouseup",scrollbar_jump);
			if (c.scroll)
			{
				if ("onmousewheel" in e[0]) {
					e[0].onmousewheel = u;
				} else {
					e[0].addEventListener('DOMMouseScroll', u, false);
				}
			}
		};

		function r()
		{
			j.obj.css(l, n / h.ratio);
			g.obj.css(l, -n);
			p["start"] = j.obj.offset()[l];
			var a = m.toLowerCase();
			h.obj.css(a, Math.round(i[c.axis]));
			i.obj.css(a, Math.round(i[c.axis]));
			j.obj.css(a, Math.round(j[c.axis]));
		}

		this.q = function ()
		{
			d.update();
			this.s();
			return d
		};

		var d = this;
		var e = b;
		var f = {obj: a(".viewport", b)};
		var g = {obj: a(".overview", b)};
		var h = {obj: a(".scrollbar", b)};
		var i = {obj: a(".track", h.obj)};
		var j = {obj: a(".thumb", h.obj)};
		var k = c.axis == "x", l = k ? "left" : "top", m = k ? "Width" : "Height";
		var n, o = {start: 0, now: 0}, p = {};
		this.update = function(a)
		{
			g[c.axis] = m == 'Height' ? $jq(g.obj[0]).outerHeight() : $jq(g.obj[0]).outerWidth();
			f[c.axis] = f.obj[0]["offset" + m];
			g.ratio = f[c.axis] / g[c.axis];
			h.obj.toggleClass("disable", g.ratio >= 1);
			i[c.axis] = c.size == "auto" ? f[c.axis] : c.size;
			j[c.axis] = Math.min(i[c.axis], Math.max(0, c.sizethumb == "auto" ? i[c.axis] * g.ratio : c.sizethumb));
			h.ratio = c.sizethumb == "auto" ? g[c.axis] / i[c.axis] : (g[c.axis] - f[c.axis]) / (i[c.axis] - j[c.axis]);
			n = a == "relative" && g.ratio <= 1 ? Math.min(g[c.axis] - f[c.axis], Math.max(0, n)) : 0;
			n = a == "bottom" && g.ratio <= 1 ? g[c.axis] - f[c.axis] : isNaN(parseInt(a)) ? n : parseInt(a);

			if (a == "overview-y")
			{
				n = -1 * parseInt($('.overview').css('top'));
			}

			r()
		};

		return this.q()
	}

	a.tiny = a.tiny || {};
	a.tiny.scrollbar = {options: {axis: "y", wheel: 40, scroll: true, size: "auto", sizethumb: "auto"}};
	a.fn.tinyscrollbar = function(c)
	{
		var c = a.extend({}, a.tiny.scrollbar.options, c);
		this.each(function()
		{
			a(this).data("tsb", new b(a(this), c))
		});
		return this
	};
	a.fn.tinyscrollbar_update = function(b)
	{
		return a(this).data("tsb").update(b)
	};
})($jq);
/** SportStats javascript proxy
 *
 * This script manages fetching and loading tables by asynchronous
 * requests. It also manages stats menu.
 *
 * Usage:
 *
 * stats_proxy.init({
 * 	"default_tab":'overall'
 * });
 */

var class_stats_proxy = function()
{
	var
		menu_class_prefix = 'stats-menu-',
		tabs = [],
		els = {},
		proxy = this,
		anchor_pos,
		anchor_sub_pos,
		anchor_subsub_pos,
		options,
		current_tab,
		initial_tab;
	this.ready = false;
	this.fired = false;
	this.loading = false;
	this.aborting = false;
	this.req;
	this.stagesMenuInitialized = false;
	var stagesContent = new Array();
	this.publicOptions = null;
	// Options that will be used as default
	var default_options = {
		"default_tab":'overall',
		"before_tab_ready":function(tab_info) { },
		"tab_ready":function(tab_info) { },
		"tab_visible":function(tab_info) { },
		"text_loading":"Loading ..",
		"use_links_hash":true,
		"init_ajax_stages_menu":false
	};

	this.restart = function(){
	    tabs = [];
	    els = {};
	    anchor_pos = undefined;
	    anchor_sub_pos = undefined;
	    anchor_subsub_pos = undefined;
	    current_tab = undefined;
	    initial_tab = undefined;

	    this.ready = false;
	    this.fired = false;
	    this.loading = false;
	    this.aborting = false;
	    this.req = undefined;
	};

	var setStageContent = function(id,content){
	    stagesContent[id] = content;
	};

	var getStageContent = function(id){
	    if(typeof stagesContent[id] != 'undefined'){
		return stagesContent[id];
	    }

	    return null;
	};

	var isStageContentAvailable = function(id){
	    if(typeof stagesContent[id] != 'undefined'){
		return true;
	    }

	    return false;
	};

	this.isStagesMenuInitialized = function(){
	    return this.stagesMenuInitialized;
	};

	this.getElsData = function()
	{
		return els;
	};

	/** Prepare and startup stats_proxy
	 * @param object opts
	 * @return bool False on failure
	 */
	this.init = function(opts)
	{
		if (!this.ready) {
			this.publicOptions = options = typeof opts == 'object' ? merge_objects(default_options, opts):default_options;
			this.ready = find_elements();
		}

		if (this.ready && !this.fired) {
			this.fired = true;
			get_tabs();

			if (typeof initial_tab != 'undefined') {
				(typeof options.before_tab_ready == 'function') && options.before_tab_ready(initial_tab);
				(typeof options.tab_ready == 'function') && options.tab_ready(initial_tab);
			}

			this.tab();
		}

		if(!this.isStagesMenuInitialized() && options.init_ajax_stages_menu){
		    this.initStagesMenu();
		}
		return this;
	};


	this.initStagesMenu = function(){

	    var menu = jQuery('.ifmenu.bubble.stages-menu li a').removeAttr('onclick').unbind('click').bind("click",this,function(e){
		e.preventDefault();
		jQuery(this).closest(".ifmenu.bubble.stages-menu").find('li').removeClass('selected');
		jQuery(this).closest('li').addClass('selected');

		var setStage = function(context,content){
		    jQuery('#glib-stats').children().remove();
		    if(jQuery(content).find(".ifmenu.bubble.stages-menu").size()){
			jQuery('#glib-stats').parent().find(".ifmenu.bubble.stages-menu").remove();
			jQuery('#glib-stats').before(jQuery(content).find(".ifmenu.bubble.stages-menu"));
			jQuery(content).find(".ifmenu.bubble.stages-menu").remove();
		    }

		    jQuery('#glib-stats').append(jQuery(content).find("#glib-stats-menu"));
		    context.restart();
		    context.init(context.options);
		    context.initStagesMenu();
		}

		if(isStageContentAvailable(jQuery(this).attr('href'))){
		    setStage(e.data,getStageContent(jQuery(this).attr('href')));
		}else{
		    loading();
		    jQuery.get(jQuery(this).attr('href'),function(context,stageId){
			return function(data){
			    setStageContent(stageId,data);

			    setStage(context,data);
			};
		    }(e.data,jQuery(this).attr('href')));
		}
	    });
	    this.stagesMenuInitialized = true;
	};

	/** Get list of all available tabs
	 * @return array
	 */
	var get_tabs = function()
	{
		if (tabs.length == 0) {
			var tab_containers = els.menu_container_main.getElementsByTagName('li');
			els.menu = {};
			register_tab_from_containers(tab_containers);
		}

		return tabs;
	};


	/** Recursive function to create data structure from HTML
	 * @param array  tab_containers List of menu <li> elements
	 * @param string parent_cname   Optional name of parent tab
	 * @return void
	 */
	var register_tab_from_containers = function(tab_containers, parent_cname, level)
	{
		var set_initial_tab;

		if (typeof parent_cname == 'undefined') {
			parent_cname = '';
		}

		if (typeof level == 'undefined') {
			level = 1;
		}

		for (var i=0; i<tab_containers.length; i++) {
			var classes = tab_containers[i].className.split(' ');

			for (var j=0; j<classes.length; j++) {

				if (classes[j].indexOf(menu_class_prefix) < 0) {
					continue;
				}

				var cname = classes[j].substr(menu_class_prefix.length).replace('-', '_');
				var el_link = tab_containers[i].getElementsByTagName('a')[0];
				var data = {
					"name":             cname,
					"has_submenu":      false,
					"menu_level":       level,
					"parent_name":      parent_cname,
					"grandparent_name": '',
					"container":        tab_containers[i],
					"link":             el_link.href,
					"box":              null,
					"menu":             {},
					"menu_keys":        []
				};

				var url_href = [cname];
				parent_cname && url_href.push(parent_cname);

				if (options.use_links_hash){
					el_link.href = '#'+url_href.reverse().join(';');
				}

				// if there's single tab on level 1, make it non-clickable
				if (level == 1 && tab_containers.length == 1) {
					var $el_link = $(el_link);
					$el_link.replaceWith('<strong>' + $el_link.html() + '</strong>');
				}

				var boxes = els.data_container.getElementsByTagName('div');

				for (var b=0; b<boxes.length; b++) {
					if (has_class(boxes[b], 'glib-stats-box-' + cname)) {
						data.box = boxes[b];
						set_initial_tab = cname;
						break;
					}
				}

				if (parent_cname) {
					els.menu[parent_cname].menu[cname] = data;
					els.menu[parent_cname].menu_keys.push(cname);
					if (typeof els.menu[parent_cname].menu_default == 'undefined' || has_class(tab_containers[i], 'selected')) {
						els.menu[parent_cname].menu_default = cname;
					}
				} else {
					els.menu[cname] = data;
					tabs.push(cname);
				}

				bind_tab(tab_containers[i], data);

				if (!parent_cname) {
					if (submenu = document.getElementById('glib-stats-submenu-' + cname)) {
						els.menu[cname].container_submenu = submenu;
						els.menu[cname].has_submenu = true;
						register_tab_from_containers(submenu.getElementsByTagName('li'), cname, level + 1);
					}
				}

				break;
			}
		}

		if (set_initial_tab) {
			initial_tab = grandparent_cname ? els.menu[grandparent_cname].menu[parent_cname].menu[set_initial_tab] :
							(parent_cname ? els.menu[parent_cname].menu[set_initial_tab] : els.menu[set_initial_tab]);
		}
	};


	/** Get all applicable data from URL hash
	 * @return array
	 */
	var get_url_data = function()
	{
		var url_data = document.location.hash.substr(1).split(';');
		var udata = [];
		var udata_processed = {};
		var index;

		// Avoid empty single field after split
		if (typeof url_data[0] != 'undefined' && url_data[0] == '') {
			url_data.splice(0, 1);
		}

		// Check for anchor data
		if (url_data.length) {
			var tabs = get_tabs();

			for (var iurl = 0; iurl < url_data.length; iurl++) {
				var data = url_data[iurl];
				for (var i=0; i<tabs.length; i++) {
					if (data == tabs[i] && !udata_processed[data]) {
						udata.push(tabs[i]);
						udata_processed[data] = true;
						typeof anchor_pos == 'undefined' && (anchor_pos = iurl);
					}

					if (els.menu[tabs[i]].has_submenu) {
						//for (var j=0; j<els.menu[tabs[i]].menu_keys.length; j++) {
						for (var submenu_item_name in els.menu[tabs[i]].menu) {
							if (data == submenu_item_name && !udata_processed[data]) {
								udata.push(submenu_item_name);
								udata_processed[data] = true;
							}

							if (els.menu[tabs[i]].menu[submenu_item_name].has_submenu) {
								for (var subsubmenu_item_name in els.menu[tabs[i]].menu[submenu_item_name].menu) {
									if (data == subsubmenu_item_name && !udata_processed[data]) {
										udata.push(subsubmenu_item_name);
										udata_processed[data] = true;
									}
								}
							}
						}
					}
				}
			}
		}

		typeof anchor_pos == 'undefined' && (anchor_pos = url_data.length);
		anchor_sub_pos = anchor_pos + 1;
		anchor_subsub_pos = anchor_sub_pos + 1;

		return array_unique(udata);
	};


	/** Switch tab to [name]
	 * @param string name
	 * @return bool
	 */
	this.tab = function(name)
	{
		var tabs = get_tabs();
		var url_data = get_url_data();
		if (typeof name == 'undefined') {
			if (url_data.length) {
				this.tab(url_data[0]);
			} else {
				this.tab(typeof els[options.default_tab] == 'undefined' ? tabs[0]:options.default_tab);
			}
		} else {

			if (typeof els.menu[name] != 'undefined' && (typeof current_tab == 'undefined' || (name != current_tab.name && name != current_tab.parent_name) || (els.menu[name].box == null && !els.menu[name].has_submenu)))
			{
				var tab = els.menu[name];

				if (typeof tab.box == 'undefined' || tab.box === null && !tab.has_submenu )
				{
					request(tab, null, this.hash_key)
				}
				else
				{
					use_tab(tab);
				}
			}
		}

		return this.ready;
	};

	/** Switch to subtab of [name]
	 * @param string parent_name
	 * @param string name
	 * @return bool;
	 */
	this.subtab = function(parent_name, name)
	{
		var url_data = get_url_data();
		var anchor_data = document.location.hash.substr(1).split(';');
		if (typeof anchor_data[0] != 'undefined' && anchor_data[0] == '') {
			anchor_data.splice(0, 1);
		}

		if (typeof name == 'undefined') {

			if (typeof anchor_data[anchor_sub_pos] != 'undefined' && typeof els.menu[parent_name].menu[anchor_data[anchor_sub_pos]] != 'undefined') {
				this.subtab(parent_name, anchor_data[anchor_sub_pos]);
			} else {
				if (typeof els.menu[parent_name].menu_default != 'undefined') {
					this.subtab(parent_name, els.menu[parent_name].menu_default);
				}
			}
		} else {

			if (typeof els.menu[parent_name].menu[name] != 'undefined') {
				var tab = els.menu[parent_name].menu[name];
				if ((typeof tab.box == 'undefined' || tab.box === null) && !tab.has_submenu)
				{
					request(tab, null, this.hash_key);
				}
				else
				{
					use_tab(tab);
				}
			}
			else
			{
				this.tab(parent_name);
			}
		}

		return this.ready;
	};


	/** Switch to subsubtab of [name]
	 * @param string grandparent_name
	 * @param string parent_name
	 * @param string name
	 * @return bool;
	 */
	this.subsubtab = function(grandparent_name, parent_name, name)
	{
		var url_data = get_url_data();
		var anchor_data = document.location.hash.substr(1).split(';');
		if (typeof anchor_data[0] != 'undefined' && anchor_data[0] == '') {
			anchor_data.splice(0, 1);
		}

		if (typeof name == 'undefined') {

			if (typeof anchor_data[anchor_subsub_pos] != 'undefined' && typeof els.menu[grandparent_name].menu[parent_name].menu[anchor_data[anchor_subsub_pos]] != 'undefined') {
				this.subsubtab(grandparent_name, parent_name, anchor_data[anchor_subsub_pos]);
			} else {
				if (typeof els.menu[grandparent_name].menu[parent_name].menu_default != 'undefined') {
					this.subsubtab(grandparent_name, parent_name, els.menu[grandparent_name].menu[parent_name].menu_default);
				}
			}
		} else {
			if (typeof els.menu[grandparent_name].menu[parent_name].menu[name] != 'undefined')
			{
				use_tab(els.menu[grandparent_name].menu[parent_name].menu[name]);
			}
			else
			{
				request(els.menu[grandparent_name].menu[parent_name], null, this.hash_key);
			}

		}

		return this.ready;
	};


	var use_tab = function(item)
	{
		hide_tab(current_tab, item);
		var invalidateLive = false;
		if (typeof current_tab != 'undefined' && current_tab.name == 'live' && item.name != 'live')
		{
			invalidateLive = true;
		}

		show_tab(item);
		if (typeof statsLiveChecker !== "undefined")
		{
			if (item.name == "live" && item.menu_level == 1)
			{
				statsLiveChecker.startChecking();

				var live_last_updated = $('#live_last_updated').text();
				if (live_last_updated)
				{
					$('#live-table-last-update span').text(timestamp2date(cjs.fullDateTimeFormat, live_last_updated, get_gmt_offset()));
				}

				$('#table-last-update').hide();
				$('#live-table-last-update').show();
			}
			else
			{
				if (typeof timestamp2date !== 'undefined' && typeof get_gmt_offset !== 'undefined')
				{
					var last_updated_prefix = 'last_updated_';
					$('[id^="' + last_updated_prefix + '"]').each(function(index)
					{
						var id = $(this).attr('id').substring(last_updated_prefix.length);
						if ($('[id="' + id + '"]').is(":visible"))
						{
							var lastUpdated = $('[id="' + last_updated_prefix + id + '"]').text();
							if (lastUpdated)
							{
								lastUpdated = timestamp2date(cjs.fullDateTimeFormat, lastUpdated, get_gmt_offset());
								$('#table-last-update span').text(lastUpdated);
								$('#live-table-last-update span').text(lastUpdated);
							}
						}
					});
				}

				statsLiveChecker.stopChecking();

				$('#live-table-last-update').hide();
				if ($('#table-last-update span').text())
				{
					$('#table-last-update').show();
				}
			}
		}
		if(item.box != null){
		   StatsTableWidthChecker_CheckItemWidth(item);
		}

		if (invalidateLive)
		{
			stats_proxy.dataInvalidatePart('live');
		}
	};


	/** Deselect tab and its' box
	 * @param string name
	 * @return this
	 */
	var hide_tab = function(item, new_tab)
	{
		if (typeof item != 'undefined') {
			remove_class(item.container, 'selected');

			if (typeof item.container_submenu != 'undefined') {
				remove_class(item.container_submenu, 'selected');
			}

			if (typeof item.box != 'undefined' && item.box !== null) {
				remove_class(item.box, 'selected');
			}

			if (item.grandparent_name)
				return hide_tab(els.menu[item.grandparent_name].menu[item.parent_name], new_tab);

			if (item.parent_name)
				return hide_tab(els.menu[item.parent_name], new_tab);
		}
		return this;
	};


	/** Select tab and its' box
	 * @param string name
	 * @return this
	 */
	var show_tab = function(item)
	{
		if (typeof item != 'undefined') {
			current_tab = item;
			add_class(item.container, 'selected');

			item.parent_name ? add_class(els.menu_spacer, 'hidden') : remove_class(els.menu_spacer, 'hidden');

			switch(item.menu_level)
			{
				case 1:
					if (item.has_submenu) {
						add_class(item.container_submenu, 'selected');
						stats_proxy.subtab(item.name);
					} else {
						save_anchor(item);
						add_class(item.box, 'selected');
					}
					break;
				case 2:
					if (item.has_submenu) {
					    	add_class(item.container_submenu, 'selected');
						add_class(item.box, 'selected');
						stats_proxy.subsubtab(item.parent_name, item.name);
					} else {
						save_anchor(item);
						add_class(item.box, 'selected');
					}

					if(els.menu[item.parent_name].box)
					{
					    add_class(els.menu[item.parent_name].box, 'selected');
					}

					add_class(els.menu[item.parent_name], 'selected');
					add_class(els.menu[item.parent_name].container, 'selected');
					add_class(els.menu[item.parent_name].container_submenu, 'selected');
					break;
				default:
				case 3:
					save_anchor(item);
					add_class(item.box, 'selected');

					add_class(els.menu[item.grandparent_name].container, 'selected');
					add_class(els.menu[item.grandparent_name].container_submenu, 'selected');
					add_class(els.menu[item.grandparent_name].menu[item.parent_name].box, 'selected');
					add_class(els.menu[item.grandparent_name].menu[item.parent_name].container, 'selected');
					add_class(els.menu[item.grandparent_name].menu[item.parent_name].container_submenu, 'selected');
					break;
			}
		}
		return this;
	};


	/** Find all essential elements to keep reference in memory
	 * @return void
	 */
	var find_elements = function()
	{
		els.container = document.getElementById('glib-stats');

		if (els.container) {
			els.menu_container = document.getElementById('glib-stats-menu');
			els.data_container = document.getElementById('glib-stats-data');

			if (els.data_container === null) {
				els.data_container = document.createElement('div');
				els.data_container.setAttribute('id', 'glib-stats-data');
				els.data_container.setAttribute('class', 'glib-stats-data');
				els.container.appendChild(els.data_container);
			}

			els.loader = document.createElement('div');
			els.loader.setAttribute('class', 'preload');
			els.loader_helper = document.createElement('span');
			els.loader_helper.innerHTML = options.text_loading;

			if (els.menu_container) {
				els.menu_container_main = els.menu_container.getElementsByTagName('ul')[0];
				menu_els = document.getElementById('glib-stats-menu').childNodes;
				for (var i=0; i<menu_els.length; i++) {
					var item = menu_els.item(i);
					if (typeof item.className != 'undefined' && item.className.indexOf('color-spacer') >= 0) {
						els.menu_spacer = item;
						break;
					}
				}
			}
		}

		return els.container && els.menu_container && els.menu_container_main && els.data_container && els.loader;
	};


	/** Bind menu tab events
	 * @return void
	 */
	var bind_tab = function(li, item)
	{
		var els = li.getElementsByTagName('a');

		for (var i=0; i<els.length; i++) {
			if (item.grandparent_name) {
				els[i].onclick = function(e) {
					if (typeof e != 'undefined') {
						e.preventDefault();
						e.stopPropagation();
						e.returnValue = false;
					}

					stats_proxy.subsubtab(item.grandparent_name, item.parent_name, item.name);
					return false;
				};
			} else if (item.parent_name) {
				els[i].onclick = function(e) {
					if (typeof e != 'undefined') {
						e.preventDefault();
						e.stopPropagation();
						e.returnValue = false;
					}

					var currentTopName = current_tab.grandparent_name ? current_tab.parent_name : current_tab.name;
					if (item.name == currentTopName)
						return false;

					// ----- Způsobí, že se po překliku na hlavní záložku vráti uživatel na defaultní subtab content (tj. overall) -----
					var url_data = get_url_data();
					var anchor_data = document.location.hash.substr(1).split(';');
					if (typeof anchor_data[0] != 'undefined' && anchor_data[0] == '') {
						anchor_data.splice(0, 1);
					}
					var hash_data = [];
					for (var i in anchor_data) {
						if (typeof url_data[1] != 'undefined' && anchor_data[i] == url_data[1])
							break;
						hash_data.push(anchor_data[i]);
					}

					if(options.use_links_hash){
					    document.location.hash = hash_data.join(';');
					}

					hash_data = null;
					// -----

					stats_proxy.subtab(item.parent_name, item.name);
					return false;
				};
			} else {
				els[i].onclick = function(e) {
					if (typeof e != 'undefined') {
						e.preventDefault();
						e.stopPropagation();
						e.returnValue = false;
					}

					var currentTopName = current_tab.grandparent_name ? current_tab.grandparent_name : ( current_tab.parent_name ? current_tab.parent_name : current_tab.name);
					if (item.name == currentTopName)
						return false;

					// ----- Způsobí, že se po překliku na hlavní záložku vráti uživatel na defaultní subtab content (tj. overall) -----
					var url_data = get_url_data();
					var anchor_data = document.location.hash.substr(1).split(';');
					if (typeof anchor_data[0] != 'undefined' && anchor_data[0] == '') {
						anchor_data.splice(0, 1);
					}
					var hash_data = [];
					for (var i in anchor_data) {
						if (typeof url_data[0] != 'undefined' && anchor_data[i] == url_data[0])
							break;
						hash_data.push(anchor_data[i]);
					}

					if(options.use_links_hash){
					    document.location.hash = hash_data.join(';');
					}

					hash_data = null;
					// -----

					stats_proxy.tab(item.name);

					return false;
				};
			}
		}
	};


	/** Get content box for a tab
	 * @param string name
	 * @return htmlDivElement
	 */
	var get_box = function(name, parent)
	{
		var parent = typeof parent == 'undefined' ? '':parent;
		var box = parent ? els.menu[parent].menu[name].box:els.menu[name].box;

		if (box == 'undefined' || box === null) {
			var box = document.createElement('div');
			els.data_container.appendChild(box);
			box.setAttribute('class', 'box glib-stats-box-' + name);
			box.className = 'box glib-stats-box-' + name;

			parent ?
				(els.menu[parent].menu[name].box = box):
				(els.menu[name].box = box);
		}

		return box;
	};


	/** Save position to hash after url
	 * @param array data
	 * @return void
	 */
	var save_anchor = function(tab)
	{
		var url_data = document.location.hash.substr(1).split(';');
		var uname = [tab.name];
		if (tab.parent_name)
			uname.push(tab.parent_name);
		if (tab.grandparent_name)
			uname.push(tab.grandparent_name);

		if (uname.length < url_data.length) {
			if (uname[uname.length-1] != url_data[0])
				url_data.splice(url_data.length-1,1);
		}

		for (var i = uname.length-1; i>=0; i--) {
			url_data[anchor_pos+uname.length-i-1] = uname[i];
		}

		detail_hashchangeIgnoreNext = true;
		if(options.use_links_hash){
		    document.location.hash = "#" + url_data.join(";");
		}
		setTimeout(function(f) {
			return function() { f(); };
		}(options.tab_visible), 50);
	};


	/** Try to remove class name
	 * @param htmlelement obj
	 * @param string className
	 * @return string
	 */
	var remove_class = function(obj, className)
	{
		if(obj == null)
		{
			return false;
		}

		reg = new RegExp("\\s*"+className+"\\s*", "g");
		if (typeof(obj.className) != "undefined")
			return obj.className = obj.className.replace(reg, ' ');
		else
			return obj.container_submenu.className = obj.container_submenu.className.replace(reg, ' ');
	};


	/** Try to add class name
	 * @param HTMLElement obj
	 * @param string className
	 * @return string
	 */
	var add_class = function(obj, className)
	{
		if(obj == null)
		{
			return false;
		}

		return obj.className = remove_class(obj, className) + ' ' + className;
	};


	/** Does element have a class
	 * @param HTMLElement obj
	 * @param string className
	 * @return bool
	 */
	var has_class = function(obj, className)
	{
		return obj.className.indexOf(className) >= 0;
	};


	/** Show preloader
	 * @return void
	 */
	var loading = function()
	{
		els.data_container.appendChild(els.loader);
		els.loader.appendChild(els.loader_helper);
	};


	/** Hide preloader
	 * @return void
	 */
	var prepared = function()
	{
		els.data_container.removeChild(els.loader);
	};


	this.set_hash = function(hash)
	{
		this.hash_key = hash;
	};

	this.dataInvalidate = function()
	{
		if (!this.ready)
		{
			return;
		}
		for(var menuItemName in els.menu)
			removeBoxes(els.menu[menuItemName]);
		els.data_container.innerHTML = '';
		if (current_tab.grandparent_name)
		{
			stats_proxy.subsubtab(current_tab.grandparent_name, current_tab.parent_name, current_tab.name);
		}
		else if (current_tab.parent_name)
		{
			stats_proxy.subtab(current_tab.parent_name, current_tab.name);
		}
		else
		{
			stats_proxy.tab(current_tab.name);
		}
	};

	this.dataInvalidatePart = function (partName)
	{
		if (typeof els.menu[partName] == "undefined")
		{
			return false;
		}

		removeBoxes(els.menu[partName]);

		if (typeof current_tab != "undefined" && current_tab.name == partName)
		{
			if (current_tab.grandparent_name)
			{
				stats_proxy.subsubtab(current_tab.grandparent_name, current_tab.parent_name, current_tab.name);
			}
			else if (current_tab.parent_name)
			{
				stats_proxy.subtab(current_tab.parent_name, current_tab.name);
			}
			else
			{
				stats_proxy.tab(current_tab.name);
			}
		}
		return true;
	}

	var removeBoxes = function(item)
	{
		if (typeof item.link === 'undefined')
		{
			return false;
		}
		if (item.menu_level > 1 && item.has_submenu == true && item.box !== null)
		{
			item.box = null;
			item.has_submenu = false;
			item.menu = {};
			item.menu_keys = [];
			delete(item.menu_default);
		}

		if(item.box && !item.has_submenu)
		{
			$(item.box).remove();
			item.box = null;
		}
		if(item.has_submenu)
		{
			for(var menuItemName in item.menu)
			{
				removeBoxes(item.menu[menuItemName]);

				if (removeBoxes(item.menu[menuItemName]) === false)
				{
					item.box = null;
					item.has_submenu = false;
					item.menu = {};
					item.menu_keys = [];
					delete(item.menu_default);
					break;
				}
			}
		}

		return true;
	};

	var get_request_url = function(item, hash_key)
	{
		if (item.link === null) {
			var name = [];
			var el_link = item.container.getElementsByTagName('a')[0];
			item.link = el_link.href;
		}

		if (hash_key && hash_key != '' && !/\?hash=/.test(item.link)) {
			item.link += '?hash=' + hash_key;
		}
		return item.link;
	};

	/** Send ajax request to get html data from server
	 * @param string name
	 * @param object extra_data
	 * @param string hash_key
	 * @return ajax
	 */
	var request = function(item, extra_data, hash_key)
	{
		loading();

		(typeof extra_data == 'undefined' || extra_data === null) && (extra_data = {});
		typeof this.req != 'undefined' && this.req !== null && this.req.abort();
		extra_data.tab = item;

		if (!hash_key) {
			hash_key = '';
		}

		var url = get_request_url(item, hash_key);

		return proxy.fetch_data(url, extra_data, function(status, headers, text, extra_data) {
			var box = get_box(extra_data.tab.name, extra_data.tab.parent_name);
			box.innerHTML = text;
			var hashKeyElement = document.getElementById('glib-hash-' + item.name);
			if (hashKeyElement)
			{
				item.hashKey = hashKeyElement.innerHTML;
			}
			else
			{
				item.hashKey = null;
			}
			var submenu_data_element = extra_data.tab.parent_name ? document.getElementById("submenu-item-"+extra_data.tab.parent_name+"-"+extra_data.tab.name) : document.getElementById("submenu-item-"+extra_data.tab.name);

			if (submenu_data_element && submenu_data_element.innerHTML) {
				var submenu_data = JSON.parse(submenu_data_element.innerHTML);
				for(var key in submenu_data) {
					item[key] = submenu_data[key];
				}

				item.container_submenu = extra_data.tab.parent_name ? document.getElementById("glib-stats-submenu-"+extra_data.tab.parent_name+"-"+extra_data.tab.name) : document.getElementById("glib-stats-submenu-"+extra_data.tab.name);

				for (var menu_key in submenu_data.menu) {
					submenu_data.menu[menu_key].box = document.getElementById(submenu_data.menu[menu_key].box_id);
					add_class(submenu_data.menu[menu_key].box, 'box');
					delete(submenu_data.menu[menu_key].box_id);
					submenu_data.menu[menu_key].container = document.getElementById(submenu_data.menu[menu_key].container_id);
					delete(submenu_data.menu[menu_key].container_id);
					bind_tab(submenu_data.menu[menu_key].container, submenu_data.menu[menu_key]);
				}
			}

			if (typeof options.before_tab_ready == 'function') {
				options.before_tab_ready(extra_data.tab);
			}

			use_tab(extra_data.tab);
			prepared();
			// Zlobilo kvuli tomu subsub menu
			//save_anchor(extra_data.tab);

			if (typeof options.tab_ready == 'function') {
				options.tab_ready(extra_data.tab);
			}

			highlightFormParticipants();

			if(StatsLiveChecker.glibStatsHandlerClone != null)
			{
				jQuery(StatsLiveChecker.glibStatsHandlerClone.originTable).css('visibility','visible');
				jQuery(StatsLiveChecker.glibStatsHandlerClone.cloneTable).remove();
				StatsLiveChecker.glibStatsHandlerClone.cloneTable = null;
			}

		});
	};

	var highlightFormParticipants = function()
	{
		var hf = function(){
			var participants = $(this).attr('class').match(/\bglib\-participants\-([^\-]*)\-([^\s]*)\b/);

			if(participants == null)
			{
				return;
			}

			typeof participants[0] != 'undefined' && delete participants[0];

			if(typeof participants[1] == 'undefined' || typeof participants[1] == 'undefined')
			{
				return;
			}

			jQuery("#glib-stats-data tr.glib-participant-"+participants[1]).toggleClass('highlight_hover');
			jQuery("#glib-stats-data tr.glib-participant-"+participants[2]).toggleClass('highlight_hover');

		};

		jQuery("#glib-stats-data").find('.form div a, .last_5 div a, a.glib-live-score').hover(hf,hf);
	};


	/** Konstruktor StatsTableWidthChecker
	 *
	 * @param object settings
	 */
	StatsTableWidthChecker = function(settings){
	    if(this.isWorking()){
		return;
	    }

	    this.tableElement = null;
	    this.maxSize = null;
	    this.minSize = null;
	    this._isCSSTextOverflowAvaible = null;
	    this._isCSSTextOverflowAvaible = this.isCSSTextOverflowAvaible();
	    this.init(settings);
	};

	StatsTableWidthChecker.prototype.isWorking = function(){


	    return typeof $(this.tableElement).data('truncate-working') == 'undefined' ? false : $(this.tableElement).data('truncate-working');
	};

	StatsTableWidthChecker.prototype.setIsWorking = function(status){

	    $(this.tableElement).data('truncate-working',status);
	};

	StatsTableWidthChecker.isWorking = function(tableElement){

	    return typeof $(tableElement).data('truncate-working') == 'undefined' ? false : $(tableElement).data('truncate-working');
	};

	StatsTableWidthChecker.prototype.isCSSTextOverflowAvaible = function(){
	    if(this._isCSSTextOverflowAvaible!== null){
		return this._isCSSTextOverflowAvaible;
	    }
	    var d = document.createElement("span");
	    try{
		if(typeof d.style.textOverflow == 'undefined'){

		    return false;
		}else{
		    d.style.textOverflow = 'ellipsis';

		    if(d.style.textOverflow == 'ellipsis'){
			return true;
		    }else{
			return false;
		    }
		}
	    }catch(e){
		    return false;
	    }

	};

	/** Inicializace dle nastaveni
	 *
	 * @param object settings
	 */
	StatsTableWidthChecker.prototype.init = function(settings){
	    this.tableElement = settings.tableElement;
	    this.minSize = settings.minSize;
	    this.maxSize = settings.maxSize;
	};

	/**
	 * Vraci rozsah presezeni dane maxWidth
	 */
	StatsTableWidthChecker.prototype.getOverflowSize = function(){
	    if(this.maxSize == 0){
		return 0;
	    }

	    var diff = $(this.tableElement).outerWidth() - this.maxSize;

	    return diff >= 0 ? diff : 0;
	};

	/**
	 * Zkrati text v kazde bunce se jmenem participanta
	 *
	 */
	StatsTableWidthChecker.prototype.truncateParticipant = function(){
	    if(this.isWorking()){
		return;
	    }

	    this.setIsWorking(true);

	    var overflow = this.getOverflowSize();

	    var participantsColumns = jQuery(this.tableElement).find("tr td.participant_name");
	    var iconSize = 0;
	    var teamLogo = jQuery(participantsColumns).find(".team-logo");
	    if(jQuery(teamLogo).size()){
		iconSize = jQuery(teamLogo).outerWidth(true);
	    }

	    var columnSize = jQuery(participantsColumns).eq(0).width();
	    var maxTextSize = columnSize-iconSize-overflow;

	    if(maxTextSize < this.minSize){
		maxTextSize = this.minSize;
	    }

	    var context = this;
	    jQuery(participantsColumns).find(".team_name_span").each(function(){


		if(context.isCSSTextOverflowAvaible()){
		    jQuery(this).css("display","inline-block");
		    if(maxTextSize <= 0){
			jQuery(this).css("width","auto");
		    }else{
			jQuery(this).css("width",maxTextSize+"px");
		    }
		    return;
		}

		context.truncate(this,maxTextSize);


	    });
	    this.setIsWorking(false);

	};
	/**
	 * Zkrati text v kazde elementu a prida '...'
	 * @param object element - element s textem
	 * @param object width - max sirka textu
	 */
	StatsTableWidthChecker.prototype.truncate =function (element,width){

	    var text = "";

	    if($(element).data('origin-text') == null){
		text = jQuery(element).text();
		$(element).data('origin-text',text);
	    }else{
		text = $(element).data('origin-text');
		jQuery(element).text(text);
	    }

	    if($(element).outerWidth(true)<=width){
	        return;
	    }

	    text = text+"...";

	    jQuery(element).text(text);

	    while($(element).outerWidth(true)>width){
		if(text == "..."){
		    return;
		}

		jQuery(element).text(text.substr(0,text.length-4)+"...");
		text = $(element).text();

	    }
	};

	StatsTableWidthChecker_CheckItemWidth = function(item){

	    var statsTable = jQuery(item.box).find("table:nth-child(1)");
	    var statsTableContainer = jQuery(item.box).find(".stats-table-container:nth-child(1)");


	    if(jQuery(statsTable).size() == 1 && jQuery(statsTableContainer).size()){
		TableCheckerCallback = function(){
		    if(StatsTableWidthChecker.isWorking(statsTable)){
			return;
		    }

		    var sizeChecker = new StatsTableWidthChecker({
			tableElement:jQuery(statsTable),
			maxSize:jQuery(statsTableContainer).innerWidth(true),
			minSize:65
		    });
		    sizeChecker.truncateParticipant();
		}

		TableCheckerCallback();
		var timeout = null;
		$(window).resize(function() {
		    var windowWidth = $(window).innerWidth();

		    clearTimeout(timeout);
		    timeout = setTimeout(function(width) {
			return function(){
				    if(width ==  $(window).innerWidth()){
					TableCheckerCallback();
				    }
		    }}(windowWidth),200);


		});
	    }
	};

	this.fetch_data = function(url, extra_data, callback)
	{
		this.req = new class_ajax(url, callback);
		return this.req.fire(extra_data);
	};


	/** Function merges two object properties as PHP array_merge
	 * @param object set1
	 * @param object set2
	 * @return object
	 */
	var merge_objects = function(set1, set2)
	{
		for (var key in set2) {
			if (set2.hasOwnProperty(key))
				set1[key] = set2[key]
		}

		return set1;
	};


	/** Make all elements in array unique
	 * @return array
	 */
	var array_unique = function(ar)
	{
		if (ar.length && typeof ar!=='string') {
			var sorter = {};
			var out = [];
			for(var i=0,j=ar.length;i<j;i++) {
				if (!sorter[ar[i]+typeof ar[i]]) {
					out.push(ar[i]);
					sorter[ar[i]+typeof ar[i]]=true;
				}
			}
		}
		return out || ar;
	};


	/** Ajax helper
	 * @param string url
	 * @param function callback
	 */
	var class_ajax = function(url, callbackFunction)
	{
		var req = this;
		var urlCall = url;
		this.ajax_async = true;
		this.container = null;
		this.return_text_after_update = false;
		this.callback = callbackFunction || function () {};


		this.abort = function()
		{
			if (proxy.loading) {
				proxy.loading = false;
				proxy.aborting = true;
				req.AJAX.abort();
				req.AJAX = null;
			}
		};


		this.fire = function(extra_data) {
			if (proxy.loading)
				return this;

			req.AJAX = window.XMLHttpRequest ? new XMLHttpRequest():new ActiveXObject("Microsoft.XMLHTTP");

			if (typeof req.AJAX != 'undefined') {
				req.AJAX.onreadystatechange = function() {
					if (req.AJAX.readyState == 4) {
						proxy.loading = false;
						if (!proxy.aborting && (req.AJAX.status == 200 || req.AJAX.status == 204 || req.AJAX.status == 0)) {
							req.callback(req.AJAX.status, req.AJAX.getAllResponseHeaders(), req.AJAX.responseText, extra_data);
							proxy.ajax = null;
						}
					}
				};

				req.AJAX.open("GET", urlCall, req.ajax_async);
				req.AJAX.setRequestHeader('Accept-Language', '*');
				req.AJAX.setRequestHeader('Accept', '*/*');
				req.AJAX.send(null);
			}

			return this;
		};
	};
};

//!!! funguje len pre 1. uroven menu, momentalne to pre live tabulky postacuje
var StatsLiveChecker = function(statsProxyObj, itemName)
{
	this.statsProxyObj = statsProxyObj;
	this.itemName = itemName;
	this.checkingState = 0;
};

StatsLiveChecker.glibStatsHandlerClone = null;

StatsLiveChecker.prototype.startChecking = function(timeout)
{
	if (this.checkingState)
	{
		return;
	}
	this.checkingState = 1;

	if (timeout == null)
	{
		timeout = 5000;
	}

	var item = this.statsProxyObj.getElsData().menu[this.itemName];
	var that = this;
	if (typeof item === "undefined")
	{
		return;
	}

	if (typeof item.hashKey === "undefined" || item.hashKey === null)
	{
		this.timerId = setTimeout(function(){
			that.checkingState = 0;
			that.startChecking(timeout);
		}, timeout);
		return;
	}

	var url = item.link.replace('_' + this.itemName + '_', '_' + this.itemName + 'hash_');
	url = url.replace(/\?hash=.*/g, '');
	this.timerId = setTimeout(function(){
		that.statsProxyObj.fetch_data(url, null, function(status, headers, text, extra_data){

			if (text != '' && status == 200)
			{
				if (item.hashKey != text)
				{
					var originTable = jQuery('#glib-stats-data');
					var cloneTable = jQuery(originTable).clone();
					jQuery(originTable).css('visibility','hidden');
					jQuery(cloneTable).addClass('table-clone');
					jQuery(cloneTable).insertAfter(originTable);
					var loader = jQuery('<div class="preload"><span>' + stats_proxy.publicOptions.text_loading + '</span></div>');
					jQuery(cloneTable).append(loader);

					StatsLiveChecker.glibStatsHandlerClone = {originTable: originTable, cloneTable: cloneTable};

					that.statsProxyObj.dataInvalidatePart(item.name);
				}
			}
			if (that.checkingState == 1)
			{
				that.checkingState = 0;
				that.startChecking(timeout);
			}
		});
	}, timeout);
};

StatsLiveChecker.prototype.stopChecking = function()
{
	if (!this.checkingState)
	{
		return;
	}
	if (typeof this.timerId !== 'undefined')
	{
		clearTimeout(this.timerId);
	}
	this.checkingState = 0;
};


var stats_proxy = new class_stats_proxy();
var statsLiveChecker = new StatsLiveChecker(stats_proxy, 'live');
var TabFilter = (function($)
{
	var TabFilter = function(tab)
	{
		this.$uls = null;
		this.$tbody = null;
		this.$allRows = null;
		this.noResultsText = '';
		this.$noResultsTFoot = null;

		var $filterRow = $('.glib-stats-filter', tab.box);
		this.$uls = $filterRow.find('ul[data-name]');
		this.$tbody = $('table.stats-table tbody', tab.box);
		this.noResultsText = $filterRow.attr('data-no-results-text');
		this.rowsVisibled = this.$tbody.find('tr:not(:hidden)').length

		if (this.$uls.length > 0)
		{
			this.$allRows = this.$tbody.children();

			// record number of visible rows
			this.$tbody.data('visibleRows', this.$allRows.not('.hidden').length);
			var obj = this;

			this.$uls.each(function() {
				if (obj.initList($(this)) === false)
				{
					$(this).parent().remove();
				}
			});
		}

		// all lists could have been removed
		if ($filterRow.find('ul[data-name]').length)
		{
			this.adjustFilterRowCss($filterRow);

			// hide opened list on any click
			$(document).on('click', {obj: this}, this.tableClicked);
		}
	}

	TabFilter.prototype.tableClicked = function(e)
	{
		var obj = e.data.obj;

		obj.$uls.removeClass('open');
	}

	TabFilter.prototype.initList = function($ul)
	{

		var option = null;
		var values = [];
		var nationalities = [];
		var valuesOrderMap = {};
		var optionsFragment = null;
		var column = $ul.attr('data-name');
		var isVirtual = !!$ul.attr('data-is-virtual');
		var valueToRowsMap = $ul.data('valueToRowsMap', {}).data('valueToRowsMap');

		var obj = this;
		if (isVirtual)
		{
			this.$allRows.each(function() {
				var $row = $(this);
				var value = $row.attr('data-virtual-' + column);
				var order = $row.attr('data-virtual-' + column + '-choice-order');
				var nationality_id = $row.attr('data-nationality-id');

				if (!value)
				{
					// continue, row won't be chosen by any value
					return true;
				}
				value = obj.processValue(column, value);
				if (!valueToRowsMap[value])
				{
					valueToRowsMap[value] = $();
					if (order)
					{
						valuesOrderMap[value] = order;
					}
				}
				valueToRowsMap[value].push(this);

				nationalities[value] = nationality_id;
			});
		}
		else
		{
			var $columnCell = this.$tbody.find('tr:eq(0) td.' + column);
			var columnPosition = null;
			// column is not present
			if (!$columnCell.length)
			{
				return false;
			}
			columnPosition = $columnCell[0].cellIndex + 1;

			this.$tbody.find('tr td:nth-child(' + columnPosition + ')').each(function() {
				var value = $(this).text();
				if (!value)
				{
					// continue, row won't be chosen by any value
					return true;
				}
				value = obj.processValue(column, value);
				if (!valueToRowsMap[value])
				{
					valueToRowsMap[value] = $();
				}
				valueToRowsMap[value].push(this.parentNode);
			});
		}

		// there are no values to filter by
		if ($.isEmptyObject(valueToRowsMap))
		{
			return false;
		}

		// collect values
		for (var value in valueToRowsMap)
		{
			values.push(value);
		}

		if ($.isEmptyObject(valuesOrderMap))
		{
			values.sort(this.alphaSort);
		}
		else
		{
			values.sort(function(a, b) {
				return valuesOrderMap[a] < valuesOrderMap[b] ? -1 : 1;
			});
		}

		// set currently selected item
		$ul.data('selectedItem', $ul.children());

		// append values as li's to ul
		// (using document fragment is faster than appending in $.each)
		optionsFragment = document.createDocumentFragment();
		for (var i in values)
		{
			option = document.createElement('li');

			// flags
			if (column == 'nationality')
			{
				var flag = document.createElement('span');
				flag.setAttribute('class', 'flag fl_' + nationalities[values[i]]);
				option.appendChild(flag);
			}

			option.appendChild(document.createTextNode(values[i]));
			optionsFragment.appendChild(option);
		}

		$ul[0].appendChild(optionsFragment);

		// bind event to do actual filtering
		$ul.on('click', {obj: this}, this.listClicked);
	}

	TabFilter.prototype.listClicked = function(e)
	{
		var obj = e.data.obj;

		var $item = null;
		var $list = $(this);

		// option was selected
		if ($list.hasClass('open'))
		{
			$item = $(e.target);
			$list.data('selectedItem').removeClass('selected');
			$item.addClass('selected');
			$list.data('selectedItem', $item);
			$list.removeClass('open');
			obj.filterChanged();
		}
		// show list
		else
		{
			obj.$uls.removeClass('open');
			$list.addClass('open');
		}

		return false;
	}

	TabFilter.prototype.filterChanged = function()
	{
		var value = null;
		var showAll = true;
		var valueToRowsMap = null;
		var $selectedRows = this.$allRows;

		this.$allRows.addClass('filtered-out');

		this.$uls.each(function() {
			value = $(this).data('selectedItem').text();
			valueToRowsMap = $(this).data('valueToRowsMap');
			if (valueToRowsMap && value in valueToRowsMap)
			{
				$selectedRows = $selectedRows.filter(valueToRowsMap[value]);
				showAll = false;
			}
		});

		if (this.$noResultsTFoot)
		{
			this.$noResultsTFoot.hide();
		}

		if (showAll)
		{
			this.$allRows.removeClass('hidden filtered-out');
			this.$allRows.filter((function(obj)
				{
					return function(i, e)
					{
						if (i>=obj.rowsVisibled)
						{
							return true;
						}
						return false;
					}
				}(this)
			)).addClass('hidden');
		}
		else
		{
			$selectedRows.removeClass('hidden filtered-out');
			if (!$selectedRows.length)
			{
				this.showNoResultsTFoot();
			}
		}

		fix_row_parity(this.$tbody);

		toggle_show_more(this.$tbody);
	}

	TabFilter.prototype.adjustFilterRowCss = function($filterRow)
	{
		var $menu = $('.stats-shared-menu .ifmenu');
		var $spacer = $('.stats-shared-menu .color-spacer:visible').last();
		var menuMargin = null;

		if ($spacer.length)
		{
			menuMargin = $menu.css('marginLeft');
			$filterRow.css({
				backgroundColor: $spacer.css('backgroundColor'),
				borderBottomWidth: $spacer.css('borderBottomWidth'),
				borderBottomStyle: $spacer.css('borderBottomStyle'),
				borderBottomColor: $spacer.css('borderBottomColor'),
				marginTop: '-' + $spacer.css('borderBottomWidth'),
				paddingTop: 1,
				paddingRight: 0,
				paddingBottom: parseInt($spacer.css('height'), 10) + 1,
				paddingLeft: menuMargin
			});
			$filterRow.find('.list-wrapper').css({
				marginRight: menuMargin
			});
		}
	}

	TabFilter.prototype.showNoResultsTFoot = function()
	{
		// To maintain header cells padding, this complicated approach has to be taken.
		if (!this.$noResultsTFoot)
		{
			this.$noResultsTFoot = $('<tfoot class=no-results-found />');
			var colspan = this.$tbody.find('tr:first-child > td').length;
			var $tr = $('<tr/>').append(
				$('<td/>').append(
					$('<div/>').append(
						$('<span/>', { text: this.noResultsText })
					)
				)
			);
			for (var i = 1; i < colspan; i++)
			{
				$tr.append('<td/>');
			}

			this.$noResultsTFoot.append($tr).insertAfter(this.$tbody);
		}

		this.$noResultsTFoot.show();
	}

	TabFilter.prototype.processValue = function(columnName, value)
	{
		// remove former teams
		if (columnName === 'team_name')
		{
			var bracketPos = value.indexOf(' (');
			if (bracketPos !== -1)
			{
				return value.substring(0, bracketPos);
			}
		}

		return value;
	}

	TabFilter.prototype.alphaSort = function(a, b)
	{
		// localeCompare() is slow, might cause performance problems
		return a.localeCompare(b);
	}

	return TabFilter;

})(jQuery);
var class_glib_ui = function()
{
	var match_id_prefix = 'glib-event-';

	var participant_id_prefix = 'glib-participant-';
	var participant_list_prefix = 'glib-participants-';


	this.get_participants_from_class = function(cname)
	{
		if (cname.indexOf(participant_list_prefix) !== -1) {
			return cname.substr(participant_list_prefix.length).split('-');
		}

		return false;
	};


	this.get_participant_selector = function(participants)
	{
		var selector = [];

		for (var p=0; p<participants.length; p++) {
			selector.push("." + participant_id_prefix + participants[p]);
		}

		return selector.join(', ');
	};


	this.get_event_from_class = function(cname)
	{
		if (cname.indexOf(match_id_prefix) !== -1) {
			return cname.substr(match_id_prefix.length);
		}

		return false;
	};
};

var glib_ui = new class_glib_ui();/**
 * Table data sorting.
 * Sorting happens in two steps. Mappers add normalized values into map
 * which is then sorted by sorters. This has performance reasons as described
 * in http://blog.rodneyrehm.de/archives/14-Sorting-Were-Doing-It-Wrong.html.
 *
 * @requires jQuery
 */

/**
 * Example usage:
 * var tsort = new gTableSort({"name":"table_sort", [default_order_cb: callback]});
 * default_order_cb            -- called if return from sort function is 0
 * $("table").table_sort({
 * 	"id_column":"int",         -- Columns with CSS class 'id_column' will be sortable as integer
 * 	"name_column":"char",      -- '.name_column' will be sortable as string
 * 	"weird":"custom_type"      -- '.weird' will be sortable by registered custom type
 * });
 */

var gTableSort = function(new_options)
{
	// Table data
	var tables = [];
	var table_count = 0;

	// Object global options
	var options = $.extend({
		"name":"table_sort",
		"default_sort":"asc",
		"idRewrite":false  // true if use with standings + ajax (identical ids)
	}, new_options);

	// Runtime options
	var sort_opts = {
		"direction":options.default_sort,
		"cellIndex":0
	};

	/**
	 * Mapper receives table row DOMNode object and extracts value at specified
	 * position. Returned value is then normalized to be easily comparable.
	 */
	var mappers =
	{
		"number": function(row) {
			var cell = row.childNodes[sort_opts.cellIndex] || { textContent: 0 };
			var value = +(cell.textContent || cell.innerText);
			return isNaN(value) ? 0 : value;
		},
		"string": function(row) {
			var cell = row.childNodes[sort_opts.cellIndex] || { textContent: '' };
			var value = cell.textContent || cell.innerText;
			return value.trim().toLowerCase();
		}
	};

	/**
	 * Sorter receives two arrays to compare. It compares second items that
	 * are values extracted earlier by mappers. Returned value be either -1, 0 or 1.
	 */
	var sorters =
	{
		"number": function(a, b) {
			return sort_opts.direction == 'asc' ? (a-b) : (b-a);
		},
		"string": function(a, b) {
			var num;
			if (a == b)
			{
				return 0;
			}
			num = a.localeCompare(b);
			return sort_opts.direction == 'asc' ? (num < 0 ? -1:1) : (num > 0 ? -1:1);
		}
	};

	/**
	 * Form a report
	 * @param string msg Message
	 */
	var report = function(msg) {
		return "gTableSort: " + msg;
	};

	/**
	 * Register type for this gTableSort object
	 * @param string   name
	 * @param function lambda - should return sort function that uses sort_opts
	 */
	this.register_type = function(name, mapper, sorter) {
		if (typeof sorters[name] == 'undefined') {
			mappers[name] = mapper(sort_opts);
			sorters[name] = sorter(sort_opts);
		} else {
			throw report("Sorter " + name + " already exists");
		}
	};

	/**
	 * Register table for sorting
	 * @param jQuery table   Instance of table
	 * @param object columns Assoc list of columns and types
	 */
	this.add_table = function(table, columns, default_col) {
		var tid
		var table = $(table);
		if (!(tid = table.attr('id')) || options.idRewrite) {
			table.attr('id', tid = options.name + '_' + table_count);
		}

		if (typeof tables[tid] == 'undefined') {
			var cols = jQuery.extend(true, {}, columns);
			tables[tid] = {"table":table, "cols":cols, "default_col": default_col};
			bind_events(table, cols);
			table_count ++;
		} else report("Table '" + tid +"' is already registered. Check for duplicate IDs.");
	};


	/**
	 * Go through table head columns and associate all texts with types
	 * @param table
	 * @param columns
	 * @return gTableSort
	 */
	var bind_events = function(table, columns) {
		// Use all defined columns in table
		$.each(columns, function(class_name, params)
		{
			// Find all thead TD and TH and create links in there
			var $cells = $()
				.add($("thead tr td." + class_name, table))
				.add($("thead tr th." + class_name, table));

			$.each($cells, function() {
				var $cell = $(this);

				$cell.wrapInner('<span class=txt />')
					.append('<span class=arrow />')
					.wrapInner('<a href=# />')
					.addClass('gTableSort-switch');

				// Bind click event to all links located in table head
				$cell.children('a')
					.addClass("gTableSort-off")
					.addClass("gTableSort-" + params[1])
					.bind("click", function(e) {
						do_sort(e, $cell, class_name, $(table), params[0]);
					});

			});
		});

		// Mark default column
		if (typeof tables[table.attr('id')].default_col != 'undefined')
		{
			table.find("thead tr ."+tables[table.attr('id')].default_col+" a").removeClass("gTableSort-off").addClass("gTableSort-on");
			var el = table.find("thead tr ."+tables[table.attr('id')].default_col)[0];
			if (el)
			{
				var colName = tables[table.attr('id')].default_col.replace('col_', '');
				table.find('tbody').find('tr').find('td:eq(' + el.cellIndex + ')').addClass('col_sorted');
				tables[table.attr('id')].cols[colName][1] = tables[table.attr('id')].cols[colName][1] ? 'desc' : 'asc';
			}

		}

		return this;
	};

	var do_sort = function(e, $cell, col_class, $table, var_type)
	{
		var table_id = $table.attr('id');
		var cellIndex = $cell[0].cellIndex;
		var prevCellIndex = sort_opts.cellIndex

		// Preset direction and cell index
		sort_opts.cellIndex = cellIndex;
		sort_opts.direction = tables[table_id].cols[col_class][1];
		// when sorting by some columns, default order has to be reversed
		sort_opts.revert_default_order = col_class === 'points'
			|| col_class === 'goals'
			|| col_class === 'assists';

		// Pick elements by link location
		var thead = $cell.parents("thead");
		var tbody = thead.parent().attr('id') == table_id
			? thead.nextAll("tbody").first()
			: $table.find("tbody");

		var children = tbody.children().toArray();
		var map = [], i, l, fragment;

		// just revert rows
		if (cellIndex == prevCellIndex)
		{
			tbody.append(children.reverse());
		}
		// sort rows
		else
		{

			// create map that will be sorted
			for (i=0, l=children.length; i < l ; i++) {
				map.push([i, mappers[var_type](children[i])]);
			}

			// sort map
			map.sort(function(a, b) {
				var result = sorters[var_type](a[1], b[1]);
				if (result == 0 && typeof options.default_order_cb === 'function')
				{
					result = options.default_order_cb(
						children[a[0]],
						children[b[0]],
						sort_opts
					);
				}
				return result;
			});

			// add rows to tbody ordered by map
			fragment = document.createDocumentFragment();
			for (i=0, l=map.length; i < l ; i++) {
				fragment.appendChild(children[map[i][0]]);
			}
			tbody[0].appendChild(fragment);

			tbody.find('td').removeClass('col_sorted');
			tbody.find('tr td:nth-child(' + (cellIndex+1) + ')').addClass('col_sorted');

			// Reset link classes
			thead.find("tr a").removeClass("gTableSort-on").addClass("gTableSort-off");
			thead.find("." + col_class + " a")
				.removeClass("gTableSort-off")
				.addClass("gTableSort-on");

		}

		thead.find("." + col_class + " a")
			.toggleClass("gTableSort-desc", sort_opts.direction === 'desc')
			.toggleClass("gTableSort-asc",  sort_opts.direction === 'asc');

		// Save direction and color rows
		switch_direction(table_id, col_class);
		fix_row_parity(tbody);

		e.preventDefault();
		return false;
	}


	/**
	 * Fix table CSS classes odd/even
	 * @param jQuery table
	 */
	var fix_row_parity = function (parent_el)
	{
		var $all = $('tr', parent_el);
		var $filteredOut = $all.filter('.filtered-out');
		var numHidden = $all.filter('.hidden').not($filteredOut).length;
		var numToShow = $(parent_el).data('visibleRows') || $all.length - numHidden;

		$all.slice(numToShow).addClass('hidden');

		$all.not($filteredOut).slice(0, numToShow)
			.removeClass('hidden')
			.each(function(i) {
				$(this)
					.toggleClass('odd',  i % 2 === 0)
					.toggleClass('even', i % 2 === 1);
			});
	};


	/**
	 * Public wrapper to fix parity of all rows and tables
	 * @returns this
	 */
	this.fix_parity = function()
	{
		for (var table_name in tables) {
			var bodies = tables[table_name].table.find('tbody');
			for (var j=0; j<bodies.length; j++) {
				fix_row_parity(bodies[j]);
			}
		}

		return this;
	}


	/**
	 * Rotate and save direction for a column
	 * @param string tid       Table ID
	 * @param string col_class Class of column
	 */
	var switch_direction = function(tid, col_class) {
		tables[tid].cols[col_class][1] = sort_opts.direction == 'asc' ? 'desc':'asc';
	};


	/**
	 * Object init
	 * @return void
	 */
	var ready = function() {
		// Register function in jQuery to make it easier

		$.fn[options.name] = function(cols, default_col) {
			$(this).each(function(index, el) {
				eval(options.name).add_table(el, cols, default_col);
			});
		};
		return true;
	}();

};

// Trim functions
if (typeof String.trim == 'undefined') {

	String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g,""); }
	String.prototype.ltrim = function() { return this.replace(/^\s+/,""); }
	String.prototype.rtrim = function() { return this.replace(/\s+$/,""); }

};


var getElementsByClassName = function(className, root, tagName) {
	root = root || document.body;

	if (typeof document.getElementsByClassName == 'function') {
		return root.getElementsByClassName(className);
	}

	if (root.querySelectorAll) {
			tagName = tagName || '';
			return root.querySelectorAll(tagName + '.' + className);
	}

	var tagName = tagName || '*', _tags = root.getElementsByTagName(tagName), _nodeList = [];
	for (var i = 0, _tag; _tag = _tags[i++];) {
		if (_tag.className.match(className)) {
			_nodeList.push(_tag);
		}
	}
	return _nodeList;
};var fstable_data = {"datetime_format_tables":"d.m.Y","datetime_format_draws":"d\/m","text_loading":"Loading ...","proxy_url":"\/x\/feed\/proxy"};class_fsTable = function()
{
	var initialized = false;
	var fst = this;

	/** Binds common event handlers to enhance UI.
	 * @param {jQuery} tableContext
	 */
	this.bind_events = function(tableContext)
	{
		var tt = new tooltip(tableContext.attr('id'));
		var tt_selector = [
			'.stats-table tbody td:first-child',
			'.form div a',
			'.last_5 div a',
			'.link-inactive',
			'.playoff-box',
			'.playoff-box-hover',
			'.playoff-box-invert',
			'.glib-live-score',
			'.glib-live-rank-up',
			'.glib-live-rank-down',
			'.glib-live-value'
		];

		tableContext.delegate(tt_selector.join(', '), "mouseenter", function (e) {
			tt.show($(this).get(0), e);
		});

		tableContext.delegate(tt_selector.join(', '), "mouseleave", function (e) {
			tt.hide($(this).get(0), e);
		});

		// Bind participant higlight and detail opening
		$('.form div a, .last_5 div a, a.glib-live-score').each(function(i, el) {
			var el = $(el);

			if (typeof el.attr('class') == 'undefined')
			{
				return;
			}

			var cname = el.attr('class').split(' ');

			for (var i = 0, _len = cname.length; i<_len; i++)
			{
				var event = glib_ui.get_event_from_class(cname[i]);

				if (event)
				{
					el.bind('click', {"event_id":event}, function(e) {
						e.preventDefault();
						if (cjs.Util.Config.get("app","detail","version") == 2)
						{
							var re = / glib-partnames-([^ ]+) /
							var partnames = re.exec(' ' + el.attr('class') + ' ');
							if (partnames && typeof partnames[1] != 'undefined')
							{
								partnames = partnames[1].split(';');
								detail_open('g_0_' + e.data.event_id, null, partnames[0], typeof partnames[1] != 'undefined' ? partnames[1] : null, $('#season_url').text());
							}
						}
						else
						{
							detail_open('g_0_' + e.data.event_id);
						}
					});
				}
			}
		});
	};

	/** Setup sorting for tables
	 * @param jQuery tableContext
	 */
	this.setup_tablesort = function(tableContext)
	{
		tableContext.find("table").fsTableSort({
			"assists":["number", "desc"],
			"assists1":["number", "desc"],
			"assists2":["number", "desc"],
			"avg_goals_match":["number", "desc"],
			"rank":["number", "asc"],
			"player_name":["string", "asc"],
			"team_name":["string", "asc"],
			"participant_name":["string", "asc"],
			"matches":["number", "desc"],
			"wins":["number", "desc"],
			"wins_ot":["number", "desc"],
			"draws":["number", "desc"],
			"losses":["number", "asc"],
			"losses_ot":["number", "asc"],
			"points":["number", "desc"],
			"goals":["resultSum", "desc"],
			"ponumbers":["int", "desc"],
			"over":["number", "desc"],
			"under":["number", "desc"],
			"winning_percentage":["number", "desc"],
			"for_against_percentage":["number", "desc"],
			"htft_ww":["number", "desc"],
			"htft_wd":["number", "desc"],
			"htft_wl":["number", "desc"],
			"htft_dw":["number", "desc"],
			"htft_dd":["number", "desc"],
			"htft_dl":["number", "desc"],
			"htft_lw":["number", "desc"],
			"htft_ld":["number", "desc"],
			"htft_ll":["number", "desc"]
		}, 'col_rank');
	};

	/** Translate utimes in tables into human time respecting the users' timezone
	 * @param jQuery tableContext
	 */
	this.setup_table_timedata = function(tableContext)
	{
		var selector = [
			'a.form-bg',
			'a.form-bg-last'
		];

		tableContext.find(selector.join(",")).each(function(i, el) {
			var item = $(el);
			var title = item.attr('title');
			var data = typeof title == 'undefined' ? '':title.split("\n");
			var dataMax = data.length - 1;

			if (dataMax >= 1 && data[dataMax] > 0)
			{
				startDateTimeStr = timestamp2date(fstable_data.datetime_format_tables, data[dataMax], get_gmt_offset());

				var dataTmp = data[0] + "\n";

				if (dataMax == 2)
				{
					dataTmp += data[1] + "\n";
				}

				item.attr('title', dataTmp + startDateTimeStr);
			}
		});

		fst.setup_timedata($('td.pdate'), fstable_data.datetime_format_tables);
		fst.setup_timedata($('.match .matches .date'), fstable_data.datetime_format_draws);
	};

	/** Wrapper to make setting timedata DRY
	 * @param jQuery context
	 */
	this.setup_timedata = function(context, format)
	{
		context.each(function() {
			var startDateTime = $(this).html();

			if (startDateTime && startDateTime > 0)
			{
				startDateTimeStr = timestamp2date(format, startDateTime, get_gmt_offset());
				$(this).html(startDateTimeStr);
			}
		});
	};

	/** Bind standings tab with all these functions
	 * @param jQuery tableContext
	 */
	this.setup_table_tab = function(tableContext)
	{
		fst.bind_events(tableContext);
		fst.setup_tablesort(tableContext);
		fst.setup_table_timedata(tableContext);
	};

	this.replace_stats_data_url = function()
	{
		$("#glib-stats-menu li a").each(function(i, el) {
			var e = $(el);
			e.attr('href', e.attr('href').replace('{feed_domain}', "d." + document.domain));
		});
	};

	/** Setup standings table proxy
	 * @return void
	 */
	this.init_table_proxy = function()
	{
		this.replace_stats_data_url();

		stats_proxy.fetch_data = function(url, extra_data, callback) {
			var req =  new cjs.AjaxJqObject(url, cjs.feedProxy, callback, extra_data, void 0, function(){return u_304;}, ie6, webkit, feed_sign);
			req.update();
		};

		if ((typeof cjs.feedProxy == 'undefined' || !cjs.feedProxy.isReady()) && !tournament)
		{
			cjs.feedProxy = new cjs.AjaxProxy('http://' + base_url.replace('www.', 'd.') + fstable_data.proxy_url, this.table_callback);
		}
		else
		{
			this.table_callback();
		}
	};

	/** Callback, that is passed to table proxy before displaying tab
	 * @return void
	 */
	this.table_callback = function()
	{
		return stats_proxy.init({
			"use_links_hash": (function()
			{
				if (typeof standingsUseLinksHash !== 'undefined')
				{
					return standingsUseLinksHash;
				}
				return true;
			})(),
			'before_tab_ready':function(tab) {
				fst.higlight_detail_participants($(tab.box));
				fst.setup_table_tab($(tab.box));
			},
			'tab_ready':function(tab) {
				fst.setup_table_playoff($(tab.box));
				if (typeof TabFilter == 'function')
				{
					new TabFilter(tab);
				}
				setTimeout(function (){
					fst.scroll_to_detail_participants($(tab.box));
				}, 0);
			},
			'tab_visible':function(tab) {
				if (typeof StatsDrawView !== 'undefined')
				{
					StatsDrawView.init(cjs.Util.Config.get("app","detail","version"));
					StatsDrawView.update_size();
				}
			},
			'text_loading':fstable_data.text_loading
		});
	};

	/** Setup playoff auto scroll and resize events
	 * @param jQuery tableContext
	 */
	this.setup_table_playoff = function(tableContext)
	{
		if (tableContext.find('#playoff-env').length > 0)
		{
			StatsDrawView.init(cjs.Util.Config.get("app","detail","version"));

			var button = $('.playoff-scroll-button');
			var buttonHeight = button.height();
			var playoffEnv = $('#playoff-env').get(0);
			var d = ($('#playoff-links').height() + $('#playoff-header').height()) - (parseInt(button.css('margin-top')) + button.height() / 2) + 8;

			if (typeof playoffEnv.getBoundingClientRect == 'function')
			{
				$(window).scroll(function()
				{
					var x = playoffEnv.getBoundingClientRect();
					var windowHeight = $(window).height();
					var xHeight = windowHeight;

					if (x.top > 0)
					{
						xHei