(function($) {
	if (!window['wordfenceExt']) {
		window['wordfenceExt'] = {
			nonce: false,
			loadingCount: 0,
			isSmallScreen: false,
			init: function(){
				this.nonce = WordfenceAdminVars.firstNonce;
				this.isSmallScreen = window.matchMedia("only screen and (max-width: 500px)").matches;
			},
			showLoading: function(){
				this.loadingCount++;
				if (this.loadingCount == 1) {
					jQuery('<div id="wordfenceWorking">Wordfence is working...</div>').appendTo('body');
				}
			},
			removeLoading: function(){
				this.loadingCount--;
				if(this.loadingCount == 0){
					jQuery('#wordfenceWorking').remove();
				}
			},
			autoUpdateChoice: function(choice){
				this.ajax('wordfence_autoUpdateChoice', {
						choice: choice
					},
					function(res){ jQuery('#wordfenceAutoUpdateChoice').fadeOut(); },
					function(){ jQuery('#wordfenceAutoUpdateChoice').fadeOut(); }
				);
			},
			misconfiguredHowGetIPsChoice : function(choice) {
				this.ajax('wordfence_misconfiguredHowGetIPsChoice', {
						choice: choice
					},
					function(res){ jQuery('#wordfenceMisconfiguredHowGetIPsNotice').fadeOut(); },
					function(){ jQuery('#wordfenceMisconfiguredHowGetIPsNotice').fadeOut(); }
				);
			},
			switchLiveTrafficSecurityOnlyChoice: function(choice) {
				this.ajax('wordfence_switchLiveTrafficSecurityOnlyChoice', {
						choice: choice
					},
					function(res){ jQuery('#switchLiveTrafficSecurityOnlyChoice').fadeOut(); },
					function(){ jQuery('#switchLiveTrafficSecurityOnlyChoice').fadeOut(); }
				);
			},
			dismissAdminNotice: function(nid) {
				this.ajax('wordfence_dismissAdminNotice', {
						id: nid
					},
					function(res){ jQuery('.wf-admin-notice[data-notice-id="' + nid + '"]').fadeOut(); },
					function(){ jQuery('.wf-admin-notice[data-notice-id="' + nid + '"]').fadeOut(); }
				);
			},
			setOption: function(key, value, successCallback) {
				var changes = {};
				changes[key] = value;
				this.ajax('wordfence_saveOptions', {changes: JSON.stringify(changes)}, function(res) {
					if (res.success) {
						typeof successCallback == 'function' && successCallback(res);
					}
				});
			},
			ajax: function(action, data, cb, cbErr, noLoading){
				if(typeof(data) == 'string'){
					if(data.length > 0){
						data += '&';
					}
					data += 'action=' + action + '&nonce=' + this.nonce;
				} else if(typeof(data) == 'object'){
					data['action'] = action;
					data['nonce'] = this.nonce;
				}
				if(! cbErr){
					cbErr = function(){};
				}
				var self = this;
				if(! noLoading){
					this.showLoading();
				}
				jQuery.ajax({
					type: 'POST',
					url: WordfenceAdminVars.ajaxURL,
					dataType: "json",
					data: data,
					success: function(json){
						if(! noLoading){
							self.removeLoading();
						}
						if(json && json.nonce){
							self.nonce = json.nonce;
						}
						cb(json);
					},
					error: function(){
						if(! noLoading){
							self.removeLoading();
						}
						cbErr();
					}
				});
			},
			parseEmails: function(raw) {
				var emails = [];
				if (typeof raw !== 'string') {
					return emails;
				}

				var rawEmails = raw.replace(/\s/g, '').split(',');
				for (var i = 0; i < rawEmails.length; i++) {
					//From https://html.spec.whatwg.org/multipage/forms.html#valid-e-mail-address
					if (/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(rawEmails[i])) {
						emails.push(rawEmails[i]);
					}
				}
				return emails;
			},
			onboardingProcessEmails: function(emails, subscribe, touppAgreed) {
				var subscribe = !!subscribe;
				wordfenceExt.setOption('alertEmails', emails.join(', '));
				
				if (touppAgreed) {
					this.ajax('wordfence_recordTOUPP', {}, function(res) {
						//Do nothing
					});
				}

				if (subscribe) {
					this.ajax('wordfence_mailingSignup', {emails: JSON.stringify(emails)}, function(res) {
						//Do nothing
					});
				}
			},
			onboardingInstallLicense: function(license, successCallback, errorCallback) {
				this.ajax('wordfence_installLicense', {license: license}, function(res) {
					if (res.success) {
						typeof successCallback == 'function' && successCallback(res);
					}
					else if (res.error) {
						typeof errorCallback == 'function' && errorCallback(res);
					}
				});
			}
		};
	}
	
	$(function() {
		wordfenceExt.init();

		$('.wf-dismiss-link').on('click', function() {
			$('#wf-extended-protection-notice').css({
				opacity: .75
			});
			$.get(this.href, function() {
				$('#wf-extended-protection-notice').fadeOut(1000);
			});
			return false;
		});
	});
})(jQuery);
