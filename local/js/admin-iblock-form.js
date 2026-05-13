document.addEventListener('DOMContentLoaded', function () {
    // Скрываем ненужные строки на форме редактирования элемента инфоблока
    ['ACTIVE_FROM', 'ACTIVE_TO', 'XML_ID', 'TAGS'].forEach(function (id) {
        var row = document.getElementById('row_' + id);
        if (row) row.style.display = 'none';
    });

    // Скрываем вкладку SEO (edit14)
    var tabs = document.querySelectorAll('.adm-detail-tab-bar a, .tabControl_tab');
    tabs.forEach(function (tab) {
        var href = tab.getAttribute('href') || '';
        var onclick = tab.getAttribute('onclick') || '';
        if (href.includes('edit14') || onclick.includes('edit14')) {
            var li = tab.closest('li') || tab.parentNode;
            if (li) li.style.display = 'none';
        }
    });

    var seoDiv = document.getElementById('edit14');
    if (seoDiv) seoDiv.style.display = 'none';
});
