// Generated by CoffeeScript 1.7.1
(function() {
  var contactFormInit, fbPageInit, pageShowCallback;

  fbPageInit = function() {
    var err;
    try {
      return FB.XFBML.parse();
    } catch (_error) {
      err = _error;
    }
  };

  contactFormInit = function(page) {
    return $('#contactform .anti_spam_holder', page).hide().find('#anti_spam_check').val('Anti-spam check');
  };

  pageShowCallback = function() {
    var page, pageActions, pageId;
    page = $.mobile.activePage;
    pageId = page.attr('id');
    pageActions = {
      'page-search': function() {
        var currentPage;
        return currentPage = new SearchPage(page, activityTracker);
      },
      'page-contact': function() {
        return contactFormInit(page);
      },
      'page-index': function() {
        return $('#cardname', page).focus();
      },
      'page-test': function() {
        return contactFormInit(page);
      }
    };
    fbPageInit();
    if (pageActions[pageId] != null) {
      pageActions[pageId]();
    }
  };

  window.activityTracker = new Tracking();

  $(document).bind('pageshow', pageShowCallback);

}).call(this);

//# sourceMappingURL=app.map
