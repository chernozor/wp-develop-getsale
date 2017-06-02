(function (w, c) {
    w[c] = w[c] || [];
    w[c].push(function (getSale) {
        getSale.event('del-from-cart');
    });
})(window, 'getSaleCallbacks');