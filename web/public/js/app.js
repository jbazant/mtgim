// Generated by CoffeeScript 1.8.0
(function() {
  var contactFormInit, fbPageInit, mobileInitCallback, pageShowCallback, searchFormCallback;

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

  searchFormCallback = function(e) {
    var cardname;
    e.preventDefault();
    cardname = $('#cardname', page).val();
    $.mobile.changePage(baseUrl + '/index/search#find-card-' + encodeURIComponent(cardname));
  };

  pageShowCallback = function() {
    var page, pageActions, pageId;
    page = $.mobile.activePage;
    pageId = page.attr('id');
    if (pageId !== 'page-index') {
      $('#searchform', page).on('submit', pageShowCallback);
    }
    pageActions = {
      'page-search': function() {
        var currentPage;
        return currentPage = new SearchPage(page, activityTracker);
      },
      'page-contact': function() {
        return contactFormInit(page);
      },
      'page-index': function() {
        var c;
        return c = $('#cardname', page).focus();
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

  mobileInitCallback = function() {
    $.mobile.ajaxFormsEnabled = false;
    $.mobile.hashListeningEnabled = false;
  };

}).call(this);
