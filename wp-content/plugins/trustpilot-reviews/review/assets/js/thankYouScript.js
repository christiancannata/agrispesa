document.addEventListener('DOMContentLoaded', function() {
    if (typeof tp != 'function') {
        document.addEventListener('readystatechange', function() {
            document.readyState === 'complete' && tp('createInvitation', trustpilot_order_data['OrderData']);
        });
    } else {
        tp('createInvitation', trustpilot_order_data['OrderData']);
    }
});
