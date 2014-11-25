YUI.add('moodle-auth_unilogin-link_injector', function(Y) {
	M.auth_unilogin = M.auth_unilogin || {};
	M.auth_unilogin.link_injector = {
		init: function(link, text, selector) {
			var elem = Y.one(selector),
				link = Y.Node.create('<a href="' + link + '">' + text + "</a>");

			console.log(selector)
			console.log(elem)
			elem.insertBefore(link);
		}
	};
}, '@VERSION@', {
  requires: ['node']
});