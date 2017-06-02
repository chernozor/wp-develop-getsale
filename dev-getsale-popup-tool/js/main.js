(function (d, w, c) {
    w[c] = {
        projectId: parseInt(getsale_vars.project_id)
    };

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () {
            n.parentNode.insertBefore(s, n);
        };
    s.type = "text/javascript";
    s.async = true;
    s.src = "//rt.getsale.io/loader.js";
    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else {
        f();
    }

})(document, window, "getSaleInit");

function getsale_add() {
    (function (w, c) {
        w[c] = w[c] || [];
        w[c].push(function (getSale) {
            getSale.event('add-to-cart');
        });
    })(window, 'getSaleCallbacks');
}

jQuery(document).ready(function () {
    if (jQuery('body.tax-product_cat').length) {
        (function (w, c) {
            w[c] = w[c] || [];
            w[c].push(function (getSale) {
                getSale.event('cat-view')
            });
        })(window, 'getSaleCallbacks');
    }
});

jQuery(document).ready(function () {
    if (jQuery('body.single-product').length) {
        (function (w, c) {
            w[c] = w[c] || [];
            w[c].push(function (getSale) {
                getSale.event('item-view')
            });
        })(window, 'getSaleCallbacks');
    }
});

jQuery(document).ready(
    function () {
        jQuery("button.single_add_to_cart_button").each(function () {
            var my_funct = "getsale_add();";
            jQuery(this).attr('onclick', my_funct);
        });

        jQuery("a.add_to_cart_button").each(function () {
            var my_funct = "getsale_add();";
            jQuery(this).attr('onclick', my_funct);
        });
    });

jQuery(document).ready(function () {
    if (jQuery('body.woocommerce-order-received').length) {
        (function (w, c) {
            w[c] = w[c] || [];
            w[c].push(function (getSale) {
                getSale.event('user-reg');
                getSale.event('success-order');
            });
        })(window, 'getSaleCallbacks');

    }
});