// Generated by CoffeeScript 1.8.0
(function() {
  var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  window.SearchPage = (function() {
    function SearchPage(page, activityTracker) {
      this.page = page;
      this.activityTracker = activityTracker;
      this.tabsactivateCallback = __bind(this.tabsactivateCallback, this);
      this.initResultHolder = __bind(this.initResultHolder, this);
      this.shopChanged = __bind(this.shopChanged, this);
      this.form = $('#searchform', this.page);
      this.shopSelect = $('#shop-select', this.page);
      this.shopResultsHolders = $('.shop-results', this.page);
      this.typeResultsHolders = $('.result-holder', this.page);
      this.tabResults = {};
      this.lastActiveTab = null;
      this.lastActiveTabIndex = 0;
      this.lastShopResultHolder = null;
      this.fadeSpeed = 'fast';
      this.initForm();
      this.initResultHolders(this.activityTracker);
      this.initTabs();
      this.initShopSelect();
      this.cardPreview = new CardDetailPopup($('#cardImgPopup'), this.page);
    }

    SearchPage.prototype.initForm = function() {
      var at, cardmatch;
      at = this.activityTracker;
      cardmatch = window.location.href.match(/\/card\/(.*)$/);
      if (cardmatch && cardmatch[1]) {
        this.form.find('#cardname', this.page).val(decodeURIComponent(cardmatch[1]));
      } else {
        $.mobile.changePage(jsParams.baseUrl + '/');
      }
      return this.form.on('submit', function() {
        var c, card;
        c = $('#cardname', this);
        card = c.val();
        at.trackEvent('SearchPage', 'Form submit', card);
        window.history.pushState({
          id: card
        }, '', encodeURIComponent(card));
        c.blur();
      });
    };

    SearchPage.prototype.initShopSelect = function() {
      this.shopSelect.change(this.shopChanged);
      return this.shopChanged();
    };

    SearchPage.prototype.shopChanged = function() {
      var holder, newCallback, oldCallback, tabLinks;
      holder = $('#' + this.shopSelect.val(), this.page);
      tabLinks = holder.find('li a');
      oldCallback = (function(_this) {
        return function() {
          var p;
          p = new $.Deferred();
          if (_this.lastShopResultHolder !== null && holder !== _this.lastShopResultHolder) {
            _this.lastShopResultHolder.fadeOut(_this.fadeSpeed, p.resolve);
          } else {
            p.resolve();
          }
          return p.promise();
        };
      })(this);
      newCallback = (function(_this) {
        return function() {
          _this.lastShopResultHolder = holder;
          tabLinks.removeClass('ui-btn-active');
          tabLinks.eq(_this.lastActiveTabIndex).addClass('ui-btn-active');
          holder.tabs('option', 'active', _this.lastActiveTabIndex);
          _this.tabsactivateCallback(holder, _this.lastActiveTabIndex);
          return holder.fadeIn(_this.fadeSpeed);
        };
      })(this);
      return $.when(oldCallback()).then(newCallback);
    };

    SearchPage.prototype.initTabs = function() {
      var callback;
      callback = this.tabsactivateCallback;
      this.shopResultsHolders.each(function() {
        return $(this).tabs({
          'show': {
            'effect': 'fadeIn',
            'duration': 'fast'
          },
          'hide': {
            'effect': 'fadeOut',
            'duration': 'fast'
          }
        });
      });
      return this.shopResultsHolders.on('tabsactivate', function(e, ui) {
        if (ui.newTab) {
          callback($(this), ui.newTab.index());
        }
      });
    };

    SearchPage.prototype.initResultHolders = function(activityTracker) {
      var callback;
      callback = this.initResultHolder;
      return this.typeResultsHolders.each(function() {
        return callback(this, activityTracker);
      });
    };

    SearchPage.prototype.initResultHolder = function(holder, activityTracker) {
      var adapter, sr;
      adapter = $(holder).data('shop-id');
      sr = new SearchResult(holder, this.form, activityTracker);
      if (typeof this.tabResults[adapter] === 'undefined') {
        this.tabResults[adapter] = [];
      }
      return this.tabResults[adapter].push(sr);
    };

    SearchPage.prototype.tabsactivateCallback = function(holder, index) {
      var newTab;
      newTab = this.tabResults[holder.data('shop-id')][index];
      if (this.lastActiveTab) {
        if (this.lastActiveTab === newTab) {
          return;
        } else {
          this.lastActiveTab.resetActive();
        }
      }
      this.lastActiveTab = newTab;
      this.lastActiveTab.setActive();
      this.lastActiveTabIndex = index;
    };

    return SearchPage;

  })();

}).call(this);
