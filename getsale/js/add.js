(function (w, c) {
    w[c] = w[c] || [];
    w[c].push(function (getSale) {
        getSale.event('add-to-cart');
    });
})(window, 'getSaleCallbacks');