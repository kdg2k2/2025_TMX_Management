async function viewFileHandler(url) {
    try {
        // Kiểm tra URL hợp lệ
        let validUrl;
        try {
            validUrl = new URL(url);
        } catch (e) {
            console.error('URL không hợp lệ');
            return;
        }

        // Lấy phần mở rộng file
        const pathname = validUrl.pathname;
        const extension = pathname.split('.').pop().toLowerCase();

        // Danh sách định dạng Office
        const officeFormats = [
            'doc', 'docx', 'docm', 'dot', 'dotx', 'dotm',
            'xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm',
            'ppt', 'pptx', 'pptm', 'pps', 'ppsx', 'ppsm', 'pot', 'potx', 'potm'
        ];

        if (officeFormats.includes(extension)) {
            const encodedUrl = encodeURIComponent(url);
            const previewUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}&embedded=true`;
            window.open(previewUrl, '_blank');
        } else if (extension === 'pdf') {
            const encodedUrl = encodeURIComponent(url);
            const previewUrl = `https://mozilla.github.io/pdf.js/web/viewer.html?file=${encodedUrl}`;
            window.open(previewUrl, '_blank');
        } else {
            const a = document.createElement('a');
            a.href = url;
            a.download = pathname.split('/').pop() || 'download';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

    } catch (error) {
        console.error('Lỗi xử lý file:', error);
    }
}
