/**
 * 日付文字列を返却する
 * @param delimiter 区切り文字、未入力の場合は「-」
 * @returns {string}
 */
Date.prototype.toFormatString = function (delimiter) {
    if (!delimiter) {
        delimiter = '-'
    }
    const y = this.getFullYear()
    const m = ("00" + (this.getMonth() + 1)).slice(-2)
    const d = ("00" + this.getDate()).slice(-2)

    return y + delimiter + m + delimiter + d
}

File.prototype.upload = function (url, callback, catchCallback, finallyCallback) {
    let formData = new FormData();
    formData.append('file', this);
    // this.$emit('input', this.loadingCount + 1);
    axios.post(url, formData)
    .then(res => {
        if (callback) {
            callback(res);
        }
    }).catch(error => {
        if (catchCallback) {
            catchCallback(error);
        }
    }).finally(() => {
        if (finallyCallback) {
            finallyCallback();
        }
    });
};

File.prototype.delete = function (url, path, callback, catchCallback, finallyCallback) {
    let delFormData = new FormData();
    delFormData.append('path', path);
    axios.post(url, delFormData)
    .then(res => {
        if (callback) {
            callback(res);
        }
    }).catch(error => {
        if (catchCallback) {
            catchCallback(error);
        }
    }).finally(() => {
        if (finallyCallback) {
            finallyCallback();
        }
    });

}