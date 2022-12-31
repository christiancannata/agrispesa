var cookie = document.cookie;

Object.defineProperty(document, 'cookie', {
    get: function () {
        if (this._value) {
            var parts = this._value.split('=');
            var name = parts[0];

            if (name) {
                console.log("cookie blocked: " + name);
                return '';
            }
        }

        return this._value;
    },
    set: function (val) {
        if (val) {
            var parts = val.split('=');
            var name = parts[0];

            if (name) {
                console.log("cookie blocked: " + name);
                return '';
            }
        }

        this._value = val;
        return this._value;
    }
});

document.cookie = cookie;