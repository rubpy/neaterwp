(function () {

  var _createContext = function (el) {
    var ctx = {
      root: el,

      data: {},
      refs: {},

      mount: function () { },
      unmount: function () { },
      update: function () { },
      _update: function () { },
    };

    function _buildRefQuery(query) {
      var q = {
        selector: '',
        optional: false,
      };

      if (typeof query === 'undefined' || query === null) {
        return q;
      }

      if (typeof query === 'string') {
        q.selector = query;
      } else if (typeof query === 'object') {
        if (typeof query.selector === 'string') {
          q.selector = query.selector;
        }

        if (query.hasOwnProperty('optional')) {
          q.optional = !!query.optional;
        }
      }

      return q;
    };
    function _lookupRefQuery(el, query) {
      if (el === null || !(el instanceof Node)) {
        return null;
      }
      if (query === null || typeof query !== 'object') {
        return null;
      }

      var res = null;
      if (query.selector.length !== 0) {
        try {
          res = el.querySelector(query.selector);
        } catch (ex) {
          if (!query.optional) {
            throw ex;
          }
        }
      }

      if (!res && !query.optional) {
        throw new Error(`unable to find ref "${query.selector}"`);
      }

      return res;
    };
    function _bindRefs(el, originalRefs) {
      var refs = {};

      if (originalRefs !== null && typeof originalRefs === 'object') {
        for (var name in originalRefs) {
          if (!originalRefs.hasOwnProperty(name)) {
            continue;
          }

          var q = _buildRefQuery(originalRefs[name]);
          refs[name] = _lookupRefQuery(el, q);
        }
      }

      return refs;
    };

    ctx.beforeMount = function () {
      this.refs = _bindRefs(this.root, this.refs);
    };

    return ctx;
  };
  var _attachElement = function (app, el) {
    if (app === null || typeof app !== 'object') {
      throw new Error('app must be an object');
    }
    if (!(el instanceof Node)) {
      throw new Error('el must be a Node');
    }
    var ctxKey = '__n_ctx';
    if (el[ctxKey] !== null && typeof el[ctxKey] === 'object') {
      return el[ctxKey];
    }

    var ctx = Object.assign({}, _createContext(el));
    _.merge(ctx, app);

    if (typeof ctx.beforeMount === 'function') {
      ctx.beforeMount();
      el[ctxKey] = ctx;
    }
    if (typeof ctx.mount === 'function') {
      ctx.mount();
    }

    if (typeof ctx.update === 'function') {
      ctx._update = ctx.update;
      ctx.update = function () {
        setTimeout((function () {
          this._update();
        }).bind(ctx), 0);
      };

      ctx.update();
    }

    return ctx;
  };
  var _attach = function (app, el) {
    if (el !== null && el instanceof NodeList) {
      el = Array.prototype.slice.call(el);
    }
    if (Array.isArray(el)) {
      return el.forEach(function (e) {
        return _attachElement(app, e);
      });
    } else if (!(el instanceof Node)) {
      throw new Error('el must be a Node, a NodeList, or an array');
    }

    return _attachElement(app, el);
  }

  /* -------------------------------------------------- */

  _attach({
    data: {
      checked: false,
    },
    refs: {
      checkbox: '.neaty-todos__item__done > input[type="checkbox"]',
    },

    mount: function () {
      this.data.checked = this.root.classList.contains('is-checked');
      this.refs.checkbox.disabled = true;

      // this.root.addEventListener('click', this.onClick.bind(this));
    },

    update: function () {
      this.root.classList.toggle('is-checked', this.data.checked);
      this.refs.checkbox.checked = this.data.checked;
    },

    onClick: function () {
      this.data.checked = !this.data.checked;
      this.update();
    },
  }, document.querySelectorAll('.neaty-todos .neaty-todos__item'));

})();
