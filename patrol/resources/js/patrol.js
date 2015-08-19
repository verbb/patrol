'use strict'

new Vue({
	el: '.patrol',
	data: {
		newIp: '',
		newUrl: '',
		sslRoutingRestrictedUrls: $settings.sslRoutingRestrictedUrls,
		maintenanceModeAuthorizedIps: $settings.maintenanceModeAuthorizedIps
	},
	methods: {
		addIp: function() {
			var ip = this.newIp && this.newIp.trim();

			if (ip) {
				this.maintenanceModeAuthorizedIps.push(ip);
				this.newIp = '';
			}
		},
		removeIp: function(ip) {
			this.maintenanceModeAuthorizedIps.$remove(ip);
		},
		addUrl: function() {
			var url = this.newUrl && this.newUrl.trim();

			if (url) {
				this.sslRoutingRestrictedUrls.push(url);
				this.newUrl = '';
			}
		},
		removeUrl: function(url) {
			this.sslRoutingRestrictedUrls.$remove(url);
		}
	}
});
